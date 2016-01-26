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
 *               2012-2014 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

/**
 * Short description of class core_kernel_persistence_smoothsql_Class
 *
 */
class core_kernel_persistence_smoothsql_Class extends core_kernel_persistence_smoothsql_Resource implements core_kernel_persistence_ClassInterface
{

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::getSubClasses()
     */
    public function getSubClasses( core_kernel_classes_Class $resource, $recursive = false)
    {
        $returnValue = array();
        
        $sqlQuery = 'SELECT subject FROM statements WHERE predicate = ? and '.$this->getPersistence()->getPlatForm()->getObjectTypeCondition() .' = ?';
        $sqlResult = $this->getPersistence()->query($sqlQuery, array(RDFS_SUBCLASSOF, $resource->getUri()));
        
        while ($row = $sqlResult->fetch()) {
            $subClass = new core_kernel_classes_Class($row['subject']);
            $returnValue[$subClass->getUri()] = $subClass;
            if ($recursive == true) {
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
        $returnValue = false;
        
        $query = 'SELECT object FROM statements WHERE subject = ? AND predicate = ? AND ' . $this->getPersistence()->getPlatForm()->getObjectTypeCondition() . ' = ?';
        $result = $this->getPersistence()->query($query, array(
            $resource->getUri(),
            RDFS_SUBCLASSOF,
            $parentClass->getUri()
        ));
        
        while ($row = $result->fetch()) {
            $returnValue =  true;
            break;
        }
        
        if (!$returnValue) {
            $parentSubClasses = $parentClass->getSubClasses(true);
            foreach ($parentSubClasses as $subClass) {
                if ($subClass->getUri() == $resource->getUri()) {
                    $returnValue =  true;
                    break;
                }
            }
        }
        
        return $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::getParentClasses()
     */
    public function getParentClasses( core_kernel_classes_Class $resource, $recursive = false)
    {
        $returnValue = array();
		
        $sqlQuery = 'SELECT object FROM statements WHERE subject = ?  AND predicate = ?';

		$sqlResult = $this->getPersistence()->query($sqlQuery, array($resource->getUri(), RDFS_SUBCLASSOF));

		while ($row = $sqlResult->fetch()){

            $parentClass = new core_kernel_classes_Class($row['object']);
            
            $returnValue[$parentClass->getUri()] = $parentClass ;
            if ($recursive == true && $parentClass->getUri() != RDFS_CLASS && $parentClass->getUri() != RDFS_RESOURCE) {
                if ($parentClass->getUri() == CLASS_GENERIS_RESOURCE) {
                    $returnValue[RDFS_RESOURCE] = new core_kernel_classes_Class(RDFS_RESOURCE);
                } else {
                    $plop = $parentClass->getParentClasses(true);
                	$returnValue = array_merge($returnValue, $plop);
                }
            }
		}

        return $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::getProperties()
     */
    public function getProperties( core_kernel_classes_Class $resource, $recursive = false)
    {
        $returnValue = array();
        
        $sqlQuery = 'SELECT subject FROM statements WHERE predicate = ?  AND '. $this->getPersistence()->getPlatForm()->getObjectTypeCondition() .' = ?';
        $sqlResult = $this->getPersistence()->query($sqlQuery, array(
            RDFS_DOMAIN,
            $resource->getUri()
        ));
        
        while ($row = $sqlResult->fetch()) {
            $property = new core_kernel_classes_Property($row['subject']);
            $returnValue[$property->getUri()] = $property;
        }
        
        if ($recursive == true) {
            $parentClasses = $this->getParentClasses($resource, true);
            foreach ($parentClasses as $parent) {
                if($parent->getUri() != RDFS_CLASS) {
                	$returnValue = array_merge($returnValue, $parent->getProperties(false));
                }
            }
        }
        
        return $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::getInstances()
     */
    public function getInstances(core_kernel_classes_Class $resource, $recursive = false, $params = array())
    {
        $returnValue = array();
        
        $params = array_merge($params, array('like' => false, 'recursive' => $recursive));
        
        $query = $this->getFilteredQuery($resource, array(), $params);
        $result = $this->getPersistence()->query($query);
        
        while ($row = $result->fetch()) {
            $foundInstancesUri = $row['subject'];
            $returnValue[$foundInstancesUri] = new core_kernel_classes_Resource($foundInstancesUri);
        }
        
        return $returnValue;
    }

    /**
     * 
     * @param core_kernel_classes_Class $resource
     * @param core_kernel_classes_Resource $instance
     * @throws common_exception_DeprecatedApiMethod
     * @return core_kernel_classes_Resource
     * @deprecated
     */
    public function setInstance( core_kernel_classes_Class $resource,  core_kernel_classes_Resource $instance)
    {
        throw new common_exception_DeprecatedApiMethod(__METHOD__ . ' is deprecated. ');
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
		$returnValue = $this->setPropertyValue($resource, $subClassOf, $iClass->getUri());

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
        throw new common_exception_DeprecatedApiMethod(__METHOD__ . ' is deprecated. ');
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
    public function searchInstances(core_kernel_classes_Class $resource, $propertyFilters = array(), $options = array())
    {
        $returnValue = array();
        
        // Avoid a 'like' search on RDF_TYPE!
        if (count($propertyFilters) === 0) {
            $options = array_merge($options, array('like' => false));
        }
        
        $query = $this->getFilteredQuery($resource, $propertyFilters, $options);
        $result = $this->getPersistence()->query($query);
        
        while ($row = $result->fetch()) {	
            $foundInstancesUri = $row['subject'];
            $returnValue[$foundInstancesUri] = new core_kernel_classes_Resource($foundInstancesUri);
        }
        
        return $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::countInstances()
     */
    public function countInstances( core_kernel_classes_Class $resource, $propertyFilters = array(), $options = array())
    {
		if (isset($options['offset'])) {
            unset($options['offset']);
        }
        
		if (isset($options['limit'])) {
            unset($options['limit']);
        }
        
        if (isset($options['order'])) {
            unset($options['order']);
        }
        
		$query = 'SELECT count(subject) FROM (' . $this->getFilteredQuery($resource, $propertyFilters, $options) . ') as countq';
		return $this->getPersistence()->query($query)->fetchColumn();
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::getInstancesPropertyValues()
     */
    public function getInstancesPropertyValues( core_kernel_classes_Class $resource,  core_kernel_classes_Property $property, $propertyFilters = array(), $options = array())
    {
        $returnValue = array();
        
        $distinct = isset($options['distinct']) ? $options['distinct'] : false;
        
        if (count($propertyFilters) === 0) {
            $options = array_merge($options, array('like' => false));
        }
        
        $filteredQuery = $this->getFilteredQuery($resource, $propertyFilters, $options);
        
        // Get all the available property values in the subset of instances
        $query = 'SELECT';
        if ($distinct) {
            $query .= ' DISTINCT';
        }
        
        $query .= " object FROM (SELECT overq.subject, valuesq.object FROM (${filteredQuery}) as overq JOIN statements AS valuesq ON (overq.subject = valuesq.subject AND valuesq.predicate = ?)) AS overrootq";
        
        $sqlResult = $this->getPersistence()->query($query, array($property->getUri()));
        while ($row = $sqlResult->fetch()) {
            $returnValue[] = common_Utils::toResource($row['object']);
        }
        
        return (array) $returnValue;
    }

    /**
     * Remove a Property from its Class definition.
     * 
     * @param core_kernel_classes_Class $resource
     * @param core_kernel_classes_Property $property
     * @deprecated
     * @throws common_exception_DeprecatedApiMethod
     */
    public function unsetProperty( core_kernel_classes_Class $resource,  core_kernel_classes_Property $property)
    {
        throw new common_exception_DeprecatedApiMethod(__METHOD__ . ' is deprecated. ');
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
        $returnValue = false;

        $class = new core_kernel_classes_Class($resource->getUri());
        $uris = array();
        
        foreach ($resources as $r) {
            $uri = (($r instanceof core_kernel_classes_Resource) ? $r->getUri() : $r);
            $uris[] = $this->getPersistence()->quote($uri);
        }
        
        if ($class->exists()) {
            $inValues = implode(',', $uris);
            $query = 'DELETE FROM statements WHERE subject IN (' . $inValues . ')';
        	
            if (true === $deleteReference) {
                $params[] = $resource->getUri();
                $query .= ' OR object IN (' . $inValues . ')';
            }
        	
            try {
        		// Even if now rows are affected, we consider the resources
        		// as deleted.
        		$this->getPersistence()->exec($query);	
        		$returnValue = true;
            } catch (PDOException $e) {
        	    throw new core_kernel_persistence_smoothsql_Exception("An error occured while deleting resources: " . $e->getMessage());
            }
        }

        return $returnValue;
    }

    /**
     * 
     * @param core_kernel_classes_Class $resource
     * @param array $propertyFilters
     * @param array $options
     * @return string
     */
    public function getFilteredQuery(core_kernel_classes_Class $resource, $propertyFilters = array(), $options = array())
    {
        $rdftypes = array();
        
        // Check recursivity...
		if (isset($options['recursive']) && $options['recursive']) {
            foreach($this->getSubClasses($resource, $options['recursive']) as $subClass){
                $rdftypes[] = $subClass->getUri();
            }
		}
	
		// Check additional classes...
        if (isset($options['additionalClasses'])) {
        	foreach ($options['additionalClasses'] as $aC) {
        		$rdftypes[] = ($aC instanceof core_kernel_classes_Resource) ? $aC->getUri() : $aC;
        		$rdftypes = array_unique($rdftypes);
        	}
        }
        
        // Add the class type of the given class
        if (!in_array($resource->getUri(), $rdftypes)) {
            $rdftypes[] = $resource->getUri();
        }
        
        $and = (isset($options['chaining']) === false) ? true : ((strtolower($options['chaining']) === 'and') ? true : false);
        $like = (isset($options['like']) === false) ? true : $options['like'];
        $lang = (isset($options['lang']) === false) ? '' : $options['lang'];
        $offset = (isset($options['offset']) === false) ? 0 : $options['offset'];
        $limit = (isset($options['limit']) === false) ? 0 : $options['limit'];
        $order = (isset($options['order']) === false) ? '' : $options['order'];
        $orderdir = (isset($options['orderdir']) === false) ? 'ASC' : $options['orderdir'];
           
        $query = core_kernel_persistence_smoothsql_Utils::buildFilterQuery($this->getModel(), $rdftypes, $propertyFilters, $and, $like, $lang, $offset, $limit, $order, $orderdir);
        
        return $query;
    }
}
