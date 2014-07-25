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
 * Test the services of wfEngine
 * 
 * @author Somsack Sipasseuth, <taosupport@tudor.lu>
 * @package wfEngine
 
 */
class wfEngineServiceTest extends TaoPhpUnitTestRunner {
	
	/**
	 * CHANGE IT MANNUALLY to see step by step the output
	 * @var boolean
	 */
	protected $OUTPUT = false;
	
	/**
	 * @var wfEngine_models_classes_UserService
	 */
	protected $userService = null;
	
	/**
	 * @var core_kernel_classes_Resource
	 */
	protected $currentUser = null;
	
	/**
	 * initialize a test method
	 */
	public function setUp(){
		
		TaoPhpUnitTestRunner::initTest();
		
		error_reporting(E_ALL);
		
		$this->userPassword = '123456';
			
		if(is_null($this->userService)){
			$this->userService = wfEngine_models_classes_UserService::singleton();
		}
		
	}
	
	public function tearDown() {
		
    }
	
	/**
	 * output messages
	 * @param string $message
	 * @param boolean $ln
	 * @return void
	 */
	protected function out($message, $ln = false){
		if($this->OUTPUT){
			if(PHP_SAPI == 'cli'){
				if($ln){
					echo "\n";
				}
				echo "$message\n";
			}
			else{
				if($ln){
					echo "<br />";
				}
				echo "$message<br />";
			}
		}
	}
	
	protected function createUser($login){
		
		$returnValue = null;
		
		$userData = array(
			PROPERTY_USER_LOGIN		=> 	$login,
			PROPERTY_USER_PASSWORD	=>	core_kernel_users_AuthAdapter::getPasswordHash()->encrypt($this->userPassword),
			PROPERTY_USER_DEFLG		=>	'http://www.tao.lu/Ontologies/TAO.rdf#Lang'.DEFAULT_LANG,
			PROPERTY_USER_UILG		=>	'http://www.tao.lu/Ontologies/TAO.rdf#Lang'.DEFAULT_LANG,
			PROPERTY_USER_ROLES		=>	INSTANCE_ROLE_WORKFLOW
		);
		
		$user = $this->userService->getOneUser($login);
		if(is_null($user)){
			$userClass = new core_kernel_classes_Class(CLASS_WORKFLOWUSER);
			$user = $userClass->createInstanceWithProperties($userData);
		}
		$returnValue = $user;
		
		if(is_null($returnValue)){
			throw new Exception('cannot get the user with login '.$login);
		}
		
		return $returnValue;
	}
	
	protected function changeUser($login){
		
		$returnValue = false;
		
		//Login another user to execute parallel branch
		$this->logoutUser();
		$loginProperty = new core_kernel_classes_Property(PROPERTY_USER_LOGIN);
		if($this->userService->loginUser($login, $this->userPassword)){
			$this->currentUser = $this->userService->getCurrentUser();
			$returnValue = true;
			$this->out("user logged in: ".$this->currentUser->getOnePropertyValue($loginProperty).' "'.$this->currentUser->getUri().'"');
		}else{
			$this->fail("unable to login user $login");
		}
		
		$activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();
		$activityExecutionService->clearCache('wfEngine_models_classes_ActivityExecutionService::checkAcl');
		
		return $returnValue;
	}
	
	protected function logoutUser(){
		if(!is_null($this->currentUser)){
			$loginProperty = new core_kernel_classes_Property(PROPERTY_USER_LOGIN);
			$this->out("logout ". $this->currentUser->getOnePropertyValue($loginProperty) . ' "' . $this->currentUser->getUri() . '"', true);
			$this->userService->logout();
			$this->currentUser = null;
		}
	}
	
	public function testService(){
		$this->assertIsA($this->userService,'wfEngine_models_classes_UserService');
    }
	
	
	protected function checkAccessControl($activityExecution){
		
		$activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();
		
		$aclMode = $activityExecutionService->getAclMode($activityExecution);
		$restricedRole = $activityExecutionService->getRestrictedRole($activityExecution);
		$restrictedTo = !is_null($restricedRole) ? $restricedRole : $activityExecutionService->getRestrictedUser($activityExecution);
		
		$this->assertNotNull($aclMode);
		if(is_null($restrictedTo)){
			$activity = $activityExecutionService->getExecutionOf($activityExecution);
			$this->fail("cannot get the restricted user or role for the activity execution {$activityExecution->getUri()} of the activity {$activity->getLabel()} ({$activity->getUri()} ");
		}
		$this->out("ACL mode: {$aclMode->getLabel()}; restricted to {$restrictedTo->getLabel()}", true);
	}
	
}
?>