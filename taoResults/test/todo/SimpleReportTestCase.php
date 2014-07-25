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
require_once dirname(__FILE__) . '/../../tao/test/TaoTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 *
 * @author Patrick plichart, <patrick@taotesting.com>
 * @package taoResults
 
 */

//todo ppl move the setup to an helper
class SimpleReportTestCase extends UnitTestCase {
	
	/**
	 * 
	 * @var taoResults_models_classes_StatisticsService
	 */
	private $statsService = null;
	/**
	 * 
	 * @var taoResults_models_classes_ReportService
	 */
	private $reportService = null;
	
	/**
	 * the data set produced by the statistics service
	 */
	private $dataSet;
	
	
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
	private $resultsService;
	/**
	 * tests initialization
	 */
	public function setUp(){		
		TaoTestRunner::initTest();
		
		$resultsService = taoResults_models_classes_ResultsService::singleton();
		$this->resultsService = $resultsService;
		
		$this->statsService = taoResults_models_classes_StatisticsService::singleton();
		$this->reportService = taoResults_models_classes_ReportService::singleton();
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
	** @see tao_models_classes_ServiceFactory::get
	 * @see taoResults_models_classes_ResultsService::__construct
	 */
	public function testService(){
		$this->assertIsA($this->statsService, 'taoResults_models_classes_StatisticsService');
		$this->assertIsA($this->reportService, 'taoResults_models_classes_ReportService');
	}
		
	public function testExtractDeliveryDataSet(){
		$dataSet = $this->statsService->extractDeliveryDataSet($this->subClass);
		$this->dataSet = $dataSet;
		$this->assertEqual($dataSet["nbExecutions"],1);
		$this->assertEqual(count($dataSet["statisticsPerVariable"]),1);
		$this->assertEqual(count($dataSet["statisticsPerVariable"]["GRADE"]),6);
		$this->assertEqual($dataSet["statisticsPerVariable"]["GRADE"]["sum"],0.4);
		$this->assertEqual($dataSet["statisticsPerVariable"]["GRADE"]["#"],1);
		$this->assertEqual($dataSet["statisticsPerVariable"]["GRADE"]["data"],array(0.4));
		$this->assertEqual($dataSet["statisticsPerVariable"]["GRADE"]["naturalid"], " (GRADE)");
		$this->assertEqual($dataSet["statisticsPerVariable"]["GRADE"]["avg"], 0.4);
		$this->assertEqual(count($dataSet["statisticsPerVariable"]["GRADE"]["splitData"]), 1);
		$this->assertEqual(count($dataSet["statisticsPerVariable"]["GRADE"]["splitData"][1]), 3);
		$this->assertEqual($dataSet["statisticsPerVariable"]["GRADE"]["splitData"][1]["sum"], 0.4);
		$this->assertEqual($dataSet["statisticsPerVariable"]["GRADE"]["splitData"][1]["avg"], 0.4);
		$this->assertEqual($dataSet["statisticsPerVariable"]["GRADE"]["splitData"][1]["#"], 1);
		
		$this->assertEqual($dataSet["statistics"]["sum"],0.4);
		$this->assertEqual($dataSet["statistics"]["#"],1);
		$this->assertEqual($dataSet["statistics"]["data"],array(0.4));
		$this->assertEqual(count($dataSet["statistics"]["splitData"]), 1);
		$this->assertEqual(count($dataSet["statistics"]["splitData"][1]), 3);
		$this->assertEqual($dataSet["statistics"]["splitData"][1]["sum"], 0.4);
		$this->assertEqual($dataSet["statistics"]["splitData"][1]["avg"], 0.4);
		$this->assertEqual($dataSet["statistics"]["splitData"][1]["#"], 1);
		$this->assertEqual($dataSet["statistics"]["avg"], 0.4);
		
	}
	
	public function testSetDataSet(){
	$this->reportService->setDataSet($this->dataSet);
	    
	}
	
	public function testBuildSimpleReport(){
	    //problem with the graph generation, not tested for the moment
	    //$report = $this->reportService->buildSimpleReport();
	    
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