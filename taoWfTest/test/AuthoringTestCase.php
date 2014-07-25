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

require_once dirname(__FILE__) . '/../../tao/test/TaoTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoTests
 * @subpackage test
 */
class AuthoringTestCase extends UnitTestCase {
	
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
	public function testAuthoring(){
		
	    $testClass = new core_kernel_classes_Class(TAO_TEST_CLASS);
		$testInstance = $this->testsService->createInstance($testClass, 'unittest test');
		$this->assertIsA($testInstance, 'core_kernel_classes_Resource');

		// testmodel associated by random, not tested here
		
		$modelInstance = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAOTest.rdf#SimpleTestModel');
		$this->testsService->setTestModel($testInstance, $modelInstance);
		
		$testModel = $this->testsService->getTestModel($testInstance);
		$this->assertTrue($modelInstance->equals($testModel));
		
		$itemClass = new core_kernel_classes_Class(TAO_ITEM_CLASS);
		$items = $itemClass->getInstances(true, array('limit' => 3));
		
		// only test set Items if items exists
		if (count($items) == 3) {
		    
        }
		
		//delete test instance
		$this->assertTrue($testInstance->delete());
	}
	
	
}