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

/**
 * Enables you to parse and validate a QTI Package.
 * The Package is formated as a zip archive containing the manifest and the
 * (item files and media files)
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_intgv2p0.html#section10003
 * @subpackage models_classes_QTI
 */
class taoQtiCommon_models_classes_ManifestParser
    extends taoQTI_models_classes_QTI_ManifestParser
{
    private $resources = null;
    
    public function getResources($filter = null) {
        $returnValue = array();
        if (is_null($filter)) {
            $returnValue = $this->getAllResources();
        } else {
            $filter = is_array($filter) ? $filter : array($filter);
            foreach ($this->getAllResources() as $resource) {
                common_Logger::i($resource->getType().' '.implode(',', $filter));
                if (in_array($resource->getType(), $filter)) {
                    $returnValue[] = $resource;
                }
            }
        }
        return $returnValue;
    }
    
    protected function getAllResources() {
        if ($this->resources == null) {
            $this->resources = $this->getResourcesFromManifest($this->getSimpleXMLElement());
        }
        return $this->resources;
    }
    
    private function getResourcesFromManifest( SimpleXMLElement $source)
    {
        $returnValue = array();
    
        //check of the root tag
        if($source->getName() != 'manifest'){
            throw new Exception("incorrect manifest root tag");
        }
        	
        $resourceNodes = $source->xpath("//*[name(.)='resource']");
        foreach($resourceNodes as $resourceNode){
            $type = (string) $resourceNode['type'];
            $id = (string) $resourceNode['identifier'];
            $href = (isset($resourceNode['href'])) ? (string) $resourceNode['href'] : '';
            	
            $auxFiles = array();
            $xmlFiles = array();
            foreach($resourceNode->file as $fileNode){
                $fileHref = (string) $fileNode['href'];
                if(preg_match("/\.xml$/", $fileHref)){
                    if(empty($href)){
                        $xmlFiles[] = $fileHref;
                    }
                }
                else{
                    $auxFiles[] = $fileHref;
                }
            }
            	
            if(count($xmlFiles) == 1 && empty($href)){
                $href = $xmlFiles[0];
            }
            $resource = new taoQtiCommon_models_classes_QtiResource($id, $type, $href);
            $resource->setAuxiliaryFiles($auxFiles);
            	
            $returnValue[] = $resource;
        }
    
        return (array) $returnValue;
    }
    
    /**
     * 
     * @throws taoItems_models_classes_Import_ImportException
     * @return SimpleXMLElement
     */
    private function getSimpleXMLElement() {
        switch($this->sourceType){
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
        if($xml === false){
            $this->addErrors(libxml_get_errors());
            libxml_clear_errors();
            throw new taoItems_models_classes_Import_ImportException('Invalid XML');
        }
        return $xml;
    }
}