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

use oat\taoQtiItem\model\qti\ManifestParser;

/**
 * Enables you to parse and validate a QTI Package.
 * The Package is formated as a zip archive containing the manifest and the
 * (item files and media files)
 *
 * @access public
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @author Joel Bout <joel@taotesting.com>
 * @package taoQTITest
 * @see http://www.imsglobal.org/question/qtiv2p1/imsqti_intgv2p1.html#section10005 IMS QTI: Packaging Tests
 
 */
class taoQtiTest_models_classes_ManifestParser
    extends ManifestParser
{
    private $resources = null;
    
    /**
     * Flag to be used while getting resources
     * by type.
     * 
     * @var integer
     */
    const FILTER_RESOURCE_TYPE = 0;
    
    /**
     * Flag to be used while getting resources
     * by identifier.
     * 
     * @var integer
     */
    const FILTER_RESOURCE_IDENTIFIER = 1;
    
    /**
     * Get the resources contained within the manifest.
     * 
     * @param string|array $filter The resource types you want to obtain. An empty $filter will make the method return all the resources within the manifest.
     * @param integer $target The critera to be used for filtering. ManifestParser::FILTER_RESOURCE_TYPE allows to filter by resource type, ManifestParser::FILTER_RESOURCE_IDENTIFIER allows to filter by resource identifier.
     * @return array An array of oat\taoQtiItem\model\qti\Resource objects matching $filter (if given).
     */
    public function getResources($filter = null, $target = self::FILTER_RESOURCE_TYPE) {
        $returnValue = array();
        
        if (is_null($filter)) {
            $returnValue = $this->getAllResources();
        }
        else {
            $filter = is_array($filter) ? $filter : array($filter);
            
            foreach ($this->getAllResources() as $resource) {
                
                $stringTarget = ($target === self::FILTER_RESOURCE_TYPE) ? $resource->getType() : $resource->getIdentifier();
                
                if (in_array($stringTarget, $filter)) {
                    $returnValue[] = $resource;
                }
            }
        }
        return $returnValue;
    }
    
    /**
     * Get all the resources contained within the manifest.
     * 
     * @return An array of oat\taoQtiItem\model\qti\Resource objects.
     */
    protected function getAllResources() {
        if ($this->resources == null) {
            $this->resources = $this->getResourcesFromManifest($this->getSimpleXMLElement());
        }
        return $this->resources;
    }
    
    /**
     * Get all the resources contained by the $source SimpleXMLElement.
     * 
     * @param SimpleXMLElement $source The SimpleXMLElement object you want to extract resources from.
     * @throws common_exception_Error If $source does not correspond to a <manifest> element.
     * @return array An array of oat\taoQtiItem\model\qti\Resource objects.
     */
    private function getResourcesFromManifest(SimpleXMLElement $source)
    {
        $returnValue = array();
    
        //check of the root tag
        if ($source->getName() != 'manifest') {
            throw new common_exception_Error("Incorrect manifest root tag '" . $source->getName() . "'.");
        }
        	
        $resourceNodes = $source->xpath("//*[name(.)='resource']");
        
        foreach ($resourceNodes as $resourceNode) {
            
            $type = (string) $resourceNode['type'];
            $id = (string) $resourceNode['identifier'];
            $href = (isset($resourceNode['href'])) ? (string) $resourceNode['href'] : '';
            	
            $idRefs = array();
            $auxFiles = array();
            $xmlFiles = array();
            
            // Retrieve Auxilliary files.
            foreach ($resourceNode->file as $fileNode) {
                $fileHref = (string) $fileNode['href'];
                
                if (preg_match("/\.xml$/", $fileHref)){
                    
                    if (empty($href) || $href === $fileHref) {
                        $xmlFiles[] = $fileHref;
                    }
                    else{
                        $auxFiles[] = $fileHref;
                    }
                }
                else {
                    $auxFiles[] = $fileHref;
                }
            }
            	
            if (count($xmlFiles) == 1 && empty($href)) {
                $href = $xmlFiles[0];
            }
            
            // Retrieve Dependencies.
            foreach ($resourceNode->dependency as $dependencyNode) {
                $idRefs[] = (string) $dependencyNode['identifierref'];
            }
            
            $resource = new taoQtiTest_models_classes_QtiResource($id, $type, $href);
            $resource->setAuxiliaryFiles($auxFiles);
            $resource->setDependencies($idRefs);
            	
            $returnValue[] = $resource;
        }
    
        return (array) $returnValue;
    }
    
    /**
     * Get the root SimpleXMLElement object of the currently parsed manifest.
     * 
     * @throws common_exception_Error
     * @return SimpleXMLElement
     */
    private function getSimpleXMLElement() {
        switch($this->sourceType) {
            
        	case self::SOURCE_FILE:
        	    $xml = simplexml_load_file($this->source);
        	break;
        	
        	case self::SOURCE_URL:
        	    $xmlContent = tao_helpers_Request::load($this->source, true);
        	    $xml = simplexml_load_string($xmlContent);
        	break;
        	
        	case self::SOURCE_STRING:
        	    $xml = simplexml_load_string($this->source);
        	break;
        	
        	default:
        	    throw new taoItems_models_classes_Import_ImportException('Invalid sourceType');
        	    
        }
        
        if ($xml === false) {
            $this->addErrors(libxml_get_errors());
            libxml_clear_errors();
            throw new common_exception_Error('Invalid XML.');
        }
        
        return $xml;
    }
}