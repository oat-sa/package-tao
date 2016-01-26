<?php
use oat\taoItems\model\media\ItemMediaResolver;
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

        //build filters
        $filters = array();
        if($this->hasRequestParameter('filters')){
            $filterParameter = $this->getRequestParameter('filters');
            if(is_array($filterParameter)){
                foreach($filterParameter as $filter){
                    if(preg_match('/\/\*/', $filter['mime'])){
                        common_Logger::w('Stars mime type are not yet supported, filter "'. $filter['mime'] . '" will fail');
                    }
                    $filters[] = $filter['mime'];
                }
            }
            else{
                if(preg_match('/\/\*/', $filterParameter)){
                    common_Logger::w('Stars mime type are not yet supported, filter "'. $filterParameter . '" will fail');
                }
                $filters = array_map('trim', explode(',', $filterParameter));
            }
        }
        $depth = $this->hasRequestParameter('depth') ? $this->getRequestParameter('depth') : 1;
        
        $resolver = new ItemMediaResolver($item, $itemLang);
        $asset = $resolver->resolve($this->getRequestParameter('path'));
        $data = $asset->getMediaSource()->getDirectory($asset->getMediaIdentifier(), $filters, $depth);
        foreach($data['children'] as &$child){
            if(isset($child['parent'])){
                $child['url'] = \tao_helpers_Uri::url(
                    'files',
                    'ItemContent',
                    'taoItems',
                    array('uri' => $itemUri,'lang' => $itemLang, '1' => $child['parent']));
                unset($child['parent']);
            }
        }
        
        $this->returnJson($data);
    }
    
    /**
     * Returns whenever or not a file exists at the indicated path
     * 
     * @throws common_exception_MissingParameter
     */
    public function fileExists() {
        if (!$this->hasRequestParameter('uri') || !$this->hasRequestParameter('path') || !$this->hasRequestParameter('lang')) {
            throw new common_exception_MissingParameter();
        }
        
        $item = new core_kernel_classes_Resource($this->getRequestParameter('uri'));
        $itemLang = $this->getRequestParameter('lang');
        
        try {
            $resolver = new ItemMediaResolver($item, $itemLang);
            $asset = $resolver->resolve($this->getRequestParameter('path'));
            $fileInfo = $asset->getMediaSource()->getFileInfo($asset->getMediaIdentifier());
            $found = true;
        } catch (tao_models_classes_FileNotFoundException $exception) {
            $found = false;
        }        
        return $this->returnJson(array(
            'exists' => $found
        ));
    }   
     
    /**
     * Upload a file to the item directory
     * 
     * @throws common_exception_MissingParameter
     */
    public function upload() {
        //as upload may be called multiple times, we remove the session lock as soon as possible
        try{
            session_write_close();
            if ($this->hasRequestParameter('uri')) {
                $itemUri = $this->getRequestParameter('uri');
                $item = new core_kernel_classes_Resource($itemUri);
            }

            if ($this->hasRequestParameter('lang')) {
                $itemLang = $this->getRequestParameter('lang');
            }

            if (!$this->hasRequestParameter('path')) {
                throw new common_exception_MissingParameter('path', __METHOD__);
            }

            if (!$this->hasRequestParameter('filters')) {
                throw new common_exception_MissingParameter('filters', __METHOD__);
            }
            $filters = $this->getRequestParameter('filters');

            $resolver = new ItemMediaResolver($item, $itemLang);
            $asset = $resolver->resolve($this->getRequestParameter('relPath'));

            $file = tao_helpers_Http::getUploadedFile('content');
            $fileTmpName = $file['tmp_name'].'_'.$file['name'];
            
            if (!tao_helpers_File::copy($file['tmp_name'], $fileTmpName)) {
                throw new common_exception_Error('impossible to copy '.$file['tmp_name'].' to '.$fileTmpName);
            }

            $mime = \tao_helpers_File::getMimeType($fileTmpName);
            if (is_string($filters)) {
                // the mime type is part of the $filters
                $filters = explode(',', $filters);
                if ((in_array($mime, $filters))) {
                    $filedata = $asset->getMediaSource()->add($fileTmpName, $file['name'],
                        $asset->getMediaIdentifier());
                } else {
                    throw new \oat\tao\helpers\FileUploadException(__('The file you tried to upload is not valid'));
                }
            } else {
                $valid = false;
                // OR the extension is part of the filter and it correspond to the mime type
                foreach ($filters as $filter) {
                    if ($filter['mime'] === $mime &&
                        (!isset($filter['extension']) || $filter['extension'] === \tao_helpers_File::getFileExtention($fileTmpName))
                    ) {
                        $valid = true;
                    }
                }
                if ($valid) {
                    $filedata = $asset->getMediaSource()->add($fileTmpName, $file['name'],
                        $asset->getMediaIdentifier());
                } else {
                    throw new \oat\tao\helpers\FileUploadException(__('The file you tried to upload is not valid'));
                }
            }

            $this->returnJson($filedata);
            return;
        }
        catch(\oat\tao\model\accessControl\data\PermissionException $e){
            $message = $e->getMessage();
        }
        catch(\oat\tao\helpers\FileUploadException $e){
            $message = $e->getMessage();
        }
        catch(common_Exception $e){
            common_Logger::w($e->getMessage());
            $message = _('Unable to upload file');
        }
        $this->returnJson(array('error' => $message));

    }

    /**
     * Download a file to the item directory* 
     * @throws common_exception_MissingParameter
     * @throws common_exception_Error
     * @throws tao_models_classes_FileNotFoundException
     */
    public function download() {
        $svgzSupport = false;
        if (!$this->hasRequestParameter('uri') || !$this->hasRequestParameter('path') || !$this->hasRequestParameter('lang')) {
            throw new common_exception_MissingParameter();
        }

        if($this->hasRequestParameter('svgzsupport')){
            $svgzSupport = true;
        }

        $item = new core_kernel_classes_Resource($this->getRequestParameter('uri'));
        $itemLang = $this->getRequestParameter('lang');
        
        $resolver = new ItemMediaResolver($item, $itemLang);

        $rawParams = $this->getRequest()->getRawParameters();//have to use raw value to respect special characters in names
        $asset = $resolver->resolve($rawParams['path']);
        $filePath = $asset->getMediaSource()->download($asset->getMediaIdentifier());

        \tao_helpers_Http::returnFile($filePath, true, $svgzSupport);
    }
    
    /**
     * Delete a file from the item directory
     * 
     * @throws common_exception_MissingParameter
     */
    public function delete() {
        if (!$this->hasRequestParameter('uri') || !$this->hasRequestParameter('path') || !$this->hasRequestParameter('lang')) {
            throw new common_exception_MissingParameter();
        }
        
        $item = new core_kernel_classes_Resource($this->getRequestParameter('uri'));
        $itemLang = $this->getRequestParameter('lang');
        
        $resolver = new ItemMediaResolver($item, $itemLang);
        $asset = $resolver->resolve($this->getRequestParameter('path'));
        $deleted = $asset->getMediaSource()->delete($asset->getMediaIdentifier());
        
        return $this->returnJson(array('deleted' => $deleted));
    }
    
    /**
     * Get the media source based on the partial url
     * 
     * @param string $urlPrefix
     * @param core_kernel_classes_resource $item
     * @param string $itemLang
     * @return \oat\tao\model\media\MediaBrowser
     */
    protected function getMediaSource($urlPrefix, $item, $itemLang)
    {
        $resolver = new ItemMediaResolver($item, $itemLang);
        $asset = $resolver->resolve($urlPrefix);
        return $asset->getMediaSource();
    }
}
