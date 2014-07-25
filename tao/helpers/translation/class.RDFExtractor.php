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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - tao/helpers/translation/class.RDFExtractor.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 13.02.2013, 12:16:28 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage helpers_translation
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * A TranslationExtractor instance extracts TranslationUnits from a given source
 * as an Item, source code, ...
 *
 * @author Jerome Bogaerts
 * @since 2.2
 * @version 1.0
 */
require_once('tao/helpers/translation/class.TranslationExtractor.php');

/* user defined includes */
// section 10-13-1-85--4f943509:1353d309872:-8000:00000000000067EB-includes begin
// section 10-13-1-85--4f943509:1353d309872:-8000:00000000000067EB-includes end

/* user defined constants */
// section 10-13-1-85--4f943509:1353d309872:-8000:00000000000067EB-constants begin
// section 10-13-1-85--4f943509:1353d309872:-8000:00000000000067EB-constants end

/**
 * Short description of class tao_helpers_translation_RDFExtractor
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage helpers_translation
 */
class tao_helpers_translation_RDFExtractor
    extends tao_helpers_translation_TranslationExtractor
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute translatableProperties
     *
     * @access private
     * @var array
     */
    private $translatableProperties = array();

    /**
     * Short description of attribute xmlBase
     *
     * @access private
     * @var array
     */
    private $xmlBase = array();

    // --- OPERATIONS ---

    /**
     * Short description of method extract
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function extract()
    {
        // section 10-13-1-85--4f943509:1353d309872:-8000:00000000000067ED begin
        foreach ($this->getPaths() as $path){
        	// In the RDFExtractor, we expect the paths to points directly to the file.
        	if (!file_exists($path)){
        		throw new tao_helpers_translation_TranslationException("No RDF file to parse at '${path}'.");	
        	}
        	else if (!is_readable($path)){
        		throw new tao_helpers_translation_TranslationException("'${path}' is not readable. Please check file system rights.");	
        	}
        	else{
	        	try{
	        		$tus = array();
	        		$rdfNS = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';
	        		$rdfsNS = 'http://www.w3.org/2000/01/rdf-schema#';
	        		$xmlNS = 'http://www.w3.org/XML/1998/namespace'; // http://www.w3.org/TR/REC-xml-names/#NT-NCName
	        		
	        		$translatableProperties = $this->translatableProperties;
	        		
	        		// Try to parse the file as a DOMDocument.
	        		$doc = new DOMDocument('1.0', 'UTF-8');
	        		$doc->load(realpath($path));
	        		if ($doc->documentElement->hasAttributeNS($xmlNS, 'base')) {
	        			$this->xmlBase[$path] = $doc->documentElement->getAttributeNodeNS($xmlNS, 'base')->value; 
	        		}
	        		
	        		$descriptions = $doc->getElementsByTagNameNS($rdfNS, 'Description');
	        		foreach ($descriptions as $description){
	        			if ($description->hasAttributeNS($rdfNS, 'about')){
	        				$about = $description->getAttributeNodeNS($rdfNS, 'about')->value;
	        				
	        				// At the moment only get rdfs:label and rdfs:comment
	        				// c.f. array $translatableProperties
	        				// In the future, this should be configured in the constructor
	        				// or by methods.
	        				$children = array();
	        				foreach ($translatableProperties as $prop){
	        					$uri = explode('#', $prop);
	        					if (count($uri) == 2){
	        						$uri[0] .= '#';
	        						$nodeList = $description->getElementsByTagNameNS($uri[0], $uri[1]);
	        						
	        						for ($i = 0; $i < $nodeList->length; $i++) {
	        							$children[] = $nodeList->item($i);
	        						}
	        					}
	        				}
	        				
	        				foreach ($children as $child) {
	        					// Only process if it has a language attribute.
	        					if ($child->hasAttributeNS($xmlNS, 'lang')){
	        						$sourceLanguage = 'en-US';
	        						$targetLanguage = $child->getAttributeNodeNS($xmlNS, 'lang')->value;
	        						$source = $child->nodeValue;
	        						$target = $child->nodeValue;
	        						
	        						$tu = new tao_helpers_translation_RDFTranslationUnit();
                                    $tu->setSource($source);
                                    $tu->setTarget($target);
	        						$tu->setSourceLanguage($sourceLanguage);
	        						$tu->setTargetLanguage($targetLanguage);
	        						$tu->setSubject($about);
	        						$tu->setPredicate($child->namespaceURI . $child->localName);
	        						
	        						$tus[] = $tu;
	        					}
	        				}
	        			}
	        			else{
	        				// Description about nothing.
	        				continue;	
	        			}
	        		}
	        		
	        		$this->setTranslationUnits($tus);
	        		
	        	} catch (DOMException $e){
	        		throw new tao_helpers_translation_TranslationException("Unable to parse RDF file at '${path}'. DOM returns '" . $e->getMessage() . "'.");
	        	}	
        	}
        }
        // section 10-13-1-85--4f943509:1353d309872:-8000:00000000000067ED end
    }

    /**
     * Short description of method addTranslatableProperty
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string propertyUri
     * @return mixed
     */
    public function addTranslatableProperty($propertyUri)
    {
        // section 10-13-1-85-2cdd3220:1353e441ff7:-8000:0000000000003A7E begin
        $this->translatableProperties[] = $propertyUri;
        // section 10-13-1-85-2cdd3220:1353e441ff7:-8000:0000000000003A7E end
    }

    /**
     * Short description of method removeTranslatableProperty
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string propertyUri
     * @return mixed
     */
    public function removeTranslatableProperty($propertyUri)
    {
        // section 10-13-1-85-2cdd3220:1353e441ff7:-8000:0000000000003A81 begin
        foreach ($this->translatableProperties as $prop){
        	if ($prop == $propertyUri){
        		unset($prop);
        	}
        }
        // section 10-13-1-85-2cdd3220:1353e441ff7:-8000:0000000000003A81 end
    }

    /**
     * Short description of method setTranslatableProperties
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array propertyUris
     * @return mixed
     */
    public function setTranslatableProperties($propertyUris)
    {
        // section 10-13-1-85-2cdd3220:1353e441ff7:-8000:0000000000003A84 begin
        $this->translatableProperties = $propertyUris;
        // section 10-13-1-85-2cdd3220:1353e441ff7:-8000:0000000000003A84 end
    }

    /**
     * Short description of method getXmlBase
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string path
     * @return string
     */
    public function getXmlBase($path)
    {
        $returnValue = (string) '';

        // section 10-30-1--78-5610a354:13cd2e07c14:-8000:00000000000053D0 begin
        if (!isset($this->xmlBase[$path])) {
        	throw new tao_helpers_translation_TranslationException('Missing xmlBase for file '.$path);
        }
        $returnValue = $this->xmlBase[$path]; 
        // section 10-30-1--78-5610a354:13cd2e07c14:-8000:00000000000053D0 end

        return (string) $returnValue;
    }

} /* end of class tao_helpers_translation_RDFExtractor */

?>