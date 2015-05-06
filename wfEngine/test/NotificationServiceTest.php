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
 * Test the service wfEngine_models_classes_NotificationService
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package wfEngine
 
 */
class NotificationServiceTest extends TaoPhpUnitTestRunner {
	
	/**
	 * CHANGE IT MANNUALLY to see step by step the output
	 * @var boolean
	 */
	const OUTPUT = false;
	
	/**
	 * @var wfEngine_models_classes_ActivityExecutionService the tested service
	 */
	protected $service = null;
	
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
		
		if(is_null($this->userService)){
			$this->userService = wfEngine_models_classes_UserService::singleton();
		}
		
		$login = 'wfTester';
		$pass = 'test123';
		$langResource = tao_models_classes_LanguageService::singleton()->getLanguageByCode(DEFAULT_LANG);
		$userData = array(
			PROPERTY_USER_LOGIN			=> 	$login,
			PROPERTY_USER_PASSWORD		=>	core_kernel_users_AuthAdapter::getPasswordHash()->encrypt($pass),
			PROPERTY_USER_DEFLG			=>	$langResource,
			PROPERTY_USER_MAIL			=>  'somsack.sipasseuth@tudor.lu',
			PROPERTY_USER_FIRSTNAME  	=>	'Sammy',
			PROPERTY_USER_LASTNAME  	=>	'Norville Rogers',
			PROPERTY_USER_ROLES			=>  INSTANCE_ROLE_WORKFLOW
		);
		
		$this->currentUser = $this->userService->getOneUser($login);
		$this->assertNull($this->currentUser);
		if(is_null($this->currentUser)){
			$userClass = new core_kernel_classes_Class(CLASS_GENERIS_USER);
			$this->currentUser = $userClass->createInstanceWithProperties($userData);
		}
		
		$this->userService->logout();
		$this->assertTrue($this->userService->loginUser($login, $pass));
		$this->assertEquals($this->currentUser->getUri(),$this->userService->getCurrentUser()->getUri());
		
		$this->service = wfEngine_models_classes_NotificationService::singleton();
	}
	
	public function tearDown() {
	    $this->assertNotNull($this->currentUser);
		$this->currentUser->delete();
    }
	
	/**
	 * output messages
	 * @param string $message
	 * @param boolean $ln
	 * @return void
	 */
	private function out($message, $ln = false){
		if(self::OUTPUT){
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
	
	/**
	 * Test the service implementation
	 */
	public function testService(){
		

		$this->assertIsA($this->service, 'tao_models_classes_Service');
		$this->assertIsA($this->service, 'wfEngine_models_classes_NotificationService');


	}
	
	public function testCreateNotification(){
		$processDefinitionLabel = 'proc_def_label';
		$processInstanceLabel = 'proc_inst_label';
		$activity1Label = 'activity1';
		$activity2Label = 'activity2';
		$notificationMessage = '';
		
		$processAuthoringService = wfAuthoring_models_classes_ProcessService::singleton();
		$processExecutionService = wfEngine_models_classes_ProcessExecutionService::singleton();
		$activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();
		$processVariableService = wfEngine_models_classes_VariableService::singleton();
		
		//create some process variables:
		$vars = array();
		$varCodes = array(
			'unitLabel', //to be initialized
			'countryCode', //to be initialized
			'languageCode' //to be initialized
		);
		
		foreach($varCodes as $varCode){
			$vars[$varCode] = $processVariableService->getProcessVariable($varCode, true);
		}
		
		$spx = array();
		$spxDefinition = array(
			VAR_PROCESS_INSTANCE => array(RDFS_LABEL),
			VAR_ACTIVITY_DEFINITION => array(RDFS_LABEL),
			VAR_ACTIVITY_INSTANCE => array(
				RDFS_LABEL,
				$vars['unitLabel']->getUri(),
				$vars['countryCode']->getUri(),
				$vars['languageCode']->getUri()
			),
			VAR_CURRENT_USER => array(
				RDFS_LABEL,
				PROPERTY_USER_FIRSTNAME,
				PROPERTY_USER_LASTNAME
			)
		);
		foreach($spxDefinition as $contextUri => $predicates){
			$context = new core_kernel_classes_Resource($contextUri);
			$spx[$contextUri] = array();
			foreach($predicates as $predicateUri){
				$term = $this->createSPX($context, new core_kernel_classes_Property($predicateUri));
				$this->assertNotNull($term);
				$spx[$contextUri][$predicateUri] = $term;
			}
		}
		
		$notificationMessage = '
			Dear {{'.$spx[VAR_CURRENT_USER][PROPERTY_USER_FIRSTNAME]->getUri().'}} {{'.$spx[VAR_CURRENT_USER][PROPERTY_USER_LASTNAME]->getUri().'}},

			Please join the translation process of the unit {{'.$spx[VAR_ACTIVITY_INSTANCE][$vars['unitLabel']->getUri()]->getUri().'}} to {{'.$spx[VAR_ACTIVITY_INSTANCE][$vars['languageCode']->getUri()]->getUri().'}}_{{'.$spx[VAR_ACTIVITY_INSTANCE][$vars['countryCode']->getUri()]->getUri().'}} to complete your next task "{{'.$spx[VAR_ACTIVITY_DEFINITION][RDFS_LABEL]->getUri().'}}".

			Bests,
			The Workflow Engine
			';
		
		$processDefinition = $processAuthoringService->createProcess($processDefinitionLabel, 'create for notification service test case');
		$this->assertNotNull($processDefinition);
		
		$activity1 = $processAuthoringService->createActivity($processDefinition, $activity1Label);
		$this->assertNotNull($processDefinition);
		
		$this->assertTrue($processAuthoringService->setFirstActivity($processDefinition, $activity1));
		
		$connector1 = $processAuthoringService->createConnector($activity1);
		$this->assertNotNull($processDefinition);
		
		$activity2 = $processAuthoringService->createSequenceActivity($connector1, null, $activity2Label);
		$this->assertNotNull($activity2);
		
		$this->service->bindProperties($connector1, array(
			PROPERTY_CONNECTORS_NOTIFY => INSTANCE_NOTIFY_USER,
			PROPERTY_CONNECTORS_USER_NOTIFIED => $this->currentUser->getUri(),
			PROPERTY_CONNECTORS_NOTIFICATION_MESSAGE => $notificationMessage
		));
		
		
		$processExecution = $processExecutionService->createProcessExecution(
			$processDefinition,
			$processInstanceLabel,
			'created for the notification service test case',
			array(
				$vars['unitLabel']->getUri() => 'myUnit',
				$vars['countryCode']->getUri() => 'FR',
				$vars['languageCode']->getUri() => 'fr'
			));
		$this->assertNotNull($processExecution);
		
		
		$activityExecs = $processExecutionService->getCurrentActivityExecutions($processExecution);
		$this->assertEquals(count($activityExecs), 1);
		$activityExecution = reset($activityExecs);
		$activity = $activityExecutionService->getExecutionOf($activityExecution);
		$this->assertEquals($activity->getUri(), $activity1->getUri());
		
		//test notificaiton creation:
		$notification = $this->service->createNotification($connector1, $this->currentUser, $activityExecution);
		$this->assertNotNull($notification);
		$builtMessage = (string) $notification->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_NOTIFICATION_MESSAGE));
		$this->assertEquals($builtMessage, '
			Dear Sammy Norville Rogers,

			Please join the translation process of the unit myUnit to fr_FR to complete your next task "activity1".

			Bests,
			The Workflow Engine
			');
		
		foreach($vars as $var){
			$var->delete();
		}
		
		foreach($spx as $predicates){
			foreach($predicates as $term){
				$term->delete();
			}
		}
		
		$this->assertTrue($processExecutionService->deleteProcessExecution($processExecution));
		$this->assertTrue($processAuthoringService->deleteProcess($processDefinition));
		
	}
	
	private function createSPX(core_kernel_classes_Resource $context, core_kernel_classes_Property $predicate){
		
		$termClass = new core_kernel_classes_Class(CLASS_TERM_SUJET_PREDICATE_X,__METHOD__);
		$termInstance = $termClass->createInstance("Term : SPX ".$context->getUri(). "-" .$predicate->getUri() , 'generated by Condition Descriptor on '. date(DATE_ISO8601));

		$subjectProperty = new core_kernel_classes_Property(PROPERTY_TERM_SPX_SUBJET,__METHOD__);
		$predicateProperty = new core_kernel_classes_Property(PROPERTY_TERM_SPX_PREDICATE,__METHOD__);
		$termInstance->setPropertyValue($subjectProperty , $context->getUri());
		$termInstance->setPropertyValue($predicateProperty , $predicate->getUri());
		
		return $termInstance;
	}
	
	/**
	 * Test the notifications in a sequencial process
	 */
	public function testNotificationsInProcess(){
		try{
			
			$processExecutionService = wfEngine_models_classes_ProcessExecutionService::singleton();
			$authoringService = wfAuthoring_models_classes_ProcessService::singleton();
			$activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();
			$roleService = wfEngine_models_classes_RoleService::singleton();
			
			//create a new process def
			$processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
			$processDefinition = $processDefinitionClass->createInstance('ProcessForUnitTest', 'Unit test');
			$this->assertIsA($processDefinition, 'core_kernel_classes_Resource');
			
			$aclModeRole		 = new core_kernel_classes_Resource(INSTANCE_ACL_ROLE);
			$aclModeUser		 = new core_kernel_classes_Resource(INSTANCE_ACL_USER);
			
			$wfRole 	 = new core_kernel_classes_Resource(INSTANCE_ROLE_WORKFLOW);
			$role1		 = $roleService->addRole('Role 1', $wfRole);
			$role2		 = $roleService->addRole('Role 2', $wfRole);
			$roleService->setRoleToUsers($role2, array($this->currentUser));
			
			
			//define activities and connectors
			$activity1 = $authoringService->createActivity($processDefinition, 'activity1');
			$this->assertNotNull($activity1);
			
			$authoringService->setFirstActivity($processDefinition, $activity1);
			
			//activity is allowed to the created role
			$activityExecutionService->setAcl($activity1, $aclModeRole, $role2);
			
			$connector1 = null;
			$connector1 = $authoringService->createConnector($activity1);
			$authoringService->setConnectorType($connector1, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
			$this->assertNotNull($connector1);
			
			
			$this->service->bindProperties($connector1, array(
				PROPERTY_CONNECTORS_NOTIFY => INSTANCE_NOTIFY_USER,
				PROPERTY_CONNECTORS_USER_NOTIFIED => $this->currentUser->getUri(),
				PROPERTY_CONNECTORS_NOTIFICATION_MESSAGE => 'Connector 1 notification to user '.$this->currentUser->getLabel()
			));
			
			
			$activity2 = $authoringService->createSequenceActivity($connector1, null, 'activity2');
			$this->assertNotNull($activity2);
			
			//2nd activity is allowed to create role
			$activityExecutionService->setAcl($activity2, $aclModeRole, $role2);
			
			$connector2  = null;
			$connector2 = $authoringService->createConnector($activity2);
			$authoringService->setConnectorType($connector2, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
			$this->assertNotNull($connector2);
			
			$this->service->bindProperties($connector2, array(
				PROPERTY_CONNECTORS_NOTIFY => INSTANCE_NOTIFY_PREVIOUS,
				PROPERTY_CONNECTORS_NOTIFICATION_MESSAGE => 'Connector 2 notification to previous activity user '.$this->currentUser->getLabel()
			));
			
			
			$activity3 = $authoringService->createSequenceActivity($connector2, null, 'activity3');
			$this->assertNotNull($activity3);
			
			$connector3  = null;
			$connector3 = $authoringService->createConnector($activity3);
			$authoringService->setConnectorType($connector3, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
			$this->assertNotNull($connector3);
			
			$this->service->bindProperties($connector3, array(
				PROPERTY_CONNECTORS_NOTIFY => INSTANCE_NOTIFY_NEXT,
				PROPERTY_CONNECTORS_NOTIFICATION_MESSAGE => 'Connector 3 notification to next activity user '.$this->currentUser->getLabel()
			));
			
			$activity4 = $authoringService->createSequenceActivity($connector3, null, 'activity4');
			$this->assertNotNull($activity4);
			
			//allowed to the currentUser only
			$activityExecutionService->setAcl($activity4, $aclModeUser, $this->currentUser);
		
		
			$connector4  = null;
			$connector4 = $authoringService->createConnector($activity4);
			$authoringService->setConnectorType($connector4, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
			$this->assertNotNull($connector4);
			
			$this->service->bindProperties($connector4, array(
				PROPERTY_CONNECTORS_NOTIFY => INSTANCE_NOTIFY_ROLE,
				PROPERTY_CONNECTORS_ROLE_NOTIFIED => $role2->getUri(),
				PROPERTY_CONNECTORS_NOTIFICATION_MESSAGE => 'Connector 4 notification to role user '.$role2->getLabel()
			));
		
			$activity5 = $authoringService->createSequenceActivity($connector4, null, 'activity5');
			$this->assertNotNull($activity5);
			
			//run the process
			$processExecName = 'Test Process Execution';
			$processExecComment = 'created for Notification service test case by '.__METHOD__;
			$proc = $processExecutionService->createProcessExecution($processDefinition, $processExecName, $processExecComment);
			
			$i = 1;
			while($i <= 5 ){
				
				$activityExecs = $processExecutionService->getAvailableCurrentActivityExecutions($proc, $this->currentUser);
				$this->assertEquals(count($activityExecs), 1);
				$activityExec = reset($activityExecs);
				$activity = $activityExecutionService->getExecutionOf($activityExec);
				
				$this->out("<strong>".$activity->getLabel()."</strong>", true);
				$this->assertTrue($activity->getLabel() == 'activity'.$i);
				
				//init execution
				$activityExecution = $processExecutionService->initCurrentActivityExecution($proc, $activityExec, $this->currentUser);
				$this->assertIsA($activityExecution, "core_kernel_classes_Resource");
				
				//transition to nextactivity
				$transitionResult = $processExecutionService->performTransition($proc, $activityExecution);
				
				$this->assertFalse($processExecutionService->isPaused($proc));
				
				$i++;
			}
			$this->assertTrue($processExecutionService->isFinished($proc));
			
			//check the created notifications
			$notifications = array();
			
			$notificationProcessExecProp 	= new core_kernel_classes_Property(PROPERTY_NOTIFICATION_PROCESS_EXECUTION);
			$notificationToProp 			= new core_kernel_classes_Property(PROPERTY_NOTIFICATION_TO);
			
			$notificationsToSend = $this->service->getNotificationsToSend();
			$this->assertTrue(count($notificationsToSend) > 0);
			foreach($this->service->getNotificationsToSend() as $notification){
				$notificationProcess = $notification->getOnePropertyValue($notificationProcessExecProp);
				if(!is_null($notificationProcess)){
					if($notificationProcess->getUri() == $proc->getUri()){
						
						$notifiedUser = $notification->getOnePropertyValue($notificationToProp);
						$this->assertNotNull($notifiedUser);
						$this->assertEquals($notifiedUser->getUri(), $this->currentUser->getUri());
						
						$notifications[] = $notification;
					}
				}
			} 
			
			$notificationCount = count($notifications);
			$this->assertEquals($notificationCount, 4);
			
//			$this->out("$notificationCount notifications to be sent");
//			$this->assertTrue($this->service->sendNotifications(new tao_helpers_transfert_MailAdapter()));
//			$this->out("All notifications sent");
			
			//delete notifications:
			foreach($notifications as $notification){
				$this->assertTrue($notification->delete());
			}
			
			$this->assertTrue($role2->delete());
			
			//delete process exec:
			$this->assertTrue($processExecutionService->deleteProcessExecution($proc));

			//delete processdef:
			$this->assertTrue($authoringService->deleteProcess($processDefinition));
		
			
			if(!is_null($this->currentUser)){
				$this->userService->logout();
				$this->assertTrue($this->userService->removeUser($this->currentUser));
			}
		}
		catch(common_Exception $ce){
			$this->fail($ce);
		}
	}
	
}
?>