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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * A scale for the measurements of an item
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 
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

        
        if ($this instanceof taoItems_models_classes_Scale_Numerical) {
        	$returnValue[TAO_ITEM_LOWER_BOUND_PROPERTY] = $this->lowerBound;
        	$returnValue[TAO_ITEM_UPPER_BOUND_PROPERTY] = $this->upperBound;
        };
        if ($this instanceof taoItems_models_classes_Scale_Discrete) {
        	$returnValue[TAO_ITEM_DISCRETE_SCALE_DISTANCE_PROPERTY] = $this->distance;
        };
        

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

        
        if (!defined(get_class($this).'::CLASS_URI')) {
        	throw new common_exception_Error('Missing CLASS_URI for Scale Implementation '.get_class($this));
        }
        $returnValue = static::CLASS_URI;
        

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
        

        return $returnValue;
    }

} /* end of abstract class taoItems_models_classes_Scale_Scale */

?>