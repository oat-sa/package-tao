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
 * Copyright (c) 2007-2010 (original work) Public Research Centre Henri Tudor & University of Luxembourg) (under the project TAO-QUAL);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
require_once dirname(__FILE__) . '/../../tao/test/TaoPhpUnitTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * Test the service wfEngine_models_classes_RecoveryService
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package wfEngine
 
 */
class RecoveryServiceTestCase extends TaoPhpUnitTestRunner {
	
	
	/**
	 * @var wfEngine_models_classes_ActivityExecutionService the tested service
	 */
	protected $service = null;
	
	protected $activityExecution = null;
	
	
	/**
	 * initialize a test method
	 */
	public function setUp(){
		
		TaoPhpUnitTestRunner::initTest();
		
		$activityExecutionClass = new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION);
		$this->activityExecution = $activityExecutionClass->createInstance('test');
		$this->service = wfEngine_models_classes_RecoveryService::singleton();
	}
	
	
	/**
	 * Test the service implementation
	 */
	public function testService(){
		$this->assertIsA($this->service, 'tao_models_classes_Service');
		$this->assertIsA($this->service, 'wfEngine_models_classes_RecoveryService');


	}
	
	/**
	 * Test the context recovery saving, retrieving and remving
	 */
	public function testContext(){
		
		$context = array(
			'data' => array(
				'boolean'	=> true,
				'integer'	=> 12,
				'array'		=> array(1, 2)
			),
			'other'	=> 12
		);
		
		$this->assertTrue($this->service->saveContext($this->activityExecution, $context));
		
		$recoveredContext = $this->service->getContext($this->activityExecution);
		$this->assertTrue(is_array($recoveredContext));
		$this->assertTrue(isset($recoveredContext['data']['array']));
		
		$this->service->removeContext($this->activityExecution);
		
		$this->assertTrue(count($this->service->getContext($this->activityExecution)) == 0);
		
		$this->assertTrue($this->activityExecution->delete());
	}
	
}
?>