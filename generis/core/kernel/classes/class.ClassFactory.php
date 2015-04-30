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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */



/**
 * Short description of class core_kernel_classes_ClassFactory
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package generis
 
 */
class core_kernel_classes_ClassFactory
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method createInstance
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Class clazz
     * @param  string label
     * @param  string comment
     * @param  string uri
     * @return core_kernel_classes_Resource
     */
    public static function createInstance( core_kernel_classes_Class $clazz, $label = '', $comment = '', $uri = '')
    {
        $returnValue = null;

        
		$newUri = (!empty($uri)) ? self::checkProvidedUri($uri) : common_Utils::getNewUri();
		$newResource = new core_kernel_classes_Class($newUri);
		$propertiesValues = array(RDF_TYPE => $clazz->getUri());
		
		if (!empty($label)) {
			$propertiesValues[RDFS_LABEL] = $label;
		}
		
		if (!empty($comment)) {
			$propertiesValues[RDFS_COMMENT] = $comment;
		}
		
		$check = $newResource->setPropertiesValues($propertiesValues);
		if ($check){
			$returnValue = $newResource;
		}
		else{
			$msg = "Failed to create an instance of class '" . $clazz->getUri() . "'.";
			throw new common_Exception($msg);
			common_Logger::e($msg);
		}
        

        return $returnValue;
    }

    /**
     * Short description of method createProperty
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Class clazz
     * @param  string label
     * @param  string comment
     * @param  boolean isLgDependent
     * @param  string uri
     * @return core_kernel_classes_Property
     */
    public static function createProperty( core_kernel_classes_Class $clazz, $label = '', $comment = '', $isLgDependent = false, $uri = '')
    {
        $returnValue = null;

        
		$property = new core_kernel_classes_Class(RDF_PROPERTY);
		$propertyInstance = self::createInstance($property, $label, $comment, $uri);
		$returnValue = new core_kernel_classes_Property($propertyInstance->getUri());
		if (!$returnValue->setDomain($clazz)){
			throw new common_Exception('An error occured during Property creation.');
		}
		else{
			$returnValue->setLgDependent($isLgDependent);
		}

        

        return $returnValue;
    }

    /**
     * Short description of method createSubClass
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Class clazz
     * @param  string label
     * @param  string comment
     * @param  string uri
     * @return core_kernel_classes_Class
     */
    public static function createSubClass( core_kernel_classes_Class $clazz, $label = '', $comment = '', $uri = '')
    {
        $returnValue = null;

        
		$class = new core_kernel_classes_Class(RDFS_CLASS);
		$instance =  self::createInstance($class, $label, $comment, $uri);
		$returnValue = new core_kernel_classes_Class($instance->getUri());
		$returnValue->setSubClassOf($clazz);

        

        return $returnValue;
    }

    /**
     * Short description of method checkProvidedUri
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string uri
     * @return string
     */
    private static function checkProvidedUri($uri)
    {
        $returnValue = (string) '';

        
        if($uri != '') {
        	if(common_Utils::isUri($uri)){
        		$returnValue = $uri;
        	}
        	else{
        		throw new common_Exception("Could not create new Resource, malformed URI provided: '" . $uri . "'.");
        	}
        }
        else{
        	$returnValue = common_Utils::getNewUri();
        }
        

        return (string) $returnValue;
    }

}