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

namespace oat\generisHard\models\hardapi;

use oat\generisHard\models\proxy\ClassProxy;
use oat\generisHard\models\proxy\ResourceProxy;
use oat\generisHard\models\proxy\PropertyProxy;
use oat\generisHard\models\switcher\PropertySwitcher;

/**
 * This class helps you to manage meta references to resources
 * (classes and instances). 
 * You can define the caching method by resource kind.
 * By default, the classes reference is cached in memory
 * and the instances are not cached
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package generisHard
 
 */
class ResourceReferencer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * single self instance
     *
     * @access private
     * @var ResourceReferencer
     */
    private static $_instance = null;

    /**
     * Short description of attribute CACHE_NONE
     *
     * @access public
     * @var int
     */
    const CACHE_NONE = 0;

    /**
     * Short description of attribute CACHE_MEMORY
     *
     * @access public
     * @var int
     */
    const CACHE_MEMORY = 1;

    /**
     * Short description of attribute CACHE_FILE
     *
     * @access public
     * @var int
     */
    const CACHE_FILE = 2;

    /**
     * Short description of attribute CACHE_DB
     *
     * @access public
     * @var int
     */
    const CACHE_DB = 3;

    /**
     * Short description of attribute cacheModes
     *
     * @access protected
     * @var array
     */
    protected $cacheModes = array();

    /**
     * Short description of attribute _classes
     *
     * @access private
     * @var mixed
     */
    private static $_classes = null;

    /**
     * Short description of attribute _resources
     *
     * @access private
     * @var array
     */
    private static $_resources = array();

    /**
     * Short description of attribute _resources_loaded
     *
     * @access private
     * @var boolean
     */
    private static $_resources_loaded = false;

    /**
     * Short description of attribute _properties
     *
     * @access private
     * @var mixed
     */
    private static $_properties = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    private function __construct()
    {
        
        
    	//default cache values
		$this->cacheModes = array(
			'instance' 	=> self::CACHE_NONE,
			'class'		=> self::CACHE_MEMORY,
			'property'	=> self::CACHE_FILE
		);
    	
        
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return ResourceReferencer
     */
    public static function singleton()
    {
        $returnValue = null;

        
        
        if (is_null(self::$_instance)){
			$class = __CLASS__;
        	self::$_instance = new $class();
        }
        $returnValue = self::$_instance;
        
        

        return $returnValue;
    }

    /**
     * Short description of method setCache
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string type
     * @param  int mode
     * @return mixed
     */
    protected function setCache($type, $mode)
    {
        
        
        if(!array_key_exists($type, $this->cacheModes)){
        	throw new Exception("Unknow cacheable object $type");
        }
        $refClass = new \ReflectionClass($this);
        if(!in_array($mode, $refClass->getConstants())){
        	throw new Exception("Unknow CACHE MODE $mode");
        }
        
        $this->cacheModes[$type] = $mode;
        
        
    }

    /**
     * Short description of method setClassCache
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  int mode
     * @return mixed
     */
    public function setClassCache($mode)
    {
        
    	
    	$this->setCache('class', $mode);
    	
        
    }

    /**
     * Short description of method setInstanceCache
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  int mode
     * @return mixed
     */
    public function setInstanceCache($mode)
    {
        
        
    	$this->setCache('instance', $mode);
    	
        
    }

    /**
     * Short description of method setPropertyCache
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  int mode
     * @return mixed
     */
    public function setPropertyCache($mode)
    {
        

    	$this->setCache('property', $mode);
    	
        
    }

    /**
     * Short description of method loadClasses
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  boolean force
     * @return mixed
     */
    private function loadClasses($force = false)
    {
        
        
    	if(is_null(self::$_classes) || $force){
			$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
			$result = $dbWrapper->query('SELECT "id", "uri", "table", "topclass" FROM "class_to_table"');

			self::$_classes = array();
			while ($row = $result->fetch()) {
	        	self::$_classes[$row['uri']] = array(
	        		'id'	=> $row['id'],
	        		'uri' 	=> $row['uri'],
	        		'table' => $row['table'],
	        		'topclass' => $row['topclass']
	        	);
	        }
	}
    	
        
    }

    /**
     * Short description of method isClassReferenced
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class class
     * @param  string table
     * @return boolean
     */
    public function isClassReferenced( \core_kernel_classes_Class $class, $table = null)
    {
        $returnValue = (bool) false;

        
        
        if(!is_null($class)){
			switch($this->cacheModes['class']){
				
				case self::CACHE_NONE:
					$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
					if(is_null($table)){

						$result = $dbWrapper->query('SELECT "id" FROM "class_to_table" WHERE "uri" = ?',array($class->getUri()));

					}
					else{
						$result = $dbWrapper->query('SELECT "id" FROM "class_to_table" WHERE "uri" = ? AND "table" = ?',array($class->getUri(), $table));

					}
					
					if($row = $result->fetch()){
						$returnValue = true;
					}
					break;
					
				case self::CACHE_MEMORY:
					
					$this->loadClasses();
					
						if(is_null($table)){
							foreach(self::$_classes as $aClass){
								if(isset($aClass['uri']) && $aClass['uri'] == $class->getUri() ){
									$returnValue = true;
									break;
								}
							}
						}
						else{
							foreach(self::$_classes as $aClass){
							if(isset($aClass['uri']) && $aClass['uri'] == $class->getUri() 
								&& isset($aClass['table']) && $aClass['table'] == $table){
								$returnValue = true;
								break;
							}
						}
					}
					
					break;
					
				default:
					throw new Exception("File and Db cache not yet implemented for classes");
					break;
					
			}
		}
        
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method referenceClass
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class class
     * @param  array options
     * @return boolean
     */
    public function referenceClass( \core_kernel_classes_Class $class, $options = array())
    {
        $returnValue = (bool) false;

        
        \common_Logger::d('Referencing: '.$class->getUri());
        
        // Get optional parameters
        $table = isset($options['table']) ? $options['table'] : '_'.Utils::getShortName($class);
        $topclass = isset($options['topclass']) ? $options['topclass'] : new \core_kernel_classes_Class(CLASS_GENERIS_RESOURCE);
        $additionalProperties = isset($options['additionalProperties']) ? $options['additionalProperties'] : array ();
        $classId = null;
        
        // Is the class is not already referenced
        if(!$this->isClassReferenced($class, $table)){
        	
        	$topclassUri = $topclass->getUri();
			$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
			
			$query = 'INSERT INTO "class_to_table" ("uri", "table", "topclass") VALUES (?,?,?)';
			$result = $dbWrapper->exec($query,array($class->getUri(), $table, $topclassUri));

			
			// Get last inserted id
			$query = 'SELECT "id" FROM "class_to_table" WHERE "uri" = ? AND "table" = ?';
			$result = $dbWrapper->query($query,array($class->getUri(), $table));
			
			
			if ($row = $result->fetch()){
				$classId = $row['id'];
				$result->closeCursor();
			} else {
				throw new Exception("Unable to retrieve the class Id of the referenced class {$class->getUri()}");
			}
			
			try{
				// Store additional properties
				if (!is_null($additionalProperties) && !empty($additionalProperties)){
					$query = 'INSERT INTO "class_additional_properties" ("class_id", "property_uri") VALUES';
					foreach ($additionalProperties as $additionalProperty){
						$query .= " ('{$classId}', '{$additionalProperty->getUri()}')";
					}
					$result = $dbWrapper->exec($query);
				} 		
				
				
				if($result !== false){
					
					$returnValue = true;
					if($this->cacheModes['class'] == self::CACHE_MEMORY && !is_null(self::$_classes)){
						$memQuery = 'SELECT "id", "uri", "table", "topclass" 
							FROM "class_to_table" 
							WHERE "uri" = ? 
							AND "table" = ?';
						$memResult = $dbWrapper->query($memQuery, array($class->getUri(), $table));
						while($row = $memResult->fetch()){
							self::$_classes[$row['uri']] = array(
				        		'id'		=> $row['id'],
				        		'uri' 		=> $row['uri'],
				        		'table' 	=> $row['table'],
								'topclass' 	=> $row['topclass']
				        	);
						}
					}
				}
			}
			catch (\PDOException $e){
				throw new \Exception("Unable to reference the additional properties of the class {$class->getUri()} in class_additional_properties: " . $e->getMessage());
			}
		}

        

        return (bool) $returnValue;
    }

    /**
     * Short description of method unReferenceClass
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class class
     * @return boolean
     */
    public function unReferenceClass( \core_kernel_classes_Class $class)
    {
        $returnValue = (bool) false;

        
        
        if($this->isClassReferenced($class)){
                
			$tableName = '_'.Utils::getShortName($class);
                        
            //need to instanciate table manager before unreferencing otherwise, the "remove table" will fail
            $tm = new TableManager($tableName);
                        
            // Delete reference of the class in classs_to_table, resource_has_class, resource_to_table
			$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
                        
			// Remove references of the resources in the resource has class table
            $queries = array();
			$queries[] = 'DELETE 
				FROM "resource_has_class" 
				WHERE "resource_has_class"."resource_id" 
					IN (SELECT "resource_to_table"."id" FROM "resource_to_table" WHERE "resource_to_table"."table" = \''.$tableName.'\' );';
			// Remove reference of the class in the additional properties tables
			$queries[] = 'DELETE 
				FROM "class_additional_properties"
				WHERE "class_id" 
					IN (SELECT "class_to_table"."id" FROM "class_to_table" WHERE "class_to_table"."table" = \''.$tableName.'\' );';
			// Remove resferences of the resources int resource to table table
			$queries[] = 'DELETE FROM "resource_to_table" WHERE "resource_to_table"."table" = \''.$tableName.'\';';
			// Remove reference of the class in the class to table table
			$queries[] = 'DELETE FROM "class_to_table" WHERE "class_to_table"."table" = \''.$tableName.'\';';
			
			$returnValue = true;
			
			try{
				foreach ($queries as $query){
					$result = $dbWrapper->exec($query);
					
					if ($result === false){
						$returnValue = false;
					}
				}
	                        
				if($returnValue !== false){
					// delete table associated to the class
					$tm->remove();
					// remove class from the cache
					if($this->cacheModes['class'] == self::CACHE_MEMORY && is_array(self::$_classes)){
						foreach(self::$_classes as $index => $aClass){
							if($aClass['uri'] == $class->getUri()){
								unset(self::$_classes[$index]);
							}
						}
					}
				}
				
				ClassProxy::$ressourcesDelegatedTo = array();
				ResourceProxy::$ressourcesDelegatedTo = array();
				PropertyProxy::$ressourcesDelegatedTo = array();
			}
			catch (\PDOException $e){
				throw new Exception("Unable to unreference class {$class->getUri()} : " .$e->getMessage());
			}
		}
        
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method classLocations
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class class
     * @return array
     */
    public function classLocations( \core_kernel_classes_Class $class)
    {
        $returnValue = array();

        
        
        if(!is_null($class)){
			switch($this->cacheModes['class']){
				
				case self::CACHE_NONE:
			        $dbWrapper = \core_kernel_classes_DbWrapper::singleton();
			        
			        $query = "SELECT id, uri, table, topclass FROM class_to_table WHERE uri=? ";
			    	$result = $dbWrapper->query($query, array ($class->getUri()));

					while($row = $result->fetch()){
						$returnValue[$row['uri']] = array(
							'id'	=> $row['id'],
			        		'uri' 	=> $row['uri'],
			        		'table' => $row['table'],
			        		'topclass' => $row['topclass']
						);
					}
			        break;
			
			   case self::CACHE_MEMORY:
			   		$this->loadClasses();
			   		foreach( self::$_classes as $key =>  $res){
						if($res['uri'] == $class->getUri()){
							$returnValue[] = $res;
						}
					}
			   break;
			}
		}
		
        

        return (array) $returnValue;
    }

    /**
     * Short description of method loadResources
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  boolean force
     * @return mixed
     */
    private function loadResources($force = false)
    {
        
        
    	if(!self::$_resources_loaded || $force){
			$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
			$result = $dbWrapper->query('SELECT "uri", "table" FROM "resource_to_table"');
			while ($row = $result->fetch()) {
	        	self::$_resources[$row['uri']] = $row['table'];
	        }
	        self::$_resources_loaded = true;
		}
    	
    	
        
    }

    /**
     * Short description of method isResourceReferenced
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function isResourceReferenced( \core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        
        
        if(!is_null($resource)){
			switch($this->cacheModes['instance']){
				
				case self::CACHE_NONE:
					if(array_key_exists($resource->getUri(), self::$_resources)){
						$returnValue = true;
						break;
					}
					
					$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
					$result = $dbWrapper->query('SELECT "table" FROM "resource_to_table" WHERE "uri" = ?', array($resource->getUri()));
					$fetch = $result->fetchAll();
					if(count($fetch) > 0){
						self::$_resources[$resource->getUri()] = $fetch[0]['table'];
						$returnValue = true;
					}	
					
					$result->closeCursor();
				break;
					
				case self::CACHE_MEMORY:
					
					$this->loadResources();
					$returnValue = array_key_exists($resource->getUri(), self::$_resources);
					break;
					
				default:
					throw new Exception("File and Db cache not yet implemented for resources");
					break;
			}
		}
        
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method referenceResource
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  string table
     * @param  array types
     * @param  boolean referenceClassLink
     * @return boolean
     */
    public function referenceResource( \core_kernel_classes_Resource $resource, $table, $types = null, $referenceClassLink = false)
    {
        $returnValue = (bool) false;

        
        $types = !is_null($types) ? $types : $resource->getTypes();
        $rows = array ();
        if(!$this->isResourceReferenced($resource)){
			$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
			
			$query = 'INSERT INTO "resource_to_table" ("uri", "table") VALUES (?,?)';
			$insertResult = $dbWrapper->exec($query, array($resource->getUri(), $table));

			if($referenceClassLink && $insertResult !== false){
				$query = 'SELECT * FROM "resource_to_table" WHERE "uri" = ? AND "table" = ?';
				$result = $dbWrapper->query($query, array($resource->getUri(), $table));
				while($row = $result->fetch(\PDO::FETCH_ASSOC)){
					$rows[] = $row;
				}
			}
			$returnValue = (bool) $insertResult;
			
        	if($referenceClassLink){
        		
				foreach($types as $type){
					
					$typeClass = new \core_kernel_classes_Class($type->getUri());
					if($this->isClassReferenced($typeClass)){
						
						$classLocations = $this->classLocations($typeClass);
						foreach ($classLocations as $classLocation){
							
							foreach($rows as $row){
								$query = "INSERT INTO resource_has_class (resource_id, class_id) VALUES (?,?)";
								$sth = $dbWrapper->exec($query,array($row['id'], $classLocation['id']));

							}
						}
					}
				}
			}
			if($returnValue){
				foreach($rows as $row){
					self::$_resources[$row['uri']] = $row['table'];
				}
			}
        }
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method unReferenceResource
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function unReferenceResource( \core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        
        
        if($this->isResourceReferenced($resource)){
                
                $dbWrapper = \core_kernel_classes_DbWrapper::singleton();

                //select id to be removed:
                $resourceId = Utils::getResourceIdByTable($resource, 'resource_to_table');
                if($resourceId){
                        $queries[] = 'DELETE FROM "resource_has_class" WHERE "resource_has_class"."resource_id" = \'' . $resourceId . '\';';
                        $queries[] = 'DELETE FROM "resource_to_table" WHERE "resource_to_table"."id" = \'' . $resourceId . '\';';

                        $returnValue = true;
                        foreach ($queries as $query) {
                                $result = $dbWrapper->exec($query);

                                if ($result === false) {
                                        $returnValue = false;
                                }
                        }

                        if ($returnValue !== false) {
                                if (array_key_exists($resource->getUri(), self::$_resources)) {
                                        unset(self::$_resources[$resource->getUri()]);
                                }
                        }
                }

        }
        
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method resourceLocation
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return string
     */
    public function resourceLocation( \core_kernel_classes_Resource $resource)
    {
        $returnValue = (string) '';

        
        
         if(!is_null($resource)){
			switch($this->cacheModes['instance']){
				
				case self::CACHE_NONE:
					if(array_key_exists($resource->getUri(), self::$_resources)){
						$returnValue = self::$_resources[$resource->getUri()];
						break;
					}
					
			        $dbWrapper = \core_kernel_classes_DbWrapper::singleton();
			        
			        $query = 'SELECT "table" FROM "resource_to_table" WHERE "uri"=?';
			    	$result = $dbWrapper->query($query, array ($resource->getUri()));

					if ($row = $result->fetch()){
						$returnValue = $row['table'];
						self::$_resources[$resource->getUri()] = $row['table'];
						$result->closeCursor();
					} else {
						\common_Logger::w("Unable to find table for ressource " .$resource->getUri(), "GENERIS");
					}
					
			        break;
			
			   case self::CACHE_MEMORY:
			   		$this->loadResources();
			   		if(array_key_exists($resource->getUri(), self::$_resources)){
						$returnValue = self::$_resources[$resource->getUri()];
						break;
					}
			   break;
			   default:
					\common_Logger::w('Unexpected cacheMode: '.$this->cacheModes['instance'], array('GENERIS'));
			}
		}
        

        return (string) $returnValue;
    }

    /**
     * Short description of method loadProperties
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  boolean force
     * @param  array additionalProperties
     * @return mixed
     */
    private function loadProperties($force = false, $additionalProperties = array())
    {
        
    	if(is_null(self::$_properties) || $force){
				
    		if(!$force && $this->cacheModes['property'] == self::CACHE_FILE){
    			
    			$serial = 'hard-api-property';
    			
    			try{
    				$cache = \common_cache_FileCache::singleton();
    				$properties = $cache->get($serial);
    				self::$_properties = $properties;
    			}
    			catch (\common_cache_NotFoundException $e){
    				// The cache cannot be accessed, build the property cache.
    				// get all the compiled tables
    				$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
    				$tables = array();
    				$query = 'SELECT DISTINCT "id","table" FROM "class_to_table"';
    				$result = $dbWrapper->query($query);
    				while($row = $result->fetch()){
    					$tables[$row['id']] = $row['table'];
    				}
    				
    				$additionalPropertiesTable = array();
    				$query = 'SELECT DISTINCT "class_id","property_uri" FROM "class_additional_properties"';
    				$result = $dbWrapper->query($query);
    				while($row = $result->fetch()){
    					$additionalPropertiesTable[$row['class_id']][] = new \core_kernel_classes_Property($row['property_uri']);
    				}
    				//retrieve each property by table
    				$this->loadClasses();
    				 
    				self::$_properties = array();
    				
    				foreach($tables as $classId => $table){
    				
    					//check in $additionalPropertiesTable if current table is concerned by additionnal properties
    					if(isset($additionalPropertiesTable[$classId])){
    						$additionalProperties = $additionalPropertiesTable[$classId];
    				
    					}
    					else{
    						$additionalProperties = array();
    					}
    					 
    					$classUri = Utils::getLongName($table);
    					$class = new \core_kernel_classes_Class($classUri);
    					$topclassUri = self::$_classes[$classUri]['topclass'];
    					$topclass = new \core_kernel_classes_Class($topclassUri);
    					$ps = new PropertySwitcher($class, $topclass);
    					$properties = $ps->getProperties($additionalProperties);
    					foreach ($properties as $property){
    						$propertyUri = $property->getUri();
    						if ($property->isMultiple() || $property->isLgDependent()){
    							
    							if(isset(self::$_properties[$propertyUri])) {
    								if (!in_array("{$table}props", self::$_properties[$propertyUri])){
    									self::$_properties[$propertyUri][] = "{$table}props";
    								}
    							} else {
    								self::$_properties[$propertyUri] = array("{$table}props");
    							}
    				
    						} else {
    							if(isset(self::$_properties[$propertyUri])) {
    								if (!in_array("{$table}", self::$_properties[$propertyUri])){
    									self::$_properties[$propertyUri][] = "{$table}";
    								}
    							} else {
    								self::$_properties[$propertyUri] = array("{$table}");
    							}
    						}
    					}
    				}
    				
    				//saving the properties in the cache file

    				
    				try{
    					$cache = \common_cache_FileCache::singleton();
    					$cache->put(self::$_properties, $serial);
    				}
    				catch (\common_cache_Exception $e){
    					throw new Exception("cannot write the required property cache file for serial '${serial}'.");
    				}
    			}
    		}
    	}
    	
        
    }

    /**
     * Short description of method isPropertyReferenced
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Property property
     * @param  inClass
     * @return boolean
     */
    public function isPropertyReferenced( \core_kernel_classes_Property $property, $inClass = null)
    {
        $returnValue = (bool) false;

        
        
        if(!is_null($property)){
			switch($this->cacheModes['property']){
				
				case self::CACHE_FILE:
				case self::CACHE_MEMORY:
					$this->loadProperties();
					if(!empty($inClass)){
						$propertyLocation = $this->propertyLocation($property);
						if(!empty($propertyLocation)){
							if($inClass instanceof \core_kernel_classes_Class){
								$classLocations = $this->classLocations($inClass);
								foreach($classLocations as $classTableData){
									if(in_array($classTableData['table'], $propertyLocation) ){
										$returnValue = true;
										break;
									}
								}
							}else if(is_string($inClass)){
								if(in_array((string) $inClass, $propertyLocation) ){
									$returnValue = true;
									break;
								}
							}
						}
					}else{
						$returnValue = array_key_exists($property->getUri(), self::$_properties);
					}
					break;
					
				case self::CACHE_NONE:
					throw new Exception("Property are always cached");
				case self::CACHE_DB:
					throw new Exception("Db cache not yet implemented for classes");
			}
		}
        
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method propertyLocation
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Property property
     * @return array
     */
    public function propertyLocation( \core_kernel_classes_Property $property)
    {
        $returnValue = array();

        
        
        if(!is_null($property)){
			switch($this->cacheModes['property']){
				
				case self::CACHE_FILE:
				case self::CACHE_MEMORY:
					
					$this->loadProperties();
					if(isset(self::$_properties[$property->getUri()]) && is_array(self::$_properties[$property->getUri()])){
						$returnValue = self::$_properties[$property->getUri()];
					}
					break;
				default:
					throw new \common_Exception('Unexpected cache-mode '.$this->cacheModes['property'].' for propertyLocation()');
			}
		}
        
        

        return (array) $returnValue;
    }

    /**
     * Short description of method referenceInstanceTypes
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class class
     * @return boolean
     */
    public function referenceInstanceTypes( \core_kernel_classes_Class $class)
    {
        $returnValue = (bool) false;

        
        
        $dbWrapper = \core_kernel_classes_DbWrapper::singleton();
        
        $query = "SELECT DISTINCT object FROM statements 
        			WHERE predicate = ? 
        			AND object != ?
         			AND subject IN (SELECT subject FROM statements 
        						WHERE predicate = ? 
        						AND object = ?)";
        $result = $dbWrapper->query($query,array(RDF_TYPE, $class->getUri(), RDF_TYPE, $class->getUri()));



		$types = array();
        while($row = $result->fetch()){
        	$types[] = $row['object'];
        }
        
        $tableName = '_'.Utils::getShortName($class);
        
        foreach($types as $type){
        	$this->referenceClass(new \core_kernel_classes_Class($type), array ("table"=>$tableName));
        }
        
        

        return (bool) $returnValue;
    }

    /**
     * please use clearCaches() instead
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @deprecated
     * @param  array additionalProperties
     * @return mixed
     */
    public function resetCache($additionalProperties = array())
    {
        
    	$this->loadClasses(true);
        $this->loadProperties(true, $additionalProperties);
        
    }

    /**
     * Clears the caches without immediately recalculating them
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function clearCaches()
    {
        
    	self::$_properties	= null;
    	
    	self::$_classes		= null;
    	
    	self::$_resources			= array();
    	self::$_resources_loaded	= false;
    	ClassProxy::$ressourcesDelegatedTo = array();
    	ResourceProxy::$ressourcesDelegatedTo = array();
    	PropertyProxy::$ressourcesDelegatedTo = array();
    	
    	// remove hard-api-property cache.
    	$cache = \common_cache_FileCache::singleton();
    	$cache->remove('hard-api-property');
        
    }

    /**
     * Get additional properties used during class' compilation.
     * This function is usefull specially during unhardening
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class clazz
     * @return array
     */
    public function getAdditionalProperties( \core_kernel_classes_Class $clazz)
    {
        $returnValue = array();

        
        
        $dbWrapper = \core_kernel_classes_DbWrapper::singleton();
        
		$query = "SELECT property_uri 
			FROM class_additional_properties, class_to_table 
			WHERE class_additional_properties.class_id = class_to_table.id
			AND class_to_table.uri = ?";
		$result = $dbWrapper->query($query, array($clazz->getUri()));
		
   		while($row = $result->fetch()){
			$returnValue[] = new \core_kernel_classes_Property($row['property_uri']);
		}
        
        

        return (array) $returnValue;
    }

}