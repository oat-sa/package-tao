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
require_once dirname(__FILE__) . '/../../tao/test/TaoTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

class ProcessDefinitionServiceTestCase extends UnitTestCase {
	
	
	protected $service = null;
	protected $authoringService = null;
	protected $processDefinition = null;
	
	/**
	 * tests initialization
	 */
	public function setUp(){
		TaoTestRunner::initTest();
		
		$processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
		$processDefinition = $processDefinitionClass->createInstance('processForUnitTest','created for the unit test of process definition service');
		if($processDefinition instanceof core_kernel_classes_Resource){
			$this->processDefinition = $processDefinition;
		}else{
			$this->fail('fail to create a process definition resource');
		}
		$this->authoringService = wfAuthoring_models_classes_ProcessService::singleton();
	}
	
	/**
	 * Test the service implementation
	 */
	public function testService(){
		
		$service = wfEngine_models_classes_ProcessDefinitionService::singleton();
		
		$this->assertIsA($service, 'tao_models_classes_Service');
		$this->assertIsA($service, 'wfEngine_models_classes_ProcessDefinitionService');

		$this->service = $service;
		
	}
	
	public function testGetRootActivities(){
		$activity1 = $this->authoringService->createActivity($this->processDefinition);
		$activity2 = $this->authoringService->createActivity($this->processDefinition);
		
		$rootActivities = $this->service->getRootActivities($this->processDefinition);
		$this->assertEqual(count($rootActivities), 1);
		$this->assertEqual($rootActivities[0]->getUri(), $activity1->getUri());
	}
	
	public function testGetAllActivities(){
		$activity1 = $this->authoringService->createActivity($this->processDefinition);
		$activity2 = $this->authoringService->createActivity($this->processDefinition);
		$activity3 = $this->authoringService->createActivity($this->processDefinition);
		
		$allActivities = $this->service->getAllActivities($this->processDefinition);
		$this->assertEqual(count($allActivities), 3);
		
		foreach($allActivities as $activity){
			$this->assertTrue(in_array($activity->getUri(), array($activity1->getUri(), $activity2->getUri(), $activity3->getUri())));
		}
	}
	
	public function testGetProcessVars(){
		
		$processVars = $this->service->getProcessVars($this->processDefinition);
		$this->assertEqual(count($processVars), 1);
		
		$variableService = wfEngine_models_classes_VariableService::singleton();
		$myProcessVarName1 = 'myProcDefVarName1';
		$myProcessVar1 = $variableService->getProcessVariable($myProcessVarName1, true);
		$this->service->setProcessVariable($this->processDefinition, $myProcessVarName1);
		//this works too: $this->service->setProcessVariable($this->processDefinition, $myProcessVar1);
		
		$processVars = $this->service->getProcessVars($this->processDefinition);
		$this->assertEqual(count($processVars), 2);
		$this->assertTrue(isset($processVars[$myProcessVar1->getUri()]));
		$secondProcessVar = $processVars[$myProcessVar1->getUri()];
		
		$this->assertEqual($secondProcessVar['name'], $myProcessVarName1);
		
		$myProcessVar1->delete();
	}
	
	public function tearDown() {
		$this->assertTrue($this->authoringService->deleteProcess($this->processDefinition));
    }

}
?>