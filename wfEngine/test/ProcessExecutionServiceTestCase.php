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
require_once dirname(__FILE__) . '/../../tao/test/TaoTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * Test ProcessExecution
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package wfEngine
 * @subpackage test
 */
class ProcessExecutionServiceTestCase extends UnitTestCase{

	/**
	 * CHANGE IT MANNUALLY to see step by step the output
	 * @var boolean
	 */
	const OUTPUT = false;
	
	/**
	 * CHANGE IT MANNUALLY to use service cache
	 * @var boolean
	 */
	const SERVICE_CACHE = false;
	
	/**
	 * @var wfEngine_models_classes_ProcessExecutionService the tested service
	 */
	protected $service = null;
	
	/**
	 * @var wfEngine_models_classes_UserService
	 */
	protected $userService = null;
	
	/**
	 * @var core_kernel_classes_Resource
	 */
	protected $currentUser = null;
	
	/**
	 * @var core_kernel_classes_Resource
	 */
	protected $testUserRole = null;
	
	/**
	 * @var core_kernel_classes_Class
	 */
	protected $testUserClass = null;
	
	protected $currentUser0 = null;
	
	/**
	 * initialize a test method
	 */
	public function setUp(){
		
		TaoTestRunner::initTest();
		
		error_reporting(E_ALL);
		
		if(is_null($this->userService)){
			$this->userService = wfEngine_models_classes_UserService::singleton();
		}
		
		list($usec, $sec) = explode(" ", microtime());
		$login = 'wfTester-0';
		$pass = 'test123';
		$userData = array(
			PROPERTY_USER_LOGIN		=> 	$login,
			PROPERTY_USER_PASSWORD	=>	md5($pass),
			PROPERTY_USER_DEFLG		=>	'http://www.tao.lu/Ontologies/TAO.rdf#Lang'.DEFAULT_LANG,
			PROPERTY_USER_UILG		=>	'http://www.tao.lu/Ontologies/TAO.rdf#Lang'.DEFAULT_LANG,
			PROPERTY_USER_ROLES		=>  INSTANCE_ROLE_WORKFLOW
		);
		
		$this->testUserClass = new core_kernel_classes_Class(CLASS_WORKFLOWUSER);
		$this->testUserRole = new core_kernel_classes_Resource(INSTANCE_ROLE_WORKFLOW);
		$this->currentUser = $this->userService->getOneUser($login);
		
		if(is_null($this->currentUser)){
			$this->currentUser = $this->testUserClass->createInstanceWithProperties($userData);
		}
		
		$this->userService->logout();
		if($this->userService->loginUser($login, $pass)){
			$this->currentUser = $this->userService->getCurrentUser();
			$this->currentUser0 = $this->currentUser;
		}
		
		$this->service = wfEngine_models_classes_ProcessExecutionService::singleton();
		$this->service->cache = (bool) self::SERVICE_CACHE;
		
		
	}
	
	public function tearDown() {
		if (!is_null($this->currentUser0)){
			$this->currentUser0->delete();
		}
    }
	
	/**
	 * output messages
	 * @param string $message
	 * @param boolean $ln
	 * @return void
	 */
	private function out($message, $ln = false){
		if(self::OUTPUT){
			if(PHP_SAPI == 'cli'){
				if($ln){
					echo "\n";
				}
				echo "$message\n";
			}
			else{
				if($ln){
					echo "<br />";
				}
				echo "$message<br />";
			}
		}
	}
	
	
	/**
	 * Test the service implementation
	 */
	public function testService(){
		$this->assertIsA($this->service , 'tao_models_classes_Service');
	}
	
	/**
	 * Test the sequential process execution:
	 */
	public function testVirtualSequencialProcess(){
		
		error_reporting(E_ALL);
		
		try{
			$t_start = microtime(true);
			
			$authoringService = wfAuthoring_models_classes_ProcessService::singleton();
			$activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();
			$activityExecutionService->cache = (bool) self::SERVICE_CACHE;
			$this->service = wfEngine_models_classes_ProcessExecutionService::singleton();
			
			//create a new process def
			$processDefinition = $authoringService->createProcess('ProcessForUnitTest', 'Unit test');
			$this->assertIsA($processDefinition, 'core_kernel_classes_Resource');
			
			//define activities and connectors
			$activity1 = $authoringService->createActivity($processDefinition, 'activity1');
			$this->assertNotNull($activity1);
			$authoringService->setFirstActivity($processDefinition, $activity1);
			
			$connector1 = null;
			$connector1 = $authoringService->createConnector($activity1);
			$authoringService->setConnectorType($connector1, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
			$this->assertNotNull($connector1);
			
			$activity2 = $authoringService->createSequenceActivity($connector1, null, 'activity2');
			$this->assertNotNull($activity2);
			
			$connector2  = null; 
			$connector2 = $authoringService->createConnector($activity2);
			$authoringService->setConnectorType($connector2, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
			$this->assertNotNull($connector2);
			
			$activity3 = $authoringService->createSequenceActivity($connector2, null, 'activity3');
			$this->assertNotNull($activity3);
			
			$connector3  = null; 
			$connector3 = $authoringService->createConnector($activity3);
			$authoringService->setConnectorType($connector3, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
			$this->assertNotNull($connector3);
			
			$activity4 = $authoringService->createSequenceActivity($connector3, null, 'activity4');
			$this->assertNotNull($activity4);
			
			$connector4  = null; 
			$connector4 = $authoringService->createConnector($activity4);
			$authoringService->setConnectorType($connector4, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
			$this->assertNotNull($connector4);
		
			$activity5 = $authoringService->createSequenceActivity($connector4, null, 'activity5');
			$this->assertNotNull($activity5);
			
			//run the process
			$processExecName = 'Test Process Execution';
			$processExecComment = 'created for processExecustionService test case by '.__METHOD__;
			$processInstance = $this->service->createProcessExecution($processDefinition, $processExecName, $processExecComment);
			$this->assertEqual($processDefinition->getUri(), $this->service->getExecutionOf($processInstance)->getUri());
			$this->assertEqual($processDefinition->getUri(), $this->service->getExecutionOf($processInstance)->getUri());
			
			$this->assertTrue($this->service->checkStatus($processInstance, 'started'));
			
			$this->out(__METHOD__, true);
			
			$currentActivityExecutions = $this->service->getCurrentActivityExecutions($processInstance);
			$this->assertEqual(count($currentActivityExecutions), 1);
			$this->assertEqual(strpos(array_pop($currentActivityExecutions)->getLabel(), 'Execution of activity1'), 0);
			
			$this->out("<strong>Forward transitions:</strong>", true);
			
			$previousActivityExecution = null;//to test undoing the transitions
			$iterationNumber = 5;
			$i = 1;
			while($i <= $iterationNumber){
				if($i<$iterationNumber){
					//try deleting a process that is not finished
					$this->assertFalse($this->service->deleteProcessExecution($processInstance, true));
				}
				
				$activitieExecs = $this->service->getCurrentActivityExecutions($processInstance);
				$this->assertEqual(count($activitieExecs), 1);
				$activityExecution = reset($activitieExecs);
				$activity = $activityExecutionService->getExecutionOf($activityExecution);
				
				$this->out("<strong>".$activity->getLabel()."</strong>", true);
				$this->assertTrue($activity->getLabel() == 'activity'.$i);
				
				//init execution
				$activityExecution = $this->service->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser);
				$this->assertNotNull($activityExecution);
				$activityExecStatus = $activityExecutionService->getStatus($activityExecution);
				$this->assertNotNull($activityExecStatus);
				$this->assertEqual($activityExecStatus->getUri(), INSTANCE_PROCESSSTATUS_STARTED);
				
				//transition to next activity
				$transitionResult = $this->service->performTransition($processInstance, $activityExecution);
				
				//try undoing the transition:
				switch($i){
					case 2:
					case 4:{
						$this->assertTrue($this->service->undoForwardTransition($processInstance, $activityExecution));
					
						$activitieExecs = $this->service->getCurrentActivityExecutions($processInstance);
						$this->assertEqual(count($activitieExecs), 1);
						$activity = $activityExecutionService->getExecutionOf(reset($activitieExecs));
						$this->assertTrue($activity->getLabel() == 'activity'.$i);

						$transitionResult = $this->service->performTransition($processInstance, $activityExecution);
						
						break;
					}
					case 3:{
						$history = $this->service->getExecutionHistory($processInstance);
						$this->assertEqual(count($history), 5);//activity 1, 2(closed), 2, 3 and 4
						$this->assertFalse($this->service->undoForwardTransition($processInstance, new core_kernel_classes_Resource(reset($history))));
						
						$this->assertNotNull($previousActivityExecution);
						$this->assertFalse($this->service->undoForwardTransition($processInstance, $previousActivityExecution));
						break;
					}
				}
				
				if($i < $iterationNumber){
					$this->assertTrue(count($transitionResult));
				}else{
					$this->assertFalse($transitionResult);
				}
				$this->out("activity status: ".$activityExecutionService->getStatus($activityExecution)->getLabel());
				$this->out("process status: ".$this->service->getStatus($processInstance)->getLabel());
				$this->assertFalse($this->service->isPaused($processInstance));
				
				$previousActivityExecution = $activityExecution;
				
				$i++;
			}
			
			$this->assertTrue($this->service->isFinished($processInstance));
			$this->assertTrue($this->service->resume($processInstance));
			
			$this->out("<strong>Backward transitions:</strong>", true);
			
			$j = 0;
			while($j < $iterationNumber){
				
				$activitieExecs = $this->service->getCurrentActivityExecutions($processInstance);
				$this->assertEqual(count($activitieExecs), 1);
				$activityExecution = reset($activitieExecs);
				$activity = $activityExecutionService->getExecutionOf($activityExecution);
				
				$this->out("<strong>".$activity->getLabel()."</strong>", true);
				$index = $iterationNumber - $j;
				$this->assertEqual($activity->getLabel(), "activity$index");
				
				//init execution
				$activityExecution = $this->service->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser);
				$this->assertNotNull($activityExecution);
				$activityExecStatus = $activityExecutionService->getStatus($activityExecution);
				$this->assertNotNull($activityExecStatus);
				$this->assertEqual($activityExecStatus->getUri(), INSTANCE_PROCESSSTATUS_RESUMED);
				
				//transition to next activity
				$transitionResult = $this->service->performBackwardTransition($processInstance, $activityExecution);
				$processStatus = $this->service->getStatus($processInstance);
				$this->assertNotNull($processStatus);
				$this->assertEqual($processStatus->getUri(), INSTANCE_PROCESSSTATUS_RESUMED);
				if($j < $iterationNumber-1){
					$this->assertTrue(count($transitionResult));
				}else{
					$this->assertFalse($transitionResult);
				}
				
				$this->out("activity status: ".$activityExecutionService->getStatus($activityExecution)->getLabel());
				$this->out("process status: ".$this->service->getStatus($processInstance)->getLabel());
				$j++;
			}
			
			$this->out("<strong>Forward transitions again:</strong>", true);
			
			$previousActivityExecution = null;
			$i = 1;
			while($i <= $iterationNumber){
				if($i<$iterationNumber){
					//try deleting a process that is not finished
					$this->assertFalse($this->service->deleteProcessExecution($processInstance, true));
				}
				
				$activitieExecs = $this->service->getCurrentActivityExecutions($processInstance);
				$this->assertEqual(count($activitieExecs), 1);
				$activityExecution = reset($activitieExecs);
				$activity = $activityExecutionService->getExecutionOf($activityExecution);
				
				$this->out("<strong>".$activity->getLabel()."</strong>", true);
				$this->assertTrue($activity->getLabel() == 'activity'.$i);
				
				//init execution
				$activityExecution = $this->service->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser);
				$this->assertNotNull($activityExecution);
				$activityExecStatus = $activityExecutionService->getStatus($activityExecution);
				$this->assertNotNull($activityExecStatus);
				if($i == 1){
					$this->assertEqual($activityExecStatus->getUri(), INSTANCE_PROCESSSTATUS_RESUMED);
				}else{
					$this->assertEqual($activityExecStatus->getUri(), INSTANCE_PROCESSSTATUS_STARTED);
				}
				
				//transition to next activity
				$transitionResult = $this->service->performTransition($processInstance, $activityExecution);
				
				//try undoing the transition:
				switch($i){
					case 2:
					case 3:{
						$this->assertTrue($this->service->undoForwardTransition($processInstance, $activityExecution));
					
						$activitieExecs = $this->service->getCurrentActivityExecutions($processInstance);
						$this->assertEqual(count($activitieExecs), 1);
						$activity = $activityExecutionService->getExecutionOf(reset($activitieExecs));
						$this->assertTrue($activity->getLabel() == 'activity'.$i);

						$transitionResult = $this->service->performTransition($processInstance, $activityExecution);
						
						break;
					}
					case 3:
					case 4:{
						$history = $this->service->getExecutionHistory($processInstance);
						$this->assertFalse($this->service->undoForwardTransition($processInstance, new core_kernel_classes_Resource(reset($history))));
						
						$this->assertNotNull($previousActivityExecution);
						$this->assertFalse($this->service->undoForwardTransition($processInstance, $previousActivityExecution));
						break;
					}
				}
				
				if($i<$iterationNumber){
					$this->assertTrue(count($transitionResult));
				}else{
					$this->assertFalse($transitionResult);
				}
				$this->out("activity status: ".$activityExecutionService->getStatus($activityExecution)->getLabel());
				$this->out("process status: ".$this->service->getStatus($processInstance)->getLabel());
				
				$this->assertFalse($this->service->isPaused($processInstance));
				
				$previousActivityExecution = $activityExecution;
				
				$i++;
			}
			$this->assertTrue($this->service->isFinished($processInstance));
			
			$t_end = microtime(true);
			$duration = $t_end - $t_start;
			$this->out('Elapsed time: '.$duration.'s', true);
		
			//delete processdef:
			$this->assertTrue($authoringService->deleteProcess($processDefinition));
			
			//delete process execution:
			$this->assertTrue($processInstance->exists());
			$this->assertTrue($this->service->deleteProcessExecution($processInstance));
			$this->assertFalse($processInstance->exists());
			
			if(!is_null($this->currentUser)){
				$this->userService->logout();
				$this->userService->removeUser($this->currentUser);
			}
		}
		catch(common_Exception $ce){
			$this->fail($ce);
		}
	}
	

	/**
	 * Test the tokens into a parallel process
	 */
	public function testVirtualParallelJoinProcess(){
		
		error_reporting(E_ALL);
		
		$t_start = microtime(true);
		
		//init services
		$activityService = wfEngine_models_classes_ActivityService::singleton();
		$processVariableService = wfEngine_models_classes_VariableService::singleton();
		$authoringService = wfAuthoring_models_classes_ProcessService::singleton();
		$activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();
		$activityExecutionService->cache = (bool) self::SERVICE_CACHE;
		
		//process definition
		$processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
		$processDefinition = $processDefinitionClass->createInstance('PJ processForUnitTest_' . date(DATE_ISO8601),'created for the unit test of process execution');
		$this->assertNotNull($processDefinition);
		
		/*
		                +---------------+
		                |  activity 0   |
		                +-------+-------+
		                        |
		                    +---v---+
		                    |  c 0  |   split
		                    +--+-+--+
		                       | |
		          3  +---------+ +---------+  unit_var_12345678
		             |                     |
		     +-------v--------+    +-------v------+
		     |   activity 1   |    |  activity 2  |
		     +-------+--------+    +--------+-----+
		             |                      |
		             +--------+    +--------+
		                   +--v----v--+
		                   |   c 2    |    join
		                   +----+-----+
		                        |
		                +-------v--------+
		                |  activity 3    |
		                +----------------+
		                
		 */

		//activities definitions
		$activity0 = $authoringService->createActivity($processDefinition, 'activity0');
		$this->assertNotNull($activity0);

		$connector0 = null;
		$authoringService->setFirstActivity($processDefinition,$activity0);
		$connector0 = $authoringService->createConnector($activity0);
		$connectorParallele = new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_PARALLEL);
		$authoringService->setConnectorType($connector0, $connectorParallele);
		$this->assertNotNull($connector0);

		$parallelActivity1 = $authoringService->createActivity($processDefinition, 'activity1');
		$this->assertNotNull($parallelActivity1);
		$roleRestrictedUser = new core_kernel_classes_Resource(INSTANCE_ACL_ROLE_RESTRICTED_USER);
		$activityService->setAcl($parallelActivity1, $roleRestrictedUser, $this->testUserRole);//!!! it is mendatory to set the role restricted user ACL mode to make this parallel process test case work
		
		$connector1 = null;
		$connector1 = $authoringService->createConnector($parallelActivity1);
		$this->assertNotNull($connector1);

		$parallelActivity2 = $authoringService->createActivity($processDefinition, 'activity2');
		$this->assertNotNull($parallelActivity2);
		$activityService->setAcl($parallelActivity2, $roleRestrictedUser, $this->testUserRole);//!!! it is mendatory to set the role restricted user ACL mode to make this parallel process test case work
		
		$connector2 = null;
		$connector2 = $authoringService->createConnector($parallelActivity2);
		$this->assertNotNull($connector2);
		
		//define parallel activities, first branch with constant cardinality value, while the second listens to a process variable:
		$parallelCount1 = 3;
		$parallelCount2 = 5;
		$parallelCount2_processVar_key = 'unit_var_'.time();
		$parallelCount2_processVar = $processVariableService->createProcessVariable('Var for unit test', $parallelCount2_processVar_key);
		$prallelActivitiesArray = array(
			$parallelActivity1->getUri() => $parallelCount1,
			$parallelActivity2->getUri() => $parallelCount2_processVar
		);
		
		$result = $authoringService->setParallelActivities($connector0, $prallelActivitiesArray);
		$this->assertTrue($result);
		
		//set several split variables:
		$splitVariable1_key = 'unit_split_var1_'.time();
		$splitVariable1 = $processVariableService->createProcessVariable('Split Var1 for unit test', $splitVariable1_key);
		$splitVariable2_key = 'unit_split_var2_'.time();
		$splitVariable2 = $processVariableService->createProcessVariable('Split Var2 for unit test', $splitVariable2_key);
		
		$splitVariablesArray = array(
			$parallelActivity1->getUri() => array($splitVariable1),
			$parallelActivity2->getUri() => array($splitVariable1, $splitVariable2)
		);
		$connectorService = wfAuthoring_models_classes_ConnectorService::singleton();
		$connectorService->setSplitVariables($connector0, $splitVariablesArray);
		
		$prallelActivitiesArray[$parallelActivity2->getUri()] = $parallelCount2;
		

		$joinActivity = $authoringService->createActivity($processDefinition, 'activity3');

		//join parallel Activity 1 and 2 to "joinActivity"
		$connector1 = wfAuthoring_models_classes_ConnectorService::singleton()->createJoin(array($parallelActivity1,$parallelActivity2),$joinActivity);
		/*
		$authoringService->createJoinActivity($connector1, $joinActivity, '', $parallelActivity1);
		$authoringService->createJoinActivity($connector2, $joinActivity, '', $parallelActivity2);
		*/

		//run the process
		$processExecName = 'Test Parallel Process Execution';
		$processExecComment = 'created for processExecustionService test case by '.__METHOD__;
		$processInstance = $this->service->createProcessExecution($processDefinition, $processExecName, $processExecComment);
		$this->assertTrue($this->service->checkStatus($processInstance, 'started'));

		$this->out(__METHOD__, true);
		$this->out("<strong>Forward transitions:</strong>", true);

		$previousActivityExecution = null;
		$numberActivities = 2 + $parallelCount1 + $parallelCount2;
		$createdUsers = array();
		$loginProperty = new core_kernel_classes_Property(PROPERTY_USER_LOGIN);
		for($i=1; $i <= $numberActivities; $i++){
			
			$activitieExecs = $this->service->getCurrentActivityExecutions($processInstance);
			$countActivities = count($activitieExecs);
			$activity = null;
			$activityExecution = null;
			if($countActivities > 1){
				//select one of the available activities in the parallel branch:
				foreach($activitieExecs as $activityExecUri => $activityExec){
					if(!$activityExecutionService->isFinished($activityExec)){
						$activityDefinition = $activityExecutionService->getExecutionOf($activityExec);
						$activityUri = $activityDefinition->getUri();
						if(isset($prallelActivitiesArray[$activityUri])){
							if($prallelActivitiesArray[$activityUri] > 0){
								$prallelActivitiesArray[$activityUri]--;
								$activityExecution = $activityExec;
								$activity = $activityDefinition;
								break;
							}
						}
					}
				}
			}else if($countActivities == 1){
				$activityExecution = reset($activitieExecs);
				$activity = $activityExecutionService->getExecutionOf($activityExecution);
			}else{
				
				$this->fail('no current activity definition found for the iteration '.$i);
			}

			$this->out("<strong> Iteration {$i} :</strong>", true);
			$this->out("<strong>".(is_null($activity) ? 'null' : $activity->getLabel())."</strong> (among {$countActivities})");
			//issue : no activity found for the last iteration...
			
			
			//init execution
			$activityExecution = $this->service->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser);
			$this->assertNotNull($activityExecution);
			if($i == 1){
				//set value of the parallel thread:
				$processVariableService->push($parallelCount2_processVar_key, $parallelCount2);
				
				//set some values to the split variables:
				$values1 = array();
				for($j = 1; $j <= $parallelCount1; $j++){
					$values1[] = 'A'.$j;
				}
				$values2 = array();
				for($j = 1; $j <= $parallelCount2; $j++){
					$values2[] = 'B'.$j;
				}
				$processVariableService->push($splitVariable1_key, serialize($values1));
				$processVariableService->push($splitVariable2_key, serialize($values2));
				
			}else{
				
				//check dispatched value:
//				$value1 = $processVariableService->get($splitVariable1_key);
//				$value2 = $processVariableService->get($splitVariable2_key);
//				var_dump($value1, $value2);
				
			}
			
			$activityExecStatus = $activityExecutionService->getStatus($activityExecution);
			$this->assertNotNull($activityExecStatus);
			$this->assertEqual($activityExecStatus->getUri(), INSTANCE_PROCESSSTATUS_STARTED);

			//transition to next activity
			$this->out("current user: ".$this->currentUser->getOnePropertyValue($loginProperty).' "'.$this->currentUser->getUri().'"');
			$this->out("performing transition ...");

			//transition to next activity
			$performed = $this->service->performTransition($processInstance, $activityExecution);
			if (!$performed) {
				$this->out('transition failed.');
			}
			$this->out("activity status: ".$activityExecutionService->getStatus($activityExecution)->getLabel());
			$this->out("process status: ".$this->service->getStatus($processInstance)->getLabel());
			
			//try undoing the transition:
			switch($i){
				case 1:{
					$this->assertTrue($this->service->undoForwardTransition($processInstance, $activityExecution));

					$activitieExecs = $this->service->getCurrentActivityExecutions($processInstance);
					$this->assertEqual(count($activitieExecs), 1);
					$activityBis = $activityExecutionService->getExecutionOf(reset($activitieExecs));
					$this->assertTrue($activity->getUri() == $activityBis->getUri());

					$transitionResult = $this->service->performTransition($processInstance, $activityExecution);

					break;
				}
				case 1 + $parallelCount1:{
					
					$this->assertFalse($this->service->undoForwardTransition($processInstance, $activityExecution));
					
					$history = $this->service->getExecutionHistory($processInstance);
					$this->assertEqual(count($history), 2*($parallelCount1 + $parallelCount2) + 1);//activity 1, 2(closed), 2, 3 and 4
					$this->assertFalse($this->service->undoForwardTransition($processInstance, new core_kernel_classes_Resource(reset($history))));

					$this->assertNotNull($previousActivityExecution);
					$this->assertFalse($this->service->undoForwardTransition($processInstance, $previousActivityExecution));
					break;
				}
				case 1 + $parallelCount1 + $parallelCount2:{
					$this->assertTrue($this->service->undoForwardTransition($processInstance, $activityExecution));

					$activitieExecs = $this->service->getCurrentActivityExecutions($processInstance);
					$this->assertEqual(count($activitieExecs), $parallelCount1 + $parallelCount2);

					$transitionResult = $this->service->performTransition($processInstance, $activityExecution);

					break;
				}
			}
				
			$previousActivityExecution = $activityExecution;
			
			if($this->service->isPaused($processInstance)){

				//Login another user to execute parallel branch
				$this->userService->logout();
				$this->out("logout ". $this->currentUser->getOnePropertyValue($loginProperty) . ' "' . $this->currentUser->getUri() . '"', true);
				
				list($usec, $sec) = explode(" ", microtime());
				$login = 'wfTester-'.$i.'-'.$usec;
				$pass = 'test123';
				$userData = array(
					PROPERTY_USER_LOGIN		=> 	$login,
					PROPERTY_USER_PASSWORD	=>	md5($pass),
					PROPERTY_USER_DEFLG		=>	'http://www.tao.lu/Ontologies/TAO.rdf#Lang'.DEFAULT_LANG,
					PROPERTY_USER_UILG		=>  'http://www.tao.lu/Ontologies/TAO.rdf#Lang'.DEFAULT_LANG,
					PROPERTY_USER_ROLES		=> 	INSTANCE_ROLE_WORKFLOW,
					RDFS_LABEL				=> $login
				);

				$otherUser = $this->userService->getOneUser($login);
				if(is_null($otherUser)){
					$otherUser = $this->testUserClass->createInstanceWithProperties($userData);
				}
				$createdUsers[$otherUser->getUri()] = $otherUser; 

				if($this->userService->loginUser($login, $pass)){
					$this->currentUser = $this->userService->getCurrentUser();
					$this->out("new user logged in: ".$this->currentUser->getOnePropertyValue($loginProperty).' "'.$this->currentUser->getUri().'"');
				}else{
					$this->fail("unable to login user $login");
				}
			}
		}
		$this->assertTrue($this->service->isFinished($processInstance));
		$this->assertTrue($this->service->resume($processInstance));
		
		$this->out("<strong>Backward transitions:</strong>", true);
		
//		var_dump($this->service->getAllActivityExecutions($processInstance));
		
		$j = 0;
		$iterationNumber = 2;
		while($j < $iterationNumber){

			$activitieExecs = $this->service->getCurrentActivityExecutions($processInstance);
			$activityExecution = null;
			$activity = null;
			switch($j){
				case 0:
					
					$this->assertEqual(count($activitieExecs), 1);//check
					
					$activityExecution = reset($activitieExecs);
					$activity = $activityExecutionService->getExecutionOf($activityExecution);
			
					break;
				case 1:
					
					$this->assertEqual(count($activitieExecs), $parallelCount1 + $parallelCount2);//check
					
					$activity = $parallelActivity2;
					foreach($this->service->getCurrentActivityExecutions($processInstance, $activity) as $activityExec){
						if($activityExecutionService->getActivityExecutionUser($activityExec)->getUri() == $this->currentUser->getUri()){
							$activityExecution = $activityExec;
						}
					}
					
					if(is_null($activityExecution)){
						$activity = $parallelActivity1;
						foreach ($this->service->getCurrentActivityExecutions($processInstance, $activity) as $activityExec) {
							if ($activityExecutionService->getActivityExecutionUser($activityExec)->getUri() == $this->currentUser->getUri()) {
								$activityExecution = $activityExec;
							}
						}
					}
					
					$this->assertNotNull($activityExecution);
					
					break;
			}

			$this->out("<strong>".$activity->getLabel()."</strong>", true);

			//init execution
			$activityExecution = $this->service->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser);
			$this->assertNotNull($activityExecution);
			$activityExecStatus = $activityExecutionService->getStatus($activityExecution);
			$this->assertNotNull($activityExecStatus);
			$this->assertEqual($activityExecStatus->getUri(), INSTANCE_PROCESSSTATUS_RESUMED);
			
			$this->out("current user: ".$this->currentUser->getOnePropertyValue($loginProperty).' "'.$this->currentUser->getUri().'"');
			$this->out("performing transition ...");

			//transition to next activity
			$transitionResult = $this->service->performBackwardTransition($processInstance, $activityExecution);
			if($j == 0){
				$this->assertTrue($transitionResult);
			}else if($j == $iterationNumber - 1){
				//var_dump($transitionResult);
				$this->assertFalse($transitionResult);
			}
			
			$processStatus = $this->service->getStatus($processInstance);
			$this->assertNotNull($processStatus);
			$this->out("activity status: ".$activityExecutionService->getStatus($activityExecution)->getLabel());
			$this->out("process status: ".$processStatus->getLabel());
			$this->assertEqual($processStatus->getUri(), INSTANCE_PROCESSSTATUS_PAUSED);
			
			$j++;
		}
		
		$this->out("<strong>Forward transitions again:</strong>", true);
		
		$currentActivityExecutions = $this->service->getCurrentActivityExecutions($processInstance);
		
		$currentActivityExecutionsCount = count($currentActivityExecutions);
		$this->assertEqual($currentActivityExecutionsCount, $parallelCount1 + $parallelCount2);
		
		for($i=0; $i<$currentActivityExecutionsCount; $i++){
			
			$currentActivityExecution = array_pop($currentActivityExecutions);
			$user = $activityExecutionService->getActivityExecutionUser($currentActivityExecution);
			$activityDefinition = $activityExecutionService->getExecutionOf($currentActivityExecution);
			$this->assertNotNull($user);
			$this->assertNotNull($activityDefinition);
			
			if(!is_null($user) && !is_null($activityDefinition)){
				
				$this->userService->logout();
				$this->out("logout ". $this->currentUser->getOnePropertyValue($loginProperty) . ' "' . $this->currentUser->getUri() . '"', true);

				$login = (string) $user->getUniquePropertyValue($loginProperty);
				$pass = 'test123';
				if ($this->userService->loginUser($login, $pass)) {
					$this->currentUser = $this->userService->getCurrentUser();
					$this->out("new user logged in: " . $this->currentUser->getOnePropertyValue($loginProperty) . ' "' . $this->currentUser->getUri() . '"');
				} else {
					$this->fail("unable to login user $login<br>");
				}
				
				$iterationNo = $i+1;
				$this->out("<strong>Iteration $iterationNo: ".$activityDefinition->getLabel()."</strong>", true);
				
				//execute activity:
				$activityExecution = $this->service->initCurrentActivityExecution($processInstance, $currentActivityExecution, $this->currentUser);
				$this->assertNotNull($activityExecution);
				$activityExecStatus = $activityExecutionService->getStatus($activityExecution);
				$this->assertNotNull($activityExecStatus);
				$this->assertEqual($activityExecStatus->getUri(), INSTANCE_PROCESSSTATUS_RESUMED);
				
				//transition to next activity
				$this->out("current user: ".$this->currentUser->getOnePropertyValue($loginProperty).' "'.$this->currentUser->getUri().'"');
				$this->out("performing transition ...");

				//transition to next activity
				$this->service->performTransition($processInstance, $activityExecution);
				$this->out("activity status: ".$activityExecutionService->getStatus($activityExecution)->getLabel());
				$this->out("process status: ".$this->service->getStatus($processInstance)->getLabel());
				
				//try undoing the transition:
				if($i < $currentActivityExecutionsCount-1){
					$this->assertFalse($this->service->undoForwardTransition($processInstance, $activityExecution));
				}
			}
		}
		
		//try undoing the transition:
		$this->assertTrue($this->service->undoForwardTransition($processInstance, $activityExecution));
		$activitieExecs = $this->service->getCurrentActivityExecutions($processInstance);
		$this->assertEqual(count($activitieExecs), $parallelCount1 + $parallelCount2);
		$transitionResult = $this->service->performTransition($processInstance, $activityExecution);
		$this->assertEqual(count($transitionResult), 1);
		
		
		$activitieExecs = $this->service->getCurrentActivityExecutions($processInstance);
		$this->assertEqual(count($activitieExecs), 1);
		$activityExecution = reset($activitieExecs);
		$activity = $activityExecutionService->getExecutionOf($activityExecution);
		$this->assertEqual($activity->getUri(), $joinActivity->getUri());
		
		$this->out("<strong>Executing last activity: ".$activity->getLabel()."</strong>", true);
			
		//init execution
		$activityExecution = $this->service->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser);
		$this->assertNotNull($activityExecution);

		$activityExecStatus = $activityExecutionService->getStatus($activityExecution);
		$this->assertNotNull($activityExecStatus);
		$this->assertEqual($activityExecStatus->getUri(), INSTANCE_PROCESSSTATUS_STARTED);

		//transition to next activity
		$this->out("current user: ".$this->currentUser->getOnePropertyValue($loginProperty).' "'.$this->currentUser->getUri().'"');
		$this->out("performing transition ...");

		//transition to next activity
		$this->service->performTransition($processInstance, $activityExecution);
		$this->out("activity status: ".$activityExecutionService->getStatus($activityExecution)->getLabel());
		$this->out("process status: ".$this->service->getStatus($processInstance)->getLabel());
		$this->assertTrue($this->service->isFinished($processInstance));
		
		$t_end = microtime(true);
		$duration = $t_end - $t_start;
		$this->out('Elapsed time: '.$duration.'s', true);
		
		$this->out('deleting created resources:', true);
		//delete process exec:
		$this->assertTrue($this->service->deleteProcessExecution($processInstance));

		//delete processdef:
		$this->assertTrue($authoringService->deleteProcess($processDefinition));
		$parallelCount2_processVar->delete();
		
		//delete created users:
		foreach($createdUsers as $createdUser){
			$this->out('deleting '.$createdUser->getLabel().' "'.$createdUser->getUri().'"');
			$this->assertTrue($this->userService->removeUser($createdUser));
		}

		if(!is_null($this->currentUser)){
			$this->userService->logout();
			$this->userService->removeUser($this->currentUser);
		}
		
	}
}

?>