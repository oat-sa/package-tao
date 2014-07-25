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
require_once dirname(__FILE__) . '/../../tao/test/TaoPhpUnitTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * Test the group management 
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoGroups
 
 */
class GroupsTest extends TaoPhpUnitTestRunner {
	
	/**
	 * @var taoGroups_models_classes_GroupsService
	 */
	protected $groupsService = null;
	
	protected $subjectsService = null;
	
	/**
	 * tests initialization
	 */
	public function setUp(){		
		TaoPhpUnitTestRunner::initTest();
		$this->subjectsService = taoSubjects_models_classes_SubjectsService::singleton();
		$this->groupsService = taoGroups_models_classes_GroupsService::singleton();
	}
	
	/**
	 * Test the user service implementation
	 * @see tao_models_classes_ServiceFactory::get
	 * @see taoGroups_models_classes_GroupsService::__construct
	 */
	public function testService(){
		
	
		$this->assertIsA($this->groupsService, 'tao_models_classes_Service');
		$this->assertIsA($this->groupsService, 'taoGroups_models_classes_GroupsService');
		

	}
	
	
	/**
	 * Usual CRUD (Create Read Update Delete) on the group class  
	 */
	public function testCrud(){
		
		//check parent class
		$this->assertTrue(defined('TAO_GROUP_CLASS'));
		$groupClass = $this->groupsService->getRootClass();
		$this->assertIsA($groupClass, 'core_kernel_classes_Class');
		$this->assertEquals(TAO_GROUP_CLASS, $groupClass->getUri());
		
		//create a subclass
		$subGroupClassLabel = 'subGroup class';
		$subGroupClass = $this->groupsService->createSubClass($groupClass, $subGroupClassLabel);
		$this->assertIsA($subGroupClass, 'core_kernel_classes_Class');
		$this->assertEquals($subGroupClassLabel, $subGroupClass->getLabel());
		$this->assertTrue($this->groupsService->isGroupClass($subGroupClass));
		
		//create instance of Group
		$groupInstanceLabel = 'group instance';
		$groupInstance = $this->groupsService->createInstance($groupClass, $groupInstanceLabel);
		$this->assertIsA($groupInstance, 'core_kernel_classes_Resource');
		$this->assertEquals($groupInstanceLabel, $groupInstance->getLabel());
		
		//create instance of subGroup
		$subGroupInstanceLabel = 'subGroup instance';
		$subGroupInstance = $this->groupsService->createInstance($subGroupClass);
		
		$this->assertTrue(defined('RDFS_LABEL'));
		$subGroupInstance->removePropertyValues(new core_kernel_classes_Property(RDFS_LABEL));
		$subGroupInstance->setLabel($subGroupInstanceLabel);
		$this->assertIsA($subGroupInstance, 'core_kernel_classes_Resource');
		$this->assertEquals($subGroupInstanceLabel, $subGroupInstance->getLabel());
		
		$subGroupInstanceLabel2 = 'my sub group instance';
		$subGroupInstance->setLabel($subGroupInstanceLabel2);
		$this->assertEquals($subGroupInstanceLabel2, $subGroupInstance->getLabel());
		
		//delete group instance
		$this->assertTrue($groupInstance->delete());
		
		$this->assertTrue($subGroupInstance->delete());
		$this->assertTrue($subGroupClass->delete());
	}
	
	
	public function testGetGroups(){
	    $groupClass = new core_kernel_classes_Class(TAO_GROUP_CLASS);
	    $this->assertTrue($this->groupsService->isGroupClass($groupClass));
	     
	    $subject = $this->subjectsService->createInstance($this->subjectsService->getRootClass(),'testSubject');
	    $oneGroup = $groupClass->createInstance('testGroupInstance');
	    
	    $oneGroup->setPropertiesValues(array(TAO_GROUP_MEMBERS_PROP => $subject->getUri()) );
	    $oneGroup2 = $groupClass->createInstance('testGroupInstance2');
	    
	    $subclass = $groupClass->createSubClass('testGroupSubclass');
	    $oneGroup3 = $subclass->createInstance('testSubGroupInstance');
	    $oneGroup3->setPropertiesValues(array(TAO_GROUP_MEMBERS_PROP => $subject->getUri()) );
	    
	    $groups = $this->groupsService->getGroups($subject->getUri());
	    
	    $this->assertTrue(is_array($groups));
	    $this->assertTrue(count($groups) ==2);
	    $this->assertTrue(array_key_exists( $oneGroup->getUri(),$groups));
	    $this->assertFalse(array_key_exists( $oneGroup2->getUri(),$groups));
	    $this->assertTrue(array_key_exists( $oneGroup3->getUri(),$groups));


	    $this->assertTrue($this->groupsService->deleteGroup($oneGroup));
	    $this->assertTrue($this->groupsService->deleteGroup($oneGroup2));
	    $this->assertTrue($this->groupsService->deleteGroup($oneGroup3));
	    
	    $this->assertTrue($this->groupsService->deleteGroupClass($subclass));

	    $subject->delete();
	}
	
	public function testSetRelatedSubjects(){
        $groupClass = new core_kernel_classes_Class(TAO_GROUP_CLASS);
        $memberProp = new core_kernel_classes_Property(TAO_GROUP_MEMBERS_PROP);
        $subject = $this->subjectsService->createInstance($this->subjectsService->getRootClass(),'testSubject');
        $subject2 = $this->subjectsService->createInstance($this->subjectsService->getRootClass(),'testSubject2');
        
        $oneGroup = $groupClass->createInstance('testGroupInstance');
        $this->assertTrue($this->groupsService->setRelatedSubjects($oneGroup, array($subject,$subject2)));
        
        $members = $oneGroup->getPropertiesValues(array($memberProp));

        $this->assertTrue(isset($members[TAO_GROUP_MEMBERS_PROP]));

        $this->assertTrue(count($members[TAO_GROUP_MEMBERS_PROP]) == 2);

        foreach ($members[TAO_GROUP_MEMBERS_PROP] as $sub){
            $this->assertTrue(in_array($sub->getUri(), array($subject->getUri(),$subject2->getUri())));
        }
        
        $subject->delete();
        $subject2->delete();
        $oneGroup->delete();
        
    }
    
    public function testGetRelatedSubjects(){
        $groupClass = new core_kernel_classes_Class(TAO_GROUP_CLASS);
        $memberProp = new core_kernel_classes_Property(TAO_GROUP_MEMBERS_PROP);
        $subject = $this->subjectsService->createInstance($this->subjectsService->getRootClass(),'testSubject');
        $subject2 = $this->subjectsService->createInstance($this->subjectsService->getRootClass(),'testSubject2');
    
        $oneGroup = $groupClass->createInstance('testGroupInstance');
        $this->assertTrue($this->groupsService->setRelatedSubjects($oneGroup, array($subject,$subject2)));
    
        $members = $this->groupsService->getRelatedSubjects($oneGroup);
    
        $this->assertTrue(count($members) == 2);
    
        foreach ($members as $sub){
            $this->assertTrue(in_array($sub, array($subject->getUri(),$subject2->getUri())));
        }

        $subject->delete();
        $subject2->delete();
        $oneGroup->delete();
    
    }
	
	
}
?>