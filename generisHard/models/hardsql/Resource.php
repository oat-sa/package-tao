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
use oat\generisHard\models\hardapi\TableManager;
use oat\generisHard\models\hardapi\Utils as HardapiUtils;
use oat\generisHard\models\hardapi\Exception as HardapiException;

/**
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package generisHard
 
 */
class Resource
    extends \core_kernel_persistence_PersistenceImpl
        implements \core_kernel_persistence_ResourceInterface
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
     * returns an array of types the ressource has
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return array
     */
    public function getTypes( \core_kernel_classes_Resource $resource)
    {
        $returnValue = array();

        
		$dbWrapper 	= \core_kernel_classes_DbWrapper::singleton();
		
		try{
			$query = 'SELECT "class_to_table"."uri"
	    		FROM "class_to_table"
	    		INNER JOIN "resource_has_class" ON "resource_has_class"."class_id" = "class_to_table"."id"
	    		INNER JOIN "resource_to_table" ON "resource_to_table"."id" = "resource_has_class"."resource_id"
	    		WHERE "resource_to_table"."uri" = ?';
			$result	= $dbWrapper->query($query, array($resource->getUri()));
	
			while ($row = $result->fetch()){
				$returnValue[$row['uri']] = new \core_kernel_classes_Class($row['uri']);
			}
		}
		catch (\PDOException $e){
			throw new Exception("Unable to getType of the resource {$resource->getUri()} : " .$e->getMessage());
		}
        

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
    public function getPropertyValues( \core_kernel_classes_Resource $resource,  \core_kernel_classes_Property $property, $options = array())
    {
        $returnValue = array();

        
        
		$referencer = ResourceReferencer::singleton();
		// hmmmm ! Perplexe
		$table = ResourceReferencer::singleton()->resourceLocation($resource);
		if(empty($table)){
			return $returnValue;
		}
		
		$dbWrapper 	= \core_kernel_classes_DbWrapper::singleton();
		$propertyLocation = $referencer->propertyLocation($property);
		
		// Select in the properties table of the class
		if (in_array("{$table}props", $propertyLocation)
		|| ! $referencer->isPropertyReferenced($property)){

			try{
				// Check if we have to return first or last entry
				$one = isset($options['one']) && $options['one'] == true ? true : false;
				$last = isset($options['last']) && $options['last'] == true ? true : false;
				
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
	            
				$query = 'SELECT "property_value", "property_foreign_uri"
					FROM "'.$table.'"
					INNER JOIN "'.$tableProps.'" on "'.$table.'"."id" = "'.$tableProps.'"."instance_id"
				   	WHERE "'.$table.'"."uri" = ?
					AND "'.$tableProps.'"."property_uri" = ?
					AND ( "l_language" = ? OR "l_language" = \'\' '.$defaultLg.')';
				
				// Select first
				if ($one) {
					$query .= ' ORDER BY "' .$tableProps. '"."id" ASC';
					$query = $dbWrapper->limitStatement($query, 1, 0);
					
					$result	= $dbWrapper->query($query, array(
						$resource->getUri()
						, $property->getUri()
						, $lang
					));
				}
				// Select Last
				else if ($last) {
					$query .= ' ORDER BY "' .$tableProps. '"."id" DESC';
					$query = $dbWrapper->limitStatement($query, 1, 0);
					
					$result	= $dbWrapper->query($query, array(
						$resource->getUri()
						, $property->getUri()
						, $lang
					));
				}
				// Select All
				else {
					$result	= $dbWrapper->query($query, array(
						$resource->getUri()
						, $property->getUri()
						, $lang
					));
				}
					
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
			try {
				$propertyAlias = HardapiUtils::getShortName($property);
				$query =  'SELECT "'.$propertyAlias.'" as "propertyValue" FROM "'.$table.'" WHERE "uri" = ?';
				$result	= $dbWrapper->query($query, array($resource->getUri()));

				while ($row = $result->fetch()){
					if ($row['propertyValue'] !== null){
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
			} catch (\common_exception_UnknownNamespace $n) {
				// unknown namespace means we have no propertyAlias, so it's impossible that a value exists
			}
		}

        

        return (array) $returnValue;
    }

    /**
     * Short description of method getPropertyValuesByLg
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  string lg
     * @return \core_kernel_classes_ContainerCollection
     */
    public function getPropertyValuesByLg( \core_kernel_classes_Resource $resource,  \core_kernel_classes_Property $property, $lg)
    {
        $returnValue = null;

        

		$options = array('lg'=>$lg);
		$returnValue = new \core_kernel_classes_ContainerCollection($resource);
		foreach ($resource->getPropertyValues($property, $options) as $value){
		    $returnValue->add(\common_Utils::toResource($value));
		}
        
        

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
    public function setPropertyValue( \core_kernel_classes_Resource $resource,  \core_kernel_classes_Property $property, $object, $lg = null)
    {
        $returnValue = (bool) false;

        
        
		// Get the table name
		$referencer = ResourceReferencer::singleton();
		$tableName = $referencer->resourceLocation ($resource);
		if(empty($tableName)){
			\common_Logger::i('resourceLocation failed');
			return $returnValue;
		}

		$dbWrapper 	= \core_kernel_classes_DbWrapper::singleton();
		$object  = $object instanceof \core_kernel_classes_Resource ? $object->getUri() : (string) $object;
		$instanceId = null;
		$propertyValue = null;
		$propertyForeignUri = null;
		$propertyRange = $property->getRange();

		// Get property instance
		$instanceId = Utils::getInstanceId($resource);

		// Get the property value or property foreign id
		if(!is_null($propertyRange)){

			// Foregin resource
			if ($propertyRange->getUri() != RDFS_LITERAL){
				$propertyForeignUri = $object;
			}
			// The object is a literal
			else {
				$propertyValue = $object;
			}
		}
		else{
			// We assume the property value is a literal.
			$propertyValue = $object;	
		}
		
		$propertyLocation = $referencer->propertyLocation($property);
		if (in_array("{$tableName}props", $propertyLocation)
		|| !$referencer->isPropertyReferenced($property)){
			
			$session 	= \core_kernel_classes_Session::singleton();
			$lang = "";
			// Define language if required
			if ($property->isLgDependent()){
				if ($lg!=null){
					$lang = $lg;
				} else {
					$lang = $session->getDataLanguage();
				}
			}

			try{
				$query = 'INSERT INTO "'.$tableName.'props"
	        		("instance_id", "property_uri", "property_value", "property_foreign_uri", "l_language") 
	        		VALUES (?, ?, ?, ?, ?)';
				$result	= $dbWrapper->exec($query, array(
					$instanceId,
					$property->getUri(),
					$propertyValue,
					$propertyForeignUri,
					$lang
				));
				$returnValue = true;
			}
			catch (\PDOException $e){
				throw new Exception("Unable to set property (multiple) Value for the instance {$resource->getUri()} in {$tableName} : " .$e->getMessage());
			}
		} 
		else {
			
			try{
				$propertyName = HardapiUtils::getShortName ($property);
				$queryUpdate = 'UPDATE "' . $tableName . '" SET "' . $propertyName . '" = ? WHERE id = ?';
				$result	= $dbWrapper->exec($queryUpdate, array(
					$propertyValue != null ? $propertyValue : $propertyForeignUri, 
					$instanceId
				));

				$returnValue = true;
			}
			catch (\PDOException $e){
				throw new Exception("Unable to set property (single) Value for the instance {$resource->getUri()} in {$tableName} : " .$e->getMessage());
			}
		}

        

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
    public function setPropertiesValues( \core_kernel_classes_Resource $resource, $properties)
    {
        $returnValue = (bool) false;

        
		if (is_array($properties)) {
			if (count($properties) > 0) {

				// Get the table name
				$referencer = ResourceReferencer::singleton();
				$tableName = $referencer->resourceLocation($resource);
				if (empty($tableName)) {
					return $returnValue;
				}

				$instanceId = Utils::getInstanceId($resource);
				$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
				$session = \core_kernel_classes_Session::singleton();

				$queryProps = '';
				$hardPropertyNames = array();

				foreach ($properties as $propertyUri => $value) {

					$property = new \core_kernel_classes_Property($propertyUri);
					$propertyLocation = $referencer->propertyLocation($property);

					if (in_array("{$tableName}props", $propertyLocation)
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
						
						if ($propertyRange instanceof \core_kernel_classes_Class && $propertyRange->getUri() == RDFS_LITERAL) {
							foreach ($formatedValues as $formatedValue) {
								$queryProps .= " ({$instanceId}, '{$property->getUri()}', {$formatedValue}, null, '{$lang}'),";
							}
						} else {
							foreach ($formatedValues as $formatedValue) {
								$queryProps .= " ({$instanceId}, '{$property->getUri()}', null, {$formatedValue}, '{$lang}'),";
							}
						}
					} else {

						$propertyName = HardapiUtils::getShortName($property);
						if ($value instanceof \core_kernel_classes_Resource) {
							$value = $value->getUri();
						} else if (is_array($value)) {
							throw new Exception("try setting multivalue for the non multiple property {$property->getLabel()} ({$property->getUri()})");
						} else {
							$value = $value; // no need to quote passed as variable
						}

						$hardPropertyNames[$propertyName] = $value;
					}
				}

				if (!empty($queryProps)) {
					try{
						$query = 'INSERT INTO "' . $tableName . 'props" ("instance_id", "property_uri", "property_value", "property_foreign_uri", "l_language") VALUES ' . $queryProps;
						$query = substr($query, 0, strlen($query) - 1);
						$result = $dbWrapper->exec($query);
						$returnValue = true;
					}
					catch (\PDOException $e){
						throw new Exception("Unable to set properties (multiple) Value for the instance {$resource->getUri()} in {$tableName} : " . $e->getMessage());
					}
				}

				if (!empty($hardPropertyNames)) {
					$variables = array();
					$query = 'UPDATE "' . $tableName . '" SET ';
					$i = 0;
					foreach ($hardPropertyNames as $hardPropertyName => $value) {
						if ($i) {
							$query .= ', ';
						}
						$query .= '"' . $hardPropertyName . '" = ? ';
						$variables[] = $value;
						$i++;
					}
					$query .= ' WHERE "id" = ?';
					
					$variables[] = $instanceId;
					
					try{
						$result = $dbWrapper->exec($query, $variables);
						$returnValue = true;
					}
					catch (\PDOException $e){
						throw new Exception("Unable to set properties (single) Value for the instance {$resource->getUri()} in {$tableName} : " . $e->getMessage());
					}
				}
			}
		}

        

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
    public function setPropertyValueByLg( \core_kernel_classes_Resource $resource,  \core_kernel_classes_Property $property, $value, $lg)
    {
        $returnValue = (bool) false;

        

		$returnValue = $this->setPropertyValue ($resource, $property, $value, $lg);

        

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
    public function removePropertyValues( \core_kernel_classes_Resource $resource,  \core_kernel_classes_Property $property, $options = array())
    {
        $returnValue = (bool) false;

        

		// Get the table name
		$referencer = ResourceReferencer::singleton();
		$tableName = $referencer->resourceLocation ($resource);
		if(empty($tableName)){
			return $returnValue;
		}

		$dbWrapper 	= \core_kernel_classes_DbWrapper::singleton();

		// Optional params
		$pattern = isset($options['pattern']) && !is_null($options['pattern']) ? $options['pattern'] : null;
		$like = isset($options['like']) && $options['like'] == true ? true : false;

		$propertyLocation = $referencer->propertyLocation($property);
		if (in_array("{$tableName}props", $propertyLocation)
		|| !$referencer->isPropertyReferenced($property, $tableName)){
			 
                        $resourceId = HardapiUtils::getResourceIdByTable($resource, $tableName);
                        
                        if($resourceId){
                                
                                $propsTableName = $tableName.'props';
                                $query = 'DELETE FROM "'.$propsTableName.'" WHERE "property_uri" = \''.$property->getUri().'\' AND "instance_id" = \''.$resourceId.'\' ';

                                //build additionnal conditions:
                                $additionalConditions = array();
                                if(!is_null($pattern)){
                                        if(is_string($pattern)){
                                                $searchPattern = \core_kernel_persistence_smoothsql_Utils::buildSearchPattern($pattern, $like);
                                                $additionalConditions[] = ' ("property_value" '.$searchPattern.' OR "property_foreign_uri" '.$searchPattern.') ';
                                        }else if(is_array($pattern)){
                                                if(count($pattern) > 0){
                                                        $multiCondition =  "(";
                                                        foreach($pattern as $i => $patternToken){
                                                                $searchPattern = \core_kernel_persistence_smoothsql_Utils::buildSearchPattern($patternToken, $like);
                                                                if($i > 0){
                                                                	$multiCondition .= " OR ";
                                                                }
                                                                $multiCondition .= ' ("property_value" '.$searchPattern.' OR "property_foreign_uri" '.$searchPattern.') ';
                                                        }
                                                        $additionalConditions[] = "{$multiCondition}) ";
                                                }
                                        }
                                }
								
								//@TODO : if the property is language dependent, add the language condition !!
//								if($property->isLgDependent()){
//									$session = \core_kernel_classes_Session::singleton();
//									$lang = $session->getDataLanguage();
//									$query .= ' AND ("l_language" = \'\' OR "l_language" = \''.$lang.'\') ';
//								}
		
                                foreach($additionalConditions as $i => $additionalCondition){
                                        $query .= " AND ( {$additionalCondition} ) ";
                                }

                                try{
	                                $result	= $dbWrapper->exec($query);
	                                $returnValue = true;
                                }
                                catch (\PDOException $e){
                                	throw new Exception("Unable to delete property values (multiple) for the instance {$resource->getUri()} : " .$e->getMessage());
                                }
                        }
		} else {
			 
			$propertyName = HardapiUtils::getShortName ($property);
			$query = 'UPDATE "'.$tableName.'" SET "'.$propertyName.'" = NULL WHERE uri = ?';
				
			//build additionnal conditions:
			$additionalConditions = array();
			if(!is_null($pattern)){
				if(is_string($pattern)){
					$searchPattern = \core_kernel_persistence_smoothsql_Utils::buildSearchPattern($pattern, $like);
					$additionalConditions[] = ' ("'.$propertyName.'" '.$searchPattern.') ';
				}else if(is_array($pattern)){
					if(count($pattern) > 0){
						$multiCondition =  "(";
						foreach($pattern as $i => $patternToken){
							$searchPattern = \core_kernel_persistence_smoothsql_Utils::buildSearchPattern($patternToken, $like);
							if($i > 0){
								$multiCondition .= " OR ";
							}
							$multiCondition .= ' ("'.$tableName.'"."'.$propertyName.'" '.$searchPattern.') ';
						}
						$additionalConditions[] = "{$multiCondition}) ";
					}
				}
			}
				
			foreach($additionalConditions as $i => $additionalCondition){
				$query .= " AND ( {$additionalCondition} ) ";
			}
			
			try{
				$result	= $dbWrapper->exec($query, array($resource->getUri()));
				$returnValue = true;
			}
			catch (\PDOException $e){
				throw new Exception("Unable to delete property values (single) for the instance {$resource->getUri()} : " .$e->getMessage());
			}
		}

        

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
    public function removePropertyValueByLg( \core_kernel_classes_Resource $resource,  \core_kernel_classes_Property $property, $lg, $options = array())
    {
        $returnValue = (bool) false;

        

		$dbWrapper 	= \core_kernel_classes_DbWrapper::singleton();

		// Optional params
		$pattern = isset($options['pattern']) && !is_null($options['pattern']) ? $options['pattern'] : null;
		$like = isset($options['like']) && $options['like'] == true ? true : false;

		// Get the table name
		$tableName = ResourceReferencer::singleton()->resourceLocation($resource);
		if($property->isLgDependent()){
                        
                        $resourceId = HardapiUtils::getResourceIdByTable($resource, $tableName);
                        if($resourceId){
                                
                                $propsTableName = $tableName.'props';
                                $query = 'DELETE FROM "'.$propsTableName.'"
                                        WHERE "property_uri" = \''.$property->getUri().'\' 
                                        AND "instance_id" = \''.$resourceId.'\'
                                        AND "l_language" = \''.$lg.'\' ';

                                //build additionnal conditions:
                                $additionalConditions = array();
                                if(!is_null($pattern)){
                                        if(is_string($pattern)){ 
                                                $searchPattern = \core_kernel_persistence_smoothsql_Utils::buildSearchPattern($pattern, $like);
                                                $additionalConditions[] = ' ("property_value" '.$searchPattern.' OR "property_foreign_uri" '.$searchPattern.') ';
                                        }else if(is_array($pattern)){
                                                if(count($pattern) > 0){
                                                        $multiCondition =  "(";
                                                        foreach($pattern as $i => $patternToken){
                                                                $searchPattern = \core_kernel_persistence_smoothsql_Utils::buildSearchPattern($patternToken, $like);
                                                                if($i > 0){
                                                                	$multiCondition .= " OR ";
                                                                }
                                                                $multiCondition .= ' ("property_value" '.$searchPattern.' OR "property_foreign_uri" '.$searchPattern.') ';
                                                        }
                                                        $additionalConditions[] = "{$multiCondition}) ";
                                                }
                                        }
                                }

                                foreach($additionalConditions as $i => $additionalCondition){
                                        $query .= " AND ( {$additionalCondition} ) ";
                                }

                                try{
                                $result	= $dbWrapper->exec($query);
                                $returnValue = true;
                                }
                                catch (\PDOException $e){
                                	throw new Exception("Unable to delete property values (multiple) for the instance {$resource->getUri()} : " .$e->getMessage());
                                }
                        }
		}

        

        return (bool) $returnValue;
    }

    /**
     * Short description of method getRdfTriples
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return \core_kernel_classes_ContainerCollection
     */
    public function getRdfTriples( \core_kernel_classes_Resource $resource)
    {
        $returnValue = null;

        
        $returnValue = new \core_kernel_classes_ContainerCollection(new \common_Object(__METHOD__));
        
		$referencer = ResourceReferencer::singleton();
		$tableName = $referencer->resourceLocation($resource);
		
		if (!empty($tableName)){
			try{
				$tblmgr = new TableManager($tableName);
				$propertiesTableName = $tblmgr->getPropertiesTable();
				
				$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
				// We get the triples for cardinality = multiple or lg dependent properties
				// as usual...
				$quotedUri = $dbWrapper->quote($resource->getUri());

				$propsQuery  = 'SELECT "b"."id", "b"."uri", "p"."property_uri" AS "property_uri", COALESCE("p"."property_value", "p"."property_foreign_uri") as "property_value", "p"."l_language"  FROM "' . $tableName . '" "b" ';
				$propsQuery .= 'INNER JOIN "' . $propertiesTableName . '" "p" ON ("b"."id" = "p"."instance_id") WHERE "b"."uri" = ' . $quotedUri;
				
				$propertyColumns = $tblmgr->getPropertyColumns();
				$baseQuery = '';
				if (!empty($propertyColumns)){
					// But if we have properties as columns in the 'base table' we 
					// have to be crafty...
					$baseQueries = array();
					foreach ($propertyColumns as $k => $pC){
						$quotedPropUri = $dbWrapper->quote($pC);
						$baseQueries[] = 'SELECT "b"."id", "b"."uri", ' . $quotedPropUri . ' AS "property_uri", "b"."' . $k . '" AS "property_value", \'\' AS "l_language" FROM "' . $tableName . '" "b" WHERE "b"."uri" = ' . $quotedUri . ' AND "b"."' . $k . '" IS NOT NULL';
					}
					
					$baseQuery = implode(' UNION ', $baseQueries);
				}
				
				$query = $propsQuery . ' UNION ' . $baseQuery . ' ORDER BY "property_uri"';
				
				try{
					$result = $dbWrapper->query($query);
					while ($row = $result->fetch()){
					    if ($row['property_value'] != null) {
    						$triple = new \core_kernel_classes_Triple();
    						$triple->subject = $row['uri'];
    						$triple->predicate = $row['property_uri'];
    						$triple->object = $row['property_value'];
    						$triple->lg = $row['l_language'];
    						
    						$returnValue->add($triple);
					    }
					}
					// In hard mode, the rdf:type given to resources is defined by
					// 'the table' their are belonging to. In this case, we need to
					// manually add these triples to the end result.
					$types = $resource->getTypes();
					foreach ($types as $class){
						$triple = new \core_kernel_classes_Triple();
						$triple->subject = $resource->getUri();
						$triple->predicate = RDF_TYPE;
						$triple->object = $class->getUri();
						$triple->lg = '';
						
						$returnValue->add($triple);
					}
				}
				catch (\PDOException $e){
					$uri = $resource->getUri();
					throw new Exception("Unable to retrieve RDF triples of resource '${uri}': " . $e->getMessage());	
				}
			}
			catch (HardapiException $e){
				throw new Exception("Unable to access data from table '${tableName}: " . $e->getMessage());
			}
		}
        

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
    public function getUsedLanguages( \core_kernel_classes_Resource $resource,  \core_kernel_classes_Property $property)
    {
        $returnValue = array();

        
		
		$tableName = ResourceReferencer::singleton()->resourceLocation($resource);
		$sqlQuery = 'SELECT "'.$tableName.'props"."l_language" FROM "'.$tableName.'props" 
			LEFT JOIN "'.$tableName.'" ON "'.$tableName.'".id = "'.$tableName.'props".instance_id
			WHERE "'.$tableName.'"."uri" = ? 
				AND "'.$tableName.'props"."property_uri" = ?';
		$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
		$sqlResult = $dbWrapper->query($sqlQuery, array (
			$resource->getUri(),
			$property->getUri()
		));
		while ($row = $sqlResult->fetch()){
		    if (!empty($row['l_language'])) {
                $returnValue[] = $row['l_language'];
		    }
		}
		
        

        return (array) $returnValue;
    }

    /**
     * Short description of method duplicate
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  array excludedProperties
     * @return \core_kernel_classes_Resource
     */
    public function duplicate( \core_kernel_classes_Resource $resource, $excludedProperties = array())
    {
        $returnValue = null;

        
		$referencer = ResourceReferencer::singleton();
		$tableName = $referencer->resourceLocation ($resource);
		if(empty($tableName)){
			return $returnValue;
		}

		//the new Uri
		$newUri = \common_Utils::getNewUri();

		$dbWrapper = \core_kernel_classes_DbWrapper::singleton();

		//duplicate the row in the main table
		$query = 'SELECT * FROM "'.$tableName.'" WHERE "uri" = ?';
		$result = $dbWrapper->query($query, array($resource->getUri()));
		$rows = $result->fetchAll();
	
		if(count($rows) > 0){
			//get the columns to duplicate
			$columnProps = array();
			for($i=0; $i < $result->columnCount(); $i++){
				$column = $result->getColumnMeta($i);
				if(preg_match("/^[0-9]{2,}/", $column['name'])){
					$propertyUri = HardapiUtils::getLongName($column['name']);
					if(!in_array($propertyUri, $excludedProperties)){	//check if the property is excluded
						$columnProps[$propertyUri] = $column['name'];
					}
				}
			}
			// Fetch the first result.
			$instanceId = $rows[0]['id'];
				
			//build the insert query
			$insertQuery ='INSERT INTO "'.$tableName.'" ("uri"';
			foreach($columnProps as $column){
			    if(!is_string($column)){
			        throw new Exception('columns should be a string');
			    }
				$insertQuery .= ', "'.$column.'"';
			}
			$insertQuery .= ') VALUES (';
			$insertQuery .= "'{$newUri}'";
			foreach($columnProps as $column){
				$insertQuery .= ", '".$rows[0][$column]."'";
			}
			$insertQuery .= ')';

			$insertResult = $dbWrapper->exec($insertQuery);
			if($insertResult !== false  && $instanceId > -1){

				//duplicated data
				$duplicatedResource = new \core_kernel_classes_Resource($newUri);
				$referencer->referenceResource($duplicatedResource, $tableName, $resource->getTypes(), true);

				$duplicateInstanceId = Utils::getInstanceId($duplicatedResource);

				//now we duplciate the rows of the Props table

				//linearize the excluded properties
				$excludedPropertyList = '';
				foreach($excludedProperties as $excludedProperty){
					$excludedPropertyList .= "'{$excludedProperty}',";
				}
				$excludedPropertyList = substr($excludedPropertyList, 0, strlen($excludedPropertyList) -1);

				//query templates of the 3 ways to insert the props rows
				$insertPropValueQuery = 'INSERT INTO "'.$tableName.'props" ("property_uri", "property_value", "l_language", "instance_id") VALUES (?,?,?,?)';
				$insertPropForeignQuery = 'INSERT INTO "'.$tableName.'props" ("property_uri", "property_foreign_uri", "l_language", "instance_id") VALUES (?,?,?,?)';
				$insertPropEmptyQuery = 'INSERT INTO "'.$tableName.'props" ("property_uri", "l_language", "instance_id") VALUES (?,?,?)';

				//get the rows to duplicate
				try{
					$propsQuery = 'SELECT * FROM "'.$tableName.'props" WHERE "instance_id" = ? ';
					$propsQuery .= empty($excludedPropertyList)?'':' AND "property_uri" NOT IN ('.$excludedPropertyList.') ';
					$propsResult = $dbWrapper->query($propsQuery, array($instanceId));
				}
				catch (\PDOException $e){
					throw new Exception("Unable to duplicate the resource {$resource->getUri()} : " .$e->getMessage());
				}
				
				while($row = $propsResult->fetch()){
						
					$propUri 		= $row['property_uri'];
					$propValue 		= $row['property_value'];
					$propForeign	= $row['property_foreign_uri'];
					$proplang 		= $row['l_language'];
						
					//insert them regarding the populated columns
					if(!is_null($propValue)  && !empty($propValue)){
						$dbWrapper->exec($insertPropValueQuery, array($propUri, $propValue, $proplang, $duplicateInstanceId));
					}
					else if(!is_null($propForeign)  && !empty($propForeign)){
						$dbWrapper->exec($insertPropForeignQuery, array($propUri, $propForeign, $proplang, $duplicateInstanceId));
					}
					else{
						$dbWrapper->exec($insertPropEmptyQuery, array($propUri, $proplang, $duplicateInstanceId));//costly to insert NULL values
					}
				}

				//return the duplciated resource
				$returnValue = $duplicatedResource;
			}
		}
        

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
    public function delete( \core_kernel_classes_Resource $resource, $deleteReference = false)
    {
        $returnValue = (bool) false;

        

        $dbWrapper = \core_kernel_classes_DbWrapper::singleton();
        $tableName = ResourceReferencer::singleton()->resourceLocation ($resource);
        if(empty($tableName)){
                return $returnValue;
        }

        $uri = $resource->getUri();

        $resourceId = HardapiUtils::getResourceIdByTable($resource, $tableName);
        if($resourceId){
                
        /*
		 * Delete all the references of the resource first, before the resource is delete of course,
		 * if the parameter $deleteReference is true
		 */
		if($deleteReference){
				
			$properties = array();
				
			//get the resource classes (type)
			$types = '';
			foreach($resource->getTypes() as $type){
				$properties[$type->getUri()] = array();
				$types = "'".$type->getUri()."',";
			}
				
			$types = substr($types, 0, strlen($types) - 1);
				
			if(!empty($types)){

				//get all the properties that have one of the resource class as range
				$sqlQuery = 'SELECT "subject", "object" FROM "statements" WHERE "predicate" = \''.RDFS_RANGE.'\' AND object IN ('.$types.')';
				$result = $dbWrapper->query($sqlQuery);

				while($row = $result->fetch()){
					//fill the properties range: propertyUri => domains:
					$propertyUri = $row['subject'];
					$rangeUri = $row['object'];
					$properties[$rangeUri][$propertyUri] = array();
						
					//get the domain of the property:
					$property = new \core_kernel_classes_Property($propertyUri);
					foreach($property->getDomain()->getIterator() as $domain){
						if($domain instanceof \core_kernel_classes_Class){
							$properties[$rangeUri][$propertyUri][] = $domain->getUri();
						}
					}
				}

				//delete the references
				$referencer = ResourceReferencer::singleton();
				foreach($properties as $rangeUri=> $propertyUris){
					foreach($propertyUris as $propertyUri => $domains){
						//property -> column
						$property = new \core_kernel_classes_Property($propertyUri);
						$isMulti = ($property->isMultiple() || $property->isLgDependent());
						$columnName = '';
						if(!$isMulti){
							$columnName = HardapiUtils::getShortName($property);
							if(empty($columnName)){
								continue;
							}
						}
							
						foreach($domains as $domainUri){
								
							//classLocations -> table
							$classLocations = $referencer->classLocations(new \core_kernel_classes_Class($domainUri));
							foreach ($classLocations as $classLocation){

								if($property->isMultiple()){
									//delete the row in the props table
									$query = 'DELETE FROM "'.$classLocation['table'].'props"
												WHERE "property_uri" = ? 
												AND ("property_value" = ? OR "property_foreign_uri" = ?)';
									$dbWrapper->exec($query, array(
										$propertyUri,
										$uri,
										$uri
									));
								}
								else {
									//set the col value to NULL
									$query = 'UPDATE "'.$classLocation['table'].'"
												SET "'.$columnName.'" = NULL 
												WHERE "'.$columnName.'" = ?';
									$dbWrapper->exec($query, array(
										$uri
									));
								}
							}
						}
					}
				}
			}
		}
                
        $queries = array();
		// Delete records in the main table 
		$queries[] = 'DELETE FROM "'.$tableName.'" WHERE "id" = \''.$resourceId.'\'';
		// Delete records in the properties table
        $queries[] = 'DELETE FROM "'.$tableName.'props" WHERE "instance_id" = \''.$resourceId.'\'';
		
		foreach ($queries as $query) {
			try{
				$result = $dbWrapper->exec($query);
				if ($result === false){
					$returnValue = false;
					break;
				}else{
					$returnValue = true;
				}
			}
			catch (\PDOException $e){
				throw new Exception("Unable to delete resource ({$resource->getUri()}) ;".$e->getMessage());
			}
		}

		// Unreference the resource
		ResourceReferencer::singleton()->unReferenceResource($resource);
        }
        

        return (bool) $returnValue;
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
    public function getPropertiesValues( \core_kernel_classes_Resource $resource, $properties)
    {
        $returnValue = array();

        
        $referencer = ResourceReferencer::singleton();
		$table = ResourceReferencer::singleton()->resourceLocation($resource);
		if (empty($table)) {
			return $returnValue;
		}
		$tableProps = $table . 'props';
		$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
		$propertiesMain = '';
		$propertiesProps = '';
		$propertyIndexes = array();
		$propertyIndex = 0;
		foreach ($properties as $propertyMixed) {
			$property = is_object($propertyMixed) ? $propertyMixed : new \core_kernel_classes_Property($propertyMixed); 
			$propertyLocation = $referencer->propertyLocation($property);

			if (in_array($tableProps, $propertyLocation)
				|| !$referencer->isPropertyReferenced($property)) {
				if (!empty($propertiesProps)) {
					$propertiesProps .= ", ";
				}
				$propertiesProps .= "'" . $property->getUri() . "'";
			} else {
				try {
					$propertyAlias = HardapiUtils::getShortName($property);
					if (!empty($propertiesMain)) {
						$propertiesMain .= ', ';
					}
					$propertiesMain .= '"' . $propertyAlias . '" as "propertyValue' . $propertyIndex . '"';
	
					$propertyIndexes[$propertyIndex] = $property;
					$propertyIndex++;
				} catch (\common_exception_UnknownNamespace $e) {
					// unknown property
				}
			}
		}

		if (!empty($propertiesProps)) {

			$session = \core_kernel_classes_Session::singleton();
			$session = \core_kernel_classes_Session::singleton();
			// Define language if required
			$lang = '';
			$defaultLg = '';
			$options = array(); //@TODO: option to be implemented
			if (isset($options['lg'])) {
				$lang = $options['lg'];
			} else {
				$lang = $session->getDataLanguage();
				$defaultLg = ' OR "l_language" = \'' . DEFAULT_LANG . '\' ';
			}

			$query = 'SELECT "property_uri", "property_value", "property_foreign_uri"
				FROM "' . $table . '"
				INNER JOIN "' . $tableProps . '" on "' . $table . '"."id" = "' . $tableProps . '"."instance_id"
			   	WHERE "' . $table . '"."uri" = ?
					AND "' . $tableProps . '"."property_uri" IN (' . $propertiesProps . ')
					AND ( "l_language" = ? OR "l_language" = \'\' ' . $defaultLg . ')
				ORDER BY "property_uri"';

			try{
				$result = $dbWrapper->query($query, array($resource->getUri(), $lang));
			}
			catch (\PDOException $e){
				throw new Exception("Unable to get property (multiple) values for {$resource->getUri()} in {$table} : " . $e->getMessage());
			}
			
			$currentPredicate = null;
			while ($row = $result->fetch()) {
				if ($currentPredicate != $row['property_uri']) {
					$currentPredicate = $row['property_uri'];
					$returnValue[$currentPredicate] = array();
				}

				$value = $row['property_value'] != null ? $row['property_value'] : $row['property_foreign_uri'];
				$returnValue[$currentPredicate][] = \common_Utils::isUri($value) ? new \core_kernel_classes_Resource($value) : new \core_kernel_classes_Literal($value);
			}
		}

		if (!empty($propertiesMain)) {
			try{
				$query = 'SELECT ' . $propertiesMain . ' FROM "' . $table . '" WHERE "uri" = ?';
				$result = $dbWrapper->query($query, array($resource->getUri()));
	
				while ($row = $result->fetch()) {
					foreach ($propertyIndexes as $propertyIndex => $property) {
						$returnValue[$property->getUri()] = array();
						if ($row['propertyValue' . $propertyIndex] != null) {
							$value = $row['propertyValue' . $propertyIndex];
							$returnValue[$property->getUri()][] = \common_Utils::isUri($value) ? new \core_kernel_classes_Resource($value) : new \core_kernel_classes_Literal($value);
						}
					}
				}
			}
			catch (\PDOException $e){
				if ($e->getCode() == $dbWrapper->getColumnNotFoundErrorCode()) {
					// Column doesn't exists is not an error. Try to get a property which does not exist is allowed
				} else if ($e->getCode() !== '00000') {
					throw new Exception("Unable to get property (single) values for {$resource->getUri()} in {$table} : " . $e->getMessage());
				}
			}
		}
        

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
    public function setType( \core_kernel_classes_Resource $resource,  \core_kernel_classes_Class $class)
    {
        $returnValue = (bool) false;
		$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
		$referencer = ResourceReferencer::singleton();
        
        if (!$resource->hasType($class)){
        	
        	$classInfo = Utils::getClassInfo($class);
        	
        	if ($classInfo !== false){
        		
        		
        		$sql = 'INSERT INTO "resource_to_table" ("uri", "table") VALUES (?, ?)';
        		$rowsAffected1 = $dbWrapper->exec($sql, array($resource->getUri(), $classInfo['table']));
        		
        		$sql = 'INSERT INTO "resource_has_class" ("resource_id", "class_id") VALUES (?, ?)';
        		$id = $dbWrapper->lastInsertId('resource_to_table');
        		$rowsAffected2 = $dbWrapper->exec($sql, array($id, $classInfo['id']));

        		$sql = 'INSERT INTO "' . $classInfo['table'] . '" ("uri") VALUES (?)';
        		$dbWrapper->exec($sql, array($resource->getUri())); 
        		
        		$referencer->clearCaches();
        		
        		$sql = 'SELECT * FROM "statements" WHERE "modelid" = ? AND "subject" = ?';
        		$result = $dbWrapper->query($sql, array(99999, $resource->getUri()));
        			
        		while ($row = $result->fetch()){
        			if ($row['predicate'] !== 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type'){
        				$resource->setPropertyValue(new \core_kernel_classes_Property($row['predicate']), $row['object']);
        			}
        				
        			$sql = 'DELETE FROM "statements" WHERE "id" = ' . $row['id'];
        			$dbWrapper->exec($sql);
        		}
        			
				
        	}
        }
        
        $returnValue = true;

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
    public function removeType( \core_kernel_classes_Resource $resource,  \core_kernel_classes_Class $class)
    {
        $returnValue = (bool) false;

        
		$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
		$referencer = ResourceReferencer::singleton();
		
		if ($resource->hasType($class)){
			$resourceId = intval(Utils::getResourceToTableId($resource));
			$classInfo = Utils::getClassInfo($class);
			$triples = $resource->getRdfTriples();
			
			if (!empty($resourceId)){
				$resource->delete(false);
				$referencer->unReferenceResource($resource);
				
				$query = 'INSERT INTO "statements" ("modelid", "subject", "predicate", "object", "l_language") VALUES  (?, ?, ?, ?, ?);';

				foreach ($triples as $t){
					$dbWrapper->exec($query, array(99999, $t->subject, $t->predicate, $t->object, $t->lg));
				}
			}
			
			
		}
		
		$returnValue = true;
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
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
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function isValidContext( \core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        
			
		if (ResourceReferencer::singleton()->isResourceReferenced($resource)){
			$returnValue = true;
		}

        

        return (bool) $returnValue;
    }

}