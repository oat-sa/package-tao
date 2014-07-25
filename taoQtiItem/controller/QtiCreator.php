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
use oat\taoQtiItem\model\qti\Service;
use oat\taoQtiItem\controller\QtiCreator;
use oat\taoQtiItem\helpers\Authoring;
use \taoItems_models_classes_ItemsService;
use \tao_actions_CommonModule;
use \tao_helpers_Uri;
use \core_kernel_classes_Session;
use \tao_helpers_File;
use \tao_helpers_Http;
use \common_exception_Error;
use \common_Logger;

/**
 * QtiCreator Controller provide actions to edit a QTI item
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoQTI

 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class QtiCreator extends tao_actions_CommonModule
{

    public function index(){

        if($this->hasRequestParameter('instance')){
            $itemUri = tao_helpers_Uri::decode($this->getRequestParameter('instance'));
            $this->setData('uri', $itemUri);

            //set the current data lang in the item content to keep the integrity
            //@todo : allow preview in a language other than the one in the session
            $lang = core_kernel_classes_Session::singleton()->getDataLanguage();
            $this->setData('lang', $lang);

            $url = tao_helpers_Uri::url('getFile', 'QtiCreator', 'taoQtiItem', array(
                'uri' => $itemUri,
                'lang' => $lang
            ));
            
            $this->setData('baseUrl', $url.'&relPath=');
        }

        $this->setView('QtiCreator/index.tpl');
    }

    public function getItemData(){

        $returnValue = array(
            'itemData' => null
        );

        if($this->hasRequestParameter('uri')){
            $itemUri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
            $itemResource = new core_kernel_classes_Resource($itemUri);
            $item = Service::singleton()->getDataItemByRdfItem($itemResource);
            if(!is_null($item)){
                $returnValue['itemData'] = $item->toArray();
            }
        }

        echo json_encode($returnValue);
    }

    public function saveItem(){

        $returnValue = array('success' => false);

        if($this->hasRequestParameter('uri')){
            
            $uri = urldecode($this->getRequestParameter('uri'));
            $xml = file_get_contents('php://input');
            $rdfItem = new core_kernel_classes_Resource($uri);
            $itemService = taoItems_models_classes_ItemsService::singleton();

            //check if the item is QTI item
            if($itemService->hasItemModel($rdfItem, array(TAO_ITEM_MODEL_QTI))){

                $xml = Authoring::validateQtiXml($xml);

                //get the QTI xml
                $returnValue['success'] = $itemService->setItemContent($rdfItem, $xml);
                $returnValue['xml'] = $xml;
            }
        }

        echo json_encode($returnValue);
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
            $filename = $folder.$path;
            tao_helpers_Http::returnFile($filename);
        }else{
            throw new common_exception_Error('invalid item preview file path');
        }
    }

}
