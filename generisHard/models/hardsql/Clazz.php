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

namespace oat\generisHard\models\hardsql;

use oat\generisHard\models\hardapi\ResourceReferencer;
use oat\generisHard\models\switcher\Switcher;
use oat\generisHard\models\hardapi\TableManager;
use oat\generisHard\models\hardapi\Utils as HardapiUtils;
use oat\generisHard\models\hardapi\Exception as HardapiException;

/**
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package generisHard
 
 */
class Clazz
    extends Resource
        implements \core_kernel_persistence_ClassInterface
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
    public function getSubClasses( \core_kernel_classes_Class $resource, $recursive = false)
    {
        $returnValue = array();

        
		if(class_exists('\core_kernel_persistence_smoothsql_Class')){
			//the model is not hardened and remains in the soft table:
			$returnValue = \core_kernel_persistence_smoothsql_Class::singleton()->getSubClasses($resource, $recursive);
		}else{
			throw new \core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
		}
        

        return (array) $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::isSubClassOf()
     */
    public function isSubClassOf( \core_kernel_classes_Class $resource,  \core_kernel_classes_Class $parentClass)
    {
        $returnValue = (bool) false;

        
		if(class_exists('\core_kernel_persistence_smoothsql_Class')){
			//the model is not hardened and remains in the soft table:
			$returnValue = \core_kernel_persistence_smoothsql_Class::singleton()->isSubClassOf($resource, $parentClass);
		}else{
			throw new \core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
		}
        

        return (bool) $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::getParentClasses()
     */
    public function getParentClasses( \core_kernel_classes_Class $resource, $recursive = false)
    {
        $returnValue = array();

        
		throw new \core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        

        return (array) $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::getProperties()
     */
    public function getProperties( \core_kernel_classes_Class $resource, $recursive = false)
    {
        $returnValue = array();

        
		throw new \core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        

        return (array) $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::getInstances()
     */
    public function getInstances( \core_kernel_classes_Class $resource, $recursive = false, $params = array())
    {
        $returnValue = array();

        

		$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
		$classLocations = ResourceReferencer::singleton()->classLocations($resource);

		if(isset($params['limit'])){
			$offset = 0;
			$limit = intval($params['limit']);
			if ($limit==0){
				$limit = 1000000;
			}
			if(isset($params['offset'])){
				$offset = intval($params['offset']);
			}
		}

		foreach ($classLocations as $classLocation){

			$tableName = $classLocation['table'];
			$sqlQuery = 'SELECT "uri" FROM "'.$tableName.'"';
			if(isset($params['limit'])){
				$offset = 0;
				$limit = intval($params['limit']);
				if ($limit==0){
					$limit = 1000000;
				}
				if(isset($params['offset'])){
					$offset = intval($params['offset']);
				}
				
				$sqlQuery  = $dbWrapper->limitStatement($sqlQuery, $limit, $offset);
			}

			try{
				$sqlResult = $dbWrapper->query($sqlQuery);
				while ($row = $sqlResult->fetch()){
	
					$instance = new \core_kernel_classes_Resource($row['uri']);
					$returnValue[$instance->getUri()] = $instance ;
				}
				if($recursive == true){
						
					$subClasses = $resource->getSubClasses(true);
					foreach ($subClasses as $subClass){
	
						if (isset($limit)){
	
							$limit = $limit - count($returnValue);
							if ($limit > 0){
								$returnValue = array_merge($returnValue, $subClass->getInstances(true, array('limit'=>$limit)));
							} else {
								break 2;
							}
						}else{
							$returnValue = array_merge($returnValue, $subClass->getInstances(true));
						}
					}
				}
			}
			catch (\PDOException $e){
				throw new Exception("Unable to get instances for the resource {$resource->getUri()} in the table {$tableName} : " .$e->getMessage());
			}
		}
		
        

        return (array) $returnValue;
    }


    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::setSubClassOf()
     */
    public function setSubClassOf( \core_kernel_classes_Class $resource,  \core_kernel_classes_Class $iClass)
    {
        $returnValue = (bool) false;
    
        
        throw new \core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        
    
        return (bool) $returnValue;
    }




    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::createInstance()
     */
    public function createInstance( \core_kernel_classes_Class $resource, $label = '', $comment = '', $uri = '')
    {
        $returnValue = null;

        

		$dbWrapper = \core_kernel_classes_DbWrapper::singleton();

		$subject = '';
		if($uri == ''){
			$subject = \common_Utils::getNewUri();
		}else if($uri[0]=='#'){ //$uri should start with # and be well formed
			$modelUri = \common_ext_NamespaceManager::singleton()->getLocalNamespace()->getUri();
			$subject = rtrim($modelUri, '#') . $uri;
		}else{
			$subject = $uri;
		}

		$returnValue = new \core_kernel_classes_Resource($subject,__METHOD__);

		try{
			$table = '_'.HardapiUtils::getShortName ($resource);
			$query = 'INSERT INTO "'.$table.'" ("uri") VALUES (?)';
			$result = $dbWrapper->exec($query, array($subject));

			// reference the newly created instance
			ResourceReferencer::singleton()->referenceResource($returnValue, $table, array($resource), true);

			if (!empty($label)){
				$returnValue->setPropertyValue(new \core_kernel_classes_Property(RDFS_LABEL), $label);
			}
			if (!empty($comment)){
				$returnValue->setPropertyValue(new \core_kernel_classes_Property(RDFS_COMMENT), $comment);
			}
		}
		catch (\PDOException $e){
			throw new Exception("Unable to create instance for the resource {$resource->getUri()} in the table {$table} : " .$e->getMessage());
		}

        

        return $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::createSubClass()
     */
    public function createSubClass( \core_kernel_classes_Class $resource, $label = '', $comment = '', $uri = '')
    {
    	// Meta-model is still in smooth mode...
		$newClass = \core_kernel_persistence_smoothsql_Class::singleton()->createSubClass($resource, $label, $comment, $uri);
		
		if (empty($newClass)){
			$classUri = $resource->getUri();
			$msg = "An error occured while creating a sub-class of '${classUri}'.";
			throw new HardapiException($msg);
		}
		
		// Simply hardify the class to keep consistency in the class hierarchy.
		// We want to avoid class hierarchies with multiple persistence implementations.
		$switcher = new Switcher();
		if (!$switcher->hardify($newClass)){
			$classUri = $resource->getUri();
			$msg = "An error occured while hardifying class '${classUri}'.";
			throw new HardapiException($msg);
		}
		
		return $newClass;
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::createProperty()
     */
    public function createProperty( \core_kernel_classes_Class $resource, $label = '', $comment = '', $isLgDependent = false)
    {
        $returnValue = null;

        
        // First we reference the property in smooth mode because meta models always remain there.
		$smoothReturnValue = \core_kernel_persistence_smoothsql_Class::singleton()->createProperty($resource, $label, $comment, $isLgDependent);
		
		if ($smoothReturnValue){
			$property = new \core_kernel_classes_Property($resource->getUri());
			$column = array('name' => HardapiUtils::getShortName($smoothReturnValue));
			$column['multi'] = ($isLgDependent) ? true : false; // If no range set, we assume it is a literal.
			$column['foreign'] = false; // We do not know the range yet.
			
			$referencer = ResourceReferencer::singleton();
			$classLocations = $referencer->classLocations($resource);
			
			foreach ($classLocations as $loc){
				$tblmgr = new TableManager($loc['table']);
				$tblmgr->addColumn($column);
			}
			
			$returnValue = $smoothReturnValue;
			
			$referencer->clearCaches();
		}
		else{
			$uri = $resource->getUri();
			throw new Exception("An error occured when creating property in smooth mode '${uri}' before handling the hard sql aspect.");
		}
		
        

        return $returnValue;
    }
    
    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::searchInstances()
     */
    public function searchInstances( \core_kernel_classes_Class $resource, $propertyFilters = array(), $options = array())
    {
        $returnValue = array();

        
    	$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
		
		// 'like' option.
		$like = true;
		if (isset($options['like'])){
			$like = ($options['like'] == true);
		}
		
		// 'chaining' option.
		$chaining = 'and';
		if (!empty($options['chaining'])){
			$chainingValue = strtolower($options['chaining']);
			if ($chainingValue == 'and' || $chainingValue == 'or'){
				$chaining = $chainingValue;
			}
		}
		
		// 'recursive' option.
		$recursive = 0;
		if (isset($options['recursive'])){
			$recursive = intval($options['recursive']);
		}
		
		// 'limit' option. If not provided, we wet it to null as well.
		$limit = null;
		if (isset($options['limit'])){
			$limit = intval($options['limit']);
		}
		
		// 'offset' option. If not provided, we set it to 0.
		$offset = 0;
		if (isset($options['offset'])){
			$offset = intval($options['offset']);
			if (empty($limit)) {
				// offset requires a limit
				$limit = 1000000;
			}
		}
		
		// 'order' and 'orderdir' options.
		$order = null;
		$orderdir = 'ASC';
		if (!empty($options['order'])){
			$order = $options['order'];
			if (!empty($options['orderdir'])){
				$orderdirValue = strtolower($options['orderdir']);
				
				if ($orderdirValue == 'asc' || $orderdirValue == 'desc'){
					$orderdir = $orderdirValue;
				}
			}
		}
		
		// 'lang' option.
		$lang = '';
		if (!empty($options['lang'])){
			$lang = $options['lang'];
		}
		
		// 'additionalClasses' option.
		$classes = array($resource);
		if (!empty($options['additionalClasses'])){
			$classes = array_merge($classes, $options['additionalClasses']);
		}
		
		$queries = array();
		foreach ($classes as $class){
			$query = '';
			
			// Get information about where and how to query the database.
	    	$baseTableName = '';
			$referencer = ResourceReferencer::singleton();
			$classLocations = $referencer->classLocations($class);
			if (isset($classLocations[0])){
				// table to query located.
				$baseTableName = $classLocations[0]['table'];
				$propsTableName = $baseTableName . 'props';
				$baseConditions = array(); 		// Conditions to apply on base table.
				$propsConditions = array(); 	// Conditions to apply on properties table.
				
				
				foreach ($propertyFilters as $propUri => $pattern){
					$property = new \core_kernel_classes_Property($propUri);
					$propertyLocations = $referencer->propertyLocation($property);
					$patterns = (!is_array($pattern)) ? array($pattern) : $pattern;
					
					if (in_array($baseTableName, $propertyLocations)){
						// The property to search is stored in the base table.
						$propShortName = HardapiUtils::getShortName($property);
						$subConditions = array();
						foreach ($patterns as $pattern){
							$searchPattern = \core_kernel_persistence_smoothsql_Utils::buildSearchPattern($pattern, $like);
							$subConditions[] = '"b"."' . $propShortName . '" ' . $searchPattern;
						}
						$baseConditions[] = '(' . implode(' OR ', $subConditions) . ')';
					}
					else {
						// The property to search is stored in the properties table
						// or might be simply unknown.
						$pUri = $dbWrapper->quote($propUri);
						$condition = '("p"."property_uri" = ' . $pUri . ' AND (';
						$subConditions = array();
						foreach ($patterns as $pattern){
							$searchPattern = \core_kernel_persistence_smoothsql_Utils::buildSearchPattern($pattern, $like);
							$subCondition = 'COALESCE("p"."property_value", "p"."property_foreign_uri") ' . $searchPattern;
							
							// Deal with the language.
							if(preg_match('/^[a-zA-Z]{2,4}$/', $lang)){
								$quotedLang = $dbWrapper->quote($lang);
								$langToken = ' AND ("p"."l_language" = \'\' OR "p"."l_language" = ' . $quotedLang . ')';
								$subCondition .= $langToken;
							}
							
							$subConditions[] = $subCondition;
						}
						$condition .= implode(' OR ', $subConditions) . '))';
						$propsConditions[] = $condition;
					}
					
					// else do nothing, the property is not referenced anywhere.
				}
				
				$quotedTblName = $dbWrapper->quote($baseTableName);
				$query .= 'SELECT "b"."id", "b"."uri", ' . $quotedTblName . ' AS "tblname" FROM "' . $baseTableName . '" "b" ';
				
				if (empty($propsConditions) && !empty($baseConditions)){
					// Scenario with only scalar values.
					$query .= 'WHERE ';
					$finalCondition = array();
					foreach ($baseConditions as $bC){
						$finalCondition[] = '(' . $bC . ')';
					}
					$query .= implode(' ' . strtoupper($chaining) . ' ', $finalCondition);
				}
				else if (!empty($propsConditions)){
					// Mixed approach scenario.
					if ($chaining == 'and'){
						for ($i = 0; $i < count($propsConditions); $i++){
							$joinName = 'bp' . $i;
							$query .= 'INNER JOIN(';
							$query .= 	'SELECT "b"."id", "b"."uri" FROM "' . $baseTableName . '" "b" ';
							$query .= 	'INNER JOIN "' . $propsTableName . '" "p" ON ("b"."id" = "p"."instance_id" AND (';
							$query .=	$propsConditions[$i];
							$query .=   '))';
							$query .= ') AS "' . $joinName . '" ON ("b"."id" = "'. $joinName . '"."id")';
						}
						
						if (!empty($baseConditions)){
							$query .= ' WHERE ';
							$query .= implode(' AND ', $baseConditions);
						}
					}
					else{
						$query .= 'INNER JOIN(';
						$query .= 	'SELECT "b"."id", "b"."uri" FROM "' . $baseTableName . '" "b" ';
						$query .= 	'INNER JOIN "' . $propsTableName . '" "p" ON ("b"."id" = "p"."instance_id" AND(';
						$query .=	implode(' OR ', array_merge($propsConditions, $baseConditions));
						$query .= 	'))';
						$query .= ') AS "bp" ON ("b"."id" = "bp"."id")';
					}
				}
				
				$queries[] = $query;
			}	
		}
		
		if (!empty($queries)){
			
			$finalQuery = implode(' UNION ', $queries);
			//TODO this work with mysql pgsql but not with MSSQL, I remove it and see no issue
			//$finalQuery .= ' GROUP BY "b"."id", "b"."uri", "tblname"';
			$finalQuery .= ' GROUP BY "b"."id", "b"."uri"';
			if (!empty($limit) || !empty($offset)){
				
				$finalQuery = $dbWrapper->limitStatement($finalQuery, $limit,$offset);
			}
			
			try{
				$result = $dbWrapper->query($finalQuery);
				$idUris = array();
				while ($row = $result->fetch()){
					if (empty($idUris[$row['tblname']])){
						$idUris[$row['tblname']] = array();
					}
					
					$idUris[$row['tblname']][$row['id']] = $row['uri'];
				}
				// Order if needed.
				if (!empty($order) && !empty($idUris)){
					
					$queries = array();
					foreach ($idUris as $tblname => $value){
						$ids = array_keys($value);
						$ids = implode(', ', $ids);
						$orderProp = new \core_kernel_classes_Property($order);
						$propertyLocation = $referencer->propertyLocation($orderProp);
						$orderPropShortName = HardapiUtils::getShortName($orderProp);
						$propstblname = $tblname . 'props';
						
						$sqlQuery  = 'SELECT "id", "uri", "property_value" FROM (';
						$sqlQuery .= 'SELECT "b"."id", "b"."uri", "p"."property_uri" AS "property_uri", COALESCE("p"."property_value", "p"."property_foreign_uri") AS "property_value" FROM "' . $tblname . '" "b" ';
						$sqlQuery .= 'INNER JOIN "' . $propstblname . '" "p" ';
						
						// Deal with the language.
						$langToken = '';
						if(preg_match('/^[a-zA-Z]{2,4}$/', $lang)){
							$quotedLang = $dbWrapper->quote($lang);
							$langToken = ' AND ("p".l_language" = \'\' OR "p".l_language" = ' . $quotedLang . ')';
						}
						
						$sqlQuery .= 'ON ("b"."id" IN(' . $ids . ') AND "b"."id" = "p"."instance_id"' . $langToken . ') ';

						if (in_array($baseTableName, $propertyLocation)){
							$sqlQuery .= 'UNION ';
							
							$sqlQuery .= 'SELECT "b"."id", "b"."uri", \'' . $order . '\' AS "property_uri", "b"."' . $orderPropShortName. '" AS "property_value" ';
							$sqlQuery .= 'FROM "' . $tblname . '" "b" ';
							$sqlQuery .= 'JOIN "' . $propstblname . '" "p" ';
							$sqlQuery .= 'ON ("b"."id" IN(' . $ids . ') AND "b"."id" = "p"."instance_id") ';
						}
						
						$sqlQuery .= ') AS "s"';
						
						$queries[] = $sqlQuery;
					}
					
					$finalQuery = implode(' UNION ', $queries);
					$finalQuery = 'SELECT "uri", "property_value" FROM (' . $finalQuery;
					$finalQuery .= ') AS "search" GROUP BY "uri", "property_value" ORDER by "property_value"';
					$idUris = array();
					$sqlResult = $dbWrapper->query($finalQuery);
					while ($row = $sqlResult->fetch()){
						$instance = new \core_kernel_classes_Resource($row['uri']);
						$returnValue[$instance->getUri()] = $instance;
					}
				}
				else{
					foreach ($idUris as $tblname => $value){
						foreach ($value as $uri){
							$instance = new \core_kernel_classes_Resource($uri);
							$returnValue[$instance->getUri()] = $instance;
						}
					}
				}
			}
			catch (\PDOException $e){
				throw new Exception("Unable to search instances for the resource {$resource->getUri()} : " .$e->getMessage());
			}
		}
        

        return (array) $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::countInstances()
     */
    public function countInstances( \core_kernel_classes_Class $resource, $propertyFilters = array(), $options = array())
    {
        $returnValue = null;

        

		$returnValue = 0;
		$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
		$classLocations = ResourceReferencer::singleton()->classLocations($resource);
		foreach ($classLocations as $classLocation){

			$tableName = $classLocation['table'];
			$sqlQuery = 'SELECT count(*) AS "count" FROM "'.$tableName.'"';
				
			$sqlResult = $dbWrapper->query($sqlQuery);
			if ($row = $sqlResult->fetch()){
				$returnValue += $row['count'];
				$sqlResult->closeCursor();
			}
		}

        

        return $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::getInstancesPropertyValues()
     */
    public function getInstancesPropertyValues( \core_kernel_classes_Class $resource,  \core_kernel_classes_Property $property, $propertyFilters = array(), $options = array())
    {
        $returnValue = array();

        
        
        //throw new \core_kernel_persistence_ProhibitedFunctionException("The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
	        
    	$distinct = isset($options['distinct']) ? $options['distinct'] : false;
    	$recursive = isset($options['recursive']) ? $options['recursive'] : false;
        $dbWrapper = \core_kernel_classes_DbWrapper::singleton();
		$referencer = ResourceReferencer::singleton();
        $uris = '';
        $searchInstancesOptions = array (
        	'recursive' 	=> $recursive
        );
        
        // Search all instances for the property filters paramter
        if(!empty($propertyFilters)){
        	$instances = $resource->searchInstances($propertyFilters, $searchInstancesOptions);
        }else{
        	$instances = $resource->getInstances($recursive);
        }
        
        if ($instances){
        	$tables = array();
	        foreach ($instances as $instance){
	        	$uris .= '\''.$instance->getUri().'\',';
	        	array_push($tables, ResourceReferencer::singleton()->resourceLocation($instance));
	        } 
	        $tables = array_unique($tables);
	        $uris = substr($uris, 0, strlen($uris)-1);
	        
	        // Get all the available property values in the subset of instances
	        
	        $table = $tables[0];
			if(empty($table)){
				return $returnValue;
			}
			$propertyAlias = HardapiUtils::getShortName($property);
			$propertyLocation = $referencer->propertyLocation($property);
	
			// Select in the properties table of the class
			if (in_array("{$table}props", $propertyLocation)
			|| ! $referencer->isPropertyReferenced($property)){
				
				$tableProps = $table."props";
				$session = \core_kernel_classes_Session::singleton();
				// Define language if required
				$lang = '';
				$defaultLg = '';
				if (isset($options['lg'])){
					$lang = $options['lg'];
				}
				else{
					$lang = $session->getDataLanguage();
					$defaultLg = ' OR "l_language" = \''.DEFAULT_LANG.'\' ';
				}
	            
				$query = 'SELECT '.$distinct.' "property_value", "property_foreign_uri"
					FROM "'.$table.'"
					INNER JOIN "'.$tableProps.'" on "'.$table.'"."id" = "'.$tableProps.'"."instance_id"
				   	WHERE "'.$table.'"."uri" IN ('.$uris.')
					AND "'.$tableProps.'"."property_uri" = ?
					AND ( "l_language" = ? OR "l_language" = \'\' '.$defaultLg.')';

				try{
					$result	= $dbWrapper->query($query, array($property->getUri(), $lang));
	
					while ($row = $result->fetch()){
						$returnValue[] = $row['property_value'] != null ? $row['property_value'] : $row['property_foreign_uri'];
					}
				}
				catch (\PDOException $e){
					throw new Exception("Unable to get property (multiple) values for {$resource->getUri()} in {$table} : " .$e->getMessage());
				}
				
			}
			// Select in the main table of the class
			else{
				try{
					$query =  'SELECT '.($distinct?'DISTINCT':'').' "'.$propertyAlias.'" as "propertyValue" FROM "'.$table.'" WHERE "uri" IN ('.$uris.')';
					$result	= $dbWrapper->query($query);
					
					while ($row = $result->fetch()){
						if ($row['propertyValue'] != null){
							$returnValue[] = $row['propertyValue'];
						}
					}
				}
				catch (\PDOException $e){
					if ($e->getCode() == $dbWrapper->getColumnNotFoundErrorCode()) {
						// Column doesn't exists is not an error. Try to get a property which does not exist is allowed
					}
					else if ($e->getCode() !== '00000'){
						throw new Exception("Unable to get property (single) values for {$resource->getUri()} in {$table} : " .$e->getMessage());
					}
				}
			}
        }
        
        

        return (array) $returnValue;
    }


    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::createInstanceWithProperties()
     */
    public function createInstanceWithProperties( \core_kernel_classes_Class $type, $properties)
    {
        $returnValue = null;

        
        if (isset($properties[RDF_TYPE])) {
        	throw new \core_kernel_persistence_Exception('Additional types in createInstanceWithProperties not permited');
        }
        
        $uri = \common_Utils::getNewUri();
        $table = '_'.HardapiUtils::getShortName ($type);
        
        // prepare properties
        $hardPropertyNames = array("uri" => $uri);
		$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
        
        if (is_array($properties)) {
			if (count($properties) > 0) {

				// Get the table name
				$referencer = ResourceReferencer::singleton();
				
				$session = \core_kernel_classes_Session::singleton();

				$queryProps = array();

				foreach ($properties as $propertyUri => $value) {

					$property = new \core_kernel_classes_Property($propertyUri);
					$propertyLocation = $referencer->propertyLocation($property);

					if (in_array("{$table}props", $propertyLocation)
						|| !$referencer->isPropertyReferenced($property)) {

						$propertyRange = $property->getRange();
						$lang = ($property->isLgDependent() ? $session->getDataLanguage() : '');
						$formatedValues = array();
						if ($value instanceof \core_kernel_classes_Resource) {
							$formatedValues[] = $dbWrapper->quote($value->getUri());
						} else if (is_array($value)) {
							foreach ($value as $val) {
								if ($val instanceof \core_kernel_classes_Resource) {
									$formatedValues[] = $dbWrapper->quote($val->getUri());
								} else {
									$formatedValues[] = $dbWrapper->quote($val);
								}
							}
						} else {
							$formatedValues[] = $dbWrapper->quote($value);
						}

						if (is_null($propertyRange) || $propertyRange->getUri() == RDFS_LITERAL) {
							
							foreach ($formatedValues as $formatedValue) {
								$queryProps[] = "'{$property->getUri()}', {$formatedValue}, null, '{$lang}'";
							}
						} else {
							foreach ($formatedValues as $formatedValue) {
								$queryProps[] = "'{$property->getUri()}', null, {$formatedValue}, '{$lang}'";
							}
						}
					} else {

						$propertyName = HardapiUtils::getShortName($property);
						if ($value instanceof \core_kernel_classes_Resource) {
							$value = $value->getUri();
						} else if (is_array($value)) {
							throw new Exception("try setting multivalue for the non multiple property {$property->getLabel()} ({$property->getUri()})");
						} else {
							$value = $value; // no need to quote, passed to prepared statement
						}

						$hardPropertyNames[$propertyName] = $value;
					}
				}
			}
		}
        
        // spawn
        $returnValue = new \core_kernel_classes_Resource($uri,__METHOD__);
		$varnames = '"'.implode('","', array_keys($hardPropertyNames)).'"';
		
		try{
			$query = 'INSERT INTO "'.$table.'" ('.$varnames.') VALUES ('.implode(',', array_fill(0, count($hardPropertyNames), '?')).')';
			$result = $dbWrapper->exec($query, array_values($hardPropertyNames));
			
			// reference the newly created instance
			ResourceReferencer::singleton()->referenceResource($returnValue, $table, array($type), true);
			
			// @todo this shoould be retrievable without an aditional query
			$instanceId = Utils::getInstanceId($returnValue);
			
			// @todo Merge into a single query
			if (!empty($queryProps)) {
				$prefixed = array();
				foreach ($queryProps as $row) {
					$prefixed[] = ' ('.$instanceId.', '.$row.')';
				}
				
				try{
					$query = 'INSERT INTO "' . $table . 'props" ("instance_id", "property_uri", "property_value", "property_foreign_uri", "l_language") VALUES ' . implode(',', $prefixed);
					$result = $dbWrapper->exec($query);
				}
				catch (\PDOException $e){
					throw new Exception("Unable to set properties (multiple) Value for the instance {$returnValue->getUri()} in {$tableName} : " . $e->getMessage());
				}
			}
		}
		catch (\PDOException $e){
			throw new Exception("Unable to create instance for the class {$type->getUri()} in the table {$table} : " .$e->getMessage());
		}
        

        return $returnValue;
    }

    /**
     * (non-PHPdoc)
     * @see core_kernel_persistence_ClassInterface::deleteInstances()
     */
    public function deleteInstances( \core_kernel_classes_Class $resource, $resources, $deleteReference = false)
    {
        $returnValue = (bool) false;

        
        foreach ($resources as $r){
        	$resource = ($r instanceof \core_kernel_classes_Resource) ? $r : new \core_kernel_classes_Resource($r);
        	$resource->delete($deleteReference);
        }
        
        $returnValue = true;
        

        return (bool) $returnValue;
    }

    /**
     * Overlaod resource::delete to remove refrence
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @param  boolean deleteReference
     * @return boolean
     */
    public function delete( \core_kernel_classes_Resource $resource, $deleteReference = false)
    {
        $returnValue = (bool) false;
    
        
        $switcher = new Switcher();
        $success = $switcher->unhardify(new \core_kernel_classes_Class($resource));
    
        if (true == $success){
            $returnValue = \core_kernel_persistence_smoothsql_Class::singleton()->delete($resource, $deleteReference);
        }
        else{
            $returnValue = false;
        }
        
    
        return (bool) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return \core_kernel_classes_Resource
     */
    public static function singleton()
    {
        $returnValue = null;

        

		if (self::$instance == null){
			self::$instance = new self();
		}
		$returnValue = self::$instance;

        

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
    public function isValidContext( \core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        

		if (ResourceReferencer::singleton()->isClassReferenced ($resource)){
			$returnValue = true;
		}

        

        return (bool) $returnValue;
    }

}