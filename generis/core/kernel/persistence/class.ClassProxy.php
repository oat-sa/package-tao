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
 * Generis Object Oriented API - core\kernel\persistence\class.ClassProxy.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 03.01.2013, 10:59:32 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_persistence
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_persistence_PersistenceProxy
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('core/kernel/persistence/class.PersistenceProxy.php');

/**
 * include core_kernel_persistence_hardsql_Class
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('core/kernel/persistence/hardsql/class.Class.php');

/**
 * include core_kernel_persistence_ClassInterface
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('core/kernel/persistence/interface.ClassInterface.php');

/**
 * include core_kernel_persistence_smoothsql_Class
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('core/kernel/persistence/smoothsql/class.Class.php');

/**
 * include core_kernel_persistence_subscription_Class
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('core/kernel/persistence/subscription/class.Class.php');

/* user defined includes */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000139B-includes begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000139B-includes end

/* user defined constants */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000139B-constants begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000139B-constants end

/**
 * Short description of class core_kernel_persistence_ClassProxy
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_persistence
 */
class core_kernel_persistence_ClassProxy
    extends core_kernel_persistence_PersistenceProxy
        implements core_kernel_persistence_ClassInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instance
     *
     * @access public
     * @var core_kernel_persistence_ClassProxy
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
     * Short description of method getSubClasses
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  boolean recursive
     * @return array
     */
    public function getSubClasses( core_kernel_classes_Resource $resource, $recursive = false)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014EB begin
    	
        $delegate = $this->getImpToDelegateTo($resource);
        if($delegate instanceof core_kernel_persistence_hardsql_Class){
                // Use the smooth sql implementation to get this information
		// Or find the right way to treat this case
                $returnValue = core_kernel_persistence_smoothsql_Class::singleton()->getSubClasses($resource, $recursive);
        }else{
                $returnValue = $delegate->getSubClasses($resource, $recursive);
        }
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014EB end

        return (array) $returnValue;
    }

    /**
     * Short description of method isSubClassOf
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  Class parentClass
     * @return boolean
     */
    public function isSubClassOf( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $parentClass)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014F0 begin
        
        $delegate = $this->getImpToDelegateTo($resource);
        if($delegate instanceof core_kernel_persistence_hardsql_Class){
                // Use the smooth sql implementation to get this information
		// Or find the right way to treat this case
                $returnValue = core_kernel_persistence_smoothsql_Class::singleton()->isSubClassOf($resource, $parentClass);
        }else{
                $returnValue = $delegate->isSubClassOf($resource, $parentClass);
        }
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014F0 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getParentClasses
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  boolean recursive
     * @return array
     */
    public function getParentClasses( core_kernel_classes_Resource $resource, $recursive = false)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014F5 begin
        
        $delegate = $this->getImpToDelegateTo($resource);
        if($delegate instanceof core_kernel_persistence_hardsql_Class){
                // Use the smooth sql implementation to get this information
		// Or find the right way to treat this case
                $returnValue = core_kernel_persistence_smoothsql_Class::singleton()->getParentClasses($resource, $recursive);
        }else{
                $returnValue = $delegate->getParentClasses($resource, $recursive);
        }
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014F5 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getProperties
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  boolean recursive
     * @return array
     */
    public function getProperties( core_kernel_classes_Resource $resource, $recursive = false)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014FA begin
        
    	$delegate = $this->getImpToDelegateTo($resource);
        if($delegate instanceof core_kernel_persistence_hardsql_Class){
                // Use the smooth sql implementation to get this information
		// Or find the right way to treat this case
                $returnValue = core_kernel_persistence_smoothsql_Class::singleton()->getProperties($resource, $recursive);
        }else{
                $returnValue = $delegate->getProperties($resource, $recursive);
        }
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014FA end

        return (array) $returnValue;
    }

    /**
     * Short description of method getInstances
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  boolean recursive
     * @param  array params
     * @return array
     */
    public function getInstances( core_kernel_classes_Resource $resource, $recursive = false, $params = array())
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001500 begin
        
        $delegate = $this->getImpToDelegateTo($resource);
		$returnValue = $delegate->getInstances($resource, $recursive, $params);
    
        if($this->isValidContext('subscription', $resource)){
        	$delegate = core_kernel_persistence_subscription_Class::singleton();
        	$subscriptionValue = $delegate->getInstances($resource, $recursive);
        	$returnValue = array_merge($returnValue, $subscriptionValue);
        }
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001500 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setInstance
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  Resource instance
     * @return core_kernel_classes_Resource
     */
    public function setInstance( core_kernel_classes_Resource $resource,  core_kernel_classes_Resource $instance)
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001506 begin
        
        $delegate = $this->getImpToDelegateTo($resource);
        $returnValue = $delegate->setInstance($resource, $instance);
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001506 end

        return $returnValue;
    }

    /**
     * Short description of method setSubClassOf
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  Class iClass
     * @return boolean
     */
    public function setSubClassOf( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $iClass)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000150F begin
        
    	$delegate = $this->getImpToDelegateTo($resource);
        if($delegate instanceof core_kernel_persistence_hardsql_Class){
                // Use the smooth sql implementation to get this information
		// Or find the right way to treat this case
                $returnValue = core_kernel_persistence_smoothsql_Class::singleton()->setSubClassOf($resource, $iClass);
        }else{
                $returnValue = $delegate->setSubClassOf($resource, $iClass);
        }
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000150F end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setProperty
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @return boolean
     */
    public function setProperty( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001512 begin
        
        $delegate = $this->getImpToDelegateTo($resource);
        if($delegate instanceof core_kernel_persistence_hardsql_Class){
                // Use the smooth sql implementation to get this information
		// Or find the right way to treat this case
                $returnValue = core_kernel_persistence_smoothsql_Class::singleton()->setProperty($resource, $property);
        }else{
                $returnValue = $delegate->setProperty($resource, $property);
        }
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001512 end

        return (bool) $returnValue;
    }

    /**
     * Should not be called by application code, please use
     * instead
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  string label
     * @param  string comment
     * @param  string uri
     * @return core_kernel_classes_Resource
     */
    public function createInstance( core_kernel_classes_Resource $resource, $label = '', $comment = '', $uri = '')
    {
        $returnValue = null;

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F27 begin
        
        $delegate = $this->getImpToDelegateTo($resource);
        $returnValue = $delegate->createInstance($resource, $label, $comment, $uri);
        
        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F27 end

        return $returnValue;
    }

    /**
     * Short description of method createSubClass
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  string label
     * @param  string comment
     * @param  string uri
     * @return core_kernel_classes_Class
     */
    public function createSubClass( core_kernel_classes_Resource $resource, $label = '', $comment = '', $uri = '')
    {
        $delegate = $this->getImpToDelegateTo($resource);
        return $delegate->createSubClass($resource, $label, $comment, $uri);
    }

    /**
     * Short description of method createProperty
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  string label
     * @param  string comment
     * @param  boolean isLgDependent
     * @return core_kernel_classes_Property
     */
    public function createProperty( core_kernel_classes_Resource $resource, $label = '', $comment = '', $isLgDependent = false)
    {
        $returnValue = null;

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F3C begin
        
    	$delegate = $this->getImpToDelegateTo($resource);
        $returnValue = $delegate->createProperty($resource, $label, $comment, $isLgDependent);
        
        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F3C end

        return $returnValue;
    }

    /**
     * Short description of method searchInstances
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  array propertyFilters
     * @param  array options
     * @return array
     */
    public function searchInstances( core_kernel_classes_Resource $resource, $propertyFilters = array(), $options = array())
    {
        $returnValue = array();

        // section 10-13-1--128--26678bb4:12fbafcb344:-8000:00000000000014F0 begin
        
        $clazzToImpls = array();
        $implsToDelegateTo = array();
        
        if (isset($options['recursive']) && $options['recursive']) {
	        $clazzes = $this->getSubClasses($resource, true);
	        $clazzes[] = $resource; 
        }
        else {
        	$clazzes = array($resource);
        }
        unset($options['recursive']);
        
        if (isset($options['additionalClasses'])) {
	        foreach ($options['additionalClasses'] as $addclass) {
	        	$clazzes[] = ($addclass instanceof core_kernel_classes_Resource) ? $addclass : new core_kernel_classes_Class($addclass);
	        }
        }
        unset($options['additionalClasses']);

        foreach ($clazzes as $clazz) {
	        $delegate = $this->getImpToDelegateTo($clazz);
	        if (!isset($clazzToImpls[get_class($delegate)])) {
		        $clazzToImpls[get_class($delegate)] = array();
		        $implsToDelegateTo[get_class($delegate)] = $delegate;
	        }
	        $clazzToImpls[get_class($delegate)][] = $clazz;
        }
                
        //@todo allow dev to search with limit and offset on multiple impls
        if (count(array_keys($implsToDelegateTo)) > 1 && (isset($options['limit']) || isset($options['offset']))) {
        	throw new common_Exception('Unable to search instances on multiple implementations with the options limit and offset');
        }

        foreach ($implsToDelegateTo as $clazzName => $delegate){
	        $classes = $clazzToImpls[$clazzName];
	        $firstClass = array_shift($classes);
	        $options['additionalClasses'] = $classes;
	                        
	        $returnValue = array_merge($returnValue, $delegate->searchInstances($firstClass, $propertyFilters, $options));
        }
        // section 10-13-1--128--26678bb4:12fbafcb344:-8000:00000000000014F0 end

        return (array) $returnValue;
    }

    /**
     * Short description of method countInstances
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  array propertyFilters
     * @param  array options
     * @return Integer
     */
    public function countInstances( core_kernel_classes_Resource $resource, $propertyFilters = array(), $options = array())
    {
        $returnValue = null;

        // section 127-0-1-1--700ce06c:130dbc6fc61:-8000:000000000000159D begin
        
		$delegate = $this->getImpToDelegateTo($resource);
        $returnValue = $delegate->countInstances($resource, $propertyFilters, $options);
        
        // section 127-0-1-1--700ce06c:130dbc6fc61:-8000:000000000000159D end

        return $returnValue;
    }

    /**
     * Short description of method getInstancesPropertyValues
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  array propertyFilters
     * @param  array options
     * @return array
     */
    public function getInstancesPropertyValues( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $propertyFilters = array(), $options = array())
    {
        $returnValue = array();

        // section 127-0-1-1--120bf54f:13142fdf597:-8000:000000000000312D begin
        
        $delegate = $this->getImpToDelegateTo($resource);
        $returnValue = $delegate->getInstancesPropertyValues($resource, $property, $propertyFilters, $options);
        
        // section 127-0-1-1--120bf54f:13142fdf597:-8000:000000000000312D end

        return (array) $returnValue;
    }

    /**
     * Short description of method unsetProperty
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @return boolean
     */
    public function unsetProperty( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4f08ff91:131764e4b1f:-8000:00000000000031F8 begin
        
        $delegate = $this->getImpToDelegateTo($resource);
        $returnValue = $delegate->unsetProperty($resource, $property);
        
        // section 127-0-1-1-4f08ff91:131764e4b1f:-8000:00000000000031F8 end

        return (bool) $returnValue;
    }

    /**
     * Should not be called by application code, please use
     * core_kernel_classes_ResourceFactory::create() 
     * or core_kernel_classes_Class::createInstanceWithProperties()
     * instead
     *
     * Creates a new instance using the properties provided.
     * May NOT contain additional types in the properties array
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Class type
     * @param  array properties
     * @return core_kernel_classes_Resource
     * @see core_kernel_classes_ResourceFactory
     */
    public function createInstanceWithProperties( core_kernel_classes_Class $type, $properties)
    {
        $returnValue = null;

        // section 127-0-1-1--49b11f4f:135c41c62e3:-8000:0000000000001947 begin
        $delegate = $this->getImpToDelegateTo($type);
        $returnValue = $delegate->createInstanceWithProperties($type, $properties);
        // section 127-0-1-1--49b11f4f:135c41c62e3:-8000:0000000000001947 end

        return $returnValue;
    }

    /**
     * Delete a collection of instances of the Class.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource The resource (class) on which to apply the deletion.
     * @param  array resources An array containing core_kernel_classes_Resource objects or URIs.
     * @param  boolean deleteReference If set to true, references to instances will be deleted accross the database.
     * @return boolean
     */
    public function deleteInstances( core_kernel_classes_Resource $resource, $resources, $deleteReference = false)
    {
        $returnValue = (bool) false;

        // section 10-13-1-85-46895b07:13b99a96e9b:-8000:0000000000001DF5 begin
        $delegate = $this->getImpToDelegateTo($resource);
        $returnValue = $delegate->deleteInstances($resource, $resources, $deleteReference);
        // section 10-13-1-85-46895b07:13b99a96e9b:-8000:0000000000001DF5 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method delete
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  boolean deleteReference
     * @return boolean
     */
    public function delete( core_kernel_classes_Resource $resource, $deleteReference = false)
    {
        $returnValue = (bool) false;

        // section 10-13-1-85--2c835591:13bffd6ae29:-8000:0000000000001E78 begin
        $delegate = $this->getImpToDelegateTo($resource);
        $returnValue = $delegate->delete($resource, $deleteReference);
        // section 10-13-1-85--2c835591:13bffd6ae29:-8000:0000000000001E78 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return core_kernel_persistence_ClassProxy
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000013FF begin
        
        if(core_kernel_persistence_ClassProxy::$instance == null){
        	core_kernel_persistence_ClassProxy::$instance = new core_kernel_persistence_ClassProxy();
        }
        $returnValue = core_kernel_persistence_ClassProxy::$instance;
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000013FF end

        return $returnValue;
    }

    /**
     * Short description of method getImpToDelegateTo
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  array params
     * @return core_kernel_persistence_ResourceInterface
     */
    public function getImpToDelegateTo( core_kernel_classes_Resource $resource, $params = array())
    {
        $returnValue = null;

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F60 begin

        if(!isset(core_kernel_persistence_ClassProxy::$ressourcesDelegatedTo[$resource->getUri()]) 
        || core_kernel_persistence_PersistenceProxy::isForcedMode()){
        	
	    	$impls = $this->getAvailableImpl($params);
			foreach($impls as $implName=>$enable){
				// If the implementation is enabled && the resource exists in this context
				if($enable && $this->isValidContext($implName, $resource)){
		        	$implClass = "core_kernel_persistence_{$implName}_Class";
		        	$reflectionMethod = new ReflectionMethod($implClass, 'singleton');
					$delegate = $reflectionMethod->invoke(null);
					
					if(core_kernel_persistence_PersistenceProxy::isForcedMode()){
						return $delegate;
					}
					
					core_kernel_persistence_ClassProxy::$ressourcesDelegatedTo[$resource->getUri()] = $delegate;
					break;
		        }
			}
        }
        
        $returnValue = core_kernel_persistence_ClassProxy::$ressourcesDelegatedTo[$resource->getUri()];
        
        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F60 end

        return $returnValue;
    }

    /**
     * Short description of method isValidContext
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string context
     * @param  Resource resource
     * @return boolean
     */
    public function isValidContext($context,  core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--499759bc:12f72c12020:-8000:000000000000155B begin
        
        $impls = $this->getAvailableImpl();             
        if(isset($impls[$context]) && $impls[$context]){
        	$implClass = "core_kernel_persistence_{$context}_Class";
        	$reflectionMethod = new ReflectionMethod($implClass, 'singleton');
			$singleton = $reflectionMethod->invoke(null);
			$returnValue = $singleton->isValidContext($resource);
        }  
        
        // section 127-0-1-1--499759bc:12f72c12020:-8000:000000000000155B end

        return (bool) $returnValue;
    }

} /* end of class core_kernel_persistence_ClassProxy */

?>