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
 * A scale for the measurements of an item
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Scale
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-6e4e28d3:1358714af41:-8000:00000000000037F5-includes begin
// section 127-0-1-1-6e4e28d3:1358714af41:-8000:00000000000037F5-includes end

/* user defined constants */
// section 127-0-1-1-6e4e28d3:1358714af41:-8000:00000000000037F5-constants begin
// section 127-0-1-1-6e4e28d3:1358714af41:-8000:00000000000037F5-constants end

/**
 * A scale for the measurements of an item
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Scale
 */
abstract class taoItems_models_classes_Scale_Scale
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Builds a Scale Object from the properties of the knowledge base
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource ressource
     * @return taoItems_models_classes_Scale_Scale
     */
    public static function buildFromRessource( core_kernel_classes_Resource $ressource)
    {
        $returnValue = null;

        // section 127-0-1-1--7ddc6625:1358a866f6a:-8000:0000000000003824 begin
        foreach ($ressource->getTypes() as $type) {
	        try {
	        	$returnValue = self::createByClass($type->getUri());
	        	break;
	        } catch (common_Exception $e) {
	        	// not nescessary an exception since ressource can have many types
	        };
        }
        if (is_null($returnValue)) {
        	throw new common_exception_Error('Unknown Scale Type for '.$ressource->getUri());	
        }
    	
    	if ($returnValue instanceof taoItems_models_classes_Scale_Numerical) {
    		$returnValue->lowerBound = (string)$ressource->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_LOWER_BOUND_PROPERTY));
    		$returnValue->upperBound = (string)$ressource->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_UPPER_BOUND_PROPERTY));
    	}
    	if ($returnValue instanceof taoItems_models_classes_Scale_Discrete) {
    		$returnValue->distance = (string)$ressource->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_DISCRETE_SCALE_DISTANCE_PROPERTY));
    	}
        // section 127-0-1-1--7ddc6625:1358a866f6a:-8000:0000000000003824 end

        return $returnValue;
    }

    /**
     * Prepares the properties for the knowledge base
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function toProperties()
    {
        $returnValue = array();

        // section 127-0-1-1--7ddc6625:1358a866f6a:-8000:0000000000003829 begin
        if ($this instanceof taoItems_models_classes_Scale_Numerical) {
        	$returnValue[TAO_ITEM_LOWER_BOUND_PROPERTY] = $this->lowerBound;
        	$returnValue[TAO_ITEM_UPPER_BOUND_PROPERTY] = $this->upperBound;
        };
        if ($this instanceof taoItems_models_classes_Scale_Discrete) {
        	$returnValue[TAO_ITEM_DISCRETE_SCALE_DISTANCE_PROPERTY] = $this->distance;
        };
        // section 127-0-1-1--7ddc6625:1358a866f6a:-8000:0000000000003829 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getClassUri
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getClassUri()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--7ddc6625:1358a866f6a:-8000:0000000000003827 begin
        if (!defined(get_class($this).'::CLASS_URI')) {
        	throw new common_exception_Error('Missing CLASS_URI for Scale Implementation '.get_class($this));
        }
        $returnValue = static::CLASS_URI;
        // section 127-0-1-1--7ddc6625:1358a866f6a:-8000:0000000000003827 end

        return (string) $returnValue;
    }

    /**
     * Short description of method createByClass
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string uri
     * @return taoItems_models_classes_Scale_Scale
     */
    public static function createByClass($uri)
    {
        $returnValue = null;

        // section 127-0-1-1-67366732:1359ace6a59:-8000:0000000000003826 begin
		switch ($uri) {
        	case taoItems_models_classes_Scale_Discrete::CLASS_URI:
        		$returnValue = new taoItems_models_classes_Scale_Discrete();
        		break;
        	case taoItems_models_classes_Scale_Numerical::CLASS_URI:
        		$returnValue = new taoItems_models_classes_Scale_Numerical();
        		break;
        	case taoItems_models_classes_Scale_Enumeration::CLASS_URI:
        		$returnValue = new taoItems_models_classes_Scale_Enumeration();
        		break;
        	default:
        		throw new common_Exception('Unknown Scale Type for '.$uri);	
        }
        // section 127-0-1-1-67366732:1359ace6a59:-8000:0000000000003826 end

        return $returnValue;
    }

} /* end of abstract class taoItems_models_classes_Scale_Scale */

?>