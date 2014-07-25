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
 * Test the group management 
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoGroups
 * @subpackage test
 */
class GroupsTestCase extends UnitTestCase {
	
	/**
	 * @var taoGroups_models_classes_GroupsService
	 */
	protected $groupsService = null;
	
	/**
	 * tests initialization
	 */
	public function setUp(){		
		TaoTestRunner::initTest();
	}
	
	/**
	 * Test the user service implementation
	 * @see tao_models_classes_ServiceFactory::get
	 * @see taoGroups_models_classes_GroupsService::__construct
	 */
	public function testService(){
		
		$groupsService = taoGroups_models_classes_GroupsService::singleton();
		$this->assertIsA($groupsService, 'tao_models_classes_Service');
		$this->assertIsA($groupsService, 'taoGroups_models_classes_GroupsService');
		
		$this->groupsService = $groupsService;
	}
	
	/**
	 * Usual CRUD (Create Read Update Delete) on the group class  
	 */
	public function testCrud(){
		
		//check parent class
		$this->assertTrue(defined('TAO_GROUP_CLASS'));
		$groupClass = $this->groupsService->getRootClass();
		$this->assertIsA($groupClass, 'core_kernel_classes_Class');
		$this->assertEqual(TAO_GROUP_CLASS, $groupClass->getUri());
		
		//create a subclass
		$subGroupClassLabel = 'subGroup class';
		$subGroupClass = $this->groupsService->createSubClass($groupClass, $subGroupClassLabel);
		$this->assertIsA($subGroupClass, 'core_kernel_classes_Class');
		$this->assertEqual($subGroupClassLabel, $subGroupClass->getLabel());
		$this->assertTrue($this->groupsService->isGroupClass($subGroupClass));
		
		//create instance of Group
		$groupInstanceLabel = 'group instance';
		$groupInstance = $this->groupsService->createInstance($groupClass, $groupInstanceLabel);
		$this->assertIsA($groupInstance, 'core_kernel_classes_Resource');
		$this->assertEqual($groupInstanceLabel, $groupInstance->getLabel());
		
		//create instance of subGroup
		$subGroupInstanceLabel = 'subGroup instance';
		$subGroupInstance = $this->groupsService->createInstance($subGroupClass);
		
		$this->assertTrue(defined('RDFS_LABEL'));
		$subGroupInstance->removePropertyValues(new core_kernel_classes_Property(RDFS_LABEL));
		$subGroupInstance->setLabel($subGroupInstanceLabel);
		$this->assertIsA($subGroupInstance, 'core_kernel_classes_Resource');
		$this->assertEqual($subGroupInstanceLabel, $subGroupInstance->getLabel());
		
		$subGroupInstanceLabel2 = 'my sub group instance';
		$subGroupInstance->setLabel($subGroupInstanceLabel2);
		$this->assertEqual($subGroupInstanceLabel2, $subGroupInstance->getLabel());
		
		//delete group instance
		$this->assertTrue($groupInstance->delete());
		
		$this->assertTrue($subGroupInstance->delete());
		$this->assertTrue($subGroupClass->delete());
	}
	
}
?>