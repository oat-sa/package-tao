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
 * @package generis
 
 */
class core_kernel_persistence_smoothsql_Class
    extends core_kernel_persistence_smoothsql_Resource
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
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::getSubClasses()
     */
    public function getSubClasses( core_kernel_classes_Class $resource, $recursive = false)
    {
        $returnValue = array();

        

		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$sqlQuery = 'SELECT subject FROM statements WHERE predicate = ? and '.$dbWrapper->getPlatForm()->getObjectTypeCondition() .' = ?';
		$sqlResult = $dbWrapper->query($sqlQuery, array(RDFS_SUBCLASSOF, $resource->getUri()));
		
		while ($row = $sqlResult->fetch()){
			$subClass = new core_kernel_classes_Class($row['subject']);
			$returnValue[$subClass->getUri()] = $subClass;
			if($recursive == true ){
				$plop = $subClass->getSubClasses(true);
				$returnValue = array_merge($returnValue, $plop);
			}
		}

        

        return (array) $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::isSubClassOf()
     */
    public function isSubClassOf( core_kernel_classes_Class $resource,  core_kernel_classes_Class $parentClass)
    {
        $returnValue = (bool) false;

        

		$dbWrapper = core_kernel_classes_DbWrapper::singleton();

		$query = 'SELECT object FROM statements
					WHERE subject = ?
					AND predicate = ? AND ' . $dbWrapper->getPlatForm()->getObjectTypeCondition() . ' = ?';
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

        

        return (bool) $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::getParentClasses()
     */
    public function getParentClasses( core_kernel_classes_Class $resource, $recursive = false)
    {
        $returnValue = array();

        
        $returnValue =  array();
		
        $sqlQuery = 'SELECT object FROM statements
        			WHERE subject = ? 
        			AND predicate = ?';

		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$sqlResult = $dbWrapper->query($sqlQuery, array($resource->getUri(), RDFS_SUBCLASSOF));

		while ($row = $sqlResult->fetch()){

			$parentClass = new core_kernel_classes_Class($row['object']);

			$returnValue[$parentClass->getUri()] = $parentClass ;
			if($recursive == true && $parentClass->getUri() != RDFS_CLASS && $parentClass->getUri() != RDFS_RESOURCE){
				if($parentClass->getUri() == CLASS_GENERIS_RESOURCE){
					$returnValue[RDFS_RESOURCE] = new core_kernel_classes_Class(RDFS_RESOURCE);
				}
				else {
    			    $plop = $parentClass->getParentClasses(true);
    				$returnValue = array_merge($returnValue, $plop);
				}
			}
		}

        

        return (array) $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::getProperties()
     */
    public function getProperties( core_kernel_classes_Class $resource, $recursive = false)
    {
        $returnValue = array();

        
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$sqlQuery = 'SELECT subject FROM statements
			WHERE predicate = ? 
			AND '. $dbWrapper->getPlatForm()->getObjectTypeCondition() .' = ?';
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

        

        return (array) $returnValue;
    }

   
    /* (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::getInstances()
     */
    public function getInstances( core_kernel_classes_Class $resource, $recursive = false, $params = array())
    {
        $returnValue = array();

        
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $sqlQuery = 'SELECT subject FROM statements 
						WHERE predicate = ?  
							AND ' . $dbWrapper->getPlatForm()->getObjectTypeCondition(). ' = ? ';
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

        

        return (array) $returnValue;
    }

    /**
     * 
     * @author lionel
     * @param core_kernel_classes_Class $resource
     * @param core_kernel_classes_Resource $instance
     * @return core_kernel_classes_Resource
     * @deprecated
     */
    public function setInstance( core_kernel_classes_Class $resource,  core_kernel_classes_Resource $instance)
    {
        $returnValue = null;

       throw new common_exception_DeprecatedApiMethod(__METHOD__ . ' is deprecated. ');
		$rdfType = new core_kernel_classes_Property(RDF_TYPE);
		$newInstance = clone $instance;	//call Resource::__clone
		$newInstance->setPropertyValue($rdfType, $resource->getUri());

		$returnValue = $newInstance;

        

        return $returnValue;
    }

    /**
     * Short description of method setSubClassOf
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  core_kernel_classes_Class resource
     * @param  Class iClass
     * @return boolean
     * 
     */
    public function setSubClassOf( core_kernel_classes_Class $resource,  core_kernel_classes_Class $iClass)
    {
        $returnValue = (bool) false;

        
		$subClassOf = new core_kernel_classes_Property(RDFS_SUBCLASSOF);
		$returnValue = $resource->setPropertyValue($subClassOf, $iClass->getUri());

        

        return (bool) $returnValue;
    }

    /**
     * Short description of method setProperty
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  core_kernel_classes_Class resource
     * @param  Property property
     * @return boolean
     * @deprecated
     * 
     */
    public function setProperty( core_kernel_classes_Class $resource,  core_kernel_classes_Property $property)
    {
        $returnValue = (bool) false;

        
        throw new common_exception_DeprecatedApiMethod(__METHOD__ . ' is deprecated. ');
		$domain = new core_kernel_classes_Property(RDFS_DOMAIN, __METHOD__);
		$instanceProperty = new core_kernel_classes_Resource($property->getUri(), __METHOD__);
		$returnValue = $instanceProperty->setPropertyValue($domain, $resource->getUri());

        

        return (bool) $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::createInstance()
     */
    public function createInstance( core_kernel_classes_Class $resource, $label = '', $comment = '', $uri = '')
    {
        $returnValue = null;

        
        
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
		else {
			common_Logger::e('already had type '. $resource);
		}

		if (!empty($label)) {
			$returnValue->setLabel($label);
		}
		if (!empty($comment)) {
			$returnValue->setComment($comment);
		}
		
        

        return $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::createSubClass()
     */
    public function createSubClass( core_kernel_classes_Class $resource, $label = '', $comment = '', $uri = '')
    {
        if (!empty($uri)) {
            common_Logger::w('Use of parameter uri in '.__METHOD__.' is deprecated');
        }
        $uri = empty($uri) ? common_Utils::getNewUri() : $uri;
        $returnValue = new core_kernel_classes_Class($uri, __METHOD__);
        $properties = array(
            RDFS_SUBCLASSOF => $resource,
        );
        if (!empty($label)) {
            $properties[RDFS_LABEL] = $label;
        }
        if (!empty($comment)) {
            $properties[RDFS_COMMENT] = $comment;
        }
            
        $returnValue->setPropertiesValues($properties);
        return $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::createProperty()
     */
    public function createProperty( core_kernel_classes_Class $resource, $label = '', $comment = '', $isLgDependent = false)
    {
        $returnValue = null;

        
        
    	$property = new core_kernel_classes_Class(RDF_PROPERTY, __METHOD__);
		$propertyInstance = $property->createInstance($label,$comment);
		$returnValue = new core_kernel_classes_Property($propertyInstance->getUri(), __METHOD__);
		$returnValue->setLgDependent($isLgDependent);

		if (!$returnValue->setDomain($resource)){
			throw new common_Exception('problem creating property');
		}
        
        

        return $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::searchInstances()
     */
    public function searchInstances( core_kernel_classes_Class $resource, $propertyFilters = array(), $options = array())
    {
        $returnValue = array();

        

		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$query = $this->getFilteredQuery($resource, $propertyFilters, $options);
		
		$result = $dbWrapper->query($query);

		while ($row = $result->fetch()){	
			$foundInstancesUri = $row['subject'];
			$returnValue[$foundInstancesUri] = new core_kernel_classes_Resource($foundInstancesUri);
		}

        

        return (array) $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::countInstances()
     */
    public function countInstances( core_kernel_classes_Class $resource, $propertyFilters = array(), $options = array())
    {
        $returnValue = null;

        

		$dbWrapper = core_kernel_classes_DbWrapper::singleton();

		if (isset($propertyFilters) && count($propertyFilters)) {
			if (isset($options['offset'])) {
                unset($options['offset']);
            }
			if (isset($options['limit'])) {
                unset($options['limit']);
            }
			$query = $this->getFilteredQuery($resource, $propertyFilters, $options);
			if (substr($query, 0, strlen('SELECT subject')) == 'SELECT subject') {
				$query = 'SELECT count(*) as count'.substr($query, strlen('SELECT subject'));
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
			$sqlQuery = 'SELECT count(subject) as count FROM statements
							WHERE predicate = ?  
								AND '.$dbWrapper->getPlatForm()->getObjectTypeCondition(). ' = ? ';
			
			$sqlResult = $dbWrapper->query($sqlQuery, array(
				RDF_TYPE,
				$resource->getUri()
			));

			if ($row = $sqlResult->fetch()) {
				$returnValue = $row['count'];
				$sqlResult->closeCursor();
			}
		}

        
        

        return $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::getInstancesPropertyValues()
     */
    public function getInstancesPropertyValues( core_kernel_classes_Class $resource,  core_kernel_classes_Property $property, $propertyFilters = array(), $options = array())
    {
        $returnValue = array();
        
    	$distinct = isset($options['distinct']) ? $options['distinct'] : false;
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
	
	
	$filteredQuery = $this->getFilteredQuery($resource, $propertyFilters, $options);
	
	//echo $filteredQuery;
	// Get all the available property values in the subset of instances
	$query = 'SELECT';
	if($distinct){
		$query .= ' DISTINCT';
	}
	$query .= ' object FROM statements
		WHERE predicate = ?
		AND subject IN ('.$filteredQuery.')';
	$sqlResult = $dbWrapper->query($query, array($property->getUri()));
	while ($row = $sqlResult->fetch()){
		$returnValue[] = common_Utils::toResource($row['object']);
	}
        
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
     * @deprecated
     */
    public function unsetProperty( core_kernel_classes_Class $resource,  core_kernel_classes_Property $property)
    {
        $returnValue = (bool) false;

        
        
        throw new common_exception_DeprecatedApiMethod(__METHOD__ . ' is deprecated. ');
        

        return (bool) $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::createInstanceWithProperties()
     */
    public function createInstanceWithProperties( core_kernel_classes_Class $type, $properties)
    {
        $returnValue = null;

        
        if (isset($properties[RDF_TYPE])) {
        	throw new core_kernel_persistence_Exception('Additional types in createInstanceWithProperties not permited');
        }
        
        $properties[RDF_TYPE] = $type;
		$returnValue = new core_kernel_classes_Resource(common_Utils::getNewUri(), __METHOD__);
		$returnValue->setPropertiesValues($properties);
        

        return $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::deleteInstances()
     */
    public function deleteInstances( core_kernel_classes_Class $resource, $resources, $deleteReference = false)
    {
        $returnValue = (bool) false;

        
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $class = new core_kernel_classes_Class($resource->getUri());
        $uris = array();
        
        foreach ($resources as $r){
        	$uri = (($r instanceof core_kernel_classes_Resource) ? $r->getUri() : $r);

        	$uris[] = $dbWrapper->quote($uri);
        }
        
        if ($class->exists()){
        	
        	$inValues = implode(',', $uris);
        	$query = 'DELETE FROM statements WHERE subject IN (' . $inValues . ')';
        	
        	if (true === $deleteReference){
        		$params[] = $resource->getUri();
        		$query .= ' OR object IN (' . $inValues . ')';
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
       
        if (core_kernel_persistence_smoothsql_Class::$instance == null){
        	core_kernel_persistence_smoothsql_Class::$instance = new core_kernel_persistence_smoothsql_Class();
        }
        return  core_kernel_persistence_smoothsql_Class::$instance;

    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_smoothsql_Resource::isValidContext()
     */
    public function isValidContext( core_kernel_classes_Resource $resource)
    {
        return true;
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
    public function getFilteredQuery( core_kernel_classes_Class $resource, $propertyFilters = array(), $options = array())
    {
        $returnValue = (string) '';

        
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$platform = $dbWrapper->getPlatForm();
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
				$langToken = ' AND ('. $platform->isNullCondition('l_language') . ' OR l_language = '.$options['lang'].')';
			}
		}
		$like = true;
		if(isset($options['like'])){
			$like = ($options['like'] === true);
		}
		
		    $query = 'SELECT subject FROM statements WHERE ';
		    $conditions = array();
		    foreach($propertyFilters as $propUri => $pattern){

			    $propUri = trim($dbWrapper->quote($propUri));
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
							    $sub[] .= $platform->getObjectTypeCondition() . ' LIKE '.$dbWrapper->quote($object);
						    }
						    else {
							    $sub[] = (strpos($object, '%') !== false)
								    ? $platform->getObjectTypeCondition() . ' LIKE '.$dbWrapper->quote($object)
								    : $platform->getObjectTypeCondition() . ' = '.$dbWrapper->quote($value);
						    }
					    break;

					    case 'object' :
						    if($value instanceof core_kernel_classes_Resource) {
							    $sub[] = $platform->getObjectTypeCondition(). ' = '.$dbWrapper->quote($value->getUri());
						    } else {
							    common_Logger::w('non ressource as search parameter: '.get_class($value), 'GENERIS');
						    }
					    break;

					    default:
						    throw new common_Exception("Unsupported type for searchinstance array: ".gettype($value));

				    }
			    }
			    if (empty($sub)) {
				    $conditions[] = "(predicate = {$propUri}{$langToken})";
			    } else {
				    $conditions[] = "(predicate = {$propUri} AND (".implode(" OR ", $sub)."){$langToken})";
			    }
		    }
		
		$intersect = true;
		if (isset($options['chaining']) && $options['chaining'] == 'or') {
			$intersect = false;
		}


		
		$q = '';
		if ($intersect) {
			foreach ($conditions as $condition) {
				if (!strlen($q)) {
				    $q = $query . $condition;
				}
				else {
                    $q = $query . $condition . ' AND subject IN (' . $q . ')';
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
		
	
		if (count ( $propertyFilters ) > 0) {
			$query .= ' subject IN (SELECT subject FROM statements WHERE predicate = ' . $dbWrapper->quote(RDF_TYPE) . ' AND '. $platform->getObjectTypeCondition(). ' in (\'' . implode ( '\',\'', $rdftypes ) . '\'))';
		} else {
			$query = 'SELECT subject FROM statements WHERE predicate = ' . $dbWrapper->quote(RDF_TYPE) . ' AND '. $platform->getObjectTypeCondition().' in (\'' . implode ( '\',\'', $rdftypes ) . '\')';
		}
		// sorting
		if (isset ( $options ['order'] ) && ! empty ( $options ['order'] )) {
			$orderUri = $options ['order'];
			$orderDir = isset ( $options ['orderdir'] ) && strtoupper ( $options ['orderdir'] ) == 'DESC' ? 'DESC' : 'ASC';
			$orderQuery = 'SELECT subject,object FROM statements WHERE predicate = ' . $dbWrapper->quote($orderUri) ;
			$query = 'SELECT DISTINCT mainq.subject, orderq.object from (' . $query . ') AS mainq
			          LEFT JOIN (' . $orderQuery . ') AS orderq ON (mainq.subject = orderq.subject)
			          ORDER BY orderq.object ' . $orderDir;
		} else if (isset ( $options ['limit'] )) {
			$query .= ' ORDER BY id';
		}

		if (isset ( $options ['limit'] )) {
			$offset = 0;
			$limit = intval( $options ['limit'] );
			if ($limit == 0) {
				$limit = 1000000;
			}
			if (isset( $options ['offset'] )) {
				$offset = intval( $options ['offset'] );
			}
				
			$returnValue = $dbWrapper->limitStatement($query, $limit, $offset );
		}
		else {
			$returnValue = $query ;
		}
		

        

        return (string) $returnValue;
    }

} /* end of class core_kernel_persistence_smoothsql_Class */

?>
