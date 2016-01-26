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
 */

namespace oat\taoQtiItem\controller;

use \core_kernel_classes_Resource;
use \tao_actions_CommonModule;
use \common_exception_Error;
use \tao_helpers_File;
use \tao_helpers_Http;

abstract class AbstractPortableElementManager extends tao_actions_CommonModule
{
    
    /**
     * Instanciate the controller
     */
    public function __construct(){
        parent::__construct();
        $this->registry = $this->getCreatorRegistry();
    }
    
    /**
     * Return the registry used by this controller
     * 
     * @return oat\taoQtiItem\model\CreatorRegistry
     */
    abstract protected function getCreatorRegistry();
    
    /**
     * Get a file of a custom interaction
     */
    public function getFile(){

        if($this->hasRequestParameter('file')){
            $file = urldecode($this->getRequestParameter('file'));
            $filePathTokens = explode('/', $file);
            $typeIdentifier = array_shift($filePathTokens);
            $relPath = implode(DIRECTORY_SEPARATOR, $filePathTokens);
            $this->renderFile($typeIdentifier, $relPath);
        }
    }
    
    /**
     * Get the directory where the implementation sits
     * 
     * @param string $typeIdentifier
     * @return string
     */
    protected function getImplementationDirectory($typeIdentifier){
        return $this->registry->getDevImplementationDirectory($typeIdentifier);
    }
    
    /**
     * Render the file to the browser
     * 
     * @param string $typeIdentifier
     * @param string $relPath
     * @throws common_exception_Error
     */
    private function renderFile($typeIdentifier, $relPath){
        
        if(tao_helpers_File::securityCheck($relPath, true)){
            
            $folder = $this->getImplementationDirectory($typeIdentifier);
            $filename = $folder.$relPath;
            
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
   
    /**
     * Add required resources for a custom interaction (css, js) in the item directory
     * 
     * @throws common_exception_Error
     */
    public function addRequiredResources(){
        
        $typeIdentifier = $this->getRequestParameter('typeIdentifier');
        $itemUri = urldecode($this->getRequestParameter('uri'));
        $item = new core_kernel_classes_Resource($itemUri);
        
        $resources = $this->registry->addRequiredResources($typeIdentifier, $item);
        
        $this->returnJson(array(
            'success' => true,
            'resources' => $resources
        ));
    }

}