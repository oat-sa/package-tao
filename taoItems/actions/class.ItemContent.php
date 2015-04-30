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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *               
 * 
 */


/**
 * Items Content Controller provide access to the files of an item
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItems
 */
class taoItems_actions_ItemContent extends tao_actions_CommonModule
{

    private function getBrowserImplementationClass($identifier){

        if(in_array($identifier,array('', 'local'))){
            return 'taoItems_helpers_ResourceManager';
        }
        return \oat\tao\model\media\MediaSource::getMediaBrowserSource($identifier);
    }

    private function getManagementImplementationClass($identifier){

        if(in_array($identifier,array('', 'local'))){
            return 'taoItems_helpers_ResourceManager';
        }
        return \oat\tao\model\media\MediaSource::getMediaManagementSource($identifier);
    }

    /**
     * Returns a json encoded array describign a directory
     * 
     * @throws common_exception_MissingParameter
     * @return string
     */
    public function files() {
        if (!$this->hasRequestParameter('uri')) {
            throw new common_exception_MissingParameter('uri', __METHOD__);
        }
        $itemUri = $this->getRequestParameter('uri');
        $item = new core_kernel_classes_Resource($itemUri);
        
        if (!$this->hasRequestParameter('lang')) {
            throw new common_exception_MissingParameter('lang', __METHOD__);
        }
        $itemLang = $this->getRequestParameter('lang');

        $options = array('item'=>$item, 'lang'=>$itemLang);

        $subPath = $this->hasRequestParameter('path') ? $this->getRequestParameter('path') : '/';
        $depth = $this->hasRequestParameter('depth') ? $this->getRequestParameter('depth') : 1;
       
        //build filters
        $filters = array();
        if($this->hasRequestParameter('filters')){
            $filterParameter = $this->getRequestParameter('filters');
            if(!empty($filterParameter)){
                if(preg_match('/\/\*/', $filterParameter)){
                    common_Logger::w('Stars mime type are not yet supported, filter "'. $filterParameter . '" will fail');
                }
                $filters = array_map('trim', explode(',', $filterParameter));
            }
        }

        $identifier = '';
        $pos = strpos($subPath, '/');
        if($pos !== false && $pos !== 0){
            $identifier = substr($subPath, 0, strpos($subPath, '/'));
            $subPath = substr($subPath, strpos($subPath, '/') + 1);
        }
        if(strlen($subPath) === 0){
            $subPath = '/';
        }

        $clazz = $this->getBrowserImplementationClass($identifier);
        $resourceBrowser = new $clazz($options);
        $data = $resourceBrowser->getDirectory($subPath, $filters, $depth);

        echo json_encode($data);
    }
    
    /**
     * Returns whenever or not a file exists at the indicated path
     * 
     * @throws common_exception_MissingParameter
     */
    public function fileExists() {
        $options = array();
        if ($this->hasRequestParameter('uri')) {
            $itemUri = $this->getRequestParameter('uri');
            $item = new core_kernel_classes_Resource($itemUri);
            $options['item'] = $item;
        }

        if ($this->hasRequestParameter('lang')) {
            $itemLang = $this->getRequestParameter('lang');
            $options['lang'] = $itemLang;
        }

        if (!$this->hasRequestParameter('path')) {
            throw new common_exception_MissingParameter('path', __METHOD__);
        }

        $identifier = substr($this->getRequestParameter('path'), 0, strpos($this->getRequestParameter('path'), '/'));
        $subPath = substr($this->getRequestParameter('path'), strpos($this->getRequestParameter('path'), '/'));
        if(strlen($subPath) === 0){
            $subPath = '/';
        }

        $clazz = $this->getBrowserImplementationClass($identifier);
        /** @var oat\tao\model\media\MediaBrowser $mediaBrowser */
        $mediaBrowser = new $clazz($options);
        $fileInfo = $mediaBrowser->getFileInfo($subPath, array());
        $fileExists = true;
        if(is_null($fileInfo)){
            $fileExists = false;
        }

        echo json_encode(array(
        	'exists' => $fileExists
        ));
    }   
     
    /**
     * Upload a file to the item directory
     * 
     * @throws common_exception_MissingParameter
     */
    public function upload() {
        //as upload may be called multiple times, we remove the session lock as soon as possible
        session_write_close();
        $options = array();
        if ($this->hasRequestParameter('uri')) {
            $itemUri = $this->getRequestParameter('uri');
            $item = new core_kernel_classes_Resource($itemUri);
            $options['item'] = $item;
        }

        if ($this->hasRequestParameter('lang')) {
            $itemLang = $this->getRequestParameter('lang');
            $options['lang'] = $itemLang;
        }

        if (!$this->hasRequestParameter('path')) {
            throw new common_exception_MissingParameter('path', __METHOD__);
        }


        $relPath = '';
        if($this->hasRequestParameter('relPath')){
            $relPath = $this->getRequestParameter('relPath');
        }

        //if the string contains something else than letters, numbers or / throw an exception
        if(!preg_match('#^$|^[\w\/\-\._]+$#', $relPath)){
            throw new InvalidArgumentException('The request parameter is invalid');
        }
        if(strpos($relPath, '/') === false){
            $identifier = $relPath;
            $subPath = '/';
        }
        else{
            $identifier = substr($relPath, 0, strpos($relPath, '/'));
            $subPath = substr($relPath, strpos($relPath, '/') + 1);
        }
        $identifier = trim($identifier);
        $subPath = empty($subPath) ? '' : $subPath.'/';

        $clazz = $this->getManagementImplementationClass($identifier);
        $mediaManagement = new $clazz($options);

        $file = tao_helpers_Http::getUploadedFile('content');

        if (!is_uploaded_file($file['tmp_name'])) {
            throw new common_exception_Error('Non uploaded file "'.$file['tmp_name'].'" returned from tao_helpers_Http::getUploadedFile()');
        }
        $filedata = $mediaManagement->add($file['tmp_name'], $file['name'], $subPath);

        echo json_encode($filedata);
    }

    /**
     * Download a file to the item directory* 
     * @throws common_exception_MissingParameter
     */
    public function download() {
        $options = array();
        if ($this->hasRequestParameter('uri')) {
            $itemUri = $this->getRequestParameter('uri');
            $item = new core_kernel_classes_Resource($itemUri);
            $options['item'] = $item;
        }

        if ($this->hasRequestParameter('lang')) {
            $itemLang = $this->getRequestParameter('lang');
            $options['lang'] = $itemLang;
        }

        if (!$this->hasRequestParameter('path')) {
            throw new common_exception_MissingParameter('path', __METHOD__);
        }

        $identifier = '';
        $subPath = $this->getRequestParameter('path');
        if(strpos($subPath, '/') !== false){
            $identifier = substr($subPath, 0, strpos($subPath, '/'));
            $subPath = substr($subPath, strpos($subPath, '/') + 1);
        }

        if(strlen($subPath) === 0){
            $subPath = '/';
        }
        $clazz = $this->getBrowserImplementationClass($identifier);
        $mediaBrowser = new $clazz($options);

        $mediaBrowser->download($subPath);
    }
    
    /**
     * Delete a file from the item directory
     * 
     * @throws common_exception_MissingParameter
     */
    public function delete() {

        $options = array();
        if ($this->hasRequestParameter('uri')) {
            $itemUri = $this->getRequestParameter('uri');
            $item = new core_kernel_classes_Resource($itemUri);
            $options['item'] = $item;
        }

        if ($this->hasRequestParameter('lang')) {
            $itemLang = $this->getRequestParameter('lang');
            $options['lang'] = $itemLang;
        }

        if (!$this->hasRequestParameter('path')) {
            throw new common_exception_MissingParameter('path', __METHOD__);
        }

        $identifier = substr($this->getRequestParameter('path'), 0, strpos($this->getRequestParameter('path'), '/'));
        $subPath = substr($this->getRequestParameter('path'), strpos($this->getRequestParameter('path'), '/'));
        if(strlen($subPath) === 0){
            $subPath = '/';
        }


        $clazz = $this->getManagementImplementationClass($identifier);
        $mediaManagement = new $clazz($options);

        $deleted = $mediaManagement->delete($subPath);

        echo json_encode(array('deleted' => $deleted));
    }
}
