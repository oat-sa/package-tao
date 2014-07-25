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
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoTests
 * @subpackage test
 */
class TestsTestCase extends UnitTestCase {
	
	/**
	 * 
	 * @var taoTests_models_classes_TestsService
	 */
	protected $testsService = null;
	
	/**
	 * tests initialization
	 */
	public function setUp(){		
		TaoTestRunner::initTest();
	}
	
	/**
	 * Test the user service implementation
	 * @see tao_models_classes_ServiceFactory::get
	 * @see taoTests_models_classes_TestsService::__construct
	 */
	public function testService(){
		
		$testsService = taoTests_models_classes_TestsService::singleton();
		$this->assertIsA($testsService, 'tao_models_classes_Service');
		$this->assertIsA($testsService, 'taoTests_models_classes_TestsService');
		
		$this->testsService = $testsService;
	}
	
	/**
	 * Usual CRUD (Create Read Update Delete) on the test class  
	 */
	public function testCrud(){
		
		//check parent class
		$this->assertTrue(defined('TAO_TEST_CLASS'));
		$testClass = $this->testsService->getRootclass();
		$this->assertIsA($testClass, 'core_kernel_classes_Class');
		$this->assertEqual(TAO_TEST_CLASS, $testClass->getUri());
		
		//create a subclass
		$subTestClassLabel = 'subTest class';
		$subTestClass = $this->testsService->createSubClass($testClass, $subTestClassLabel);
		$this->assertIsA($subTestClass, 'core_kernel_classes_Class');
		$this->assertEqual($subTestClassLabel, $subTestClass->getLabel());
		$this->assertTrue($this->testsService->isTestClass($subTestClass));
		
		//create instance of Test
		$testInstanceLabel = 'test instance';
		$testInstance = $this->testsService->createInstance($testClass, $testInstanceLabel);
		$this->assertIsA($testInstance, 'core_kernel_classes_Resource');
		$this->assertEqual($testInstanceLabel, $testInstance->getLabel());
		
		//create instance of subTest
		$subTestInstanceLabel = 'subTest instance';
		$subTestInstance = $this->testsService->createInstance($subTestClass);
		$this->assertTrue(defined('RDFS_LABEL'));
		$subTestInstance->removePropertyValues(new core_kernel_classes_Property(RDFS_LABEL));
		$subTestInstance->setLabel($subTestInstanceLabel);
		$this->assertIsA($subTestInstance, 'core_kernel_classes_Resource');
		$this->assertEqual($subTestInstanceLabel, $subTestInstance->getLabel());
		
		$subTestInstanceLabel2 = 'my sub test instance';
		$subTestInstance->setLabel($subTestInstanceLabel2);
		$this->assertEqual($subTestInstanceLabel2, $subTestInstance->getLabel());
		
		
		//delete test instance
		$this->assertTrue($testInstance->delete());
		
		//delete subclass and check if the instance is deleted
		$this->assertTrue($subTestInstance->delete());
		$this->assertFalse($subTestInstance->exists());
		
		$this->assertTrue($subTestClass->delete());
	}
	
	
}
?>