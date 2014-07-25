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
 * Short description of class core_kernel_persistence_smoothsql_Class
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_smoothsql
 */
class core_kernel_persistence_smoothsql_Class
    extends core_kernel_persistence_PersistenceImpl
        implements core_kernel_persistence_ClassInterface
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

		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$sqlQuery = 'SELECT "subject" FROM "statements" WHERE "predicate" = ? and "object" = ?';
		$sqlResult = $dbWrapper->query($sqlQuery, array(RDFS_SUBCLASSOF, $resource->getUri()));
		
		while ($row = $sqlResult->fetch()){
			$subClass = new core_kernel_classes_Class($row['subject']);
			$returnValue[$subClass->getUri()] = $subClass;
			if($recursive == true ){
				$plop = $subClass->getSubClasses(true);
				$returnValue = array_merge($returnValue, $plop);
			}
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

		$dbWrapper = core_kernel_classes_DbWrapper::singleton();

		$query = 'SELECT "object" FROM "statements"
					WHERE "subject" = ?
					AND "predicate" = ? AND "object" = ?';
		$result = $dbWrapper->query($query, array(
			$resource->getUri(),
			RDFS_SUBCLASSOF,
			$parentClass->getUri()
		));
		while($row = $result->fetch()){
			
			$returnValue =  true;
			break;
		}
		if(!$returnValue){
			$parentSubClasses = $parentClass->getSubClasses(true);
			foreach ($parentSubClasses as $subClass){
				if ($subClass->getUri() == $resource->getUri()) {
					$returnValue =  true;
					break;
				}
			}
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
        $returnValue =  array();
		
        $sqlQuery = 'SELECT "object" FROM "statements" 
        			WHERE "subject" = ? 
        			AND ("predicate" = ? OR "predicate" = ?)';

		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$sqlResult = $dbWrapper->query($sqlQuery, array($resource->getUri(), RDFS_SUBCLASSOF, RDF_TYPE));

		while ($row = $sqlResult->fetch()){

			$parentClass = new core_kernel_classes_Class($row['object']);

			$returnValue[$parentClass->getUri()] = $parentClass ;
			if($recursive == true && $parentClass->getUri() != RDFS_CLASS && $parentClass->getUri() != RDFS_RESOURCE){
				$plop = $parentClass->getParentClasses(true);
				$returnValue = array_merge($returnValue, $plop);
			}
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
		$sqlQuery = 'SELECT "subject" FROM "statements"
			WHERE "predicate" = ? 
			AND "object" = ?';
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$sqlResult = $dbWrapper->query($sqlQuery, array(
			RDFS_DOMAIN,
			$resource->getUri()
		));

		while ($row = $sqlResult->fetch()){
			$property = new core_kernel_classes_Property($row['subject']);
			$returnValue[$property->getUri()] = $property;
		}
		if($recursive == true) {
			$parentClasses = $resource->getParentClasses(true);
			foreach ($parentClasses as $parent) {
				if($parent->getUri() != RDFS_CLASS) {
					$returnValue = array_merge($returnValue, $parent->getProperties(true));
				}
			}
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
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $sqlQuery = 'SELECT "subject" FROM "statements" 
						WHERE "predicate" = ?  
							AND "object" = ? ';
		if(isset($params['limit'])){
			$limit = intval($params['limit']);
			$offset = isset($params['offset']) ? intval($params['offset']) : 0;
			if ($limit == 0) {
				throw new common_exception_InvalidArgumentType('Invalid limit in '.__FUNCTION__.': '.$params['limit']);
			}
			$sqlQuery = $dbWrapper->limitStatement($sqlQuery, $limit, $offset);
		}
		
		
		$sqlResult = $dbWrapper->query($sqlQuery, array (
			RDF_TYPE,
			$resource->getUri()
		));

		while ($row = $sqlResult->fetch()){

			$instance = new core_kernel_classes_Resource($row['subject']);
			$returnValue[$instance->getUri()] = $instance;

			//In case of a meta class, subclasses of instances may be returned*/
			if (($instance->getUri() != RDFS_CLASS)
			&& ($resource->getUri() == RDFS_CLASS)
			&& ($instance->getUri() != RDFS_RESOURCE)) {

				$instanceClass = new core_kernel_classes_Class($instance->getUri());
				$subClasses = $instanceClass->getSubClasses(true);

				foreach($subClasses as $subClass) {
					$returnValue[$subClass->getUri()] = $subClass;
				}
			}
		}
		
		if($recursive == true){
			$subClasses = $resource->getSubClasses(true);
			foreach ($subClasses as $subClass){
				$returnValue = array_merge($returnValue, $subClass->getInstances(false));
			}
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
		$rdfType = new core_kernel_classes_Property(RDF_TYPE);
		$newInstance = clone $instance;	//call Resource::__clone
		$newInstance->setPropertyValue($rdfType, $resource->getUri());

		$returnValue = $newInstance;

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

		$subClassOf = new core_kernel_classes_Property(RDFS_SUBCLASSOF);
		$returnValue = $resource->setPropertyValue($subClassOf, $iClass->getUri());

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

		$domain = new core_kernel_classes_Property(RDFS_DOMAIN, __METHOD__);
		$instanceProperty = new core_kernel_classes_Resource($property->getUri(), __METHOD__);
		$returnValue = $instanceProperty->setPropertyValue($domain, $resource->getUri());

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
        
    	$subject = '';
    	if ($uri == ''){
			$subject = common_Utils::getNewUri();
		}
		else if ( $uri[0]=='#'){ //$uri should start with # and be well formed
				$modelUri = common_ext_NamespaceManager::singleton()->getLocalNamespace()->getUri();
				$subject = rtrim($modelUri, '#') . $uri;
		}
		else{
				$subject = $uri;
		}

		$returnValue = new core_kernel_classes_Resource($subject, __METHOD__);
		if (!$returnValue->hasType($resource)){
			$returnValue->setType($resource);
		}

		if (!empty($label)) {
			$returnValue->setLabel($label);
		}
		if (!empty($comment)) {
			$returnValue->setComment($comment);
		}
		
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
        $returnValue = null;

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F32 begin
        
        $class = new core_kernel_classes_Class(RDFS_CLASS, __METHOD__);
		$intance = $class->createInstance($label, $comment, $uri);
		$returnValue = new core_kernel_classes_Class($intance->getUri());
		$returnValue->setSubClassOf($resource);
        
        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F32 end

        return $returnValue;
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
        
    	$property = new core_kernel_classes_Class(RDF_PROPERTY, __METHOD__);
		$propertyInstance = $property->createInstance($label,$comment);
		$returnValue = new core_kernel_classes_Property($propertyInstance->getUri(), __METHOD__);
		$returnValue->setLgDependent($isLgDependent);

		if (!$resource->setProperty($returnValue)){
			throw new common_Exception('problem creating property');
		}
        
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

		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$query = $this->getFilteredQuery($resource, $propertyFilters, $options);
		
		$result = $dbWrapper->query($query);

		while ($row = $result->fetch()){	
			$foundInstancesUri = $row['subject'];
			$returnValue[$foundInstancesUri] = new core_kernel_classes_Resource($foundInstancesUri);
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

		$dbWrapper = core_kernel_classes_DbWrapper::singleton();

		if (isset($propertyFilters) && count($propertyFilters)) {
			if (isset($options['offset'])) {
                unset($options['offset']);
            }
			if (isset($options['limit'])) {
                unset($options['limit']);
            }
			$query = $this->getFilteredQuery($resource, $propertyFilters, $options);
			if (substr($query, 0, strlen('SELECT "subject"')) == 'SELECT "subject"') {
				$query = 'SELECT count(*) as count'.substr($query, strlen('SELECT "subject"'));
				$sqlResult = $dbWrapper->query($query);
				if ($row = $sqlResult->fetch()) {
					$returnValue = $row['count'];
					$sqlResult->closeCursor();
				}
			} else {
				common_Logger::w('getFilteredQuery was updated, please update countInstances as well');
				$sqlResult = $dbWrapper->query($query);
				$returnValue = count($sqlResult->fetchAll());
			}
		}
		else {
			$sqlQuery = 'SELECT count("subject") as count FROM "statements"
							WHERE "predicate" = ?  
								AND "object" = ? ';
			
			$sqlResult = $dbWrapper->query($sqlQuery, array(
				RDF_TYPE,
				$resource->getUri()
			));

			if ($row = $sqlResult->fetch()) {
				$returnValue = $row['count'];
				$sqlResult->closeCursor();
			}
		}

        
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
    	$distinct = isset($options['distinct']) ? $options['distinct'] : false;
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
	
	
	$filteredQuery = $this->getFilteredQuery($resource, $propertyFilters, $options);
	
	//echo $filteredQuery;
	// Get all the available property values in the subset of instances
	$query = 'SELECT';
	if($distinct){
		$query .= ' DISTINCT';
	}
	$query .= ' "object" FROM "statements"
		WHERE "predicate" = ?
		AND "subject" IN ('.$filteredQuery.')';
	$sqlResult = $dbWrapper->query($query, array($property->getUri()));
	while ($row = $sqlResult->fetch()){
	    if(!common_Utils::isUri($row['object'])) {
	    $returnValue[] = new core_kernel_classes_Literal($row['object']);
	    }
	    else {
		$returnValue[] = new core_kernel_classes_Resource($row['object']);
	    }
	}
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
        
		$domain = new core_kernel_classes_Property(RDFS_DOMAIN, __METHOD__);
		$instanceProperty = new core_kernel_classes_Resource($property->getUri(), __METHOD__);
		$returnValue = $instanceProperty->removePropertyValues($domain, array('pattern' => $resource->getUri()));
        
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
        if (isset($properties[RDF_TYPE])) {
        	throw new core_kernel_persistence_Exception('Additional types in createInstanceWithProperties not permited');
        }
        
        $properties[RDF_TYPE] = $type;
		$returnValue = new core_kernel_classes_Resource(common_Utils::getNewUri(), __METHOD__);
		$returnValue->setPropertiesValues($properties);
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
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $class = new core_kernel_classes_Class($resource->getUri());
        $uris = array();
        
        foreach ($resources as $r){
        	$uri = (($r instanceof core_kernel_classes_Resource) ? $r->getUri() : $r);
        	$uris[] = $dbWrapper->dbConnector->quote($uri);
        }
        
        if ($class->exists()){
        	
        	$inValues = implode(',', $uris);
        	$query = 'DELETE FROM "statements" WHERE "subject" IN (' . $inValues . ')';
        	
        	if (true === $deleteReference){
        		$params[] = $resource->getUri();
        		$query .= ' OR "object" IN (' . $inValues . ')';
        	}
        	
        	try{
        		// Even if now rows are affected, we consider the resources
        		// as deleted.
        		$dbWrapper->exec($query);	
        		$returnValue = true;
        	}
        	catch (PDOException $e){
        		throw new core_kernel_persistence_smoothsql_Exception("An error occured while deleting resources: " . $e->getMessage());
        	}
        }
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
        $returnValue = core_kernel_persistence_smoothsql_Resource::singleton()->delete($resource, $deleteReference);
        // section 10-13-1-85--2c835591:13bffd6ae29:-8000:0000000000001E78 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return core_kernel_classes_Resource
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001493 begin
        
        if (core_kernel_persistence_smoothsql_Class::$instance == null){
        	core_kernel_persistence_smoothsql_Class::$instance = new core_kernel_persistence_smoothsql_Class();
        }
        $returnValue = core_kernel_persistence_smoothsql_Class::$instance;
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001493 end

        return $returnValue;
    }

    /**
     * Short description of method isValidContext
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function isValidContext( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F4E begin
        
        $returnValue = true;
        
        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F4E end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getFilteredQuery
     *
     * @access public
     * @author Jehan Bihin
     * @param  Resource resource
     * @param  array propertyFilters
     * @param  array options
     * @return string
     * @version 1.0
     */
    public function getFilteredQuery( core_kernel_classes_Resource $resource, $propertyFilters = array(), $options = array())
    {
        $returnValue = (string) '';

        // section 127-0-1-1--1bdaa580:13412f85251:-8000:00000000000017CC begin"
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		
		//add the type check to the filters
		/*if (isset($propertyFilters[RDF_TYPE])) {
			if (!is_array($propertyFilters[RDF_TYPE])) $propertyFilters[RDF_TYPE] = array($propertyFilters[RDF_TYPE], $resource->getUri());
			else $propertyFilters[RDF_TYPE] = array_merge($propertyFilters[RDF_TYPE], array($resource->getUri()));
		}
		else $propertyFilters[RDF_TYPE] = $resource->getUri();*/

		$rdftypes = array();
        //If recursive, get the subclasses of the given class
		if (isset($options['recursive']) && $options['recursive']) {
            foreach($this->getSubClasses($resource, $options['recursive']) as $subClass){
                $rdftypes[] = $subClass->getUri();
            }
		}
        //If additionalClasses are required
        if(isset($options['additionalClasses'])){
        	foreach ($options['additionalClasses'] as $aC){
        		$rdftypes[] = ($aC instanceof core_kernel_classes_Resource) ? $aC->getUri() : $aC;
        		$rdftypes = array_unique($rdftypes);
        	}
        }
        //Add the class type of the given class
        if(!in_array($resource->getUri(), $rdftypes)){
            $rdftypes[] = $resource->getUri();
        }
           
		$langToken = '';
		if(isset($options['lang'])){
			if(preg_match('/^[a-zA-Z]{2,4}$/', $options['lang'])){
				$langToken = ' AND ("l_language" = \'\' OR "l_language" = \''.$options['lang'].'\')';
			}
		}
		$like = true;
		if(isset($options['like'])){
			$like = ($options['like'] === true);
		}
		
		    $query = 'SELECT "subject" FROM "statements" WHERE ';
		    $conditions = array();
		    foreach($propertyFilters as $propUri => $pattern){

			    $propUri = trim($dbWrapper->dbConnector->quote($propUri));
			    $values = is_array($pattern) ? $pattern : array($pattern);
			    $sub = array();
			    foreach ($values as $value) {
				    switch (gettype($value)) {
					    case 'string' :
					    case 'numeric':
						    $object = trim(str_replace('*', '%', $value));

						    if($like){
							    if(!preg_match("/^%/", $object)){
								    $object = "%".$object;
							    }
							    if(!preg_match("/%$/", $object)){
								    $object = $object."%";
							    }
							    $sub[] .= '"object" LIKE '.$dbWrapper->dbConnector->quote($object);
						    }
						    else {
							    $sub[] = (strpos($object, '%') !== false)
								    ? '"object" LIKE '.$dbWrapper->dbConnector->quote($object)
								    : '"object" = '.$dbWrapper->dbConnector->quote($value);
						    }
					    break;

					    case 'object' :
						    if($value instanceof core_kernel_classes_Resource) {
							    $sub[] = '"object" = \''.$value->getUri().'\'';
						    } else {
							    common_Logger::w('non ressource as search parameter: '.get_class($value), 'GENERIS');
						    }
					    break;

					    default:
						    throw new common_Exception("Unsupported type for searchinstance array: ".gettype($value));

				    }
			    }
			    if (empty($sub)) {
				    $conditions[] = "(\"predicate\" = {$propUri}{$langToken})";
			    } else {
				    $conditions[] = "(\"predicate\" = {$propUri} AND (".implode(" OR ", $sub)."){$langToken})";
			    }
		    }
		
		$intersect = true;
		if (isset($options['chaining']) && $options['chaining'] == 'or') {
			$intersect = false;
		}

		$queryLimit = "";
                if(isset($options['limit'])){
                    $offset = 0;
                    $limit = intval($options['limit']);
                    if ($limit==0){
                            $limit = 1000000;
                    }
                    if(isset($options['offset'])){
                            $offset = intval($options['offset']);
                    }
                    $queryLimit = $dbWrapper->limitStatement($queryLimit, $limit, $offset);
		}
		
		$q = '';
		if ($intersect) {
			foreach ($conditions as $condition) {
				if (!strlen($q)) {
				    $q = $query . $condition;
				}
				else {
                    $q = $query . $condition . ' AND "subject" IN (' . $q . ')';
                }
			}
                        if(!empty($q)){
                            $query = $q;
                        }
		}
		else {
            $query .= join(' OR ', $conditions);
        }

		if(!empty($conditions)){
			$query .= ' AND';
		}
		
	
	if (count($propertyFilters) > 0){
	$query .= ' "subject" IN (SELECT "subject" FROM "statements" WHERE "predicate" = \''.RDF_TYPE.'\' AND "object" in (\''.implode('\',\'', $rdftypes).'\'))';
	} else {
	    $query = 'SELECT "subject" FROM "statements" WHERE "predicate" = \''.RDF_TYPE.'\' AND "object" in (\''.implode('\',\'', $rdftypes).'\')';
	}
		// sorting
        if (isset($options['order']) && !empty($options['order'])) {
        	$orderUri = $options['order'];
        	$orderDir = isset($options['orderdir']) && strtoupper($options['orderdir']) == 'DESC' ? 'DESC' : 'ASC';
        	$orderQuery = 'SELECT "subject","object" FROM "statements" WHERE "predicate" = \''.$orderUri.'\'';
			$query = 'SELECT DISTINCT "mainq"."subject", "orderq"."object" from ('.$query.') AS mainq
			          LEFT JOIN ('.$orderQuery.') AS orderq ON ("mainq"."subject" = "orderq"."subject")
			          ORDER BY "orderq"."object" '.$orderDir;
        } else if (isset($options['limit'])) {
        	$query .= ' ORDER BY "id"';
        }
         
        $returnValue = $query . $queryLimit;
        // section 127-0-1-1--1bdaa580:13412f85251:-8000:00000000000017CC end

        return (string) $returnValue;
    }

} /* end of class core_kernel_persistence_smoothsql_Class */

?>
