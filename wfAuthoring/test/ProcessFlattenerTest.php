<?php
require_once dirname(__FILE__) . '/../../tao/test/TaoPhpUnitTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

class ProcessFlattenerTest extends TaoPhpUnitTestRunner {
	
	
	protected $authoringService = null;
	protected $proc = null;
	protected $apiModel = null;
	
	/**
	 * tests initialization
	 */
	public function setUp(){
	    parent::setUp();
		TaoPhpUnitTestRunner::initTest();
	}

	public function testTheTester(){
	    $process1 = $this->createLinearProcess();
	    
	    $arr = wfAuthoring_models_classes_ProcessService::singleton()->getInitialSteps($process1);
	    $this->assertEquals(count($arr), 1);
	    $startP1 = current($arr);
	    $arr = wfAuthoring_models_classes_ProcessService::singleton()->getFinalSteps($process1);
	    $this->assertEquals(count($arr), 1);
	    $endP1 = current($arr);
	     
	    $this->assertEquals($startP1, $startP1);
	    $final = $this->assertProcessPartCorresponds($startP1, $startP1);
	    $arr = wfEngine_models_classes_StepService::singleton()->getNextSteps($final);
	    $this->assertTrue(empty($arr));
	     
	    
	    $cloner = new wfAuthoring_models_classes_ProcessCloner();
	    $clone = $cloner->cloneProcess($process1);
	    $arr = wfAuthoring_models_classes_ProcessService::singleton()->getInitialSteps($clone);
	    $this->assertEquals(count($arr), 1);
	    $startClone = current($arr);
	    $this->assertNotEquals($startP1, $startClone);
	    $this->assertCorresponds($startP1, $startClone);
	    
	    $final = $this->assertProcessPartCorresponds($startP1, $startClone);
	    $arr = wfEngine_models_classes_StepService::singleton()->getNextSteps($final);
	    $this->assertTrue(empty($arr));
	     
	    wfAuthoring_models_classes_ProcessService::singleton()->deleteProcess($clone);
	    wfAuthoring_models_classes_ProcessService::singleton()->deleteProcess($process1);
	}
	
	public function testFlatten(){
	    // single
	    $process1 = $this->createLinearProcess();
	    $arr = wfAuthoring_models_classes_ProcessService::singleton()->getInitialSteps($process1);
	    $this->assertEquals(count($arr), 1);
	    $startP1 = current($arr);
	    $arr = wfAuthoring_models_classes_ProcessService::singleton()->getFinalSteps($process1);
	    $this->assertEquals(count($arr), 1);
	    $endP1 = current($arr);
	    
	    $super1 = $this->createLinearSuperProcess(array($process1));
	    $flattener = new wfAuthoring_models_classes_ProcessFlattener($super1);
	    $flattener->flatten();
	    $activities = $super1->getPropertyValues(new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES));
	    $this->assertEquals(count($activities), 3);
	    $arr = wfAuthoring_models_classes_ProcessService::singleton()->getInitialSteps($super1);
	    $this->assertEquals(count($arr), 1);
	    $start = current($arr);

	    $final = $this->assertProcessPartCorresponds($start, $startP1);
	    $arr = wfEngine_models_classes_StepService::singleton()->getNextSteps($final);
	    $this->assertTrue(empty($arr));
	     
	    wfAuthoring_models_classes_ProcessService::singleton()->deleteProcess($super1);
	    
	    //recursiv
	    $super1 = $this->createLinearSuperProcess(array($process1));
	    $super2 = $this->createLinearSuperProcess(array($super1));
	    $flattener = new wfAuthoring_models_classes_ProcessFlattener($super2);
	    $flattener->flatten();
	    $activities = $super2->getPropertyValues(new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES));
	    $this->assertEquals(count($activities), 3);
	     
	    $arr = wfAuthoring_models_classes_ProcessService::singleton()->getInitialSteps($super2);
	    $this->assertEquals(count($arr), 1);
	    $start = current($arr);
	    
	    $final = $this->assertProcessPartCorresponds($start, $startP1);
	    $arr = wfEngine_models_classes_StepService::singleton()->getNextSteps($final);
	    $this->assertTrue(empty($arr));
	    
	    wfAuthoring_models_classes_ProcessService::singleton()->deleteProcess($super1);
	    wfAuthoring_models_classes_ProcessService::singleton()->deleteProcess($super2);
	     
	    // multiple
	    $process2 = $this->createLinearProcess();
	    $arr = wfAuthoring_models_classes_ProcessService::singleton()->getInitialSteps($process2);
	    $this->assertEquals(count($arr), 1);
	    $startP2 = current($arr);
	    $process3 = $this->createLinearProcess();
	    $arr = wfAuthoring_models_classes_ProcessService::singleton()->getInitialSteps($process3);
	    $this->assertEquals(count($arr), 1);
	    $startP3 = current($arr);
	     
	    $super3 = $this->createLinearSuperProcess(array($process1, $process2, $process3));
	    $flattener = new wfAuthoring_models_classes_ProcessFlattener($super3);
	    $flattener->flatten();
	     
	    $activities = $super3->getPropertyValues(new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES));
	    $this->assertEquals(count($activities), 9);
	    $arr = wfAuthoring_models_classes_ProcessService::singleton()->getInitialSteps($super3);
	    $this->assertEquals(count($arr), 1);
	    $start = current($arr);
	    
	    $last = $this->assertProcessPartCorresponds($start, $startP1);
	    $start = $this->advanceTwo($last);
	    $last = $this->assertProcessPartCorresponds($start, $startP2);
	    $start = $this->advanceTwo($last);
	    $final = $this->assertProcessPartCorresponds($start, $startP3);
	    $arr = wfEngine_models_classes_StepService::singleton()->getNextSteps($final);
	    $this->assertTrue(empty($arr));
	     
	    wfAuthoring_models_classes_ProcessService::singleton()->deleteProcess($super3);
	     
	    wfAuthoring_models_classes_ProcessService::singleton()->deleteProcess($process1);
	    wfAuthoring_models_classes_ProcessService::singleton()->deleteProcess($process2);
	    wfAuthoring_models_classes_ProcessService::singleton()->deleteProcess($process3);
	     
	}
	
	protected function createLinearProcess() {
	    $processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
	    $processDefinition = $processDefinitionClass->createInstance('process for '.__CLASS__);
	    $this->assertIsA($processDefinition, 'core_kernel_classes_Resource');
	     
	    $activityService = wfAuthoring_models_classes_ActivityService::singleton();
	    $connectorService = wfAuthoring_models_classes_ConnectorService::singleton();
	    $webService = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#ServiceWebService');
	    $webServiceParam = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#WebServiceUrl');
	    
	    $last = null;
	    for ($i = 0; $i < 3; $i++) {
	        $serviceCall = new tao_models_classes_service_ServiceCall($webService);
	        $serviceCall->addInParameter(new tao_models_classes_service_ConstantParameter($webServiceParam, 'https://www.google.com/#q='.substr($processDefinition->getUri(), -5).'_'.$i));
	        $current = $activityService->createActivity($processDefinition, 'L Activity '.$i);
	        $current->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES), $serviceCall->toOntology());
	        if (is_null($last)) {
	            $current->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_TRUE);
	        } else {
	            $connectorService->createSequential($last, $current);
	        }
	        $last = $current;
	    }
	    return $processDefinition;
	}
	
	protected function advanceTwo($processes) {
	    $arr = wfEngine_models_classes_StepService::singleton()->getNextSteps($processes);
	    $this->assertEquals(count($arr), 1);
	    $next = current($arr);
	    $arr = wfEngine_models_classes_StepService::singleton()->getNextSteps($next);
	    $this->assertEquals(count($arr), 1);
	    return current($arr);
	}
	
	protected function createLinearSuperProcess($processes) {
	    $processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
	    $processDefinition = $processDefinitionClass->createInstance('process for '.__CLASS__);
	    $this->assertIsA($processDefinition, 'core_kernel_classes_Resource');
	    
	    $activityService = wfAuthoring_models_classes_ActivityService::singleton();
	    $connectorService = wfAuthoring_models_classes_ConnectorService::singleton();
	    $processRunnerService = new core_kernel_classes_Resource(INSTANCE_SERVICE_PROCESSRUNNER);
	    $processRunnerParam = new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_PROCESSDEFINITION);
	    
	    $last = null;
	    foreach ($processes as $subProcess) {
	        $serviceCall = new tao_models_classes_service_ServiceCall($processRunnerService);
	        $serviceCall->addInParameter(new tao_models_classes_service_ConstantParameter($processRunnerParam, $subProcess));
	        $current = $activityService->createActivity($processDefinition);
	        $current->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES), $serviceCall->toOntology());
	        if (is_null($last)) {
	            $current->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_TRUE);
	        } else {
	            $connectorService->createSequential($last, $current);
	        }
	        $last = $current;
	    }
	    return $processDefinition;
	}
	
	/**
	 * Tests a process, to see if it corresponds to the reference
	 * Returns the last activity after the end of the reference was reached 
	 * 
	 * @param unknown $activity
	 * @param unknown $reference
	 */
	protected function assertProcessPartCorresponds($activity, $reference) {

	    $current = $activity;
	    $currentRef = $reference;
	    
	    do {
	        $this->assertCorresponds($current, $currentRef);
	        
	        $arr = wfEngine_models_classes_StepService::singleton()->getNextSteps($currentRef);
	        $currentRef = count($arr) == 1 ? current($arr) : null;
	        
	        if (!is_null($currentRef)) {
    	        $arr = wfEngine_models_classes_StepService::singleton()->getNextSteps($current);
    	        if (count($arr) != 1) {
    	            $this->fail(count($arr).' next activities instead of 1');
    	            return $current;
    	        } else {
    	           $current = current($arr);
    	        }
	        }
	         
	    } while (!is_null($currentRef));
	    
	    return $current;
	}
	
	protected function assertCorresponds(core_kernel_classes_Resource $step1, core_kernel_classes_Resource $step2) {
	    //echo 'Compare '.$step1->getLabel().' and '.$step2->getLabel().'<br />';
	    $services1 = wfEngine_models_classes_ActivityService::singleton()->getInteractiveServices($step1);
	    $services2 = wfEngine_models_classes_ActivityService::singleton()->getInteractiveServices($step2);
	    $this->assertEquals(count($services1),count($services2));
	    if (count($services1) == count($services2)) {
    	    foreach ($services1 as $service1) {
    	        $service2 = array_shift($services2);
    	        $call1 = tao_models_classes_service_ServiceCall::fromResource($service1);
    	        $call2 = tao_models_classes_service_ServiceCall::fromResource($service2);
    	        $this->assertEquals($call1->serializeToString(), $call2->serializeToString());
    	    }
	    }
	}
	
}