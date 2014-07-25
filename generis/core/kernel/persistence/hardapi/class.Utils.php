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
 * Utility class that provides transversal methods 
 * to manage  the hard api
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_hardapi
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_persistence_Switcher
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('core/kernel/persistence/class.Switcher.php');

/* user defined includes */
// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:0000000000001596-includes begin
// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:0000000000001596-includes end

/* user defined constants */
// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:0000000000001596-constants begin
// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:0000000000001596-constants end

/**
 * Utility class that provides transversal methods 
 * to manage  the hard api
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_hardapi
 */
class core_kernel_persistence_hardapi_Utils
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute namespaceIds
     *
     * @access private
     * @var array
     */
    private static $namespaceIds = array();

    /**
     * Short description of attribute shortNames
     *
     * @access private
     * @var array
     */
    private static $shortNames = array();

    // --- OPERATIONS ---

    /**
     * Get the namespace identifier of an URI,
     * using the modelID/baseUri mapping
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string namespaceUri
     * @return string
     */
    private static function getNamespaceId($namespaceUri)
    {
        $returnValue = (string) '';

        // section 127-0-1-1--5a63b0fb:12f72879be9:-8000:000000000000159A begin
        
		if(count(self::$namespaceIds) == 0){
			$namespaces = common_ext_NamespaceManager::singleton()->getAllNamespaces();
			foreach($namespaces as $namespace){
				if( ((int)$namespace->getModelId()) < 10){
					self::$namespaceIds[$namespace->getUri()] = '0' . $namespace->getModelId();
				}
				else{
					self::$namespaceIds[$namespace->getUri()] = (string)$namespace->getModelId();
				}
			}
		}
		if(isset(self::$namespaceIds[$namespaceUri])){
			$returnValue = self::$namespaceIds[$namespaceUri];
		}
        
        // section 127-0-1-1--5a63b0fb:12f72879be9:-8000:000000000000159A end

        return (string) $returnValue;
    }

    /**
     * Get the shortname of a resource.
     * It helps you for the tables and columns names
     * that cannot be longer than 64 characters
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @return string
     */
    public static function getShortName( core_kernel_classes_Resource $resource)
    {
        $returnValue = (string) '';

        // section 127-0-1-1--5a63b0fb:12f72879be9:-8000:000000000000159D begin
    	if(!is_null($resource)){
    		
    		if (isset(self::$shortNames[$resource->getUri()])){
    			$returnValue = self::$shortNames[$resource->getUri()];
    		} else {
    			$namespace = substr($resource->getUri(), 0, strpos($resource->getUri(), '#')+1);
    			$namespaceId = self::getNamespaceId($namespace);
    			if (empty($namespaceId)) {
    				throw new common_exception_UnknownNamespace($namespace);
    			}
				$returnValue = $namespaceId . substr($resource->getUri(), strlen($namespace));
				self::$shortNames[$resource->getUri()] = $returnValue;
    		}
		}
        
        // section 127-0-1-1--5a63b0fb:12f72879be9:-8000:000000000000159D end

        return (string) $returnValue;
    }

    /**
     * Get the long name (full URI) by providing a short name.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string shortName
     * @return string
     */
    public static function getLongName($shortName)
    {
        $returnValue = (string) '';

        // section 127-0-1-1--151fe597:12f7c91b993:-8000:00000000000014C7 begin
        
        if (!empty($shortName) && strlen($shortName)>2){
        	$shortName = preg_replace("/^_/", '', $shortName);
        	$modelID = intval (substr($shortName, 0, 2));
        	
        	if ($modelID != null && $modelID >0){
	        	$nsUri = common_ext_NamespaceManager::singleton()->getNamespace ($modelID);
	         	$returnValue = $nsUri . substr($shortName, 2);
        	}
        }
        
        // section 127-0-1-1--151fe597:12f7c91b993:-8000:00000000000014C7 end

        return (string) $returnValue;
    }

    /**
     * Get a simple description about a Property in the database. 
     *
     * This method returns an associative array where the keys have the
     * meaning:
     * + 'name' (string) is the short name of the Property.
     * + 'isMultiple' (bool) is set to true if the Property accepts multiple
     * + 'isLgDependant' (bool) is set to true if the Property values depend on
     * language.
     * + 'range' (array) contains a collection classes corresponding to the
     * range.
     *
     * If hardRangeClassOnly is set to true, only classes that are hardified
     * be returned in 'range'.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Property property A Property in the database.
     * @param  boolean hardRangeClassOnly Get only ranges that are in Hard SQL Mode.
     * @return array
     */
    public static function propertyDescriptor( core_kernel_classes_Property $property, $hardRangeClassOnly = false)
    {
        $returnValue = array();

        // section 10-13-1--128-743691ae:12fc0ed9381:-8000:0000000000001525 begin
        
		$returnValue = array(
			'name'			=> core_kernel_persistence_hardapi_Utils::getShortName($property),
			'isMultiple' 	=> $property->isMultiple(),
			'isLgDependent'	=> $property->isLgDependent(),
			'range'			=> array()
		);
		
		$range = $property->getRange();
		
		
		if($hardRangeClassOnly){
			$is_class_referenced = core_kernel_persistence_hardapi_ResourceReferencer::singleton()->isClassReferenced($range);
			if($range->getUri()!=RDFS_LITERAL && $is_class_referenced){
				$classLocations = core_kernel_persistence_hardapi_ResourceReferencer::singleton()->classLocations($range);
				
				if(isset($classLocations[0])){
					$rangeClassName = $classLocations[0]['table'];
					$returnValue['range'][] = $rangeClassName;
				}
			}
		}else{
			$returnValue['range'][] = $range;
		}
		
        // section 10-13-1--128-743691ae:12fc0ed9381:-8000:0000000000001525 end

        return (array) $returnValue;
    }

    /**
     * Build a SQL search pattern on basis of a pattern and a comparison mode.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string pattern A value to compare.
     * @param  boolean like The manner to compare values. If set to true, the LIKE SQL operator will be used. If set to false, the = (equal) SQL operator will be used.
     * @return string
     */
    public static function buildSearchPattern($pattern, $like = true)
    {
        $returnValue = (string) '';

        // section 10-13-1--128-743691ae:12fc0ed9381:-8000:000000000000152E begin
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		
    	switch (gettype($pattern)) {
			case 'string' :
			case 'numeric':
				$patternToken = $pattern;
				$object = trim(str_replace('*', '%', $patternToken));

			    if($like){
				    if(!preg_match("/^%/", $object)){
					    $object = "%" . $object;
				    }
				    if(!preg_match("/%$/", $object)){
					    $object = $object . "%";
				    }
				    $returnValue .= ' LIKE '. $dbWrapper->dbConnector->quote($object);
			    }
			    else {
				    $returnValue = (strpos($object, '%') !== false)
					    ? 'LIKE '. $dbWrapper->dbConnector->quote($object)
					    : '= '. $dbWrapper->dbConnector->quote($patternToken);
			    }
		    break;

		    case 'object' :
			    if($pattern instanceof core_kernel_classes_Resource) {
				    $returnValue = ' = ' . $dbWrapper->dbConnector->quote($pattern->getUri());
			    } else {
				    common_Logger::w('non ressource as search parameter: '. get_class($pattern), 'GENERIS');
			    }
		    break;

		    default:
			    throw new common_Exception("Unsupported type for searchinstance array: " . gettype($value));
		}
        // section 10-13-1--128-743691ae:12fc0ed9381:-8000:000000000000152E end

        return (string) $returnValue;
    }

    /**
     * Get the ID of a resource in database in a given table.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource The Resource you want the ID.
     * @param  string tableName The name of the table the resource should be located.
     * @return int
     */
    public static function getResourceIdByTable( core_kernel_classes_Resource $resource, $tableName)
    {
        $returnValue = (int) 0;

        // section 127-0-1-1--4d72422d:1316c1e6091:-8000:000000000000162E begin
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        
        $selectQuery = 'SELECT id FROM "' . $tableName . '" WHERE uri = \'' . $resource->getUri() . '\'';
        $selectQuery = $dbWrapper->limitStatement($selectQuery, 1);
        $selectResult = $dbWrapper->query($selectQuery);
        try{
	        while ($row = $selectResult->fetch()) {
	                $returnValue = $row['id'];
	                $selectResult->closeCursor();
	                break;
	        }
        }
        catch (PDOException $e){
        	throw new core_kernel_persistence_hardsql_Exception("Unable to get the id of the resource {$resource->getUri()} in the table '{$tableName}': " . $e->getMessage());
        }
        // section 127-0-1-1--4d72422d:1316c1e6091:-8000:000000000000162E end

        return (int) $returnValue;
    }

    /**
     * Change single-valued property to multiple-valued.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource property The property to modify.
     * @param  int batchSize Data must be transfered from a given column to the properties table. This parameter indicates the size of each pack of data transfered from the column to the properties tables.
     * @return void
     */
    public static function scalarToMultiple( core_kernel_classes_Resource $property, $batchSize = 100)
    {
        // section 10-13-1-85--4d7ce118:13bdccf0439:-8000:0000000000001E49 begin
        $referencer = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $propertyDescription = core_kernel_persistence_hardapi_Utils::propertyDescriptor($property);
        $propertyLocations = $referencer->propertyLocation($property);
        
        $propName = $propertyDescription['name'];
        $propUri = $property->getUri();
        $propRanges = array();
        foreach ($propertyDescription['range'] as $range){
        	// If no range provided, we assume it is a Literal.
        	$propRanges[] = (!empty($range)) ? $range->getUri() : RDFS_LITERAL;
        }
        
        $offset = 0;
        
        foreach ($propertyLocations as $tblname){
        	$tblmgr = new core_kernel_persistence_hardapi_TableManager($tblname);
        	if ($tblmgr->exists()){
        		// Reset offset.
        		$offset = 0;
        		
        		try{	
        			// We go from single to multiple.
        			do {
        				$hasResult = false;
        				
	        			$setPropertyValue = (empty($propRanges) || in_array(RDFS_LITERAL, $propRanges)) ? true : false;
	        			$lang = ($setPropertyValue) ? DEFAULT_LANG : '';
	        			$sql = 'SELECT "id","' . $propName . '" AS "val" FROM "' . $tblname . '"';
	        			$sql = $dbWrapper->limitStatement($sql, $batchSize, $offset);
	        			$result = $dbWrapper->query($sql);
	        			
	        			// Prepare the insert statement.
	        			$sql  = 'INSERT INTO "' . $tblname . 'Props" ';
	        			$sql .= '("property_uri", "property_value", "property_foreign_uri", "l_language", "instance_id") ';
	        			$sql .= 'VALUES (?, ?, ?, ?, ?)';
	        			$sth = $dbWrapper->prepare($sql);
	        			
	        			while ($row = $result->fetch()){
	        				// Transfer to the 'properties table'.
	        				$hasResult = true;
	        				$propertyValue = ($setPropertyValue == true) ? $row['val'] : null;
	        				$propertyForeignUri = ($setPropertyValue == false) ? $row['val'] : null;
	        				
	        				$sth->execute(array($propUri, $propertyValue, $propertyForeignUri, $lang, $row['id'])); 
	        			}
	        			
	        			$offset += $batchSize;
        			}
        			while($hasResult === true);
        			
        			// Remove old column containing the scalar values.
        			// we do not need it anymore.
        			if ($tblmgr->removeColumn($propName) == false){
        				$msg = "Cannot successfully set multiplicity of Property '${propUri}' because its table column could not be removed from database.";
        				throw new core_kernel_persistence_hardapi_Exception($msg);
        			}
        		}
        		catch (PDOException $e){
        			$msg = "Cannot set multiplicity of Property '${propUri}': " . $e->getMessage();
        			throw new core_kernel_persistence_hardapi_Exception($msg);
        		}
        	}
        	else{
        		$msg = "Cannot set multiplicity of Property '${propUri}' because the corresponding database location '${tblname}' does not exist.";
        		throw new core_kernel_persistence_hardapi_Exception($msg);
        	}
        }
        
        $referencer->clearCaches();
        // section 10-13-1-85--4d7ce118:13bdccf0439:-8000:0000000000001E49 end
    }

    /**
     * Change a multi-valued property to a single-valued one.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource property The property to modifiy.
     * @param  int batchSize Data must be transfered from the properties table to a given column. This parameter indicates the size of each pack of data transfered from the properties table to the column.
     * @return void
     */
    public static function multipleToScalar( core_kernel_classes_Resource $property, $batchSize = 100)
    {
        // section 10-13-1-85--4d7ce118:13bdccf0439:-8000:0000000000001E50 begin
        $referencer = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $propertyDescription = core_kernel_persistence_hardapi_Utils::propertyDescriptor($property);
        $propertyLocations = $referencer->propertyLocation($property);
        
        $propName = $propertyDescription['name'];
        $propUri = $property->getUri();
        $propRanges = array();
        foreach ($propertyDescription['range'] as $range){
        	// If no range provided, we assume it is a Literal.
        	$propRanges[] = (!empty($range)) ? $range->getUri() : RDFS_LITERAL;
        }
        
        $offset = 0;
        
        foreach ($propertyLocations as $tblname){
        	$tblmgr = new core_kernel_persistence_hardapi_TableManager($tblname);
        	if ($tblmgr->exists()){
        		// Reset offset.
        		$offset = 0;
        		
        		try{
        			// We go from multiple to single.
        			$toDelete = array(); // will contain ids of rows to delete in the 'properties table' after data transfer.
        			
        			// Add a column to the base table to receive single value.
        			$baseTableName = str_replace('Props', '', $tblname);
        			$tblmgr->setName($baseTableName);
        			$shortName = core_kernel_persistence_hardapi_Utils::getShortName($property);
        			$columnAdded = $tblmgr->addColumn(array('name' => $shortName,
        											  		'multi' => false));
        			if ($columnAdded == true){
        				// Now get the values in the props table. Group by instance ID in order to get only
        				// one value to put in the target column.
        				do {
        					$hasResult = false;
        					
		        			$retrievePropertyValue = (empty($propRanges) || in_array(RDFS_LITERAL, $propRanges)) ? true : false;
		        			$sql  = 'SELECT "a"."id", "a"."instance_id", "a"."property_value", "a"."property_foreign_uri" FROM "' . $tblname . '" "a" ';
		        			$sql .= 'RIGHT JOIN (SELECT "instance_id", MIN("id") AS "id" FROM "' . $tblname . '" WHERE "property_uri" = ? ';
		        			$sql .= 'GROUP BY "instance_id") AS "b" ON ("a"."id" = "b"."id")';
		        			$sql  = $dbWrapper->limitStatement($sql, $batchSize, $offset);
							
		        			$result = $dbWrapper->query($sql, array($propUri));
		        			// prepare the update statement.
		        			$sql  = 'UPDATE "' . $baseTableName . '" SET "' . $shortName . '" = ? WHERE "id" = ?';
		        			$sth = $dbWrapper->prepare($sql);
		        			
		        			while ($row = $result->fetch()){
		        				// Transfer to the 'base table'.
		        				$hasResult = true;
		        				$propertyValue = ($retrievePropertyValue == true) ? $row['property_value'] : $row['property_foreign_uri'];
		        				$sth->execute(array($propertyValue, $row['instance_id']));
		        				$toDelete[] = $row['id'];
		        			}
		        			
		        			$offset += $batchSize;
        				}
        				while($hasResult === true);
        				
        				$inData = implode(',', $toDelete);
	        			$sql = 'DELETE FROM "' . $tblname . '" WHERE "id" IN (' . $inData . ')';
	        			
	        			if ($dbWrapper->exec($sql) == 0){
	        				// If an error occured or no rows removed, we
	        				// have a problem.
	        				$msg = "Cannot set multiplicity of Property '${propUri}' because data transfered to the 'base table' could not be deleted";
	        				throw new core_kernel_persistence_hardapi_Exception($msg);	
	        			}
        			}
        			else{
        				$msg = "Cannot set multiplicity of Property '${propUri}' because the corresponding 'base table' column could not be created.";
        				throw new core_kernel_persistence_hardapi_Exception($msg);
        			}
        		}
        		catch (PDOException $e){
        			$msg = "Cannot set multiplicity of Property '${propUri}': " . $e->getMessage();
        			throw new core_kernel_persistence_hardapi_Exception($msg);
        		}
        	}
        	else{
        		$msg = "Cannot set multiplicity of Property '${propUri}' because the corresponding database location '${tblname}' does not exist.";
        		throw new core_kernel_persistence_hardapi_Exception($msg);
        	}
        }
        
        $referencer->clearCaches();
        // section 10-13-1-85--4d7ce118:13bdccf0439:-8000:0000000000001E50 end
    }

} /* end of class core_kernel_persistence_hardapi_Utils */

?>