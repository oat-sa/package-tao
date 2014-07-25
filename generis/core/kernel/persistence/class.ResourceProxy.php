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
 * Generis Object Oriented API - core/kernel/persistence/class.ResourceProxy.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 14.03.2012, 16:36:04 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package core
 * @subpackage kernel_persistence
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_persistence_PersistenceProxy
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('core/kernel/persistence/class.PersistenceProxy.php');

/**
 * include core_kernel_persistence_hardsql_Resource
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('core/kernel/persistence/hardsql/class.Resource.php');

/**
 * include core_kernel_persistence_ResourceInterface
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('core/kernel/persistence/interface.ResourceInterface.php');

/**
 * include core_kernel_persistence_smoothsql_Resource
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('core/kernel/persistence/smoothsql/class.Resource.php');

/**
 * include core_kernel_persistence_subscription_Resource
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('core/kernel/persistence/subscription/class.Resource.php');

/* user defined includes */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012E3-includes begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012E3-includes end

/* user defined constants */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012E3-constants begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012E3-constants end

/**
 * Short description of class core_kernel_persistence_ResourceProxy
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package core
 * @subpackage kernel_persistence
 */
class core_kernel_persistence_ResourceProxy
    extends core_kernel_persistence_PersistenceProxy
        implements core_kernel_persistence_ResourceInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instance
     *
     * @access public
     * @var PersistanceProxy
     */
    public static $instance = null;

    /**
     * Short description of attribute ressourcesDelegatedTo
     *
     * @access public
     * @var array
     */
    public static $ressourcesDelegatedTo = array();

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
        common_Logger::d('Use of deprecated function getType() please use getTypes().', 'DEPRECATED');
		$returnValue = $this->getTypes($resource);
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
        $delegate = $this->getImpToDelegateTo($resource);
		$returnValue = $delegate->getTypes($resource);
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

		$delegate = $this->getImpToDelegateTo($resource);
		$returnValue = $delegate->getPropertyValues($resource, $property, $options);

		if($this->isValidContext('subscription', $resource)){
			$delegate = core_kernel_persistence_subscription_Resource::singleton();
			$subscriptionValue = $delegate->getPropertyValues($resource, $property, $options);
			$returnValue = array_merge($returnValue, $subscriptionValue);
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

        $delegate = $this->getImpToDelegateTo($resource);
        $returnValue = $delegate->getPropertyValuesCollection($resource, $property);

        if($this->isValidContext('subscription', $resource)){
                $delegate = core_kernel_persistence_subscription_Resource::singleton();
                $subscriptionValue = $delegate->getPropertyValuesCollection($resource, $property);
                $returnValue = $returnValue->union($subscriptionValue);
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
        
        $delegate = $this->getImpToDelegateTo($resource);
        $returnValue = $delegate->getOnePropertyValue($resource, $property, $last);
        
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

		$delegate = $this->getImpToDelegateTo($resource);
		$returnValue = $delegate->getPropertyValuesByLg($resource, $property, $lg);

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

		$delegate = $this->getImpToDelegateTo($resource);
		$returnValue = $delegate->setPropertyValue($resource, $property, $object);

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

		$delegate = $this->getImpToDelegateTo($resource);
		$returnValue = $delegate->setPropertiesValues($resource, $properties);

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

		$delegate = $this->getImpToDelegateTo($resource);
		$returnValue = $delegate->setPropertyValueByLg($resource, $property, $value, $lg);

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

		$delegate = $this->getImpToDelegateTo($resource);
		$returnValue = $delegate->removePropertyValues($resource, $property, $options);

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

		$delegate = $this->getImpToDelegateTo($resource);
		$returnValue = $delegate->removePropertyValueByLg($resource, $property, $lg, $options);

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

		$delegate = $this->getImpToDelegateTo($resource);
		$returnValue = $delegate->getRdfTriples($resource);

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

		$delegate = $this->getImpToDelegateTo($resource);
		$returnValue = $delegate->getUsedLanguages($resource, $property);

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

		$delegate = $this->getImpToDelegateTo($resource);
		$returnValue = $delegate->duplicate($resource, $excludedProperties);

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

		$delegate = $this->getImpToDelegateTo($resource);
		$returnValue = $delegate->delete($resource, $deleteReference);

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

		$delegate = $this->getImpToDelegateTo($resource);
		$returnValue = $delegate->getLastModificationDate($resource, $property);

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

		$delegate = $this->getImpToDelegateTo($resource);
		$returnValue = $delegate->getLastModificationUser($resource);

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
        
		$delegate = $this->getImpToDelegateTo($resource);
		$returnValue = $delegate->getPropertiesValues($resource, $properties/*, $last*/);
        
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
        
		$delegate = $this->getImpToDelegateTo($resource);
		$returnValue = $delegate->setType($resource, $class);
		
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
        
		$delegate = $this->getImpToDelegateTo($resource);
		$returnValue = $delegate->removeType($resource, $class);
		
        // section 127-0-1-1--398d2ad6:12fd3f7ebdd:-8000:000000000000154C end

        return (bool) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_persistence_PersistanceProxy
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000138E begin

		if(core_kernel_persistence_ResourceProxy::$instance == null){
			core_kernel_persistence_ResourceProxy::$instance = new core_kernel_persistence_ResourceProxy();
		}
		$returnValue = core_kernel_persistence_ResourceProxy::$instance;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000138E end

        return $returnValue;
    }

    /**
     * Short description of method getImpToDelegateTo
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  array params
     * @return core_kernel_persistence_ResourceInterface
     */
    public function getImpToDelegateTo( core_kernel_classes_Resource $resource, $params = array())
    {
        $returnValue = null;

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F5D begin
        if(!isset(core_kernel_persistence_ResourceProxy::$ressourcesDelegatedTo[$resource->getUri()]) 
        || core_kernel_persistence_PersistenceProxy::isForcedMode()){
        	
	    	$impls = $this->getAvailableImpl($params);
			foreach($impls as $implName=>$enable){
				// If the implementation is enabled && the resource exists in this context
				if($enable && $this->isValidContext($implName, $resource)){
		        	$implClass = "core_kernel_persistence_{$implName}_Resource";
		        	$reflectionMethod = new ReflectionMethod($implClass, 'singleton');
					$delegate = $reflectionMethod->invoke(null);
					
					if(core_kernel_persistence_PersistenceProxy::isForcedMode()){
						return $delegate;
					}
					
					core_kernel_persistence_ResourceProxy::$ressourcesDelegatedTo[$resource->getUri()] = $delegate;
					break;
		        }
			}
        }
		if(isset(core_kernel_persistence_ResourceProxy::$ressourcesDelegatedTo[$resource->getUri()])){
			$returnValue = core_kernel_persistence_ResourceProxy::$ressourcesDelegatedTo[$resource->getUri()];
		}else{
			$errorMessage = "The resource with uri {$resource->getUri()} does not exist in the available implementation(s): ";
			$i = 0;
			foreach($this->getAvailableImpl() as $name => $valid){
				if($valid){
					if($i>0) {
                        $errorMessage .= ", ";
                    }
					$errorMessage .= $name;
				}
				$i++;
			}
			throw new core_kernel_persistence_Exception($errorMessage);
		}
		
        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F5D end

        return $returnValue;
    }

    /**
     * Short description of method isValidContext
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string context
     * @param  Resource resource
     * @return boolean
     */
    public function isValidContext($context,  core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--499759bc:12f72c12020:-8000:0000000000001558 begin

        $impls = $this->getAvailableImpl();
        if(isset($impls[$context]) && $impls[$context]){
        	$implClass = "core_kernel_persistence_{$context}_Resource";
                if(class_exists($implClass)){
                        $reflectionMethod = new ReflectionMethod($implClass, 'singleton');
                        $singleton = $reflectionMethod->invoke(null);
                        $returnValue = $singleton->isValidContext($resource);  
                }else{
                        throw new Exception('the persistence class does not exists: '.$implClass);
                }
        }
		 
        // section 127-0-1-1--499759bc:12f72c12020:-8000:0000000000001558 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getAllPropertyValues
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  array options
     * @return array
     */
    public function getAllPropertyValues( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $options = array())
    {
        $returnValue = array();

        // section 127-0-1-1-3b11b49e:1323ea85daa:-8000:0000000000003DBC begin
        
        $propertyValues = $this->getPropertyValues($resource, $property, $options);
		
		// Format output data
        
    	foreach ($propertyValues as $propertyValue){
			if(!common_Utils::isUri($propertyValue)) {
				$returnValue[] = new core_kernel_classes_Literal($propertyValue);
			} else {
				$returnValue[] = new core_kernel_classes_Resource($propertyValue);
			}
		}
		
        // section 127-0-1-1-3b11b49e:1323ea85daa:-8000:0000000000003DBC end

        return (array) $returnValue;
    }

} /* end of class core_kernel_persistence_ResourceProxy */

?>