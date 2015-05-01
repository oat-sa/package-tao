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

use oat\generis\model\data\ModelManager;
use oat\generis\model\data\permission\PermissionManager;

/**
 * The class of rdfs:classes. It implements basic tests like isSubClassOf(Class
 * instances, properties and subclasses retrieval, but also enable to edit it
 * setSubClassOf setProperty, etc.
 *
 *
 * @author patrick.plichart@tudor.lu
 * @package generis
 
 * @see http://www.w3.org/RDF/
 * @see http://www.w3.org/TR/rdf-schema/
 *
 */
class core_kernel_classes_Class
    extends core_kernel_classes_Resource
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---
    /**
     * 
     * @return core_kernel_persistence_ClassInterface
     */
    private function getImplementation() {
        return ModelManager::getModel()->getRdfsInterface()->getClassImplementation();
    }
    
    
    /**
     * returns the collection of direct subClasses (see getIndirectSubClassesOf
     * a complete list of subclasses)
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @param  boolean recursive
     * @return array
     * @see http://www.w3.org/TR/rdf-schema/
     */
    public function getSubClasses($recursive = false)
    {
        return (array) $this->getImplementation()->getSubClasses($this, $recursive);
    }

    /**
     * returns true if this is a rdfs:subClassOf $parentClass
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @param  Class parentClass
     * @return boolean
     */
    public function isSubClassOf( core_kernel_classes_Class $parentClass)
    {
        return (bool) $this->getImplementation()->isSubClassOf($this, $parentClass);
    }

    /**
     * returns all parent classes as a collection
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @param  boolean recursive
     * @return array
     */
    public function getParentClasses($recursive = false)
    {
       return (array) $this->getImplementation()->getParentClasses($this, $recursive);
    }

    /**
     * Returns the Properties bound to the Class. If the $recursive parameter is
     * to true, the whole class hierarchy will be inspected from the current
     * to the top one to retrieve tall its properties.
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @param  boolean recursive Recursive Properties retrieval accross the Class hierarchy.
     * @return array
     */
    public function getProperties($recursive = false)
    {
        return (array) $this->getImplementation()->getProperties($this, $recursive);
    }

    /**
     * return direct instances of this class as a collection
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @param  boolean recursive
     * @param  array params
     * @return array
     */
    public function getInstances($recursive = false, $params = array())
    {
        return (array) $this->getImplementation()->getInstances($this, $recursive, $params);
    }

    /**
     * creates a new instance of the class todo : different from the method
     * which simply link the previously created ressource with this class
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource instance
     * @return core_kernel_classes_Resource
     * @deprecated
     */
    public function setInstance( core_kernel_classes_Resource $instance)
    {
        $returnValue = null;

        
		
        $returnValue = $this->getImplementation()->setInstance($this, $instance);
        
        

        return $returnValue;
    }

    /**
     * alias to setPropertyValues using rdfs: subClassOf, uriClass must be a
     * Class otherwise it returns false
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @param  Class iClass
     * @return boolean
     */
    public function setSubClassOf( core_kernel_classes_Class $iClass)
    {
        return (bool) $this->getImplementation()->setSubClassOf($this, $iClass);
    }

    /**
     * add a property to the class, uriProperty must be a valid property
     * the method returns false
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @param  Property property
     * @return boolean
     * @deprecated
     */
    public function setProperty( core_kernel_classes_Property $property)
    {
        return (bool) $this->getImplementation()->setProperty($this, $property);
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string uri
     * @param  string debug
     */
    public function __construct($uri, $debug = '')
    {
        
		parent::__construct($uri, $debug);
        
    }


    /**
     * Should not be called by application code, please use
     * core_kernel_classes_ResourceFactory::create() instead
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string label
     * @param  string comment
     * @param  string uri
     * @return core_kernel_classes_Resource
     */
    public function createInstance($label = '', $comment = '', $uri = '')
    {
        $returnValue = null;

        $returnValue = $this->getImplementation()->createInstance($this, $label, $comment, $uri);
        PermissionManager::getPermissionModel()->onResourceCreated($returnValue);

        return $returnValue;
    }

    /**
     * Short description of method createSubClass
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string label
     * @param  string comment
     * @param  string uri
     * @return core_kernel_classes_Class
     */
    public function createSubClass($label = '', $comment = '', $uri = "")
    {
        $returnValue = null;
		
        $returnValue = $this->getImplementation()->createSubClass($this, $label, $comment, $uri);
        PermissionManager::getPermissionModel()->onResourceCreated($returnValue);
        
        return $returnValue;
    }

    /**
     * Short description of method createProperty
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string label
     * @param  string comment
     * @param  boolean isLgDependent
     * @return core_kernel_classes_Property
     */
    public function createProperty($label = '', $comment = '', $isLgDependent = false)
    {
        $returnValue = null;

        
		
        $returnValue = $this->getImplementation()->createProperty($this, $label, $comment, $isLgDependent);
        
        

        return $returnValue;
    }

    /**
     * Retrieve available methods on class
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getMethodes()
    {
        return array( 'instanciate' => true , 'addSubclass' => true , 'addPropery' => true);
    }

    /**
     * Search for a specific instances according to filters and options
     * 
     * options lists:
     * like			: (bool) 	true/false (default: true)
     * chaining		: (string) 	'or'/'and' (default: 'and')
     * recursive	: (bool) 	saerch in subvlasses(default: false)
     * lang			: (string) 	e.g. 'en-US', 'fr-FR' (default: '') for all properties!
     * offset  		: default 0
     * limit        : default select all
     * order		: property to order by
     * orderdir		: direction of order (default: 'ASC')
     * 
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array propertyFilters
     * @param  array options
     * @return array
     */
    public function searchInstances($propertyFilters = array(), $options = array())
    {
        return (array) $this->getImplementation()->searchInstances($this, $propertyFilters, $options);
    }

    /**
     * Short description of method countInstances
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array propertyFilters
     * @param  array options
     * @return Integer
     */
    public function countInstances($propertyFilters = array(), $options = array())
    {
		return $this->getImplementation()->countInstances($this, $propertyFilters, $options);
    }

    /**
     * Get instances' property values.
     * The instances can be filtered.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Property property
     * @param  array propertyFilters
     * @param  array options
     * @return array
     */
    public function getInstancesPropertyValues( core_kernel_classes_Property $property, $propertyFilters = array(), $options = array())
    {
        return (array) $this->getImplementation()->getInstancesPropertyValues($this, $property, $propertyFilters, $options);
    }

    /**
     * Unset the domain of the property related to the class
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Property property
     * @deprecated
     */
    public function unsetProperty( core_kernel_classes_Property $property)
    {
        $this->getImplementation()->unsetProperty($this, $property);
    }

    /**
     * please use core_kernel_classes_ResourceFactory::create()
     * instead of this function whenever possible
     *
     * Creates a new instance using the properties provided.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array properties May contain additional types
     * @return core_kernel_classes_Resource
     * @see core_kernel_classes_ResourceFactory
     */
    public function createInstanceWithProperties($properties)
    {
        $returnValue = null;

        // remove the additional types, because they might be implemented differently
        
        $additonalTypes = array();
        if (isset($properties[RDF_TYPE])) {
        	$types = is_array($properties[RDF_TYPE]) ? $properties[RDF_TYPE] : array($properties[RDF_TYPE]);
        	foreach ($types as $type) {
        		$uri = is_object($type) ? $type->getUri() : $type;
        		if ($uri != $this->getUri()) {
        			$additonalTypes[] = new core_kernel_classes_Class($uri);
        		}
        	}
        	unset($properties[RDF_TYPE]);
        }
        // create the instance
        $returnValue = $this->getImplementation()->createInstanceWithProperties($this, $properties);
        
        foreach ($additonalTypes as $type) {
        	$returnValue->setType($type);
        }
        PermissionManager::getPermissionModel()->onResourceCreated($returnValue);

        return $returnValue;
    }

    /**
     * Delete instances of a Class from the database.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array resources An array of core_kernel_classes_Resource or URIs.
     * @param  boolean deleteReference If set to true, references about the resources will also be deleted from the database.
     * @return boolean
     */
    public function deleteInstances($resources, $deleteReference = false)
    {
        return (bool) $this->getImplementation()->deleteInstances($this, $resources, $deleteReference);
    }

    /**
     * Short description of method delete
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  boolean deleteReference
     * @return boolean
     */
    public function delete($deleteReference = false)
    {
        return (bool) $this->getImplementation()->delete($this, $deleteReference);
    }

    /**
     * States if the Class exists or not in persistent memory. The rule is
     * if the Class has parent classes, it exists. It works even for the
     * class because it inherits itself.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return boolean
     */
    public function exists()
    {
        // If the Class has one or more direct parent classes (this rdfs:isSubClassOf C),
        // we know that the class exists. 
        return (bool) (count($this->getParentClasses(false)) > 0);
    }

}