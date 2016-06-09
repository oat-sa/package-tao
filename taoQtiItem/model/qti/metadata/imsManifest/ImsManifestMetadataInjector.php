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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoQtiItem\model\qti\metadata\imsManifest;

use \DOMDocument;
use \DOMElement;
use oat\taoQtiItem\model\qti\metadata\MetadataInjectionException;
use oat\taoQtiItem\model\qti\metadata\MetadataInjector;
use oat\taoQtiItem\model\qti\metadata\MetadataValue;
use \InvalidArgumentException;

/**
 * A MetadataExtractor implementation.
 * 
 * This implementation simply iterate through nodes and create an array of MetadataSimpleInstance object
 *
 * @author Antoine Robin <antoine.robin@vesperiagroup.com>
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 */
class ImsManifestMetadataInjector implements MetadataInjector
{
    /**
     * An array of IMSManifesMapping object.
     * 
     * @var ImsManifestMapping[]
     */
    private $mappings;
    
    /**
     * Create a new ImsManifestMetadataInjector object.
     * 
     * @param ImsManifestMapping[] $mappings (optional) An array of ImsManifestMapping objects.
     */
    public function __construct(array $mappings = array())
    {
        $this->setMappings($mappings);
    }
    
    /**
     * Set the ImsManifestMapping objects of this injector.
     * 
     * @param ImsManifestMapping[] $mappings An array of ImsManifestMapping objects.
     * @throws InvalidArgumentException If $mappings contains objects/values different from ImsManifestMapping.
     */
    protected function setMappings(array $mappings = array())
    {
        foreach ($mappings as $mapping) {
            if (!$mapping instanceof ImsManifestMapping) {
                $msg = "The mappings argument must be an array composed of ImsManifestMapping objects";
                throw new InvalidArgumentException($msg);
            }
        }
        
        $this->mappings = $mappings;
    }
    
    /**
     * Get the registered ImsManifestMapping objects. If no mapping
     * already registered, this method returns an empty array.
     * 
     * @return ImsManifestMapping[] An array of ImsManifestMapping.
     */
    public function getMappings()
    {
        return $this->mappings;
    }
    
    /**
     * Add an XML mapping to this Manifest Extractor.
     * 
     * If a mapping with an already registered XML namespace is given as
     * a $mapping, it is simply ignored.
     * 
     * @param ImsManifestMapping $mapping An XML mapping.
     */
    public function addMapping(ImsManifestMapping $mapping)
    {
        $mappings = $this->getMappings();
        
        $ns = $mapping->getNamespace();
        
        if (isset($mappings[$ns]) === false) {
            $mappings[$ns] = $mapping;
        }
        
        $this->setMappings($mappings);
    }
    
    /**
     * Remove an already registered ImsManifestMapping. 
     * 
     * If $mapping cannot be found as a previously registered mapping, nothing happens.
     * 
     * @param ImsManifestMapping $mapping An ImsManifestMapping object.
     */
    public function removeMapping(ImsManifestMapping $mapping)
    {
        $mappings = $this->getMappings();
        
        if (($key = array_search($mapping, $mappings, true)) !== false) {
            unset($mappings[$key]);
        }
    }
    
    /**
     * Remove a previously registered ImsManifestMapping by its namespace.
     * 
     * If no previously registered ImsManifestMapping object can be found
     * for the given $namespace, nothing happens.
     * 
     * @param string $namespace An XML namespace.
     */
    public function removeMappingByNamespace($namespace)
    {
        $mappings = $this->getMappings();
        
        if (isset($mappings[$namespace]) === true) {
            unset($mappings[$namespace]);
        }
    }
    
    /**
     * Clear all the previously registered ImsManifestMapping objects
     * from this injector.
     */
    public function clearMappings()
    {
        $this->setMappings();
    }
    
    /**
     * Inject some MetadataValue objects into the $target DOMElement object.
     * 
     * The injection will take care of serializing the MetadataValue objects into the correct sections of the
     * the IMS Manifest File, by looking at previously registered IMSManifestMapping objects.
     * 
     * @throws MetadataInjectionException If $target is not a DOMDocument object or something goes wrong during the injection process.
     */
    public function inject($target, array $values)
    {
        /** @var $target DOMDocument */
        if($target instanceof DOMDocument){

            // Inject the mapping in the root node
            foreach($this->getMappings() as $mapping){
                /** @var $root DOMElement */
                $root = $target->getElementsByTagName('manifest')->item(0);
                $root->setAttribute('xmlns:'.$mapping->getPrefix(), $mapping->getNamespace());
                $root->setAttribute(
                    'xsi:schemaLocation',
                    $root->getAttribute('xsi:schemaLocation') . ' ' . $mapping->getNamespace(
                    ) . ' ' . $mapping->getSchemaLocation());

                $map[$mapping->getNamespace()] = $mapping->getPrefix();
            }

            // Get all resource nodes
            $resources = $target->getElementsByTagName('resource');

            // Iterate through values to inject them in the DOMElement
            foreach($values as $identifier => $metadataValues){
                // Search the node that has the given identifier
                /** @var $resource DOMElement */
                foreach($resources as $resource){
                    if($resource->getAttribute('identifier') === $identifier){
                        // If metadata already exists we take it
                        if($resource->getElementsByTagName('metadata')->length !== 0){
                            $metadataNode = $resource->getElementsByTagName('metadata')->item(0);
                        }
                        else{
                            $metadataNode = $target->createElement('metadata');
                        }

                        if($resource->getElementsByTagName('file')->length !== 0){
                            $fileNode = $resource->getElementsByTagName('file')->item(0);
                            $resource->insertBefore($metadataNode, $fileNode);
                        }
                        else{
                            $resource->appendChild($metadataNode);
                        }
                        break;
                    }
                }

                // Add the metadata values into the right path
                /** @var $metadata MetaDataValue */
                foreach($metadataValues as $metadata){
                    $path = $metadata->getPath();
                    $path = array_reverse($path);
                    $oldChildNode = null;
                    foreach($path as $index => $element){
                        $name = substr($element,(strpos($element,'#') + 1));
                        $base = substr($element,0,(strpos($element,'#')));

                        // If node already exists we don't re create one
                        if(!is_null($oldChildNode) && $metadataNode->getElementsByTagName($map[$base].':'.$name)->length !== 0){
                            $node = $metadataNode->getElementsByTagName($map[$base].':'.$name)->item(0);
                        }
                        else{
                        $node = $target->createElement($map[$base].':'.$name);
                        }

                        if($name === 'langstring'){
                            $node->setAttribute('xml:lang', $metadata->getLanguage());
                        }
                        if(isset($oldChildNode)){
                            $node->appendChild($oldChildNode);
                        }
                        else{
                            $node->nodeValue = $metadata->getValue();
                        }
                        $oldChildNode = $node;
                    }

                    $metadataNode->appendChild($oldChildNode);
                }
            }

        }else{
            throw new MetadataInjectionException(__('The target must be an instance of DOMDocument'));
        }


    }
}