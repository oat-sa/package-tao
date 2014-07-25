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


//TODO simpletest testcase that need to be migrate to phpunit

include_once dirname(__FILE__) . '/../includes/raw_start.php';

class ProcessGenerationTestCase extends UnitTestCase {
	
	protected $deliveryService = null;
	protected $delivery = null;
	
	/**
	 * tests initialization
	 */
	public function setUp(){
		TaoTestRunner::initTest();
		
		$this->deliveryService = taoDelivery_models_classes_DeliveryService::singleton();
		$this->delivery = $this->deliveryService->createInstance(new core_kernel_classes_Class(TAO_DELIVERY_CLASS), 'UnitTestDelivery');
	}
	
	public function tearDown() {
	   $this->deliveryService->deleteDelivery($this->delivery);
    }

	public function testGenerateProcess(){
		//create 2 tests with 2 items:
		$itemClass = new core_kernel_classes_Class(TAO_ITEM_CLASS);
		$item1 = $itemClass->createInstance('UnitDelivery Item1', 'Item 1 created for delivery unit test');
		$item2 = $itemClass->createInstance('UnitDelivery Item2', 'Item 2 created for delivery unit test');
		$item3 = $itemClass->createInstance('UnitDelivery Item3', 'Item 3 created for delivery unit test');
		$item4 = $itemClass->createInstance('UnitDelivery Item4', 'Item 4 created for delivery unit test');
		
		//create required test authoring:
		$testsService = taoWfTest_models_classes_WfTestService::singleton();
		$this->assertIsA($testsService, 'tao_models_classes_GenerisService');
		$this->assertIsA($testsService, 'taoTests_models_classes_TestsService');
		
		//create 2 test instances with the tests service (to initialize the test processes)
		$testClass = new core_kernel_classes_Class(TAO_TEST_CLASS);
		$test1 = $testsService->createInstance($testClass, 'UnitDelivery Test1');
		$test2 = $testsService->createInstance($testClass, 'UnitDelivery Test2');
		$this->assertIsA($test1, 'core_kernel_classes_Resource');
		$this->assertIsA($test2, 'core_kernel_classes_Resource');
		$this->assertIsA($test1->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP)), 'core_kernel_classes_Resource');
		
		//set item 1 and 2 to test 1 and items 3 and 4 to test 2
		$this->assertTrue($testsService->setTestItems($test1, array($item1, $item2)));
		$this->assertTrue($testsService->setTestItems($test2, array($item3, $item4)));
		
		//set the 2 tests to the delivery
		$this->assertTrue($this->deliveryService->setDeliveryTests($this->delivery, array($test1, $test2)));
		$this->assertEqual(count($this->deliveryService->getDeliveryTests($this->delivery)), 2);
		
		//generate the actual delivery process:
		$generationResult = $this->deliveryService->generateProcess($this->delivery);
		$this->assertTrue($generationResult['success']);
		$deliveryProcess = $this->delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_PROCESS));
		
		$authoringService = taoDelivery_models_classes_DeliveryAuthoringService::singleton();
		$this->assertEqual(count($authoringService->getActivitiesByProcess($deliveryProcess)), 4);//there should be 4 activities (i.e. items)
	
		$item1->delete();
		$item2->delete();
		$item3->delete();
		$item4->delete();
		$testsService->deleteTest($test1);
		$testsService->deleteTest($test2);
	}
	
	
	public function testGenerateProcessConditionalTest(){
		$id = "!item: UnitDelivery ";
		
		
		//create 2 tests with 2 items:
		$itemClass = new core_kernel_classes_Class(TAO_ITEM_CLASS);
		$item1 = $itemClass->createInstance('UnitDelivery Item1', 'Item 1 created for delivery unit test');
		$item2 = $itemClass->createInstance('UnitDelivery Item2', 'Item 2 created for delivery unit test');
		$item3 = $itemClass->createInstance('UnitDelivery Item3', 'Item 3 created for delivery unit test');
		$item4 = $itemClass->createInstance('UnitDelivery Item4', 'Item 4 created for delivery unit test');
		
		//create required test authoring:
		$testsService = taoTests_models_classes_TestsService::singleton();
		$this->assertIsA($testsService, 'tao_models_classes_GenerisService');
		$this->assertIsA($testsService, 'taoTests_models_classes_TestsService');
		
		//create 2 test instances with the tests service (to initialize the test processes)
		$testClass = new core_kernel_classes_Class(TAO_TEST_CLASS);
		$test1 = $testsService->createInstance($testClass, 'UnitDelivery Test1');
		$test2 = $testsService->createInstance($testClass, 'UnitDelivery Test2');
		$this->assertIsA($test1, 'core_kernel_classes_Resource');
		$this->assertIsA($test2, 'core_kernel_classes_Resource');
		
		$this->assertIsA($test1->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP)), 'core_kernel_classes_Resource');
		
		//set item 1 and 2 to test 1 and items 3 and 4 to test 2
		$processTest1 = $test1->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
				
		$authoringService = taoDelivery_models_classes_DeliveryAuthoringService::singleton();
		$activityItem1 = $authoringService->createActivity($processTest1, "{$id}Item_1");
		$activityItem1->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_TRUE);
		$connectorItem1 = $authoringService->createConnector($activityItem1);
		
		$activityItem2 = $authoringService->createConditionalActivity($connectorItem1, 'then', null, "{$id}Item_2");//create actiivty for item 2:
		$activityItem3 = $authoringService->createConditionalActivity($connectorItem1, 'else', null, "{$id}Item_3");
		
		//processTest2 is sequential:
		$this->assertTrue($testsService->setTestItems($test2, array($item4)));
		
		//set the 2 tests to the delivery sequentially:
		$this->assertTrue($this->deliveryService->setDeliveryTests($this->delivery, array($test1, $test2)));
		$this->assertEqual(count($this->deliveryService->getDeliveryTests($this->delivery)), 2);
		
		//generate the actual delivery process:
		$generationResult = $this->deliveryService->generateProcess($this->delivery);
		$this->assertTrue($generationResult['success']);$deliveryProcess = $this->delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_PROCESS));
				
		$this->assertEqual(count($authoringService->getActivitiesByProcess($deliveryProcess)), 4);//there should be 4 activities (i.e. items)
		
		$item1->delete();
		$item2->delete();
		$item3->delete();
		$item4->delete();
		$testsService->deleteTest($test1);
		$testsService->deleteTest($test2);
	}
	
	
	public function testGenerateProcessConditionalDelivery(){
		$prefix_item = "!item: UnitCondDelivery ";
		$prefix_test = "!test: UnitCondDelivery ";
		
		//create 2 tests with 2 items:
		$itemClass = new core_kernel_classes_Class(TAO_ITEM_CLASS);
		$item1 = $itemClass->createInstance('UnitDelivery Item1', 'Item 1 created for delivery unit test');
		$item2 = $itemClass->createInstance('UnitDelivery Item2', 'Item 2 created for delivery unit test');
		$item3 = $itemClass->createInstance('UnitDelivery Item3', 'Item 3 created for delivery unit test');
		$item4 = $itemClass->createInstance('UnitDelivery Item4', 'Item 4 created for delivery unit test');
		$item5 = $itemClass->createInstance('UnitDelivery Item5', 'Item 5 created for delivery unit test');
		
		//create required test authoring:
		$testsService = taoTests_models_classes_TestsService::singleton();
		$this->assertIsA($testsService, 'tao_models_classes_GenerisService');
		$this->assertIsA($testsService, 'taoTests_models_classes_TestsService');
		
		//create 2 test instances with the tests service (to initialize the test processes)
		$testClass = new core_kernel_classes_Class(TAO_TEST_CLASS);
		$test1 = $testsService->createInstance($testClass, 'UnitDelivery Test1');
		$test2 = $testsService->createInstance($testClass, 'UnitDelivery Test2');
		$test3 = $testsService->createInstance($testClass, 'UnitDelivery Test3');
		$this->assertIsA($test1, 'core_kernel_classes_Resource');
			
		$this->assertIsA($test1->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP)), 'core_kernel_classes_Resource');
		
		//init authoring service:
		$authoringService = taoDelivery_models_classes_DeliveryAuthoringService::singleton();
		
		//set item 1 and 2 to test 1 and items 3 and 4 to test 2
		$processTest1 = $test1->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
		
		
		$activityItem1 = $authoringService->createActivity($processTest1, "{$prefix_item}Item_1");
		$activityItem1->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_TRUE);
		$connectorItem1 = $authoringService->createConnector($activityItem1);
		
		$activityItem2 = $authoringService->createConditionalActivity($connectorItem1, 'then', null, "{$prefix_item}Item_2");//create actiivty for item 2:
		$activityItem3 = $authoringService->createConditionalActivity($connectorItem1, 'else', null, "{$prefix_item}Item_3");
		
		//processTest2 and 3 are sequential:
		$this->assertTrue($testsService->setTestItems($test2, array($item4)));
		$this->assertTrue($testsService->setTestItems($test3, array($item5)));
		
		//set the 3 tests in a conditional delivery:
		$processDelivery = $this->delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
		$activityTest1 = $authoringService->createActivity($processDelivery, "{$prefix_test}Test_1");
		$activityTest1->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_TRUE);
		$connectorTest1 = $authoringService->createConnector($activityTest1);
		$activityTest2 = $authoringService->createConditionalActivity($connectorTest1, 'then', null, "{$prefix_test}Test_2");//create actiivty for item 2:
		$activityTest3 = $authoringService->createConditionalActivity($connectorTest1, 'else', null, "{$prefix_test}Test_3");
		
		$interactiveService = $authoringService->setTestByActivity($activityTest1, $test1);
		$this->assertNotNull($interactiveService);
		$interactiveService = $authoringService->setTestByActivity($activityTest2, $test2);
		$interactiveService = $authoringService->setTestByActivity($activityTest3, $test3);
				
		//generate the actual delivery process:
		$generationResult = $this->deliveryService->generateProcess($this->delivery);
		$this->assertTrue($generationResult['success']);
		
		$deliveryProcess = $this->delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_PROCESS));
		$this->assertIsA($deliveryProcess, 'core_kernel_classes_Resource');
		$deliveryProcessChecker = new taoDelivery_models_classes_DeliveryProcessChecker($deliveryProcess);
		$this->assertTrue($deliveryProcessChecker->check());
		
		
		$this->assertEqual(count($authoringService->getActivitiesByProcess($deliveryProcess)), 5);
	
		$item1->delete();
		$item2->delete();
		$item3->delete();
		$item4->delete();
		$item5->delete();
		$testsService->deleteTest($test1);
		$testsService->deleteTest($test2);
		$testsService->deleteTest($test3);
	}
	
}

