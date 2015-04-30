<?php
/**  
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
use oat\tao\test\TaoPhpUnitTestRunner;
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * Test the user management 
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package tao
 
 */
class UserTestCase extends TaoPhpUnitTestRunner {
	
	/**
	 * @var tao_models_classes_UserService
	 */
	protected $userService = null;
	
	/**
	 * @var array user data set
	 */
	protected $testUserData = array(
		PROPERTY_USER_LOGIN		=> 	'tjdoe',
		PROPERTY_USER_PASSWORD	=>	'test123',
		PROPERTY_USER_LASTNAME	=>	'Doe',
		PROPERTY_USER_FIRSTNAME	=>	'John',
		PROPERTY_USER_MAIL		=>	'jdoe@tao.lu',
		PROPERTY_USER_DEFLG		=>	'http://www.tao.lu/Ontologies/TAO.rdf#Langen-US',
		PROPERTY_USER_UILG		=>	'http://www.tao.lu/Ontologies/TAO.rdf#Langen-US',
		PROPERTY_USER_ROLES		=>  INSTANCE_ROLE_GLOBALMANAGER
	);
	
	/**
	 * @var array user data set with special chars
	 */
	protected $testUserUtf8Data = array(
		PROPERTY_USER_LOGIN		=> 	'f.lecé',
		PROPERTY_USER_PASSWORD	=>	'6crète!',
		PROPERTY_USER_LASTNAME	=>	'Lecéfranc',
		PROPERTY_USER_FIRSTNAME	=>	'François',
		PROPERTY_USER_MAIL		=>	'f.lecé@tao.lu',
		PROPERTY_USER_DEFLG		=>	'http://www.tao.lu/Ontologies/TAO.rdf#Langen-US',
		PROPERTY_USER_UILG		=>	'http://www.tao.lu/Ontologies/TAO.rdf#Langfr-FR',
		PROPERTY_USER_ROLES		=>  INSTANCE_ROLE_GLOBALMANAGER
	);
	
	/**
	 * @var core_kernel_classes_Resource
	 */
	protected $testUser = null;
	
	/**
	 * @var core_kernel_classes_Resource
	 */
	protected $testUserUtf8 = null;
	
	/**
	 * tests initialization
	 */
	public function setUp(){		
		TaoPhpUnitTestRunner::initTest();
		$this->userService = tao_models_classes_UserService::singleton();
		$this->testUserData[PROPERTY_USER_PASSWORD] = core_kernel_users_Service::getPasswordHash()->encrypt($this->testUserData[PROPERTY_USER_PASSWORD]);
		$this->testUserUtf8Data[PROPERTY_USER_PASSWORD] = core_kernel_users_Service::getPasswordHash()->encrypt($this->testUserUtf8Data[PROPERTY_USER_PASSWORD]);
	}
	
	/**
	 * Test the user service implementation
	 * @see tao_models_classes_ServiceFactory::get
	 */
	public function testService(){

		$this->assertIsA($this->userService, 'tao_models_classes_Service');
		$this->assertIsA($this->userService, 'tao_models_classes_UserService');
		
		
	}

	/**
	 * Test user insertion
	 * @see tao_models_classes_UserService::saveUser
	 */
	public function testAddUser(){

		//insert it
		$this->assertTrue($this->userService->loginAvailable($this->testUserData[PROPERTY_USER_LOGIN]));
		$tmclass = new core_kernel_classes_Class(CLASS_TAO_USER);
		$this->testUser = $tmclass->createInstance();
		$this->assertNotNull($this->testUser);
		$this->assertTrue($this->testUser->exists());
		$result = $this->userService->bindProperties($this->testUser, $this->testUserData);
		$this->assertNotNull($result);
		$this->assertNotEquals($result,false);
		$this->assertFalse($this->userService->loginAvailable($this->testUserData[PROPERTY_USER_LOGIN]));
		
		//check inserted data
		$this->testUser = $this->getUserByLogin($this->testUserData[PROPERTY_USER_LOGIN]);
		$this->assertIsA($this->testUser, 'core_kernel_classes_Resource');
		foreach($this->testUserData as $prop => $value){
			try{
				$p = new core_kernel_classes_Property($prop);
				$v = $this->testUser->getUniquePropertyValue($p);
				$v = ($v instanceof core_kernel_classes_Literal) ? $v->literal : $v->getUri();
				$this->assertEquals($value, $v);
			}
			catch(common_Exception $ce){ 
				$this->fail($ce);
			}
		}
	}
	
	/**
	 * Test user insertion with special chars
	 */
	public function testAddUtf8User(){
		
		$this->assertTrue($this->userService->loginAvailable($this->testUserUtf8Data[PROPERTY_USER_LOGIN]));
		$tmclass = new core_kernel_classes_Class(CLASS_TAO_USER);
		$this->testUserUtf8 = $tmclass->createInstance();
		$this->assertNotNull($this->testUserUtf8);
		$this->assertTrue($this->testUserUtf8->exists());
		$result = $this->userService->bindProperties($this->testUserUtf8, $this->testUserUtf8Data);
		$this->assertNotNull($result);
		$this->assertNotEquals($result,false);
		$this->assertFalse($this->userService->loginAvailable($this->testUserUtf8Data[PROPERTY_USER_LOGIN]));
		
		//check inserted data
		$this->testUserUtf8 = $this->getUserByLogin($this->testUserUtf8Data[PROPERTY_USER_LOGIN]);
		$this->assertIsA($this->testUserUtf8, 'core_kernel_classes_Resource');
		foreach($this->testUserUtf8Data as $prop => $value){
			try{
				$p = new core_kernel_classes_Property($prop);
				$v = $this->testUserUtf8->getUniquePropertyValue($p);
				$v = ($v instanceof core_kernel_classes_Literal) ? $v->literal : $v->getUri();
				$this->assertEquals($value, $v);
			}
			catch(common_Exception $ce){ 
				$this->fail($ce);
			}
		}
	}
	
	public function testLoginAvailability(){
		$user = $this->getUserByLogin($this->testUserUtf8Data[PROPERTY_USER_LOGIN]);
		$loginProperty = new core_kernel_classes_Property(PROPERTY_USER_LOGIN);
		
		$this->assertTrue(!empty($user));
		$this->assertFalse($this->userService->loginAvailable($this->testUserUtf8Data[PROPERTY_USER_LOGIN]));
		
		// Test to cover issue #2135
		$this->assertTrue($this->userService->loginAvailable('my new user'));
		$user->editPropertyValues($loginProperty, 'my new user');
		$this->assertTrue($this->userService->loginExists('my new user'));
		$this->assertFalse($this->userService->loginAvailable('my new user'));
		
		$user->EditPropertyValues($loginProperty, $this->testUserUtf8Data[PROPERTY_USER_LOGIN]);
	}
	
	/**
	 * Test user removing
	 * @see tao_models_classes_UserService::removeUser
	 */
	public function testDelete(){
		$this->testUser = $this->getUserByLogin($this->testUserData[PROPERTY_USER_LOGIN]);
		$this->assertIsA($this->testUser, 'core_kernel_classes_Resource');
		$this->assertTrue($this->userService->removeUser($this->testUser));
		$this->assertTrue($this->userService->loginAvailable($this->testUserData[PROPERTY_USER_LOGIN]));
		
		
		$this->testUserUtf8 = $this->getUserByLogin($this->testUserUtf8Data[PROPERTY_USER_LOGIN]);
		$this->assertIsA($this->testUserUtf8, 'core_kernel_classes_Resource');
		$this->assertTrue($this->userService->removeUser($this->testUserUtf8));
		$this->assertTrue($this->userService->loginAvailable($this->testUserUtf8Data[PROPERTY_USER_LOGIN]));
	}
	
	protected function getUserByLogin($login) {
        $class = new core_kernel_classes_Class(CLASS_GENERIS_USER);
        $users = $class->searchInstances(
            array(PROPERTY_USER_LOGIN => $login),
            array('like' => false, 'recursive' => true)
        );

        $this->assertEquals(1, count($users));
        return current($users);
    }
}