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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               
 * 
 */

/**
 * Short description of class core_kernel_persistence_ClassInterface
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package generis
 
 */
interface core_kernel_persistence_ClassInterface extends core_kernel_persistence_ResourceInterface
{


    // --- OPERATIONS ---

    /**
     * Retrieve all subclass of the class
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  core_kernel_classes_Class $resource
     * @param  boolean recursive
     * @return array
     */
    public function getSubClasses( core_kernel_classes_Class $resource, $recursive = false);

    /**
     * 
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param core_kernel_classes_Class $resource
     * @param core_kernel_classes_Class $iClass
     * @return boolean
     */
    public function setSubClassOf( core_kernel_classes_Class $resource,  core_kernel_classes_Class $iClass);
    
    
    /**
     * check if the resource is a subclass of given parentClass
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  core_kernel_classes_Class resource
     * @param  core_kernel_classes_Class parentClass
     * @return boolean
     */
    public function isSubClassOf( core_kernel_classes_Class $resource,  core_kernel_classes_Class $parentClass);

    /**
     * retrieve parent Classes
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  core_kernel_classes_Class $resource
     * @param  boolean recursive
     * @return array
     */
    public function getParentClasses( core_kernel_classes_Class $resource, $recursive = false);

    /**
     * retrieve properties
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource $resource
     * @param  boolean recursive
     * @return array
     */
    public function getProperties( core_kernel_classes_Class $resource, $recursive = false);

    /**
     * retrieve all instances
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource $resource
     * @param  boolean recursive
     * @param  array params
     * @return array
     */
    public function getInstances( core_kernel_classes_Class $resource, $recursive = false, $params = array());

    /**
     * Should not be called by application code, please use
     * core_kernel_classes_ResourceFactory::create() 
     * or core_kernel_classes_Class::createInstanceWithProperties()
     * instead
     * 
     * Creates a new instance using the properties provided.
     * May NOT contain additional types in the properties array
     * 
     * 
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  string label
     * @param  string comment
     * @param  string uri
     * @return core_kernel_classes_Resource
     * @see core_kernel_classes_ResourceFactory
     */
    public function createInstance( core_kernel_classes_Class $resource, $label = '', $comment = '', $uri = '');

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
    public function createSubClass( core_kernel_classes_Class $resource, $label = '', $comment = '', $uri = '');

    /**
     * Short description of method createProperty
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  core_kernel_classes_Class $resource
     * @param  string label
     * @param  string comment
     * @param  boolean isLgDependent
     * @return core_kernel_classes_Property
     */
    public function createProperty( core_kernel_classes_Class $resource, $label = '', $comment = '', $isLgDependent = false);

    /**
     * Short description of method searchInstances
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  core_kernel_classes_Class $resource
     * @param  array propertyFilters
     * @param  array options
     * @return array
     */
    public function searchInstances( core_kernel_classes_Class $resource, $propertyFilters = array(), $options = array());

    /**
     * Short description of method countInstances
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  core_kernel_classes_Class $resource
     * @param  array propertyFilters
     * @param  array options
     * @return Integer
     */
    public function countInstances( core_kernel_classes_Class $resource, $propertyFilters = array(), $options = array());

    /**
     * Short description of method getInstancesPropertyValues
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  core_kernel_classes_Class $resource
     * @param  Property property
     * @param  array propertyFilters
     * @param  array options
     * @return array
     */
    public function getInstancesPropertyValues( core_kernel_classes_Class $resource,  core_kernel_classes_Property $property, $propertyFilters = array(), $options = array());

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
     * @param  core_kernel_classes_Class type
     * @param  array properties
     * @return core_kernel_classes_Resource
     * @see core_kernel_classes_ResourceFactory
     */
    public function createInstanceWithProperties( core_kernel_classes_Class $type, $properties);

    /**
     * Delete a collection of instances of the Class.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource $resource The resource (class) on which to apply the deletion.
     * @param  array resources An array containing core_kernel_classes_Resource objects or URIs.
     * @param  boolean deleteReference If set to true, references to instances will be deleted accross the database.
     * @return boolean
     */
    public function deleteInstances( core_kernel_classes_Class $resource, $resources, $deleteReference = false);



} 

?>