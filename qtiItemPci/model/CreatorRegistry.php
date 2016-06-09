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

namespace oat\qtiItemPci\model;

use oat\taoQtiItem\model\CreatorRegistry as ParentRegistry;
use \core_kernel_classes_Resource;
use \core_kernel_classes_Class;
use \core_kernel_classes_Property;
use \tao_models_classes_service_FileStorage;
use \common_ext_ExtensionsManager;
use \tao_helpers_Uri;
use oat\qtiItemPci\model\CreatorPackageParser;

/**
 * CreatorRegistry stores reference to 
 *
 * @package qtiItemPci
 */
class CreatorRegistry extends ParentRegistry
{

    private $registryClass;
    private $storage;
    private $propTypeIdentifier;
    private $propDirectory;
    
    /**
     * constructor
     */
    public function __construct(){
        
        parent::__construct();
        
        $this->registryClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/QtiItemPci.rdf#PciCreatorHook');
        $this->storage = tao_models_classes_service_FileStorage::singleton();
        $this->propTypeIdentifier = new core_kernel_classes_Property('http://www.tao.lu/Ontologies/QtiItemPci.rdf#PciCreatorIdentifier');
        $this->propDirectory = new core_kernel_classes_Property('http://www.tao.lu/Ontologies/QtiItemPci.rdf#PciCreatorDirectory');
    }
    
    protected function getBaseDevDir(){
        $extension = common_ext_ExtensionsManager::singleton()->getExtensionById('qtiItemPci');
        return $extension->getConstant('DIR_VIEWS').'js/pciCreator/dev/'; 
    }
    
    protected function getBaseDevUrl(){
        $extension = common_ext_ExtensionsManager::singleton()->getExtensionById('qtiItemPci');
        return $extension->getConstant('BASE_WWW').'js/pciCreator/dev/'; 
    }
    
    protected function getHookFileName(){
        return 'pciCreator';
    }
    
    /**
     * Register a custom interaction from a zip package
     * 
     * @param string $archive
     * @param boolean $replace
     * @return array
     * @throws \common_Exception
     * @throws ExtractException
     */
    public function add($archive, $replace = false){

        $returnValue = null;

        $qtiPackageParser = new CreatorPackageParser($archive);
        $qtiPackageParser->validate();
        if($qtiPackageParser->isValid()){

            //obtain the id from manifest file
            $manifest = $qtiPackageParser->getManifest(true);
            $typeIdentifier = $manifest['typeIdentifier'];
            $label = $manifest['label'];

            //check if such PCI creator already exists
            $existingInteraction = $this->getResource($typeIdentifier);
            if($existingInteraction){
                if($replace){
                    $this->remove($typeIdentifier);
                }else{
                    throw new \common_Exception('The Creator Package already exists');
                }
            }

            //extract the package
            $folder = $qtiPackageParser->extract();
            if(!is_dir($folder)){
                throw new ExtractException();
            }

            $directory = $this->storage->spawnDirectory(true);
            $directoryId = $directory->getId();

            //copy content in the directory:
            $this->storage->import($directoryId, $folder);

            $this->registryClass->createInstanceWithProperties(array(
                $this->propTypeIdentifier->getUri() => $typeIdentifier,
                $this->propDirectory->getUri() => $directoryId,
                RDFS_LABEL => $label
            ));

            $returnValue = $this->get($typeIdentifier);
            
        }else{
            throw new \common_Exception('invalid PCI creator package format');
        }

        return $returnValue;
    }
    
    /**
     * Get the data for all registered interactions
     * 
     * @return array
     */
    public function getRegisteredImplementations(){

        $returnValue = array();

        $all = $this->registryClass->getInstances();
        foreach($all as $pci){
            $pciData = $this->getData($pci);
            $returnValue[$pciData['typeIdentifier']] = $pciData;
        }

        return $returnValue;
    }
    
    /**
     * Remove a registered interaction from the registry
     * 
     * @param string $typeIdentifier
     */
    public function remove($typeIdentifier){

        $hook = $this->getResource($typeIdentifier);
        if($hook){
            $hook->delete();
            //@todo : remove the directory too!
        }
    }
    
    /**
     * Remove all registered interactions form the registry
     */
    public function removeAll(){

        $all = $this->registryClass->getInstances();
        foreach($all as $pci){
            $pci->delete();
        }
    }
    
    /**
     * Return the rdf resource of a custom interaction from its typeIdentifier
     * 
     * @param string $typeIdentifier
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function getResource($typeIdentifier){

        $returnValue = null;

        if(!empty($typeIdentifier)){
            $resources = $this->registryClass->searchInstances(array($this->propTypeIdentifier->getUri() => $typeIdentifier), array('like'=>false));
            $returnValue = reset($resources);
        }else{
            throw new \InvalidArgumentException('the type identifier must not be empty');
        }

        return $returnValue;
    }
    
    /**
     * Get the data of a registered custom interaction from its rdf resource 
     * 
     * @param core_kernel_classes_Resource $hook
     * @return array
     */
    protected function getData(core_kernel_classes_Resource $hook){

        $directory = (string) $hook->getUniquePropertyValue($this->propDirectory);
        $label = $hook->getLabel();
        $folder = $this->storage->getDirectoryById($directory)->getPath();
        $typeIdentifier = (string) $hook->getUniquePropertyValue($this->propTypeIdentifier);
        $manifestFile = $folder.DIRECTORY_SEPARATOR.'pciCreator.json';
        $manifest = json_decode(file_get_contents($manifestFile), true);
        $baseUrl = tao_helpers_Uri::url('getFile', 'PciManager', 'qtiItemPci', array(
            'file' => $typeIdentifier.'/'
        ));

        return array(
            'typeIdentifier' => $typeIdentifier,
            'label' => $label,
            'directory' => $folder,
            'baseUrl' => $baseUrl,
            'manifest' => $manifest,
            'file' => $this->getEntryPointFile($typeIdentifier),
            'registry' => get_class($this)
        );
    }
    
    /**
     * Return the data of a registered custom interaction from its typeIdentifier
     * 
     * @param string $typeIdentifier
     * @return array
     */
    public function get($typeIdentifier){

        $returnValue = null;
        $hook = $this->getResource($typeIdentifier);

        if($hook){
            $returnValue = $this->getData($hook);
        }

        return $returnValue;
    }
    
    /**
     * Get the data of the implementation by its typeIdentifier
     * 
     * @param string $typeIdentifier
     * @return array
     */
    protected function getImplementatioByTypeIdentifier($typeIdentifier){
        $implementationData = $this->get($typeIdentifier);
        if(is_null($implementationData)){
            $implementationData = $this->getDevImplementation($typeIdentifier);
        }
        return $implementationData;
    }
}