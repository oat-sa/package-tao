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
require_once dirname(__FILE__) . '/../../../tao/test/TaoTestRunner.php';
include_once dirname(__FILE__) . '/../../includes/raw_start.php';

class CleanMassInsertTestCase extends UnitTestCase {

	public function setUp(){

		TaoTestRunner::initTest();
		error_reporting(E_ALL);

		Bootstrap::loadConstants ('tao');
		Bootstrap::loadConstants ('taoGroups');
		Bootstrap::loadConstants ('taoTests');
		Bootstrap::loadConstants ('wfEngine');
		Bootstrap::loadConstants ('taoDelivery');
		
		$this->testService = taoTests_models_classes_TestsService::singleton();
		$this->deliveryService = taoDelivery_models_classes_DeliveryService::singleton();
	}

	public function testRemoveAll(){
		// Remove all subjects
		$subjectClass = new core_kernel_classes_Class(LOCAL_NAMESPACE."#SimulatedTestCaseSubjectClass");
		foreach ($subjectClass->getInstances () as $subject){
			$subject->delete (true);
		}
		$subjectClass->delete (true);
		// Remove all groups
		$groupClass = new core_kernel_classes_Class(LOCAL_NAMESPACE."#SimulatedTestCaseGroupClass");
		foreach ($groupClass->getInstances () as $group){
			$group->delete (true);
		}
		$groupClass->delete (true);
		// Remove all tests
		$testClass = new core_kernel_classes_Class(LOCAL_NAMESPACE."#SimulatedTestCaseTestClass");
		foreach ($testClass->getInstances () as $test){
			$this->testService->deleteTest($test);
		}
		$testClass->delete (true);
		// Remove all deliveries
		$deliveryClass = new core_kernel_classes_Class(LOCAL_NAMESPACE."#SimulatedTestCaseDeliveryClass");
		foreach ($deliveryClass->getInstances () as $delivery){
			$this->deliveryService->deleteDelivery($delivery);
		}
		$deliveryClass->delete (true);
		
		$userService = wfEngine_models_classes_UserService::singleton();
		$users = $userService->getAllUsers();
		$systemUsers = array(LOCAL_NAMESPACE.'#superUser', 'http://www.tao.lu/Ontologies/TAO.rdf#installator');
		foreach($users as $user){
		   
		    if(in_array($user->getUri(),$systemUsers)){
		        continue;
		    }
		    $firstnameProp = new core_kernel_classes_Property(PROPERTY_USER_FIRSTNAME);
		    $lastnameProp = new core_kernel_classes_Property(PROPERTY_USER_LASTNAME );
		    $firstname = $user->getOnePropertyValue($firstnameProp);
		    $lastname = $user->getOnePropertyValue($lastnameProp);
		    
		    if($firstname == 'Generated'&& $lastname== 'Generated'){
		        $user->delete();
		    }
		}
	}

}
?>
