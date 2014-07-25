<?php
require_once dirname(__FILE__) . '/../../tao/test/TaoTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

class ProcessCheckerTestCase extends UnitTestCase {
	
	protected $authoringService = null;
	protected $proc = null;
	
	
	/**
	 * tests initialization
	 */
	public function setUp(){
		TaoTestRunner::initTest();
		
		$processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
		$processDefinition = $processDefinitionClass->createInstance('process of Checker UnitTest','created for the unit test of process cloner');
		if($processDefinition instanceof core_kernel_classes_Resource){
			$this->proc = $processDefinition;
		}
		$this->authoringService = wfAuthoring_models_classes_ProcessService::singleton();
	}
	
	public function testInitialActivity(){
	
		$activity1 = $this->authoringService->createActivity($this->proc);
		
		$processChecker = new wfAuthoring_models_classes_ProcessChecker($this->proc);
		$this->assertTrue($processChecker->checkInitialActivity());
		
		$activity1->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_FALSE);
		$this->assertFalse($processChecker->checkInitialActivity());
		
	}
	
	public function testIsolatedConnector(){
		
		$processChecker = new wfAuthoring_models_classes_ProcessChecker($this->proc);
		
		$activity1 = $this->authoringService->createActivity($this->proc);
		$this->assertTrue($processChecker->checkNoIsolatedConnector());
		
		$connector1 = $this->authoringService->createConnector($activity1);
		$this->assertFalse($processChecker->checkNoIsolatedConnector());
		
		$activity2 = $this->authoringService->createSequenceActivity($connector1);
		$this->assertTrue($processChecker->checkNoIsolatedConnector());
	}
	
	public function testIsolatedActivity(){
		
		$processChecker = new wfAuthoring_models_classes_ProcessChecker($this->proc);
		
		$activity1 = $this->authoringService->createActivity($this->proc);
		$connector1 = $this->authoringService->createConnector($activity1);
		$activity2 = $this->authoringService->createSequenceActivity($connector1);
		$this->assertTrue($processChecker->checkNoIsolatedActivity());
		
		wfAuthoring_models_classes_ConnectorService::singleton()->delete($connector1);
		$this->assertFalse($processChecker->checkNoIsolatedActivity());
	}
	
	public function testCheckProcess(){
		$id= '_unit_pr_check_';
		$processChecker = new wfAuthoring_models_classes_ProcessChecker($this->proc);
		
		$activity1 = $this->authoringService->createActivity($this->proc, "{$id}Activity_1");
		$activity1->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_TRUE);
		$connector1 = $this->authoringService->createConnector($activity1);
		
		$then1 = $this->authoringService->createConditionalActivity($connector1, 'then', null, "{$id}Activity_2");//create "Activity_2"
		$else1 = $this->authoringService->createConditionalActivity($connector1, 'else', null, '', true);//create another connector
		
		$this->assertEqual($connector1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE))->getUri(), INSTANCE_TYPEOFCONNECTORS_CONDITIONAL);
		$activityService = wfEngine_models_classes_ActivityService::singleton();
		$this->assertTrue($activityService->isActivity($then1));
		$connectorService =  wfEngine_models_classes_ConnectorService::singleton();
		$this->assertTrue($connectorService->isConnector($else1));
		
		$transitionRule = $connector1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
		$this->assertEqual($then1->getUri(), $transitionRule->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN))->getUri());
		$this->assertEqual($else1->getUri(), $transitionRule->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_ELSE))->getUri());
		
		//create a sequential a
		$connector2 = $this->authoringService->createConnector($then1);
		$lastActivity = $this->authoringService->createSequenceActivity($connector2, null, "{$id}Activity_3");
		
		//connector "else1": connect the "then" to the activity "then1" and the "else" to 
		$then2 = $this->authoringService->createConditionalActivity($else1, 'then', $connector2);//connect to the activity $then1
		$else2 = $this->authoringService->createConditionalActivity($else1, 'else', $lastActivity);//connect to the connector of the activity $then1
		$this->assertEqual($then2->getUri(), $connector2->getUri());
		$this->assertEqual($else2->getUri(), $lastActivity->getUri());
		
		$this->assertTrue($processChecker->check());
	}
		
	public function tearDown() {
       $this->authoringService->deleteProcess($this->proc);
    }

}
?>