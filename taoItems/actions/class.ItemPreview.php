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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2013 (update and modification) Open Assessment Technologies SA;
 */

use \oat\taoMediaManager\model\FileManager;
/**
 * Preview API 
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoItems
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoItems_actions_ItemPreview extends tao_actions_CommonModule
{
    public function forwardMe(){
        $item = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('uri')));
        $lang = DEFAULT_LANG;
        $previewUrl = taoItems_models_classes_ItemsService::singleton()->getPreviewUrl($item, $lang);
        $this->forwardUrl($previewUrl);
    }
    
    /**
     * @requiresRight uri READ
     */
    public function index(){
        $item = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('uri')));

        $itemService = taoItems_models_classes_ItemsService::singleton();
        if($itemService->hasItemContent($item) && $itemService->isItemModelDefined($item)){

            //this is this url that will contains the preview
            //@see taoItems_actions_LegacyPreviewApi
            $previewUrl = $this->getPreviewUrl($item);
            
            $this->setData('previewUrl', $previewUrl);
            $this->setData('client_config_url', $this->getClientConfigUrl());
            $this->setData('resultServer', $this->getResultServer());
        }

        $this->setView('ItemPreview/index.tpl', 'taoItems');
    }

    protected function getPreviewUrl($item, $options = array()){
        $code = base64_encode($item->getUri());
        return _url('render/'.$code.'/index', 'ItemPreview', 'taoItems', $options);
    }

    public function render(){
        $relPath = tao_helpers_Request::getRelativeUrl();
        list($extension, $module, $action, $codedUri, $path) = explode('/', $relPath, 5);
        
        $uri = base64_decode($codedUri);
        $item = new core_kernel_classes_Resource($uri);
        if($path == 'index'){
            $this->renderItem($item);
        } else {
            $this->renderResource($item, urldecode($path));
        }
    }

    protected function getRenderedItem($item){
        $itemModel = $item->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
        $impl = taoItems_models_classes_ItemsService::singleton()->getItemModelImplementation($itemModel);
        if(is_null($impl)){
            throw new common_Exception('preview not supported for this item type '.$itemModel->getUri());
        }
        return $impl->render($item, '');
    }

    private function renderItem($item){
        echo $this->getRenderedItem($item);
    }

    private function renderResource($item, $path){

        $identifier = '';
        $subPath = $path;
        if(strpos($path, '://') !== false){
            $identifier = substr($path, 0, strpos($path, '://'));
            $subPath = substr($path, strpos($path, '://') + 3);
        }

        //@todo : allow preview in a language other than the one in the session
        $lang = common_session_SessionManager::getSession()->getDataLanguage();
        $folder = taoItems_models_classes_ItemsService::singleton()->getItemFolder($item, $lang);
        if(tao_helpers_File::securityCheck($path, true)){
            if($identifier === 'taomgr'){
                $fileManager = FileManager::getFileManagementModel();
                $filename = $fileManager->retrieveFile($subPath);
            }
            else{
                $filename = $folder.$path;
            }
            tao_helpers_Http::returnFile($filename);
        }else{
            throw new common_exception_Error('invalid item preview file path');
        }
    }

    /**
     * Get the ResultServer API call to be used by the item.
     *
     * @return string A string representing JavaScript instructions.
     */
    protected function getResultServer(){
        return array(
            'module' => 'taoItems/runtime/ConsoleResultServer'
        );
    }

}
