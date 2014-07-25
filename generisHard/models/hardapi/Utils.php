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

namespace oat\generisHard\models\hardapi;

use oat\generisHard\models\hardsql\Exception as HardsqlException;

/**
 * Utility class that provides transversal methods 
 * to manage  the hard api
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package generisHard
 
 */
class Utils
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
     * using the modelid/baseUri mapping
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string namespaceUri
     * @return string
     */
    private static function getNamespaceId($namespaceUri)
    {
        $returnValue = (string) '';

        
        
		if(count(self::$namespaceIds) == 0){
			$namespaces = \common_ext_NamespaceManager::singleton()->getAllNamespaces();
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
    public static function getShortName( \core_kernel_classes_Resource $resource)
    {
        $returnValue = (string) '';

        
    	if(!is_null($resource)){
    		
    		if (isset(self::$shortNames[$resource->getUri()])){
    			$returnValue = self::$shortNames[$resource->getUri()];
    		} else {
    			$namespace = substr($resource->getUri(), 0, strpos($resource->getUri(), '#')+1);
    			$namespaceId = self::getNamespaceId($namespace);
    			if (empty($namespaceId)) {
    				throw new \common_exception_UnknownNamespace($namespace);
    			}
				$returnValue = $namespaceId . substr($resource->getUri(), strlen($namespace));
				
				self::$shortNames[$resource->getUri()] = $returnValue;
    		}
		}
        
        

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

        
        
        if (!empty($shortName) && strlen($shortName)>2){
        	$shortName = preg_replace("/^_/", '', $shortName);
        	$modelid = intval (substr($shortName, 0, 2));
        	
        	if ($modelid != null && $modelid >0){
	        	$nsUri = \common_ext_NamespaceManager::singleton()->getNamespace ($modelid);
	         	$returnValue = $nsUri . substr($shortName, 2);
        	}
        }
        
        

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
    public static function propertyDescriptor( \core_kernel_classes_Property $property, $hardRangeClassOnly = false)
    {
        $returnValue = array();

        
        
		$returnValue = array(
			'name'			=> self::getShortName($property),
			'isMultiple' 	=> $property->isMultiple(),
			'isLgDependent'	=> $property->isLgDependent(),
			'range'			=> array()
		);
		
		$range = $property->getRange();
		
		
		if($hardRangeClassOnly){
			$is_class_referenced = ResourceReferencer::singleton()->isClassReferenced($range);
			if($range->getUri()!=RDFS_LITERAL && $is_class_referenced){
				$classLocations = ResourceReferencer::singleton()->classLocations($range);
				
				if(isset($classLocations[0])){
					$rangeClassName = $classLocations[0]['table'];
					$returnValue['range'][] = $rangeClassName;
				}
			}
		}else{
			$returnValue['range'][] = $range;
		}
		
        

        return (array) $returnValue;
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
    public static function getResourceIdByTable( \core_kernel_classes_Resource $resource, $tableName)
    {
        $returnValue = (int) 0;

        
        $dbWrapper = \core_kernel_classes_DbWrapper::singleton();
        
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
        catch (\PDOException $e){
        	throw new HardsqlException("Unable to get the id of the resource {$resource->getUri()} in the table '{$tableName}': " . $e->getMessage());
        }
        

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
    public static function scalarToMultiple( \core_kernel_classes_Resource $property, $batchSize = 100)
    {
        
        $referencer = ResourceReferencer::singleton();
        $dbWrapper = \core_kernel_classes_DbWrapper::singleton();
        $propertyDescription = self::propertyDescriptor($property);
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
        	$tblmgr = new TableManager($tblname);
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
	        			$sql  = 'INSERT INTO "' . $tblname . 'props" ';
	        			$sql .= '("property_uri", "property_value", "property_foreign_uri", "l_language", "instance_id") ';
	        			$sql .= 'VALUES (?, ?, ?, ?, ?)';
	        			
	        			while ($row = $result->fetch()){
	        				// Transfer to the 'properties table'.
	        				$hasResult = true;
	        				$propertyValue = ($setPropertyValue == true) ? $row['val'] : null;
	        				$propertyForeignUri = ($setPropertyValue == false) ? $row['val'] : null;
	        				
	        				$dbWrapper->exec($sql,array($propUri, $propertyValue, $propertyForeignUri, $lang, $row['id'])); 
	        			}
	        			
	        			$offset += $batchSize;
        			}
        			while($hasResult === true);
        			
        			// Remove old column containing the scalar values.
        			// we do not need it anymore.
        			if ($tblmgr->removeColumn($propName) == false){
        				$msg = "Cannot successfully set multiplicity of Property '${propUri}' because its table column could not be removed from database.";
        				throw new Exception($msg);
        			}
        		}
        		catch (\PDOException $e){
        			$msg = "Cannot set multiplicity of Property '${propUri}': " . $e->getMessage();
        			throw new Exception($msg);
        		}
        	}
        	else{
        		$msg = "Cannot set multiplicity of Property '${propUri}' because the corresponding database location '${tblname}' does not exist.";
        		throw new Exception($msg);
        	}
        }
        
        $referencer->clearCaches();
        
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
    public static function multipleToScalar( \core_kernel_classes_Resource $property, $batchSize = 100)
    {
        
        $referencer = ResourceReferencer::singleton();
        $dbWrapper = \core_kernel_classes_DbWrapper::singleton();
        $propertyDescription = self::propertyDescriptor($property);
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
        	$tblmgr = new TableManager($tblname);
        	if ($tblmgr->exists()){
        		// Reset offset.
        		$offset = 0;
        		
        		try{
        			// We go from multiple to single.
        			$toDelete = array(); // will contain ids of rows to delete in the 'properties table' after data transfer.
        			
        			// Add a column to the base table to receive single value.
        			$baseTableName = str_replace('props', '', $tblname);
        			$tblmgr->setName($baseTableName);
        			$shortName = self::getShortName($property);
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
		        			
		        			while ($row = $result->fetch()){
		        				// Transfer to the 'base table'.
		        				$hasResult = true;
		        				$propertyValue = ($retrievePropertyValue == true) ? $row['property_value'] : $row['property_foreign_uri'];
		        				$dbWrapper->exec($sql,array($propertyValue, $row['instance_id']));

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
	        				throw new Exception($msg);	
	        			}
        			}
        			else{
        				$msg = "Cannot set multiplicity of Property '${propUri}' because the corresponding 'base table' column could not be created.";
        				throw new Exception($msg);
        			}
        		}
        		catch (\PDOException $e){
        			$msg = "Cannot set multiplicity of Property '${propUri}': " . $e->getMessage();
        			throw new Exception($msg);
        		}
        	}
        	else{
        		$msg = "Cannot set multiplicity of Property '${propUri}' because the corresponding database location '${tblname}' does not exist.";
        		throw new Exception($msg);
        	}
        }
        
        $referencer->clearCaches();
        
    }

} /* end of class self */

?>