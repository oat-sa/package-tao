<?php
/*
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

namespace oat\taoQtiItem\model;

use \core_kernel_classes_Resource;
use \common_exception_Error;
use oat\taoQtiItem\model\qti\Service;
use oat\taoQtiItem\helpers\Authoring;
use oat\tao\model\ClientLibRegistry;

/**
 * CreatorRegistry stores reference to 
 *
 * @package taoQtiItem
 */
abstract class CreatorRegistry
{

    /**
     * constructor
     */
    public function __construct(){
    }

    /**
     * @return string - e.g. DIR_VIEWS/js/pciCreator/dev/
     */
    abstract protected function getBaseDevDir();

    /**
     * @return string - e.g. BASE_WWW/js/pciCreator/dev/
     */
    abstract protected function getBaseDevUrl();

    /**
     * get the hook file name to distinguish various implementation
     * 
     * @return string
     */
    abstract protected function getHookFileName();

    /**
     * Get the entry point file path from the baseUrl
     * 
     * @param string $baseUrl
     * @return string
     */
    protected function getEntryPointFile($baseUrl){
        return $baseUrl.'/'.$this->getHookFileName();
    }

    /**
     * Get PCI Creator hooks directly located at views/js/pciCreator/myCustomInteraction:
     * 
     * @return array
     */
    public function getDevImplementations(){

        $returnValue = array();

        $hookFileName = $this->getHookFileName();

        foreach(glob($this->getBaseDevDir().'*/'.$hookFileName.'.js') as $file){

            $dir = str_replace($hookFileName.'.js', '', $file);
            $manifestFile = $dir.$hookFileName.'.json';

            if(file_exists($manifestFile)){

                $typeIdentifier = basename($dir);
                $baseUrl = $this->getBaseDevUrl().$typeIdentifier.'/';
                $manifest = json_decode(file_get_contents($manifestFile), true);
                
                $returnValue[] = array(
                    'typeIdentifier' => $typeIdentifier,
                    'label' => $manifest['label'],
                    'directory' => $dir,
                    'baseUrl' => $baseUrl,
                    'file' => $this->getEntryPointFile($typeIdentifier),
                    'manifest' => $manifest,
                    'dev' => true,
                    'debug' => (isset($manifest['debug']) && $manifest['debug']),
                    'registry' => get_class($this)
                );
            }
        }

        return $returnValue;
    }

    /**
     * Get PCI Creator hook located at views/js/{{hookFileName}}/$typeIdentifier
     * 
     * @param string $typeIdentifier
     * @return array
     */
    public function getDevImplementation($typeIdentifier){

        //@todo : re-implement it to be more optimal
        $devImplementations = $this->getDevImplementations();
        foreach($devImplementations as $impl){
            if($impl['typeIdentifier'] == $typeIdentifier){
                return $impl;
            }
        }
        return null;
    }

    /**
     * Get the path to the directory of a the Creator located at views/js/{{hookFileName}}/
     * 
     * @param string $typeIdentifier
     * @return string
     * @throws \common_Exception
     */
    public function getDevImplementationDirectory($typeIdentifier){
        $dir = $this->getBaseDevDir().$typeIdentifier;
        if(file_exists($dir)){
            return $dir;
        }else{
            throw new \common_Exception('the type identifier cannot be found');
        }
    }

    /**
     * Get the data of the implementation by its typeIdentifier
     * 
     * @param string $typeIdentifier
     * @return array
     */
    protected function getImplementatioByTypeIdentifier($typeIdentifier){
        return $this->getDevImplementation($typeIdentifier);
    }

    /**
     * Add required resources for a custom interaction (css, js) in the item directory
     * 
     * @param string $typeIdentifier
     * @param \core_kernel_classes_Resource $item
     * @throws common_exception_Error
     */
    public function addRequiredResources($typeIdentifier, core_kernel_classes_Resource $item){
        
        //find the interaction in the registry
        $implementationData = $this->getImplementatioByTypeIdentifier($typeIdentifier);
        if(is_null($implementationData)){
            throw new common_exception_Error('no implementation found with the type identifier '.$typeIdentifier);
        }

        //get the root directory of the interaction
        $directory = $implementationData['directory'];

        //get the lists of all required resources
        $manifest = $implementationData['manifest'];
        $required = array($manifest['entryPoint']);

        //include libraries remotely only, so this block is temporarily disabled
        foreach($manifest['libraries'] as $lib){
            if(!ClientLibRegistry::getRegistry()->isRegistered($lib)){
                $lib = preg_replace('/^.\//', '', $lib);
                $lib .= '.js'; //add js extension
                $required[] = $lib;
            }
        }

        //include custom interaction specific css in the item
        if(isset($manifest['css'])){
            $required = array_merge($required, array_values($manifest['css']));
        }

        //include media in the item
        if(isset($manifest['media'])){
            $required = array_merge($required, array_values($manifest['media']));
        }

        //add them to the rdf item
        $resources = Authoring::addRequiredResources($directory, $required, $typeIdentifier, $item, '');
        
        return $resources;
    }

}