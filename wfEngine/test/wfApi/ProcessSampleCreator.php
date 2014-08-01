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
 * Copyright (c) 2007-2010 (original work) Public Research Centre Henri Tudor & University of Luxembourg) (under the project TAO-QUAL);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
include_once dirname(__FILE__) . '/../../includes/raw_start.php';

define('PROPERTY_IS_SAMPLE', LOCAL_NAMESPACE.'#isSample');

class ProcessSampleCreator{
	
	//created resources:
	protected static $processes = array();
	protected static $variables = array();
	protected static $roles = array();
	protected static $users = array();
	protected static $propertyIsSample = null;
	
	protected $activityService = null;
	protected $connectorService = null;
	protected $processVariableService = null;
	protected $authoringService = null;
	protected $activityExecutionService = null;
	
	public function __construct(){
		
		//init services
		$this->activityService = wfEngine_models_classes_ActivityService::singleton();
		$this->processVariableService = wfEngine_models_classes_VariableService::singleton();
		$this->authoringService = wfAuthoring_models_classes_ProcessService::singleton();
		$this->activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();
		$this->connectorService = wfEngine_models_classes_ConnectorService::singleton();
		$this->processVariablesClass = new core_kernel_classes_Class(CLASS_PROCESSVARIABLES);
		
		$this->propertyIsSample = new core_kernel_classes_Property(PROPERTY_IS_SAMPLE);
		
	}
	
	public static function getProcesses(){
		return self::$processes;
	}
	
	public static function getVariables(){
		return self::$variables;
	}
	
	public static function getRoles(){
		return self::$roles;
	}
	
	public static function getUsers(){
		return self::$users;
	}
	
	public static function clean(){
		
		$returnValue = false;
		
		//fetch sample resources:
		
		$classVariable = new core_kernel_classes_Class(CLASS_PROCESSVARIABLES);
		
		self::$variables = $classVariable->searchInstances(array(PROPERTY_IS_SAMPLE => GENERIS_TRUE), array('like' => false, 'recursive' => false));
		
		$returnValue = self::deleteProcesses();
		
		foreach(self::$variables as $code => $variable){
			if($variable instanceof core_kernel_classes_Resource){
				$returnValue = $variable->delete();
			}
			unset(self::$processes[$code]);
		}
		
		foreach(self::$roles as $uri => $role){
			if($role instanceof core_kernel_classes_Resource){
				$returnValue = $role->delete();
			}
			unset(self::$roles[$uri]);
		}
		
		foreach(self::$users as $uri => $user){
			if($user instanceof core_kernel_classes_Resource){
				$returnValue = $user->delete();
			}
			unset(self::$users[$uri]);
		}
		
		return $returnValue;
	}
	
	public static function deleteProcesses(){
		
		$returnValue = false;
		
		$classProcess = new core_kernel_classes_Class(CLASS_PROCESS);
		$processAuthoringService = wfAuthoring_models_classes_ProcessService::singleton();
		
		self::$processes = array_merge(
			self::$processes, 
			$classProcess->searchInstances(
				array(PROPERTY_IS_SAMPLE => GENERIS_TRUE), 
				array('like' => false, 'recursive' => false)
			)
		);
		
		foreach(self::$processes as $processUri => $process){
			
			if($process instanceof core_kernel_classes_Resource && $process->exists()){
				$returnValue = $processAuthoringService->deleteProcess($process);
			}
			unset(self::$processes[$processUri]);
			
		}
		
		return $returnValue;
	}

	
	protected function createProcess($label, $comment = ''){
		
		$returnValue = null;
		
		$processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
		$returnValue = $processDefinitionClass->createInstance($label, empty($comment)?'created by the script CreateProcess.php on ' . date(DATE_ISO8601):$comment);
		if(!is_null($returnValue) && $returnValue instanceof core_kernel_classes_Resource){
			$returnValue->setPropertyValue($this->propertyIsSample, GENERIS_TRUE);
			self::$processes[$returnValue->getUri()] = $returnValue;
		}else{
			throw new Exception('cannot create process '.$label);
		}
		
		return $returnValue;
	}
	
	protected function getVariable($code){
		
		$returnValue = null;
		
		if(isset(self::$variables[$code])){
			$returnValue = self::$variables[$code];
		}else{
			$variables = $this->processVariablesClass->searchInstances(array(PROPERTY_PROCESSVARIABLES_CODE => $code), array('like' => false, 'recursive' => 0));
			if (!empty($variables)) {
				$returnValue = reset($variables);
			} else {
				$returnValue = $this->processVariableService->createProcessVariable($code, $code);
				if (is_null($returnValue)) {
					throw new Exception("the process variable ({$code}) cannot be created.");
				} else {
					$returnValue->setPropertyValue($this->propertyIsSample, GENERIS_TRUE);
					self::$variables[$code] = $returnValue;
				}
			}
		}
		
		return $returnValue;
	}
	
	public function createSimpleSequenceProcess($label = 'Simple Sequence Process', $comment = ''){
		
		//create a new process def
		$processDefinition = $this->createProcess($label, $comment);

		//define activities and connectors
		$activity1 = $this->authoringService->createActivity($processDefinition, 'activity1');
		$this->authoringService->setFirstActivity($processDefinition, $activity1);
		$connector1 = $this->authoringService->createConnector($activity1);
		
		$activity2 = $this->authoringService->createSequenceActivity($connector1, null, 'activity2');
		$connector2 = $this->authoringService->createConnector($activity2);

		$activity3 = $this->authoringService->createSequenceActivity($connector2, null, 'activity3');
		$connector3 = $this->authoringService->createConnector($activity3);

		$activity4 = $this->authoringService->createSequenceActivity($connector3, null, 'activity4');
		$connector4 = $this->authoringService->createConnector($activity4);

		$activity5 = $this->authoringService->createSequenceActivity($connector4, null, 'activity5');
		
		return $processDefinition;
	}
	
	public function createSimpleParallelProcess($label = 'Simple Parallel Process', $comment = ''){
		
		//set testUserRole
		$testUserRole = new core_kernel_classes_Resource(INSTANCE_ROLE_WORKFLOW);
		
		//process definition
		$processDefinition = $this->createProcess($label, $comment);
		
		//activities definitions
		$activity0 = $this->authoringService->createActivity($processDefinition, 'activity0');
		$this->authoringService->setFirstActivity($processDefinition, $activity0);
		$connector0 = $this->authoringService->createConnector($activity0);
		
		$connectorParallele = new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_PARALLEL);
		$this->authoringService->setConnectorType($connector0, $connectorParallele);

		$parallelActivity1 = $this->authoringService->createActivity($processDefinition, 'activity1');
		$roleRestrictedUser = new core_kernel_classes_Resource(INSTANCE_ACL_ROLE_RESTRICTED_USER);
		$this->activityService->setAcl($parallelActivity1, $roleRestrictedUser, $testUserRole); //!!! it is mendatory to set the role restricted user ACL mode to make this parallel process test case work

		$connector1 = $this->authoringService->createConnector($parallelActivity1);

		$parallelActivity2 = $this->authoringService->createActivity($processDefinition, 'activity2');
		$this->activityService->setAcl($parallelActivity2, $roleRestrictedUser, $testUserRole); //!!! it is mendatory to set the role restricted user ACL mode to make this parallel process test case work

		$connector2 = $this->authoringService->createConnector($parallelActivity2);

		//define parallel activities, first branch with constant cardinality value, while the second listens to a process variable:
		$parallelCount1 = 3;
		$parallelCount2 = 5;
		$parallelCount2_processVar_key = 'unit_var';
		$parallelCount2_processVar = $this->getVariable($parallelCount2_processVar_key);
		$prallelActivitiesArray = array(
			$parallelActivity1->getUri() => $parallelCount1,
			$parallelActivity2->getUri() => $parallelCount2_processVar
		);

		$result = $this->authoringService->setParallelActivities($connector0, $prallelActivitiesArray);

		//set several split variables:
		$splitVariable1_key = 'unit_split_var1';
		$splitVariable1 = $this->getVariable($splitVariable1_key);
		$splitVariable2_key = 'unit_split_var2';
		$splitVariable2 = $this->getVariable($splitVariable2_key);

		$splitVariablesArray = array(
			$parallelActivity1->getUri() => array($splitVariable1),
			$parallelActivity2->getUri() => array($splitVariable1, $splitVariable2)
		);
		$this->connectorService->setSplitVariables($connector0, $splitVariablesArray);

		$prallelActivitiesArray[$parallelActivity2->getUri()] = $parallelCount2;


		$joinActivity = $this->authoringService->createActivity($processDefinition, 'activity3');

		//join parallel Activity 1 and 2 to "joinActivity"
		$this->authoringService->createJoinActivity($connector1, $joinActivity, '', $parallelActivity1);
		$this->authoringService->createJoinActivity($connector2, $joinActivity, '', $parallelActivity2);
		
		return $processDefinition;
	}
	
}

?>
