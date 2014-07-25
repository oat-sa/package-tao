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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\classes\class.ClassFactory.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 28.12.2012, 09:51:28 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-3c0ae01:12c2c9debde:-8000:000000000000138C-includes begin
// section 127-0-1-1-3c0ae01:12c2c9debde:-8000:000000000000138C-includes end

/* user defined constants */
// section 127-0-1-1-3c0ae01:12c2c9debde:-8000:000000000000138C-constants begin
// section 127-0-1-1-3c0ae01:12c2c9debde:-8000:000000000000138C-constants end

/**
 * Short description of class core_kernel_classes_ClassFactory
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_classes
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

        // section 127-0-1-1-3c0ae01:12c2c9debde:-8000:000000000000138D begin
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
        // section 127-0-1-1-3c0ae01:12c2c9debde:-8000:000000000000138D end

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

        // section 127-0-1-1-3c0ae01:12c2c9debde:-8000:0000000000001393 begin
		$property = new core_kernel_classes_Class(RDF_PROPERTY);
		$propertyInstance = self::createInstance($property, $label, $comment, $uri);
		$returnValue = new core_kernel_classes_Property($propertyInstance->getUri());
		if (!$clazz->setProperty($returnValue)){
			throw new common_Exception('An error occured during Property creation.');
		}
		else{
			$returnValue->setLgDependent($isLgDependent);
		}

        // section 127-0-1-1-3c0ae01:12c2c9debde:-8000:0000000000001393 end

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

        // section 127-0-1-1-3c0ae01:12c2c9debde:-8000:00000000000013A2 begin
		$class = new core_kernel_classes_Class(RDFS_CLASS);
		$intance =  self::createInstance($class, $label, $comment, $uri);
		$returnValue = new core_kernel_classes_Class($instance->getUri());
		$returnValue->setSubClassOf($clazz);

        // section 127-0-1-1-3c0ae01:12c2c9debde:-8000:00000000000013A2 end

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

        // section 127-0-1-1-706d7d33:138909bcd61:-8000:0000000000001B14 begin
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
        // section 127-0-1-1-706d7d33:138909bcd61:-8000:0000000000001B14 end

        return (string) $returnValue;
    }

} /* end of class core_kernel_classes_ClassFactory */

?>