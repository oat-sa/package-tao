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

namespace oat\generisHard\models\switcher;

use oat\generisHard\models\proxy\ClassProxy;
use oat\generisHard\models\proxy\ResourceProxy;
use oat\generisHard\models\proxy\PropertyProxy;
use oat\generisHard\models\proxy\PersistenceProxy;
use oat\generisHard\models\hardapi\ResourceReferencer;
use oat\generisHard\models\hardapi\Utils;
use oat\generisHard\models\hardapi\TableManager;
use oat\generisHard\models\hardapi\Exception;
use oat\generisHard\models\hardapi\RowManager;
use core_kernel_persistence_smoothsql_SmoothModel;

/**
 * The Switcher class aims at providing a programming interface to:
 * 
 * - Hardify a specific class.
 * - Unhardify a specific class.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package generisHard
 */
class Switcher
{
	// --- ASSOCIATIONS ---


	// --- ATTRIBUTES ---

	/**
	 * The list of classes that must never be compiled.
	 * 
	 * @var array
	 */
	private static $blackList = array();
	
	/**
	 * The list of classes that were compiled during the last call of the
	 * hardify method.
	 * 
	 * @var array
	 */
	private $hardenedClasses = array();
	
	/**
	 * The list of classes that were decompiled during the last call of the
	 * unhardify method.
	 * 
	 * @var array
	 */
	private $decompiledClasses = array();

	// --- OPERATIONS ---

	/**
	 * Creates a new instance of Switcher. 
	 * 
	 * @access public
	 * @param array $blackList An array of URIs (as strings) that must never be implied in compiling.
	 */
	public function __construct($blackList = array()){
		self::$blackList = array_merge(array(RDFS_CLASS, RDFS_MEMBER, RDF_PROPERTY), $blackList);
	}

	/**
	 * Behaviour to adopt when an instancer of Switcher is destroyed by PHP.
	 * 
	 * @access public
	 */
	public function __destruct(){
	    ClassProxy::$ressourcesDelegatedTo = array();
		ResourceProxy::$ressourcesDelegatedTo = array();
		PropertyProxy::$ressourcesDelegatedTo = array();
	}

	/**
	 * Count the amount of RDF statements in the statements table.
	 * 
	 * @access public
	 * @return int
	 */
	private function countStatements (){
		$query =  "SELECT count(*) FROM statements";
		$result = \core_kernel_classes_DbWrapper::singleton()->query($query);
		$row = $result->fetch();
		return $row[0];
	}

	/**
	 * Unhardify a specific class. Unhardifying a class implies that the instances of this
	 * class will be transfered from specific optimized tables to the statement table, as RDF
	 * triples.
	 * 
	 * The $options array is an associative array where values are all booleans. The keys that
	 * can be used to pass specific unhardify options are the following:
	 * 
	 * - recursive: Unhardify the target class and its subclasses (default: false).
	 *
	 * @access public
	 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
	 * @param  \core_kernel_classes_Class class
	 * @param  array options
	 * @return boolean true if the resource was correctly unhardified, false otherwise.
	 */
	public function unhardify (\core_kernel_classes_Class $class, $options = array ()) {

		$returnValue = (bool) false;
        
        $classLabel = $class->getLabel();
        \common_Logger::i("Unhardifying class ${classLabel}", 'GENERIS');
        
		if (defined ("DEBUG_PERSISTENCE") && DEBUG_PERSISTENCE){
			var_dump('unhardify '.$class->getUri());
		}

		// Check if the class has been hardened
		if (!ResourceReferencer::singleton()->isClassReferenced($class)){
			\common_Logger::w("Class ${classLabel} could not be unhardened because it is not hardified.");
			return false;
		}

		//if defined, we take all the properties of the class and it's parents till the topclass
		$classLocations = ResourceReferencer::singleton()->classLocations($class);
		$topclass = null;
		if (count($classLocations)>1){
			throw new Exception("Try to unhardify the class {$class->getUri()} which has multiple locations");
		}
		else {
			$topclass = new \core_kernel_classes_Class($classLocations[0]['topclass']);
		}

		//recursive will unhardify the class and it's subclasses in the same table!
		(isset($options['recursive'])) ? $recursive = $options['recursive'] : $recursive = false;

		//removeForeigns will unhardify the class that are range of the properties
		(isset($options['removeForeigns'])) ? $removeForeigns = $options['removeForeigns'] : $removeForeigns = false;

		//rmSources will remove the related data from the hard data after
		//transfer to the smooth data. 
		$rmSources = true;

		// Get class' properties
		$propertySwitcher = new PropertySwitcher($class);
		$additionalProperties = array();
		$properties = $propertySwitcher->getProperties($additionalProperties);
		$columns = $propertySwitcher->getTableColumns($additionalProperties, self::$blackList);

		// Get all instances of this class
		$startIndex = 0;
		$instancePackSize = 100;
		$instances = $class->getInstances(false, array('offset'=>$startIndex, 'limit'=> $instancePackSize));
		$count = count($instances);
		$existingInstances = array ();
		do{
			//reset timeout:
			\helpers_TimeOutHelper::setTimeOutLimit(\helpers_TimeOutHelper::MEDIUM);

			// lionel did that :d le salop
			PersistenceProxy::forceMode(PERSISTENCE_SMOOTH);
			foreach ($instances as $uri => $instance){
				if ($instance->exists()){
					PersistenceProxy::forceMode(PERSISTENCE_HARD);
					$instance->delete();
					PersistenceProxy::restoreImplementation();
					unset($instances[$uri]);
					$existingInstances[] = $uri;
				}
			}
			PersistenceProxy::restoreImplementation();
			
			
			foreach ($instances as $instance) {

				// Get table name where the resource is located
				$tableName = ResourceReferencer::singleton()->resourceLocation($instance);

				// Get Instance type
				$types = $instance->getTypes();

				// Create instance in the smooth implementation
				PersistenceProxy::forceMode(PERSISTENCE_SMOOTH);
				$class->createInstance('', '', $instance->getUri());
				// set types to the newly created instance
				foreach ($types as $type) {

					if (!$type->equals($class)) {
						$instance->setType($type);
					}
				}
				PersistenceProxy::restoreImplementation();

				// Export properties of the instance
				foreach ($columns as $column) {

					$property = new \core_kernel_classes_Property(Utils::getLongName($column['name']));
					// Multiple property
					if (isset($column['multi']) && $column['multi']) {

						$sqlQuery = 'SELECT
								"'.$tableName.'props"."property_value",
								"'.$tableName.'props"."property_foreign_uri", 
								"'.$tableName.'props"."l_language" 
							FROM "'.$tableName.'props"
							LEFT JOIN "'.$tableName.'" ON "'.$tableName.'"."id" = "'.$tableName.'props"."instance_id"
							WHERE "'.$tableName.'"."uri" = ? 
								AND "'.$tableName.'props"."property_uri" = ?';
						$dbWrapper = \core_kernel_classes_DbWrapper::singleton();
						$sqlResult = $dbWrapper->query($sqlQuery, array(
						$instance->getUri(),
						$property->getUri()
						));
						if ($sqlResult->errorCode() !== '00000') {
							throw new Exception("unable to unhardify : " . $dbWrapper->errorMessage());
						}

						// ENTER IN SMOOTH SQL MODE
						PersistenceProxy::forceMode(PERSISTENCE_SMOOTH);

						while ($row = $sqlResult->fetch()) {
							$value = null;
							if (!empty($row['property_value'])) {
								$value = $row['property_value'];
							} else {
								$value = $row['property_foreign_uri'];
							}

							$lg = $row['l_language'];
							if (!empty($lg)) {
								$instance->setPropertyValueByLg($property, $value, $lg);
							} else {
								$instance->setPropertyValue($property, $value);
							}
						}
						/// EXIT HARD SQL MODE
						PersistenceProxy::restoreImplementation();
					}
					// Single property
					else {
						$value = $instance->getOnePropertyValue($property);
						if ($value != null) {
							PersistenceProxy::forceMode(PERSISTENCE_SMOOTH);
							$instance->setPropertyValue($property, $value);
							PersistenceProxy::restoreImplementation();
						}
					}
				}
				
				// delete instance in the hard implementation
				$instance->delete();
			}

			//record decompiled instances number
			if(isset($this->decompiledClasses[$class->getUri()])){
				$this->decompiledClasses[$class->getUri()] += $count;
			}else{
				$this->decompiledClasses[$class->getUri()] = $count;
			}

			//update instance array and count value
			$instances = $class->getInstances(false, array('offset'=>$startIndex, 'limit'=> $instancePackSize));
			foreach ($existingInstances as $uri){
				unset($instances[$uri]);
			}

			$count = count($instances);
			\helpers_TimeOutHelper::reset();
		}while($count > 0);

		// Unreference the class
		$returnValue = ResourceReferencer::singleton()->unReferenceClass($class);

		// If recursive, treat the subclasses
		if($recursive){

			foreach($class->getSubClasses(true) as $subClass){
				if (ResourceReferencer::singleton()->isClassReferenced($subClass)){
					$returnValue = $this->unhardify($subClass, $options);
				}
			}
		}

		return (bool) $returnValue;
	}


	public static $debug_tables = array();
	protected $foreignPropertiesWaitingList = array();

	/**
	 * Calling this method will transfer all instances of $class from the statements table
	 * to specific optimized relational tables.
	 * 
	 * During optimization, the current user has all privileges on the persistent memory. At
	 * the end of the process, the old privileges will be set back.
	 * 
	 * The $options array can contain the following key => values (all booleans):
	 * 
	 * - recursive: compile the target class and its subclasses (default: false).
	 * - append: append data to the existing optimized table if it already exists (default: false).
	 * - rmSources: remove the triples in the statement table after transfer (default: true).
	 *
	 * @access public
	 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
	 * @param  \core_kernel_classes_Class class
	 * @param  array options
	 * @return boolean Will return true if it succeeds, false otherwise.
	 */
	public function hardify( \core_kernel_classes_Class $class, $options = array())
	{
		$returnValue = (bool) false;
		$session = \core_kernel_classes_Session::singleton();
		$oldUpdatableModels = core_kernel_persistence_smoothsql_SmoothModel::getUpdatableModelIds();
		
		try{
			// Give access to all models during hardification.
			core_kernel_persistence_smoothsql_SmoothModel::forceUpdatableModelIds(self::getAllModelIds());
			
			$classLabel = $class->getLabel();
			\common_Logger::i("Hardifying class ${classLabel}", array("GENERIS"));
			
			if (defined ("DEBUG_PERSISTENCE") && DEBUG_PERSISTENCE){
				if (in_array($class->getUri(), self::$debug_tables)){
					return;
				}
				\common_Logger::d('hardify ' .$class->getUri());
				self::$debug_tables[] = $class->getUri();
				$countStatement = $this->countStatements();
			}
			
			if(in_array($class->getUri(), self::$blackList)){
				return $returnValue;
			}
			
			// ENTER IN SMOOTH SQL MODE
			PersistenceProxy::forceMode(PERSISTENCE_SMOOTH);
			
			//recursive will hardify the class and it's subclasses in the same table!
			(isset($options['recursive'])) ? $recursive = $options['recursive'] : $recursive = false;
			
			//createForeigns will hardify the class that are range of the properties
			(isset($options['createForeigns'])) ? $createForeigns = $options['createForeigns'] : $createForeigns = false;
			
			//check if we append the data in case the hard table exists or truncate the table and add the new rows
			(isset($options['append'])) ? $append = $options['append'] : $append = false;
			
			//if true, the instances of the class will  be removed from the statements table!
			(isset($options['rmSources'])) ? $rmSources = (bool) $options['rmSources'] : $rmSources = false;
			
			//if defined, we took all the properties of the class and it's parents till the topclass
			(isset($options['topclass'])) ? $topclass = $options['topclass'] : $topclass = new \core_kernel_classes_Class(RDFS_RESOURCE);
			
			//if defined, compile the additional properties
			(isset($options['additionalProperties'])) ? $additionalProperties = $options['additionalProperties'] : $additionalProperties = array();
			
			//if defined, reference the additional class to the table
			(isset($options['referencesAllTypes'])) ? $referencesAllTypes = $options['referencesAllTypes'] : $referencesAllTypes = false;
			
			$tableName = '_'.Utils::getShortName($class);
			$myTableMgr = new TableManager($tableName);
			
			$referencer = ResourceReferencer::singleton();
			
			//get the table columns from the class properties
			$columns = array();
			$ps = new PropertySwitcher($class);
			$properties = $ps->getProperties($additionalProperties);
			$columns = $ps->getTableColumns($additionalProperties, self::$blackList);
			
			//init the count value in hardened classes:
			if(isset($this->hardenedClasses[$class->getUri()])){
				PersistenceProxy::restoreImplementation();
				return true;//already being compiled
			}else{
				$this->hardenedClasses[$class->getUri()] = 0;
			}
			
			if(!$append || ($append && !$myTableMgr->exists())){
			
				//create the table
				if($myTableMgr->exists()){
					$myTableMgr->remove();
				}
				$myTableMgr->create($columns);
			
				//reference the class
				$referencer->referenceClass($class, array (
						"topclass" 				=> $topclass,
						"additionalProperties" 	=> $additionalProperties
				));
			
				if($referencesAllTypes){
					$referencer->referenceInstanceTypes($class);
				}
			}
			
			//insert the resources
			$startIndex = 0;
			$instancePackSize = 100;
			$instances = $class->getInstances(false, array('offset'=>$startIndex, 'limit'=> $instancePackSize));
			$count = count($instances);
			$notDeletedInstances = array ();
			do{
				//reset timeout:
				//set_time_limit(30);
			    \helpers_TimeOutHelper::setTimeOutLimit(\helpers_TimeOutHelper::MEDIUM);
			
				$rows = array();
			
				foreach($instances as $index =>  $resource){
					if($referencer->isResourceReferenced($resource)){
						PersistenceProxy::forceMode(PERSISTENCE_HARD);
						$resource->delete();
						PersistenceProxy::restoreImplementation();
					}
					$row = array('uri' => $resource->getUri());
					foreach($properties as $property){
						$propValue = $resource->getOnePropertyValue($property);
						$row[Utils::getShortName($property)] = $propValue;
					}
			
					$rows[] = $row;
				}
			
				$rowMgr = new RowManager($tableName, $columns);
				$rowMgr->insertRows($rows);
				foreach($instances as $resource){
					$referencer->referenceResource($resource, $tableName, null, true);
			
					if($rmSources){
						//remove exported resources in smooth sql, if required:
						// Be carefull, the resource can still exist even if
						// delete returns true. Indeed, modelIds can be mixed between
						// multiple models and only a part of the triples that consitute
						// the resource might have been deleted.
						if (!$resource->delete() || $resource->exists()){//@TODO : modified resource::delete() because resource not in local modelId cannot be deleted
							$notDeletedInstances[] = $resource->getUri();
							$startIndex++;
						}
					}
				}
			
				if(!$rmSources){
					//increment start index only if not removed
					$startIndex += $instancePackSize;
				}
			
				//record hardened instances number
				if(isset($this->hardenedClasses[$class->getUri()])){
					$this->hardenedClasses[$class->getUri()] += $count;
				}else{
					$this->hardenedClasses[$class->getUri()] = $count;
				}
			
				//update instance array and count value
				$instances = $class->getInstances(false, array('offset'=>$startIndex, 'limit'=> $instancePackSize));
				foreach($notDeletedInstances as $uri){
					unset($instances[$uri]);
				}
			
				$count = count($instances);
				\helpers_TimeOutHelper::reset();
			} while($count> 0);
			
			$returnValue = true;
			
			// Treat subclasses of the current class
			if($recursive){
				foreach($class->getSubClasses(true) as $subClass){
					$returnValue = $this->hardify($subClass, array_merge($options, array(
							'recursive' 	=> false,
							'append' 	=> true
					)));
				}
			}
			
			//reset cache:
			$referencer->clearCaches();
			// EXIT SMOOTH SQL MODE
			PersistenceProxy::restoreImplementation();
			
			if (defined ("DEBUG_PERSISTENCE") && DEBUG_PERSISTENCE){
				$this->unhardify($class, array_merge($options, array(
						'recursive' 		=> false,
						'removeForeigns' 	=> false
				)));
				\common_Logger::d('unhardened result statements '.$this->countStatements(). ' / '.$countStatement);
			}
			
			// Give the normal rights on models to the session.
			core_kernel_persistence_smoothsql_SmoothModel::forceUpdatableModelIds($oldUpdatableModels);
		}
		catch (Exception $e){
			\common_Logger::e('An error occured during hardification: ' . $e->getMessage());
			core_kernel_persistence_smoothsql_SmoothModel::forceUpdatableModelIds($oldUpdatableModels);
		}

		return (bool) $returnValue;
	}

	/**
	 * Returns the classes that were successfully compiled during the last call of the
	 * hardify method.
	 * 
	 * @access public
	 * @return array
	 */
	public function getHardenedClasses(){
		return $this->hardenedClasses;
	}

	/**
	 * Returns the classes that were successfully decompiled during the last call of the
	 * unhardify method.
	 * 
	 * @access public
	 * @return array
	 */
	public function getDecompiledClasses(){
		return $this->decompiledClasses;
	}

	/**
	 * Will create an Index (in the RDBMS) for all the promperties passed as
	 * a parameter. This may increase the performance of the system.
	 * 
	 * @static
	 * @access public
	 * @param array $indexProperties
	 * @throws Exception
	 * @return boolean If it succeeds, false otherwise.
	 */
	public static function createIndex($indexProperties = array()){

		$referencer = ResourceReferencer::singleton();
		$dbWrapper = \core_kernel_classes_DbWrapper::singleton();

		foreach($indexProperties as $indexProperty){
			$property = new \core_kernel_classes_Property($indexProperty);
			$propertyAlias = Utils::getShortName($property);
			foreach($referencer->propertyLocation($property) as $table){
				if(!preg_match("/props$/", $table) && preg_match("/^_[0-9]{2,}/", $table)){
					try{
					    $indexName = 'idx_'.$table.'_'.$propertyAlias;
					    if(strlen($indexName) > 64 ){
					        $md5 = md5($indexName);
					        $indexName = substr($indexName,0,30) . $md5;
					    }
					    $dbWrapper->createIndex($indexName, $dbWrapper->quoteIdentifier($table), array($dbWrapper->quoteIdentifier($propertyAlias) => 255));
					}
					catch (\Exception $e){
						if($e->getCode() != $dbWrapper->getIndexAlreadyExistsErrorCode() && $e->getCode() != '00000'){
							throw new Exception("Unable to create index 'idx_${propertyAlias}' for property alias '${propertyAlias}' on table '${table}': {$e->getMessage()}");
						}
						else {
						    \common_Logger::e('something go wrong when creating index idx_' . $propertyAlias . ' on table ' . $table . ' : '.$e->getMessage() );
						}
					}
				}
			}
		}

		return true;

	}
	
	/**
	 * Returns the ids of all models
	 * 
	 * @return array
	 */
	private static function getAllModelIds(){
		$nsManager = \common_ext_NamespaceManager::singleton();
		
		$modelIds = array();
		foreach ($nsManager->getAllNamespaces() as $m){
			$modelIds[] = $m->getModelId();
		}
		
		return $modelIds;
	}
}