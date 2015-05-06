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

use oat\tao\helpers\FileUploadException;
 
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
        
        $data = taoItems_helpers_ResourceManager::buildDirectory($item, $itemLang, $subPath, $depth, $filters);
        echo json_encode($data);
    }
    
    /**
     * Upload a file to the item directory
     * 
     * @throws common_exception_MissingParameter
     */
    public function upload() {
        if (!$this->hasRequestParameter('uri')) {
            throw new common_exception_MissingParameter('uri', __METHOD__);
        }
        $itemUri = $this->getRequestParameter('uri');
        $item = new core_kernel_classes_Resource($itemUri);
        
        if (!$this->hasRequestParameter('lang')) {
            throw new common_exception_MissingParameter('lang', __METHOD__);
        }
        $itemLang = $this->getRequestParameter('lang');
        
        if (!$this->hasRequestParameter('path')) {
            throw new common_exception_MissingParameter('path', __METHOD__);
        }
    
        //as upload may be called multiple times, we remove the session lock as soon as possible
        session_write_close();
       
		//TODO path traversal and null byte poison check ? 
        $baseDir = taoItems_models_classes_ItemsService::singleton()->getItemFolder($item, $itemLang);
        $relPath = trim($this->getRequestParameter('path'), '/');
        $relPath = empty($relPath) ? '' : $relPath.'/';
      
        try{ 
            $file = tao_helpers_Http::getUploadedFile('content');
            $fileName = $this->removeSpecChars($file['name']);
            
            if(!move_uploaded_file($file["tmp_name"], $baseDir.$relPath.$fileName)){
                throw new common_exception_Error('Unable to move uploaded file');
            } 
            
            $fileData = taoItems_helpers_ResourceManager::buildFile($item, $itemLang, $relPath.$fileName);
            echo json_encode($fileData);    

        } catch(FileUploadException $fe){
            
            echo json_encode(array( 'error' => $fe->getMessage()));    
        }
    }

    /**
     * Download a file to the item directory* 
     * @throws common_exception_MissingParameter
     */
    public function download() {
        if (!$this->hasRequestParameter('uri')) {
            throw new common_exception_MissingParameter('uri', __METHOD__);
        }
        $itemUri = $this->getRequestParameter('uri');
        $item = new core_kernel_classes_Resource($itemUri);
        
        if (!$this->hasRequestParameter('lang')) {
            throw new common_exception_MissingParameter('lang', __METHOD__);
        }
        $itemLang = $this->getRequestParameter('lang');
        
        if (!$this->hasRequestParameter('path')) {
            throw new common_exception_MissingParameter('path', __METHOD__);
        }
        
        $baseDir = taoItems_models_classes_ItemsService::singleton()->getItemFolder($item, $itemLang);
        $path = $baseDir.ltrim($this->getRequestParameter('path'), '/');
        
        tao_helpers_Http::returnFile($path);
    }
    
    /**
     * Delete a file from the item directory
     * 
     * @throws common_exception_MissingParameter
     */
    public function delete() {

        $deleted = false;

        if (!$this->hasRequestParameter('uri')) {
            throw new common_exception_MissingParameter('uri', __METHOD__);
        }
        $itemUri = $this->getRequestParameter('uri');
        $item = new core_kernel_classes_Resource($itemUri);
        
        if (!$this->hasRequestParameter('lang')) {
            throw new common_exception_MissingParameter('lang', __METHOD__);
        }
        $itemLang = $this->getRequestParameter('lang');
        
        if (!$this->hasRequestParameter('path')) {
            throw new common_exception_MissingParameter('path', __METHOD__);
        }
        
        $baseDir = taoItems_models_classes_ItemsService::singleton()->getItemFolder($item, $itemLang);
        $path = $baseDir.ltrim($this->getRequestParameter('path'), '/');

        //TODO path traversal and null byte poison check ? 
        if(is_file($path) && !is_dir($path)){
            $deleted = unlink($path);
        } 
        echo json_encode(array('deleted' => $deleted));
    }
    
    /**
     * This function removes all special characters from a string. They are replaced by $repl,
     * multiple $repl are replaced by just one, $repl is also trimmed from the beginning and the end
     * of the string.
     *
     * @param string $string the original text
     * @param string $repl the replacement, - by default
     * @param bool $lower return string in lower case, true by default
     * @return string $string the modified string
     * @author Dieter Raber
     */
    private function removeSpecChars($string, $repl='-', $lower=true) {
        $lastDot = strrpos($string, '.');
        $file = $lastDot ? substr($string, 0, $lastDot) : $string;
        $ending = $lastDot ? substr($string, $lastDot+1) : '';
        $spec_chars = array (
            'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'Ae', 'Å' => 'A','Æ' => 'A', 'Ç' => 'C',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I',
            'Ï' => 'I', 'Ð' => 'E', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
            'Ö' => 'Oe', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U','Û' => 'U', 'Ü' => 'Ue', 'Ý' => 'Y',
            'Þ' => 'T', 'ß' => 'ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'ae',
            'å' => 'a', 'æ' => 'ae', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i',  'ï' => 'i', 'ð' => 'e', 'ñ' => 'n', 'ò' => 'o',
            'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'oe', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u',
            'û' => 'u', 'ü' => 'ue', 'ý' => 'y', 'þ' => 't', 'ÿ' => 'y', ' ' => $repl, '?' => $repl,
            '\'' => $repl, '.' => $repl, '/' => $repl, '&' => $repl, ')' => $repl, '(' => $repl,
            '[' => $repl, ']' => $repl, '_' => $repl, ',' => $repl, ':' => $repl, '-' => $repl,
            '!' => $repl, '"' => $repl, '`' => $repl, '°' => $repl, '%' => $repl, ' ' => $repl,
            '  ' => $repl, '{' => $repl, '}' => $repl, '#' => $repl, '’' => $repl
        );
        $string = strtr($file, $spec_chars);
        $string = trim(preg_replace("~[^a-z0-9]+~i", $repl, $string), $repl).(strlen($ending) == 0 ? '' : '.'.$ending);
        return $lower ? strtolower($string) : $string;
    }
}
