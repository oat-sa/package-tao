<?php
require_once dirname(__FILE__) . '/../../wfEngine/test/wfEngineServiceTest.php';


class ConnectorAuthoringTest extends wfEngineServiceTest {
	
	/**
	 * @var wfAuthoring_models_classes_ConnectorService
	 */
	private $service;
	
	/**
	 * tests initialization
	 */
	public function setUp(){
		parent::setUp();

		
		$this->service = wfAuthoring_models_classes_ConnectorService::singleton();
	}

	// wfAuthoring Connector Service not finished yet

	/**
						+---------------+
                  		|  activity 1   |
		                +---------------+
		                        |
		                    +---v---+
		                    |   c   |
		                    +--+-+--+
		                        |
		                +-------v--------+
		                |   activity 2   |
		                +-------+--------+
		                        |
		                    +---v---+
		                    |   c   |
		                    +--+-+--+
		                        |
		                +-------v--------+
		                |  activity 3    |
		                +-------+--------+
		                        |
		                    +---v---+
		                    |   c   |
		                    +--+-+--+
		                        |
		                +-------v--------+
		                |  activity 4    |
		                +----------------+
	 */
	public function testSequential(){
		$process = wfAuthoring_models_classes_ProcessService::singleton()->createProcess('test process for '.__FUNCTION__);
	
		$activityAuthoring = wfAuthoring_models_classes_ActivityService::singleton();
		
		$webservice = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAODelivery.rdf#ServiceWebService');
		$activity = array();
		for ($i = 1; $i <= 4; $i++) {
			$activity[$i] = $activityAuthoring->createFromServiceDefinition($process, $webservice, array());
		}
		
		wfAuthoring_models_classes_ProcessService::singleton()->setFirstActivity($process, $activity[1]);
		
		$this->service->createSequential($activity[1], $activity[2]);
		$this->service->createSequential($activity[2], $activity[3]);
		$this->service->createSequential($activity[3], $activity[4]);
		
		$this->runProcess($process, 4);
		
		wfAuthoring_models_classes_ProcessService::singleton()->deleteProcess($process);
	}
	
	/*
	
		             	+---------------+
                		|  activity 1   |
		                +---------------+
		                        |
		                    +---v---+
		                    |  c 1  |
		                    +--+-+--+
		              t        | |       f
		            +----------+ +---------+
		            |                      |
		    +-------v--------+     +-------v--------+
		    |   activity 2   |     |  activity 3    |
		    +-------+--------+     +----------------+
		            |
		            +-----------+
		                        |
		                    +---v---+
		                    |  c 2  |
		                    +--+-+--+
		              t        | |       f
		            +----------+ +---------+
		            |                      |
		    +-------v--------+     +-------v--------+
		    |   activity 4   |     |  activity 5    |
		    +----------------+     +-------+--------+
					              		   |
					                   +---v---+
					                   |   c   |
					                   +--+-+--+
					                       |
					               +-------v--------+
					               |  activity 6    |
					               +----------------+
		*/
			
	public function testConditional() {
		$process = wfAuthoring_models_classes_ProcessService::singleton()->createProcess('test process for '.__FUNCTION__);
	
		$activityAuthoring = wfAuthoring_models_classes_ActivityService::singleton();
		
		$webservice = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAODelivery.rdf#ServiceWebService');
		$activity = array();
		for ($i = 1; $i <= 6; $i++) {
			$activity[$i] = $activityAuthoring->createFromServiceDefinition($process, $webservice, array());
		}
		
		wfAuthoring_models_classes_ProcessService::singleton()->setFirstActivity($process, $activity[1]);
		
		$alwaysTrue		= wfAuthoring_models_classes_RuleService::singleton()->createConditionExpressionFromString('2 > 1');
		$alwaysFalse	= wfAuthoring_models_classes_RuleService::singleton()->createConditionExpressionFromString('2 < 1');
		
		$c1 = $this->service->createConditional($activity[1], $alwaysTrue, $activity[2], $activity[3]);
		$c2 = $this->service->createConditional($activity[2], $alwaysFalse, $activity[4], $activity[5]);
		$this->service->createSequential($activity[5], $activity[6]);
		
		$this->runProcess($process, 4);
		
		wfAuthoring_models_classes_ProcessService::singleton()->deleteProcess($process);
		
	}
	
	/*
		             	+---------------+
                		|  activity 1   |
		                +---------------+
		                        |
		                    +---v---+
		                    |  c 1  |
		                    +--+-+--+
		              1        | |       1
		            +----------+ +---------+
		            |                      |
		    +-------v--------+     +-------v--------+
		    |   activity 2   |     |  activity 3    |
		    +-------+--------+     +--------+-------+
		            |						|
		            +-----------+-----------+
		                        |
		                    +---v---+
		                    |  c 2  |
		                    +---+---+
		                        |
		             	+-------v-------+
                		|  activity 4   |
		                +---------------+
	*/
		                    
	public function testSplitJoin() {
		$process = wfAuthoring_models_classes_ProcessService::singleton()->createProcess('test process for '.__FUNCTION__);
	
		$activityAuthoring = wfAuthoring_models_classes_ActivityService::singleton();
		
		$webservice = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAODelivery.rdf#ServiceWebService');
		$activity = array();
		for ($i = 1; $i <= 4; $i++) {
			$activity[$i] = $activityAuthoring->createFromServiceDefinition($process, $webservice, array());
		}
		
		wfAuthoring_models_classes_ProcessService::singleton()->setFirstActivity($process, $activity[1]);
		
		$c1 = $this->service->createSplit($activity[1], array($activity[2], $activity[3]));
		$c2 = $this->service->createJoin(array($activity[2], $activity[3]), $activity[4]);
		
		$this->runProcess($process, 4);
		
		wfAuthoring_models_classes_ProcessService::singleton()->deleteProcess($process);
		
	}
	
/*
		             	+---------------+
                		|  activity 1   |
		                +---------------+
		                        |
		                    +---v---+
		                    |  c 1  |
		                    +---+---+
								|
							3   |
								|
					    +-------v--------+
					    |   activity 2   |
					    +-------+--------+
		                        |
		                    +---v---+
		                    |  c 2  |
		                    +---+---+
		                        |
		             	+-------v-------+
                		|  activity 3   |
		                +---------------+
	*/
	public function testSplitJoinVariable() {
		$process = wfAuthoring_models_classes_ProcessService::singleton()->createProcess('test process for '.__FUNCTION__);
	
		$activityAuthoring = wfAuthoring_models_classes_ActivityService::singleton();
		
		$webservice = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAODelivery.rdf#ServiceWebService');
		$activity = array();
		for ($i = 1; $i <= 3; $i++) {
			$activity[$i] = $activityAuthoring->createFromServiceDefinition($process, $webservice, array());
		}
		
		wfAuthoring_models_classes_ProcessService::singleton()->setFirstActivity($process, $activity[1]);
		
		$c1 = $this->service->createSplit($activity[1], array($activity[2]));
		$c2 = $this->service->createJoin(array($activity[2]), $activity[3]);
		$this->service->setSplitCardinality($c1, array(
			$activity[2]->getUri() => '3'
		));
		$this->service->setJoinCardinality($c2, array(
			$activity[2]->getUri() => '3'
		));
		
		$this->runProcess($process, 5);
		
		wfAuthoring_models_classes_ProcessService::singleton()->deleteProcess($process);
		
	}
	
	private function runProcess($processDefinition, $expectedSteps) {
		$user = $this->createUser('timmy');
		$this->changeUser('timmy');
		$processExecutionService = wfEngine_models_classes_ProcessExecutionService::singleton();
		$processInstance = $processExecutionService->createProcessExecution($processDefinition, $processDefinition->getLabel().' instance', '');
		
		$currentActivityExecutions = $processExecutionService->getCurrentActivityExecutions($processInstance);
		$steps = 0;
		while (count($currentActivityExecutions) > 0) {
			$steps++;
			$current = array_shift($currentActivityExecutions);
			$transitionResult = $processExecutionService->performTransition($processInstance, $current);
			if ($transitionResult !== false) {
				foreach ($transitionResult as $executed) {
					$this->assertTrue($executed->hasType(new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION)));
				}
			}
			$currentActivityExecutions = $processExecutionService->getCurrentActivityExecutions($processInstance);
			foreach ($currentActivityExecutions as $key => $exec) {
				$status = wfEngine_models_classes_ActivityExecutionService::singleton()->getStatus($exec);
				if (!is_null($status) && $status->getUri() == INSTANCE_PROCESSSTATUS_FINISHED) {
					unset($currentActivityExecutions[$key]);
				}
			}
		}
		$this->assertEquals($steps, $expectedSteps);
		$processExecutionService->deleteProcessExecution($processInstance);
		$user->delete();
		$this->logoutUser();
	}
	
	public function tearDown() {
		parent::tearDown();
    }



}
?>