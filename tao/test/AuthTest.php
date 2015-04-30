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
use oat\tao\test\TaoPhpUnitTestRunner;

include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package tao
 
 */
class AuthTestCase extends TaoPhpUnitTestRunner {
	
	/**
	 * @var tao_models_classes_UserService
	 */
	protected $userService = null;
	
	/**
	 * @var array user data set
	 */
	protected $testUserData = array(
		PROPERTY_USER_LOGIN		=> 	'jane.doe',
		PROPERTY_USER_PASSWORD	=>	'p34@word',
		PROPERTY_USER_LASTNAME	=>	'Doe',
		PROPERTY_USER_FIRSTNAME	=>	'Jane',
		PROPERTY_USER_MAIL		=>	'jane.doe@tao.lu',
		PROPERTY_USER_DEFLG		=>	'http://www.tao.lu/Ontologies/TAO.rdf#Langen-US',
		PROPERTY_USER_UILG		=>	'http://www.tao.lu/Ontologies/TAO.rdf#Langen-US',
		PROPERTY_USER_ROLES		=>	INSTANCE_ROLE_BACKOFFICE
	);
	
	/**
	 * @var core_kernel_classes_Resource
	 */
	protected $testUser = null;
	
	/**
	 * @var string
	 */
	private $clearPassword = '';
	
	
	/**
	 * tests initialization
	 */
	public function setUp(){		
		TaoPhpUnitTestRunner::initTest();
		
		$this->clearPassword = $this->testUserData[PROPERTY_USER_PASSWORD];
		$this->testUserData[PROPERTY_USER_PASSWORD] = core_kernel_users_Service::getPasswordHash()->encrypt($this->testUserData[PROPERTY_USER_PASSWORD]);
		
		$this->userService = tao_models_classes_UserService::singleton();
		
		$class = new core_kernel_classes_Class(CLASS_GENERIS_USER);
		$this->testUser = $class->createInstance();
		$this->assertNotNull($this->testUser);          
	    $this->userService->bindProperties($this->testUser,$this->testUserData);
		
	}
	
	/**
	 * tests clean up
	 */
	public function tearDown(){
		if (!is_null($this->userService)) {
			$this->userService->removeUser($this->testUser);
		}
		if(tao_models_classes_UserService::singleton()->isASessionOpened()){
			tao_models_classes_UserService::singleton()->logout();
		}
		if(isset($_SESSION)){
		    //session not started in testsuite
		    //session_destroy();
		}
	}

	/* !!!
	 * DO NOT ADD OTHER TEST METHODS 
	 * BECAUSE SESSION IS DESTROYED 
	 * AFTER AuthTestCase::testAuth
	 * IN AuthTestCase::tearDown
	 * !!!
	 */

	/**
	 * test the user authentication to TAO and to the API
	 */
	public function testAuth(){
		//is the user in the db
		$this->assertFalse(	$this->userService->loginAvailable($this->testUserData[PROPERTY_USER_LOGIN]) );
		
		if(tao_models_classes_UserService::singleton()->isASessionOpened()){
			tao_models_classes_UserService::singleton()->logout();
		}
	
		//no other user session
		$this->assertFalse( tao_models_classes_UserService::singleton()->isASessionOpened() );

		//check user login
		$this->assertTrue( $this->userService->loginUser($this->testUserData[PROPERTY_USER_LOGIN], $this->clearPassword));
		
		//check session
		$this->assertTrue( tao_models_classes_UserService::singleton()->isASessionOpened() );
		
		
		$currentUser =  $this->userService->getCurrentUser();
		$this->assertIsA($currentUser, 'core_kernel_classes_Resource');
		foreach($this->testUserData as $prop => $value){
			try{
				$property = new core_kernel_classes_Property($prop);
				$v = $currentUser->getUniquePropertyValue(new core_kernel_classes_Property($prop));
				$v = ($v instanceof core_kernel_classes_Resource) ? $v->getUri() : $v->literal; 
				$this->assertEquals($value, $v);
			}
			catch(common_Exception $ce){ 
				$this->fail($ce);
			}
		}
	}
}