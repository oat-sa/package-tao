<?php
require_once dirname(__FILE__) . '/../../tao/test/TaoTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

class ProcessAuthoringServiceTestCase extends UnitTestCase {
	
	
	protected $authoringService = null;
	protected $proc = null;
	protected $apiModel = null;
	
	/**
	 * tests initialization
	 */
	public function setUp(){
		TaoTestRunner::initTest();
		
		$processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
		$processDefinition = $processDefinitionClass->createInstance('processForUnitTest','created for the unit test of process authoring service');
		if($processDefinition instanceof core_kernel_classes_Resource){
			$this->proc = $processDefinition;
		}else{
			$this->fail('fail to create a process definition resource');
		}
		$this->apiModel = core_kernel_impl_ApiModelOO::singleton();
		$this->variableService = wfEngine_models_classes_VariableService::singleton();
	}
	
	/**
	 * Test the service implementation
	 */
	public function testService(){
		
		$authoringService = wfAuthoring_models_classes_ProcessService::singleton();
		
		$this->assertIsA($authoringService, 'tao_models_classes_Service');
		$this->assertIsA($authoringService, 'wfAuthoring_models_classes_ProcessService');

		$this->authoringService = $authoringService;
		
	}
	
	public function testCreateDeleteProcess(){
		
		$processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
		$processDefinition = $this->authoringService->createProcess('myProcess','created for the unit test of process authoring service');
		if($processDefinition instanceof core_kernel_classes_Resource){
			$this->assertTrue($this->authoringService->deleteProcess($processDefinition));
			
			$foundProcesses = $processDefinitionClass->searchInstances(array(RDFS_LABEL => 'myProcess'), array('like' => false));
			$this->assertTrue(empty($foundProcesses));
		}
		
	}
	
	public function testCreateActivity(){
		
		$activity1 = $this->authoringService->createActivity($this->proc);
		$this->assertEqual($activity1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL))->getUri(), GENERIS_TRUE);
		$this->assertEqual($activity1->getLabel(), 'Activity_1');
		$this->assertEqual($activity1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISHIDDEN))->getUri(), GENERIS_FALSE);
		
		$activity2 = $this->authoringService->createActivity($this->proc, 'myActivity');
		$this->assertEqual($activity2->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL))->getUri(), GENERIS_FALSE);
	}
	
	public function testIsActivity(){
		$activity1 = $this->authoringService->createActivity($this->proc);
		$activityService = wfEngine_models_classes_ActivityService::singleton();
		$this->assertTrue($activityService->isActivity($activity1));
	}
	
	public function testIsConnector(){
		$activity1 = $this->authoringService->createActivity($this->proc, 'myActivity');
		$connector1 = $this->authoringService->createConnector($activity1);
		$connectorService = wfEngine_models_classes_ConnectorService::singleton();
		$this->assertTrue($connectorService->isConnector($connector1));
	}
	
	public function testCreateConnector(){
		
		$activity1 = $this->authoringService->createActivity($this->proc, 'myActivity');
		$connector1 = $this->authoringService->createConnector($activity1);
		$next = $activity1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_STEP_NEXT));
		$this->assertEqual($next->getUri(), $connector1->getUri());
		$this->assertEqual($connector1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE))->getUri(), $activity1->getUri());
		
		//create a connector of a connector:
		$connector2 = $this->authoringService->createConnector($connector1);
		$this->assertEqual($connector2->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE))->getUri(), $activity1->getUri());
	}
	
	public function testAnalyseExpression(){
		
		$conditionDom = $this->authoringService->analyseExpression('(3*(^var +  1) = 2 or ^var > 7)', true);
		$this->assertIsA($conditionDom, 'DOMDocument');
		$isCondition = false;
		foreach ($conditionDom->childNodes as $childNode) {
			foreach ($childNode->childNodes as $childOfChildNode) {
				if ($childOfChildNode->nodeName == "condition"){
					$isCondition = true;
					break 2;//once is enough...
				
				}
			}
		}
		$this->assertTrue($isCondition);
		
		$assignmentDom = $this->authoringService->analyseExpression('^var = ^var*32 + ^SCR');
		$isAssignment = false;
		foreach ($assignmentDom->childNodes as $childNode) {
			foreach ($childNode->childNodes as $childOfChildNode) {
				if ($childOfChildNode->nodeName == "then"){
					$isAssignment = true;
					break 2;//stop at the first occurence of course
				}
			}
		}
		$this->assertTrue($isAssignment);
		
	}
	
	public function testCreateSequenceActivity(){
		$activity1 = $this->authoringService->createActivity($this->proc, 'myActivity');
		$connector1 = $this->authoringService->createConnector($activity1);
		
		$followingActivity1 = $this->authoringService->createSequenceActivity($connector1);
		$this->assertIsA($followingActivity1, 'core_kernel_classes_Resource');
		$this->assertEqual($followingActivity1->getLabel(), 'Activity_2');
		$this->assertEqual($connector1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE))->getUri(), INSTANCE_TYPEOFCONNECTORS_SEQUENCE);
		
		$followingConnector1 = $this->authoringService->createConnector($followingActivity1);
		$this->assertIsA($followingConnector1, 'core_kernel_classes_Resource');

		$shouldBeActivity1 = null;
		$shouldBeActivity1 = $this->authoringService->createSequenceActivity($followingConnector1, $activity1);
		$this->assertEqual($activity1->getUri(), $shouldBeActivity1->getUri());
		
		$shouldBeActivity1 = null;
		$shouldBeActivity1 = $followingConnector1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_STEP_NEXT));
		$this->assertEqual($activity1->getUri(),$shouldBeActivity1->getUri());
	}
	
	public function testCreateConditionalActivity(){
		$activity1 = $this->authoringService->createActivity($this->proc, 'myActivity');
		$connector1 = $this->authoringService->createConnector($activity1);
		
		$then = $this->authoringService->createConditionalActivity($connector1, 'then');//create "Activity_2"
		$else = $this->authoringService->createConditionalActivity($connector1, 'else', null, '', true);//create another connector
		$this->assertEqual($connector1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE))->getUri(), INSTANCE_TYPEOFCONNECTORS_CONDITIONAL);
		$activityService = wfEngine_models_classes_ActivityService::singleton();
		$this->assertTrue($activityService->isActivity($then));
		$connectorService =  wfEngine_models_classes_ConnectorService::singleton();
		$this->assertTrue($connectorService->isConnector($else));
		
		$activity3 = $this->authoringService->createSequenceActivity($else, null, 'Act3');
		$this->assertEqual($activity3->getLabel(), 'Act3');
		
		$transitionRule = $connector1->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
		$this->assertEqual($then->getUri(), $transitionRule->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN))->getUri());
		$this->assertEqual($else->getUri(), $transitionRule->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_ELSE))->getUri());
		
		$myProcessVar1 = null;
		$myProcessVar1 = $this->variableService->getProcessVariable('myProcessVarCode1', true);
		$transitionRuleBis = $this->authoringService->createTransitionRule($connector1, '^myProcessVarCode1 == 1');
		$this->assertEqual($transitionRule->getUri(), $transitionRuleBis->getUri());
		
		
		$this->assertTrue($this->variableService->deleteProcessVariable('myProcessVarCode1'));
	}
	
	
	public function testDeleteConnectorNextActivity(){
		$activity1 = $this->authoringService->createActivity($this->proc, 'myActivity');
		$connector1 = $this->authoringService->createConnector($activity1);
		$this->authoringService->createSequenceActivity($connector1, null, '2ndActivityForUnitTest');
		
		$nextActivitiesProp = new core_kernel_classes_Property(PROPERTY_STEP_NEXT);
		
		$activity2 = $connector1->getUniquePropertyValue($nextActivitiesProp);
		$this->assertIsA($activity2 , 'core_kernel_classes_Resource');
		
		$this->authoringService->deleteConnectorNextActivity($connector1, 'next');
		$followingActivity1 = $connector1->getOnePropertyValue($nextActivitiesProp);
		$this->assertNull($followingActivity1);
		
		$connector2 = $this->authoringService->createConnector($activity2);
		$then = $this->authoringService->createConditionalActivity($connector2, 'then');//create "Activity_2"
		$else = $this->authoringService->createConditionalActivity($connector2, 'else', null, '', true);//create another connector
		
		$this->authoringService->deleteConnectorNextActivity($connector2, 'then');
		$this->authoringService->deleteConnectorNextActivity($connector2, 'else');
		
		$transitionRule = $connector2->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
		
		$this->assertNull($transitionRule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN)));
		$this->assertNull($transitionRule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_ELSE)));
		
		$connectorClass = new core_kernel_classes_Class(CLASS_CONNECTORS);
		$connectors = $connectorClass->searchInstances(array(RDFS_LABEL => '2ndActivityForUnitTest_c_c'), array('like' => false));
		$this->assertTrue(empty($connectors));
	}
	
	public function testParallelJoinActivities(){
		$activityA = $this->authoringService->createActivity($this->proc, 'A');
		$connectorA = $this->authoringService->createConnector($activityA);
		
		$activityB = $this->authoringService->createSequenceActivity($connectorA, null, 'B');
		$connectorB = $this->authoringService->createConnector($activityB);
		
		//create the parallel branch 'C' acivities and connectors
		$activityC = $this->authoringService->createActivity($this->proc, 'C');
		$connectorC = $this->authoringService->createConnector($activityC);
				
		$activityC1 = $this->authoringService->createSequenceActivity($connectorC, null, 'C1');
		$connectorC1 = $this->authoringService->createConnector($activityC1);
		
		$activityC2 = $this->authoringService->createSequenceActivity($connectorC1, null, 'C2');
		$connectorC2 = $this->authoringService->createConnector($activityC2);
		
		//create the parallel branch 'D' activities and connectors
		$activityD = $this->authoringService->createActivity($this->proc, 'D');
		$connectorD = $this->authoringService->createConnector($activityD);
		
		//create the merging actvity F
		$activityF = $this->authoringService->createActivity($this->proc, 'F');
		$connectorF = $this->authoringService->createConnector($activityF);
		
		$newActivitiesArray = array(
			$activityC->getUri() => 2,
			$activityD->getUri() => 3
		);
		
		$this->assertTrue($this->authoringService->setParallelActivities($connectorB, $newActivitiesArray));
		$nextActivitiesCollection = $connectorB->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_STEP_NEXT));
		$this->assertEqual($nextActivitiesCollection->count(), 2);

		//merge all activity D instance to F:
		$this->authoringService->createJoinActivity($connectorD, $activityF, '', $activityD);
		$activitiyClass = new core_kernel_classes_Class(CLASS_ACTIVITIES);
		$nexts = wfEngine_models_classes_ConnectorService::singleton()->getPreviousActivities($connectorD);
		$this->assertEqual(count($nexts), 2);//2 cardinality resources
	}
	
	public function testCreateServiceDefinition(){
	
		$myProcessVar1 = null;
		$myProcessVar1 = $this->variableService->getProcessVariable('myProcessVarCode1', true);
		
		$parameterNames = array(
			'param1'.time(),
			'param2'.time(),
			'param3'.time(),
			'param4'.time()
		);
		$inputParameters = array(
			$parameterNames[0] => $myProcessVar1,
			$parameterNames[1] => '^myProcessVarCode2',
			$parameterNames[2] => 'myConstantValue',
			$parameterNames[3] => null
		);
		
		$serviceUrl = 'http://www.myWebSite.com/myServiceScript.php';
		$serviceDefinition = $this->authoringService->createServiceDefinition('myServiceDefinition', $serviceUrl, $inputParameters);
		$this->assertIsA($serviceDefinition, 'core_kernel_classes_Resource');
		
		//check its formal param value:
		$propServiceParam = new core_kernel_classes_Property(PROPERTY_SERVICESDEFINITION_FORMALPARAMIN);
		$propParameterName = new core_kernel_classes_Property(PROPERTY_FORMALPARAMETER_NAME);
		$propParameterConstantVal = new core_kernel_classes_Property(PROPERTY_FORMALPARAMETER_DEFAULTCONSTANTVALUE);
		$propParameterProcessVar = new core_kernel_classes_Property(PROPERTY_FORMALPARAMETER_DEFAULTPROCESSVARIABLE);
		$propCode = new core_kernel_classes_Property(PROPERTY_PROCESSVARIABLES_CODE);
		
		$createdFormalParamCollection = $serviceDefinition->getPropertyValuesCollection($propServiceParam);
		$this->assertEqual($createdFormalParamCollection->count(), 4);
		foreach($createdFormalParamCollection->getIterator() as $formalParam){
			$propName = (string)$formalParam->getUniquePropertyValue($propParameterName);
			if(in_array($propName, array('param1', 'param2', 'param3'))){
				switch($propName){
					case 'param1':{
						$procVar = $formalParam->getUniquePropertyValue($propParameterProcessVar);
						$this->assertNotNull($procVar);
						$this->assertEqual($procVar->getUniquePropertyValue($propCode), 'myProcessVarCode1');
						break;
					}
					case 'param2':{
						$procVar = $formalParam->getUniquePropertyValue($propParameterProcessVar);
						$this->assertNotNull($procVar);
						$this->assertEqual($procVar->getUniquePropertyValue($propCode), 'myProcessVarCode2');
						break;
					}
					case 'param3':{
						$procVar = $formalParam->getUniquePropertyValue($propParameterConstantVal);
						$this->assertNotNull($procVar);
						$this->assertEqual((string) $procVar, 'myConstantValue');
						break;
					}
					case 'param4':{
						break;
					}
				}
			}
		}
		
		$this->assertTrue($this->authoringService->deleteServiceDefinition($serviceUrl));
		$this->assertTrue($this->variableService->deleteProcessVariable('myProcessVarCode1'));
		$this->assertTrue($this->variableService->deleteProcessVariable('myProcessVarCode2'));
		
		for($i=0;$i<4;$i++){
			$formalParam = $this->authoringService->getFormalParameter($parameterNames[$i]);
			$this->assertNotNull($formalParam);
			if(!is_null($formalParam) && $formalParam instanceof core_kernel_classes_Resource){
				$this->assertTrue($formalParam->delete());
			}
		}
	}
	
	public function tearDown() {
		$this->assertTrue($this->authoringService->deleteProcess($this->proc));
    }

}
?>