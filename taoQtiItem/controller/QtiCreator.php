<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */

namespace oat\taoQtiItem\controller;

use \core_kernel_classes_Resource;
use oat\tao\helpers\MediaRetrieval;
use oat\tao\model\media\MediaSource;
use oat\taoMediaManager\model\fileManagement\FileManager;
use oat\taoQtiItem\model\qti\Service;
use oat\taoQtiItem\helpers\Authoring;
use \taoItems_models_classes_ItemsService;
use \tao_actions_CommonModule;
use \tao_helpers_Uri;
use \tao_helpers_File;
use \tao_helpers_Http;
use \common_exception_Error;
use oat\taoQtiItem\model\CreatorConfig;
use oat\taoQtiItem\model\HookRegistry;
use oat\taoQtiItem\model\ItemModel;

/**
 * QtiCreator Controller provide actions to edit a QTI item
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoQTI

 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class QtiCreator extends tao_actions_CommonModule
{
    /**
     * create a new QTI item
     * 
     * @requiresRight id WRITE
     */
    public function createItem()
    {
        if(!\tao_helpers_Request::isAjax()){
            throw new \Exception("wrong request mode");
        }
        $clazz = new \core_kernel_classes_Resource($this->getRequestParameter('id'));
        if ($clazz->isClass()) {
            $clazz = new \core_kernel_classes_Class($clazz);
        } else {
            foreach ($clazz->getTypes() as $type) {
                // determine class from selected instance
                $clazz = $type;
                break;
            }
        }
        $service = \taoItems_models_classes_ItemsService::singleton();
        
        $label = $service->createUniqueLabel($clazz);
        $item = $service->createInstance($clazz, $label);
        
        if(!is_null($item)){
            $service->setItemModel($item, new \core_kernel_classes_Resource(ItemModel::MODEL_URI));
            $response = array(
                'label'	=> $item->getLabel(),
                'uri' 	=> $item->getUri()
            );
        } else {
            $response = false;
        }
        $this->returnJson($response);
    }

    public function index()
    {

        $config = new CreatorConfig();

        $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem');
        $creatorConfig = $ext->getConfig('qtiCreator');
        
        if(is_array($creatorConfig)){
            foreach($creatorConfig as $prop => $value){
                $config->setProperty($prop, $value);
            }
        }
        
        if($this->hasRequestParameter('instance')){
            //uri:
            $itemUri = tao_helpers_Uri::decode($this->getRequestParameter('instance'));
            $config->setProperty('uri', $itemUri);

            //get label:
            $rdfItem = new core_kernel_classes_Resource($itemUri);
            $config->setProperty('label', $rdfItem->getLabel());

            //set the current data lang in the item content to keep the integrity
            //@todo : allow preview in a language other than the one in the session
            $lang = \common_session_SessionManager::getSession()->getDataLanguage();
            $config->setProperty('lang', $lang);

            //base url:
            $url = tao_helpers_Uri::url('getFile', 'QtiCreator', 'taoQtiItem', array(
                        'uri' => $itemUri,
                        'lang' => $lang
            ));
            $config->setProperty('baseUrl', $url.'&relPath=');
        }

        // get the config media Sources
        $sources = array_keys(MediaSource::getMediaBrowserSources());
        $sources[] = 'local';
        $config->setProperty('mediaSources', $sources);

        //initialize all registered hooks:
        $hookClasses = HookRegistry::getRegistry()->getMap();
        foreach($hookClasses as $hookClass){
            $hook = new $hookClass();
            $hook->init($config);
        }

        $config->init();
        $this->setData('config', $config->toArray());
        $this->setView('QtiCreator/index.tpl');
    }

    public function getItemData(){

        $returnValue = array(
            'itemData' => null
        );

        if($this->hasRequestParameter('uri')){
            $lang = taoItems_models_classes_ItemsService::singleton()->getSessionLg();
            $itemUri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
            $itemResource = new core_kernel_classes_Resource($itemUri);
            $item = Service::singleton()->getDataItemByRdfItem($itemResource, $lang);
            if(!is_null($item)){
                $returnValue['itemData'] = $item->toArray();
            }
        }

        $this->returnJson($returnValue);
    }

    public function saveItem(){

        $returnValue = array('success' => false);

        if($this->hasRequestParameter('uri')){

            $uri = urldecode($this->getRequestParameter('uri'));
            $xml = file_get_contents('php://input');
            $rdfItem = new core_kernel_classes_Resource($uri);
            $itemService = taoItems_models_classes_ItemsService::singleton();

            //check if the item is QTI item
            if($itemService->hasItemModel($rdfItem, array(ItemModel::MODEL_URI))){

                $xml = Authoring::validateQtiXml($xml);

                //get the QTI xml
                $returnValue['success'] = $itemService->setItemContent($rdfItem, $xml);
                $returnValue['xml'] = $xml;
            }
        }

        $this->returnJson($returnValue);
    }

    public function getFile(){

        if($this->hasRequestParameter('uri') && $this->hasRequestParameter('lang') && $this->hasRequestParameter('relPath')){
            $uri = urldecode($this->getRequestParameter('uri'));
            $rdfItem = new core_kernel_classes_Resource($uri);

            $lang = urldecode($this->getRequestParameter('lang'));
            $relPath = urldecode($this->getRequestParameter('relPath'));

            $this->renderFile($rdfItem, $relPath, $lang);
        }
    }

    private function renderFile($item, $path, $lang){

        $folder = taoItems_models_classes_ItemsService::singleton()->getItemFolder($item, $lang);
        if(tao_helpers_File::securityCheck($path, true)){
            $mediaInfo = MediaRetrieval::getRealPathAndIdentifier($path);
            extract($mediaInfo);

            if($identifier === '' || $identifier === 'local'){
                $filename = $folder.$relPath;
            }else if($identifier === 'mediamanager'){
                $fileManager = FileManager::getFileManagementModel();
                $filename = $fileManager->retrieveFile($relPath);
            }else{
                $filename = $folder.$relPath;
            }

            //@todo : find better way to to this
            //load amd module
            if(!file_exists($filename) && file_exists($filename.'.js')){
                $filename = $filename.'.js';
            }
            tao_helpers_Http::returnFile($filename);
        }else{
            throw new common_exception_Error('invalid item preview file path');
        }
    }

}