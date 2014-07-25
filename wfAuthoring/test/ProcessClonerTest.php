<?php
require_once dirname(__FILE__) . '/../../tao/test/TaoPhpUnitTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

class ProcessClonerTest extends TaoPhpUnitTestRunner {
	
	
	protected $processCloner = null;
	protected $authoringService = null;
	/**
	 * @var wfAuthoring_models_classes_ActivityService
	 */
	protected $activityService = null;
	/**
	 * @var wfAuthoring_models_classes_ConnectorService
	 */
	protected $connectorService = null;	
	protected $proc = null;
	protected $apiModel = null;
	
	/**
	 * tests initialization
	 */
	public function setUp(){
		TaoPhpUnitTestRunner::initTest();
		
		$processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
		$processDefinition = $processDefinitionClass->createInstance('process of Cloning UnitTest','created for the unit test of process cloner');
		if($processDefinition instanceof core_kernel_classes_Resource){
			$this->proc = $processDefinition;
		}
		$this->apiModel = core_kernel_impl_ApiModelOO::singleton();
		$this->authoringService = wfAuthoring_models_classes_ProcessService::singleton();
		$this->activityService = wfAuthoring_models_classes_ActivityService::singleton();
		$this->connectorService = wfAuthoring_models_classes_ConnectorService::singleton();
		$this->processCloner = new wfAuthoring_models_classes_ProcessCloner();
	}
	
	
	public function testService(){
		$this->assertIsA($this->processCloner, 'wfAuthoring_models_classes_ProcessCloner');

	}
	
	/*
	public function testCloneActivity(){
		$this->processCloner->initCloningVariables();
		
		$activity1 = $this->authoringService->createActivity($this->proc);
		$service1 = $this->authoringService->createInteractiveService($activity1);
		$activity1Clone = $this->processCloner->cloneActivity($activity1);
		
		$propInitial = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL);
		$propHidden = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISHIDDEN);
		$propService = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES);
		
		$this->assertEquals($activity1->getUniquePropertyValue($propInitial)->getUri(), $activity1Clone->getUniquePropertyValue($propInitial)->getUri());
		$this->assertEquals($activity1->getLabel(), $activity1Clone->getLabel());
		
		$activity1services = $activity1->getPropertyValuesCollection($propService);
		$activity1clonedServices = $activity1Clone->getPropertyValuesCollection($propService);
		$this->assertEquals($activity1services->count(), 1);
		$this->assertEquals($activity1clonedServices->count(), 1);
		
		$clonedService = $activity1clonedServices->get(0);
		
		$this->assertIsA($clonedService, 'core_kernel_classes_Resource');
		$this->assertNotEqual($clonedService->getUri(), $activity1services->get(0)->getUri());
		
		$this->authoringService->deleteActivity($activity1);
		$this->authoringService->deleteActivity($activity1Clone);
		$this->assertFalse($clonedService->exists());
		$this->assertFalse($service1->exists());
	}
	*/
	/*
	public function testCloneConnector(){
		$this->processCloner->initCloningVariables();
		
		$activity1 = $this->authoringService->createActivity($this->proc);
		$this->authoringService->createInteractiveService($activity1);
		$connector1 = $this->authoringService->createConnector($activity1);
		$activity2 = $this->authoringService->createSequenceActivity($connector1);
		
		$activity1Clone = $this->processCloner->cloneActivity($activity1);
		$activity2Clone = $this->processCloner->cloneActivity($activity2);
		$this->processCloner->addClonedActivity($activity1Clone, $activity1);
		$this->processCloner->addClonedActivity($activity2Clone, $activity2);
		
		//clone it!
		$connector1Clone = $this->processCloner->cloneConnector($connector1);
		
		$this->assertTrue($this->connectorService->isConnector($connector1Clone));
		
		$this->assertIsA($this->processCloner->getClonedConnector($connector1), 'core_kernel_classes_Resource');
		$this->assertEquals($connector1Clone->getUri(), $this->processCloner->getClonedConnector($connector1)->getUri());
		
		$this->authoringService->deleteActivity($activity1Clone);
		$this->authoringService->deleteActivity($activity2Clone);
		// $this->authoringService->deleteConnector($connector1Clone);
	}
	*/

	/*public function testCloneSequentialProcess(){
		$this->processCloner->initCloningVariables();
		
		$activity1 = $this->activityService->createInteractiveServiceActivity($this->proc);
		$activity2 = $this->authoringService->createActivity($this->proc);
		$connector1 = $this->connectorService->createSequential($activity1, $activity2);
		$activity3 = $this->activityService->createActivity($this->proc);
		$connector2 = $this->connectorService->createSequential($activity2, $activity3);
		
				
		$activity1 = $this->authoringService->createActivity($this->proc);
		$this->authoringService->createInteractiveService($activity1);
		$connector1 = $this->authoringService->createConnector($activity1);
		$activity2 = $this->authoringService->createSequenceActivity($connector1);
		$connector2 = $this->authoringService->createConnector($activity2);
		$activity3 = $this->authoringService->createSequenceActivity($connector2);
		
		$processClone = $this->processCloner->cloneProcess($this->proc);
		
		$this->assertIsA($processClone, 'core_kernel_classes_Resource');
		$activities = $this->authoringService->getActivitiesByProcess($processClone);
		$this->assertEquals(count($activities), 3);
		foreach($activities as $activity){
			$this->assertTrue($this->activityService->isActivity($activity));
		}
		
		$this->authoringService->deleteProcess($processClone);
	}*/
	
	/*
	public function testCloneProcessSegment(){
		$this->processCloner->initCloningVariables();
		
		$activity1 = $this->authoringService->createActivity($this->proc);
		$this->authoringService->createInteractiveService($activity1);
		$connector1 = $this->authoringService->createConnector($activity1);
		$activity2 = $this->authoringService->createSequenceActivity($connector1);
		$connector2 = $this->authoringService->createConnector($activity2);
		$activity3 = $this->authoringService->createSequenceActivity($connector2);
		
		$segmentInterface = $this->processCloner->cloneProcessSegment($this->proc);
		$this->assertEquals($segmentInterface['in']->getLabel(), $activity1->getLabel());
		$this->assertEquals($segmentInterface['out'][0]->getLabel(), $activity3->getLabel());
		$this->assertEquals(count($this->processCloner->getClonedActivities()), 3);
		
		$segmentInterface = $this->processCloner->cloneProcessSegment($this->proc, true);
		
		$this->assertEquals(count($this->processCloner->getClonedActivities()), 5);
		$this->assertEquals($segmentInterface['in']->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISHIDDEN))->getUri(), GENERIS_TRUE);
		
		// var_dump($this->processCloner);
		
		$this->processCloner->revertCloning();
	}
	*/
	
	public function testCloneConditionnalProcess(){
		$this->processCloner->initCloningVariables();
		
		$id = "P_condProc7_";//for var_dump identification
		$this->processCloner->setCloneLabel("__Clone7");
		
		$activity1 = $this->authoringService->createActivity($this->proc, "{$id}Activity_1");
		$activity1->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_TRUE);
		$connector1 = $this->authoringService->createConnector($activity1);
		
		$then1 = $this->authoringService->createConditionalActivity($connector1, 'then', null, "{$id}Activity_2");//create "Activity_2"
		$else1 = $this->authoringService->createConditionalActivity($connector1, 'else', null, '', true);//create another connector
		// $else1 = $this->authoringService->createConditionalActivity($connector1, 'else');
		
		$this->assertEquals($connector1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE))->getUri(), INSTANCE_TYPEOFCONNECTORS_CONDITIONAL);
		$this->assertTrue($this->activityService->isActivity($then1));
		$this->assertTrue($this->connectorService->isConnector($else1));
		
		$transitionRule = $connector1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
		$this->assertEquals($then1->getUri(), $transitionRule->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN))->getUri());
		$this->assertEquals($else1->getUri(), $transitionRule->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_ELSE))->getUri());
		
		//create a sequential a
		$connector2 = $this->authoringService->createConnector($then1);
		$lastActivity = $this->authoringService->createSequenceActivity($connector2, null, "{$id}Activity_3");
		
		//connector "else1": connect the "then" to the activity "then1" and the "else" to 
		$then2 = $this->authoringService->createConditionalActivity($else1, 'then', $connector2);//connect to the activity $then1
		$else2 = $this->authoringService->createConditionalActivity($else1, 'else', $lastActivity);//connect to the connector of the activity $then1
		$this->assertEquals($then2->getUri(), $connector2->getUri());
		$this->assertEquals($else2->getUri(), $lastActivity->getUri());
		
		
		//clone the process now!
		$processClone = $this->processCloner->cloneProcess($this->proc);
		
		
		$this->assertIsA($processClone, 'core_kernel_classes_Resource');
		$this->assertEquals(count($this->processCloner->getClonedActivities()), 3);
		$this->assertEquals(count($this->processCloner->getClonedConnectors()), 3);
		
		//count the number of activities in the cloned process
		$activities = $this->authoringService->getActivitiesByProcess($processClone);
		$this->assertEquals(count($activities), 3);
		foreach($activities as $activity){
			$this->assertTrue($this->activityService->isActivity($activity));
		}
		
		$this->authoringService->deleteProcess($processClone);
	}
	
	/*
	public function testCloneConditionnalProcessSegment(){
		$this->processCloner->initCloningVariables();
		
		$id = "P_condSeg_";//for var_dump identification
		$this->processCloner->setCloneLabel("__Clone3");
		
		$activity1 = $this->authoringService->createActivity($this->proc, "{$id}Activity_1");
		$activity1->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_TRUE);
		$connector1 = $this->authoringService->createConnector($activity1);
		
		$then1 = $this->authoringService->createConditionalActivity($connector1, 'then', null, "{$id}Activity_2");//create "Activity_2"
		$else1 = $this->authoringService->createConditionalActivity($connector1, 'else', null, '', true);//create another connector
		// $else1 = $this->authoringService->createConditionalActivity($connector1, 'else');
		
		$this->assertEquals($connector1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE))->getUri(), INSTANCE_TYPEOFCONNECTORS_CONDITIONAL);
		$this->assertTrue($this->activityService->isActivity($then1));
		$this->assertTrue($this->connectorService->isConnector($else1));
		
		$transitionRule = $connector1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
		$this->assertEquals($then1->getUri(), $transitionRule->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN))->getUri());
		$this->assertEquals($else1->getUri(), $transitionRule->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_ELSE))->getUri());
		
		//create a sequential a
		$connector2 = $this->authoringService->createConnector($then1);
		$lastActivity = $this->authoringService->createSequenceActivity($connector2, null, "{$id}Activity_3");
		
		//connector "else1": connect the "then" to the activity "then1" and the "else" to 
		$then2 = $this->authoringService->createConditionalActivity($else1, 'then', $connector2);//connect to the activity $then1
		$else2 = $this->authoringService->createConditionalActivity($else1, 'else', $lastActivity);//connect to the connector of the activity $then1
		$this->assertEquals($then2->getUri(), $connector2->getUri());
		$this->assertEquals($else2->getUri(), $lastActivity->getUri());
		
		// var_dump($this->processCloner);
		
		$segmentInterface = $this->processCloner->cloneProcessSegment($this->proc);
		$this->assertEquals($segmentInterface['in']->getLabel(), $activity1->getLabel().$this->processCloner->getCloneLabel());
		// $this->assertEquals($segmentInterface['out'][0]->getLabel(), $activity3->getLabel());
		$this->assertEquals(count($this->processCloner->getClonedActivities()), 3);
//		var_dump($segmentInterface, $segmentInterface['in']->getLabel(), $segmentInterface['out'][0]->getLabel());
		
		$segmentInterface = $this->processCloner->cloneProcessSegment($this->proc, true);
		$this->assertEquals(count($this->processCloner->getClonedActivities()), 5);
		$this->assertEquals($segmentInterface['in']->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISHIDDEN))->getUri(), GENERIS_TRUE);
		// var_dump($segmentInterface);
		
		$this->processCloner->revertCloning();
	}
	/**/
	
	public function tearDown() {
       $this->authoringService->deleteProcess($this->proc);
    }

}
?>