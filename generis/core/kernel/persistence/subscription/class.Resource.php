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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 14.03.2012, 16:36:03 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_subscription
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_persistence_PersistenceImpl
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('core/kernel/persistence/class.PersistenceImpl.php');

/**
 * include core_kernel_persistence_ResourceInterface
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('core/kernel/persistence/interface.ResourceInterface.php');

/* user defined includes */
// section 127-0-1-1--499759bc:12f72c12020:-8000:000000000000140B-includes begin
// section 127-0-1-1--499759bc:12f72c12020:-8000:000000000000140B-includes end

/* user defined constants */
// section 127-0-1-1--499759bc:12f72c12020:-8000:000000000000140B-constants begin
// section 127-0-1-1--499759bc:12f72c12020:-8000:000000000000140B-constants end

/**
 * Short description of class core_kernel_persistence_subscription_Resource
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_subscription
 */
class core_kernel_persistence_subscription_Resource
    extends core_kernel_persistence_PersistenceImpl
        implements core_kernel_persistence_ResourceInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instance
     *
     * @access public
     * @var Resource
     */
    public static $instance = null;

    // --- OPERATIONS ---

    /**
     * Please use getTypes instead
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @deprecated
     * @param  Resource resource
     * @return array
     */
    public function getType( core_kernel_classes_Resource $resource)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001298 begin
        throw new core_kernel_persistence_ProhibitedFunctionException('getType() called, RessourceProxy should have delegated this to getTypes()');
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001298 end

        return (array) $returnValue;
    }

    /**
     * returns an array of types the ressource has
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return array
     */
    public function getTypes( core_kernel_classes_Resource $resource)
    {
        $returnValue = array();

        // section 127-0-1-1--1ee05ee5:13611d6d34c:-8000:000000000000196B begin

		throw new core_kernel_persistence_ProhibitedFunctionException("The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        
		// section 127-0-1-1--1ee05ee5:13611d6d34c:-8000:000000000000196B end

        return (array) $returnValue;
    }

    /**
     * Short description of method getPropertyValues
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  array options
     * @return array
     */
    public function getPropertyValues( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $options = array())
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000129B begin

		if ($property->getUri() != PROPERTY_SUBCRIPTION_MASK && $property->getUri() != PROPERTY_SUBCRIPTION_URL
		&& $property->getUri() != PROPERTY_MASK_SUBJECT && $property->getUri() != PROPERTY_MASK_PREDICATE
		&& $property->getUri() != PROPERTY_MASK_OBJECT){
		
			$subcriptions = core_kernel_subscriptions_Service::singleton()->getSubscriptions ($resource, $property, null);
			foreach ($subcriptions as $sub){
				$subcriptionResource = new core_kernel_classes_Resource ($sub);
				$subcriptionsInstances = core_kernel_subscriptions_Service::singleton()->getPropertyValuesFromSubscription ($subcriptionResource, $resource, $property);
				$returnValue = array_merge ($returnValue, $subcriptionsInstances);
			}
			
		}

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000129B end

        return (array) $returnValue;
    }

    /**
     * Short description of method getPropertyValuesCollection
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @return core_kernel_classes_ContainerCollection
     */
    public function getPropertyValuesCollection( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000129F begin
		
		$returnValue = new core_kernel_classes_ContainerCollection($resource);
		
		if ($property->getUri() != PROPERTY_SUBCRIPTION_MASK && $property->getUri() != PROPERTY_SUBCRIPTION_URL
		&& $property->getUri() != PROPERTY_MASK_SUBJECT && $property->getUri() != PROPERTY_MASK_PREDICATE
		&& $property->getUri() != PROPERTY_MASK_OBJECT){
			
			$propertiesValues = array ();
			$subcriptions = core_kernel_subscriptions_Service::singleton()->getSubscriptions ($resource, $property, null);

			foreach ($subcriptions as $sub){
				$subcriptionResource = new core_kernel_classes_Resource ($sub);
				$subcriptionsInstances = core_kernel_subscriptions_Service::singleton()->getPropertyValuesFromSubscription ($subcriptionResource, $resource, $property);
				$propertiesValues = array_merge ($propertiesValues, $subcriptionsInstances);
			}

			foreach ($propertiesValues as $value){
				if(!common_Utils::isUri($value)) {
					$container = new core_kernel_classes_Literal($value);
				}
				else {
					$container = new core_kernel_classes_Resource($value);
				}

				if(DEBUG_MODE){
					$container->debug = __METHOD__ .'|' . $property->debug;
				}
				$returnValue->add($container);
			}
			
		}

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000129F end

        return $returnValue;
    }

    /**
     * Short description of method getOnePropertyValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  boolean last
     * @return core_kernel_classes_Container
     */
    public function getOnePropertyValue( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $last = false)
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012A3 begin

		throw new core_kernel_persistence_ProhibitedFunctionException("The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");


        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012A3 end

        return $returnValue;
    }

    /**
     * Short description of method getPropertyValuesByLg
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  string lg
     * @return core_kernel_classes_ContainerCollection
     */
    public function getPropertyValuesByLg( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $lg)
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012A9 begin

		throw new core_kernel_persistence_ProhibitedFunctionException("The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");


        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012A9 end

        return $returnValue;
    }

    /**
     * Short description of method setPropertyValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  string object
     * @param  string lg
     * @return boolean
     */
    public function setPropertyValue( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $object, $lg = null)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012AE begin

		throw new core_kernel_persistence_ProhibitedFunctionException("The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");


        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012AE end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setPropertiesValues
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  array properties
     * @return boolean
     */
    public function setPropertiesValues( core_kernel_classes_Resource $resource, $properties)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012B3 begin

		throw new core_kernel_persistence_ProhibitedFunctionException("The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");


        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012B3 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setPropertyValueByLg
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  string value
     * @param  string lg
     * @return boolean
     */
    public function setPropertyValueByLg( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $value, $lg)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012B7 begin

		throw new core_kernel_persistence_ProhibitedFunctionException("The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");


        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012B7 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method removePropertyValues
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  array options
     * @return boolean
     */
    public function removePropertyValues( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $options = array())
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012BD begin

		throw new core_kernel_persistence_ProhibitedFunctionException("The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");


        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012BD end

        return (bool) $returnValue;
    }

    /**
     * Short description of method removePropertyValueByLg
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  string lg
     * @param  array options
     * @return boolean
     */
    public function removePropertyValueByLg( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $lg, $options = array())
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012C1 begin

		throw new core_kernel_persistence_ProhibitedFunctionException("The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");


        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012C1 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getRdfTriples
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return core_kernel_classes_ContainerCollection
     */
    public function getRdfTriples( core_kernel_classes_Resource $resource)
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012C6 begin

		throw new core_kernel_persistence_ProhibitedFunctionException("The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");


        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012C6 end

        return $returnValue;
    }

    /**
     * Short description of method getUsedLanguages
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @return array
     */
    public function getUsedLanguages( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012C9 begin

		throw new core_kernel_persistence_ProhibitedFunctionException("The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");


        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012C9 end

        return (array) $returnValue;
    }

    /**
     * Short description of method duplicate
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  array excludedProperties
     * @return core_kernel_classes_Resource
     */
    public function duplicate( core_kernel_classes_Resource $resource, $excludedProperties = array())
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012CD begin

		throw new core_kernel_persistence_ProhibitedFunctionException("The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");


        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012CD end

        return $returnValue;
    }

    /**
     * Short description of method delete
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  boolean deleteReference
     * @return boolean
     */
    public function delete( core_kernel_classes_Resource $resource, $deleteReference = false)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012D2 begin

		throw new core_kernel_persistence_ProhibitedFunctionException("The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");


        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012D2 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getLastModificationDate
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @return core_kernel_persistence_doc_date
     */
    public function getLastModificationDate( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property = null)
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012D7 begin

		throw new core_kernel_persistence_ProhibitedFunctionException("The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");


        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012D7 end

        return $returnValue;
    }

    /**
     * Short description of method getLastModificationUser
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return string
     */
    public function getLastModificationUser( core_kernel_classes_Resource $resource)
    {
        $returnValue = (string) '';

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012DC begin

		throw new core_kernel_persistence_ProhibitedFunctionException("The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012DC end

        return (string) $returnValue;
    }

    /**
     * Short description of method getPropertiesValues
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  array properties
     * @return array
     */
    public function getPropertiesValues( core_kernel_classes_Resource $resource, $properties)
    {
        $returnValue = array();

        // section 127-0-1-1-77557f59:12fa87873f4:-8000:00000000000014D1 begin
        
		throw new core_kernel_persistence_ProhibitedFunctionException("The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
		
        // section 127-0-1-1-77557f59:12fa87873f4:-8000:00000000000014D1 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setType
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Class class
     * @return boolean
     */
    public function setType( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $class)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--398d2ad6:12fd3f7ebdd:-8000:0000000000001548 begin
        
		throw new core_kernel_persistence_ProhibitedFunctionException("The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
		
        // section 127-0-1-1--398d2ad6:12fd3f7ebdd:-8000:0000000000001548 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method removeType
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Class class
     * @return boolean
     */
    public function removeType( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $class)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--398d2ad6:12fd3f7ebdd:-8000:000000000000154C begin
        
		throw new core_kernel_persistence_ProhibitedFunctionException("The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
		
        // section 127-0-1-1--398d2ad6:12fd3f7ebdd:-8000:000000000000154C end

        return (bool) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_classes_Resource
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 127-0-1-1--499759bc:12f72c12020:-8000:000000000000142F begin

		if (core_kernel_persistence_subscription_Resource::$instance == null){
			core_kernel_persistence_subscription_Resource::$instance = new core_kernel_persistence_subscription_Resource();
		}
		$returnValue = core_kernel_persistence_subscription_Resource::$instance;

        // section 127-0-1-1--499759bc:12f72c12020:-8000:000000000000142F end

        return $returnValue;
    }

    /**
     * Short description of method isValidContext
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function isValidContext( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--499759bc:12f72c12020:-8000:0000000000001437 begin

		if(defined('ENABLE_SUBSCRIPTION') && ENABLE_SUBSCRIPTION
		&& $resource->getUri() != CLASS_SUBCRIPTION 	&& $resource->getUri() != CLASS_MASK){
			$returnValue = true;
		}

        // section 127-0-1-1--499759bc:12f72c12020:-8000:0000000000001437 end

        return (bool) $returnValue;
    }

} /* end of class core_kernel_persistence_subscription_Resource */

?>