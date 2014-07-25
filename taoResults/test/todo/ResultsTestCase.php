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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
//TODO simpletest testcase that need to be migrate to phpunit
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 *
 * @author Patrick plichart, <patrick@taotesting.com>
 * @package taoResults
 
 */
class ResultsTestCase extends UnitTestCase {
	
	/**
	 * 
	 * @var taoResults_models_classes_ResultsService
	 */
	private $resultsService = null;
	
	//a stored grade
	private $grade = null;
	
	//a stored response
	private $response = null;
	//core_kernel_classes_Class where the delviery result is being ceated
	protected $subClass;
	private $delivery;
	private $activityExecution;
	private $activityDefinition;
	private $interactiveService;
	/**
	 * tests initialization
	 */
	public function setUp(){		
		TaoTestRunner::initTest();
		
		$resultsService = taoResults_models_classes_ResultsService::singleton();
		$this->resultsService = $resultsService;
		//create an activity execution
		$activityExecutionClass = new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION);
		
		//create an activity definition
		$activityDefinitionClass = new core_kernel_classes_Class(CLASS_ACTIVITIES);
		
		
		$this->activityExecution = $activityExecutionClass->createInstance("MyActivityExecution");
		
		//links the activity execution to the activity definition
		$this->activityDefinition = $activityDefinitionClass->createInstance("MyActivityDefinition");
		$this->activityExecution->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_ACTIVITY), $this->activityDefinition->getUri());

		//links the call of service to the activity execution 
		$interactiveServiceClass = new core_kernel_classes_Class(CLASS_CALLOFSERVICES);
		$this->interactiveService = $interactiveServiceClass->createInstance("MyInteractiveServiceCall");
		$this->activityDefinition->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES), $this->interactiveService->getUri());
		
		
		$this->interactiveService->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION), "#interactiveServiceDefinition");
		
		
		$deliveryResult = new core_kernel_classes_Resource("#MyDeliveryResult");
		$variableIDentifier = "GRADE";
		$value = 0.4;
		//create a small delivery
		$this->subClass = $this->resultsService->createSubClass(new core_kernel_classes_Class(TAO_DELIVERY_RESULT), "UnitTestingGenClass");
		$this->delivery = $this->subClass->createInstance("UnitTestingGenDelivery");
		$this->delivery->setPropertyValue(new core_kernel_classes_Property(PROPERTY_RESULT_OF_DELIVERY), "#unitTestResultOfDelivery");
		$this->delivery->setPropertyValue(new core_kernel_classes_Property(PROPERTY_RESULT_OF_SUBJECT), "#unitTestResultOfSubject");
		$this->delivery->setPropertyValue(new core_kernel_classes_Property(PROPERTY_RESULT_OF_PROCESS), "#unitTestResultOfProcess");
		//stores a grade in this delivery
		$this->grade = $this->resultsService->storeGrade($this->delivery,$this->activityExecution, $variableIDentifier, $value);
		$this->response = $this->resultsService->storeResponse($this->delivery,$this->activityExecution, $variableIDentifier, $value);
	}
	
	/**
	 * Test the user service implementation
	 * @see tao_models_classes_ServiceFactory::get
	 * @see taoResults_models_classes_ResultsService::__construct
	 */
	public function testService(){
		$this->assertIsA($this->resultsService, 'tao_models_classes_GenerisService');
		$this->assertIsA($this->resultsService, 'tao_models_classes_Service');
		$this->assertIsA($this->resultsService, 'taoResults_models_classes_ResultsService');
	}
		
	public function testStoreGrade(){
	    $this->assertIsA($this->grade, 'core_kernel_classes_Resource');
	     //$this->fail("Not implemented yet");
	}
	
	public function testStoreResponse(){
	    $this->assertIsA($this->response, 'core_kernel_classes_Resource');
	     //$this->fail("Not implemented yet");
	}
	public function testGetScoreVariables(){
	    $deliveryResult = $this->delivery;
	    $scoreVariables = $this->resultsService->getScoreVariables($deliveryResult);
	    //tricky if the unit test fails, it probably means that there is some ghost data not correctly removed from previous executions 
	    $this->assertEqual(count($scoreVariables),1);
	     $variable = array_pop($scoreVariables);
	     $this->assertIsA($variable, 'core_kernel_classes_Resource');
	    $value = $variable->getUniquePropertyValue(new core_kernel_classes_Property(RDF_VALUE));
	    $variableIdentifier = $variable->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_VARIABLE_IDENTIFIER));
    	    $variableOrigin = $variable->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_VARIABLE_ORIGIN));
	    $this->assertEqual($value,"0.4");
	    $this->assertEqual($variableIdentifier,"GRADE");
	     $this->assertIsA($variableOrigin, 'core_kernel_classes_Resource');
	    $this->assertEqual($variableOrigin->getLabel(),"MyActivityExecution");
	}
	
	public function testGetVariables(){
	    $deliveryResult = $this->delivery;
	    $scoreVariables = $this->resultsService->getVariables($deliveryResult);
	    
	    //tricky if the unit test fails, it probably means that there is some ghost data not correctly removed from previous executions 
	    $this->assertEqual(count($scoreVariables),2);
	     $variable = array_pop($scoreVariables);
	    
	     $this->assertIsA($variable, 'core_kernel_classes_Resource');
	    $value = $variable->getUniquePropertyValue(new core_kernel_classes_Property(RDF_VALUE));
	    $variableIdentifier = $variable->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_VARIABLE_IDENTIFIER));
    	    $variableOrigin = $variable->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_VARIABLE_ORIGIN));
	    
	    $this->assertEqual($value,"0.4");
	    $this->assertEqual($variableIdentifier,"GRADE");
	    $this->assertIsA($variableOrigin, 'core_kernel_classes_Resource');
	    $this->assertEqual($variableOrigin->getLabel(),"MyActivityExecution");
	}
	
	public function getScoreVariables(){
	    $deliveryResult = $this->delivery;
	    $scoreVariables = $this->resultsService->getScoreVariables($deliveryResult);
	    //tricky if the unit test fails, it probably means that there is some ghost data not correctly removed from previous executions 
	    $this->assertEqual(count($scoreVariables),1);
	}
	
	public function testGetTestTaker(){
		 $deliveryResult = $this->delivery;
		
		$testTaker = $this->resultsService->getTestTaker($deliveryResult);
		 $this->assertIsA($testTaker, 'core_kernel_classes_Resource');
		 $this->assertEqual($testTaker->getUri(),"#unitTestResultOfSubject");
	}
	
	public function testDeliveryManagement(){
	     //checks that the delviery created for the unit test has been successfully created
	     $this->assertIsA($this->subClass, 'core_kernel_classes_Class');
	     $this->assertEqual($this->subClass->getLabel(),"UnitTestingGenClass");
	     
	     $this->assertIsA($this->delivery, 'core_kernel_classes_Resource');
	     $this->assertEqual($this->delivery->getLabel(),"UnitTestingGenDelivery");
	     $this->assertEqual(count($this->subClass->getInstances()),1);
	     
	     
	}
	public function testGetVariableData(){
	    
	    $variableData = $this->resultsService->getVariableData($this->grade);
	    $this->assertEqual(count($variableData),3);
	    $this->assertEqual($variableData["value"],0.4);
	    $this->assertEqual($variableData["variableIdentifier"], "GRADE"); 
	    
	}
	
	public function testGetRootClass(){
	    $rootResultClass = $this->resultsService->getRootClass();
	    $this->assertIsA($rootResultClass, "core_kernel_classes_Class");
	    $this->assertEqual($rootResultClass->getUri(),TAO_DELIVERY_RESULT);
	}
	
	public function tearDown(){
	    $this->assertTrue($this->grade->delete());
	    $this->assertTrue($this->subClass->delete());
	    $this->assertTrue($this->delivery->delete());
	    $this->assertTrue($this->activityExecution->delete());
	    $this->assertTrue($this->activityDefinition->delete());
	    $this->assertTrue($this->interactiveService->delete());
	    
	}
}   
?>