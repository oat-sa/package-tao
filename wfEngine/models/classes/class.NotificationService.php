<?php
/**
 *   
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

/**
 * Short description of class wfEngine_models_classes_NotificationService
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 
 */
class wfEngine_models_classes_NotificationService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute notificationClass
     *
     * @access protected
     * @var Class
     */
    protected $notificationClass = null;

    /**
     * Short description of attribute notificationSentProp
     *
     * @access protected
     * @var Property
     */
    protected $notificationSentProp = null;

    /**
     * Short description of attribute notificationToProp
     *
     * @access protected
     * @var Property
     */
    protected $notificationToProp = null;

    /**
     * Short description of attribute notificationConnectorProp
     *
     * @access protected
     * @var Property
     */
    protected $notificationConnectorProp = null;

    /**
     * Short description of attribute notificationDateProp
     *
     * @access protected
     * @var Property
     */
    protected $notificationDateProp = null;

    /**
     * Short description of attribute notificationProcessExecProp
     *
     * @access protected
     * @var Property
     */
    protected $notificationProcessExecProp = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        
        
    	$this->notificationClass 			= new core_kernel_classes_Class(CLASS_NOTIFICATION);
    	$this->notificationSentProp 		= new core_kernel_classes_Property(PROPERTY_NOTIFICATION_SENT);
    	$this->notificationToProp 			= new core_kernel_classes_Property(PROPERTY_NOTIFICATION_TO);
        $this->notificationConnectorProp 	= new core_kernel_classes_Property(PROPERTY_NOTIFICATION_CONNECTOR);
        $this->notificationDateProp 		= new core_kernel_classes_Property(PROPERTY_NOTIFICATION_DATE);
        $this->notificationProcessExecProp 	= new core_kernel_classes_Property(PROPERTY_NOTIFICATION_PROCESS_EXECUTION);
		$this->notificationMessageProp		= new core_kernel_classes_Property(PROPERTY_NOTIFICATION_MESSAGE);
    	$this->connectorNotificationProp	= new core_kernel_classes_Property(PROPERTY_CONNECTORS_NOTIFICATION_MESSAGE);
		
        
    }

    /**
     * Short description of method trigger
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource connector
     * @param  Resource activityExecution
     * @param  Resource processExecution
     * @return int
     */
    public function trigger( core_kernel_classes_Resource $connector,  core_kernel_classes_Resource $activityExecution,  core_kernel_classes_Resource $processExecution = null)
    {
        $returnValue = (int) 0;

        
        
        if(!is_null($connector) && !is_null($activityExecution)){
	        
        	//initialize properties 
        	$connectorUserNotifiedProp			= new core_kernel_classes_Property(PROPERTY_CONNECTORS_USER_NOTIFIED);
        	$connectorRoleNotifiedProp 			= new core_kernel_classes_Property(PROPERTY_CONNECTORS_ROLE_NOTIFIED);
        	$connectorNextActivitiesProp 		= new core_kernel_classes_Property(PROPERTY_STEP_NEXT);
        	$activityExecutionUserProp 			= new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER);
        	$activityAclModeProp 				= new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ACL_MODE);
        	$activityAclUserProp				= new core_kernel_classes_Property(PROPERTY_ACTIVITIES_RESTRICTED_USER);
        	$activityAclRoleProp				= new core_kernel_classes_Property(PROPERTY_ACTIVITIES_RESTRICTED_ROLE);
        	
			$connectorService			= wfEngine_models_classes_ConnectorService::singleton();
			$transitionRuleService		= wfEngine_models_classes_TransitionRuleService::singleton();
        	$roleService 				= wfEngine_models_classes_RoleService::singleton();
        	$activityExecutionService 	= wfEngine_models_classes_ActivityExecutionService::singleton();
        	$processExecutionService	= wfEngine_models_classes_ProcessExecutionService::singleton();
			
			if(is_null($processExecution)){
				$processExecution = $activityExecutionService->getRelatedProcessExecution($activityExecution);
			}
						
        	$users = array();
        	
        	//get the notifications mode defined for that connector
        	$notifyModes = $connector->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CONNECTORS_NOTIFY));
	        foreach($notifyModes->getIterator() as $notify){
	        	
				$nextActivities = array();
	        	//get the users regarding the notification mode
	        	switch($notify->getUri()){
	        		
	        		//users directly defined 
	        		case INSTANCE_NOTIFY_USER: 
	        			foreach($connector->getPropertyValues($connectorUserNotifiedProp) as $userUri){
	        				if(!in_array($userUri, $users)){
	        					$users[] = $userUri;
	        				}
	        			}
	        			break;
	        			
	        		//users from roles directly defined 
	        		case INSTANCE_NOTIFY_ROLE:
        				foreach($connector->getPropertyValues($connectorRoleNotifiedProp)  as $roleUri){
        					foreach($roleService->getUsers(new core_kernel_classes_Resource($roleUri)) as $userUri){
        						if(!in_array($userUri, $users)){
        							$users[] = $userUri;
        						}
        					}
        				}
	        			break;
	        			
	        		//get the users who have executed the previous activity
	        		case INSTANCE_NOTIFY_PREVIOUS:
						
						$previousActivities = array();
						$cardinalityService = wfEngine_models_classes_ActivityCardinalityService::singleton();
						foreach($connectorService->getPreviousActivities($connector) as $prevActivity){
							if($cardinalityService->isCardinality($prevActivity)){
								$previousActivities[] = $cardinalityService->getSource($prevActivity)->getUri();
							}else{
								$previousActivities[] = $prevActivity->getUri();
							}
						}
						
						$activity = $activityExecutionService->getExecutionOf($activityExecution);
						
						//check activity execution against connector
						if(in_array($activity->getUri(), $previousActivities)){
							$activityExecutionUser = $activityExecutionService->getActivityExecutionUser($activityExecution);
							if (!is_null($activityExecutionUser)) {
								if (!in_array($activityExecutionUser->getUri(), $users)) {
									$users[] = $activityExecutionUser->getUri();
								}
							}
						}
										
	        			break;
	        			
	        		//get the users 
					case INSTANCE_NOTIFY_THEN:{
						if($connectorService->getType($connector)->getUri() == INSTANCE_TYPEOFCONNECTORS_CONDITIONAL){
							$transitionRule = $connectorService->getTransitionRule($connector);
							if($transitionRule instanceof core_kernel_classes_Resource) {
								$then = $transitionRuleService->getThenActivity($transitionRule);
								if($then instanceof core_kernel_classes_Resource){
									$nextActivities[] = $then->getUri();
								}
							}
						}else{
							//wrong connector type!
							break;
						}
						//do not break, continue to the INSTANCE_NOTIFY_NEXT case
					}
					case INSTANCE_NOTIFY_ELSE:{
						if(empty($nextActivities)){
							if ($connectorService->getType($connector)->getUri() == INSTANCE_TYPEOFCONNECTORS_CONDITIONAL) {
								$transitionRule = $connectorService->getTransitionRule($connector);
								if ($transitionRule instanceof core_kernel_classes_Resource) {
									$else = $transitionRuleService->getElseActivity($transitionRule);
									if ($else instanceof core_kernel_classes_Resource) {
										$nextActivities[] = $else->getUri();
									}
								}
							} else {
								//wrong connector type!
								break;
							}
						}
						//do not break, continue to the INSTANCE_NOTIFY_NEXT case
					}
	        		case INSTANCE_NOTIFY_NEXT:
						
						if(empty($nextActivities)){
							$cardinalityService = wfEngine_models_classes_ActivityCardinalityService::singleton();
							foreach($connectorService->getNextActivities($connector) as $nextActivity){
								if($cardinalityService->isCardinality($nextActivity)){
									$nextActivities[] = $cardinalityService->getDestination($nextActivity)->getUri();
								}else{
									$nextActivities[] = $nextActivity->getUri();
								}
							}
						}
						
						$nextActivityExecutions = $activityExecutionService->getFollowing($activityExecution);
						foreach($nextActivityExecutions as $activityExec){
							
							$activity = $activityExecutionService->getExecutionOf($activityExec);
							if(!in_array($activity->getUri(), $nextActivities)){
								//invalid activity exec
								continue;
							}
							
							//check if it is among the next activity of the connector:
							$mode = $activityExecutionService->getAclMode($activityExec);
							if ($mode instanceof core_kernel_classes_Resource) {
								switch ($mode->getUri()) {
									case INSTANCE_ACL_USER:
										$restrictedUser = $activityExecutionService->getRestrictedUser($activityExec);//@TODO: implemente multiple restricted users?
										if(!is_null($restrictedUser)){
											if (!in_array($restrictedUser->getUri(), $users)) {
												$users[] = $restrictedUser->getUri();
											}
										}
										break;
									case INSTANCE_ACL_ROLE:
									case INSTANCE_ACL_ROLE_RESTRICTED_USER:
									case INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED:
										$restrictedRole = $activityExecutionService->getRestrictedRole($activityExec);//@TODO: implemente multiple restricted roles?
										if(!is_null($restrictedRole)){
											foreach ($roleService->getUsers($restrictedRole) as $userUri) {
												if (!in_array($userUri, $users)) {
													$users[] = $userUri;
												}
											}
										}
										break;
								}
							}
							
						}
						
	        			break;
	        	}
	        }
			
			//build notification message for every user here:
	        foreach($users as $userUri){
				if($this->createNotification($connector, new core_kernel_classes_Resource($userUri), $activityExecution, $processExecution)){
					//get message from connector:
					//replace SPX in message bodies
					$returnValue++;
				}
	        }
			
        }
        
        

        return (int) $returnValue;
    }

    /**
     * Short description of method getNotificationsToSend
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return array
     */
    public function getNotificationsToSend()
    {
        $returnValue = array();

        
        
        //get the notifications with the sent property to false
		$notifications = $this->notificationClass->searchInstances(array($this->notificationSentProp->getUri() => GENERIS_FALSE), array('like' => false, 'recursive' => 0));
	    foreach($notifications as $notification){
	    	//there a date prop by sending try. After 4 try, we stop to try (5 because the 4 try and the 1st date is the creation date) 
	    	$dates = $notification->getPropertyValues($this->notificationDateProp);
	    	if(count($dates) < 5){
	    		$returnValue[] = $notification;
	    	} 
	    }
        
        

        return (array) $returnValue;
    }

    /**
     * Short description of method sendNotifications
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Adapter adapter
     * @return boolean
     */
    public function sendNotifications( tao_helpers_transfert_Adapter $adapter)
    {
        $returnValue = (bool) false;

        
        
        if(!is_null($adapter)){
        	
        	//initialize properties used in the loop
        	
        	$userMailProp 						= new core_kernel_classes_Property(PROPERTY_USER_MAIL);
        	$processExecutionOfProp 			= new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_EXECUTIONOF);
        	
        	//create messages from the notifications resources
        	$messages = array();
        	$notificationsToSend = $this->getNotificationsToSend();
        	foreach($notificationsToSend as $notificationResource){
        		
				//get the message content from the notification
				//@TODO: "getNotificationMessage" can be cached
        		$content = (string) $notificationResource->getOnePropertyValue($this->notificationMessageProp);
				
        		//get the email of the user
        		$toEmail = '';
        		$to = $notificationResource->getOnePropertyValue($this->notificationToProp);
        		if(!is_null($to)){
        			$toEmail = (string)$to->getOnePropertyValue($userMailProp);
        		}
        		
        		//get the name of the concerned process
        		$processName = '';
        		$processExec = $notificationResource->getOnePropertyValue($this->notificationProcessExecProp);
        		if($processExec instanceof core_kernel_classes_Resource){
        			$process = $processExec->getOnePropertyValue($processExecutionOfProp);
        			if($process instanceof core_kernel_classes_Resource){
        				$processName = $process->getLabel()." / ".$processExec->getLabel();
        			}
        		}
        		
        		//create the message instance
        		
        		if(!empty($toEmail) && !empty($content)){
        			$message = new tao_helpers_transfert_Message();
        			$message->setTitle(__("[TAO Notification System] Workflow").' : '.$processName);
        			$message->setBody($content);
        			$message->setTo($toEmail);
        			$message->setFrom("tao.notification.system@tao.lu");
        			
        			$messages[$notificationResource->getUri()] = $message;
        		}
        	}
        	
        	if(count($messages) > 0){
        		$adapter->setMessages($messages);
        		$returnValue = (count($messages) == $adapter->send());
        		
        		foreach($adapter->getMessages() as $notificationUri => $message){
        			if($message->getStatus() == tao_helpers_transfert_Message::STATUS_SENT){
        				$notificationResource = new core_kernel_classes_Resource($notificationUri);
        				$notificationResource->editPropertyValues($this->notificationSentProp, GENERIS_TRUE);
        			}
        			//add a new date at each sending try
        			$notificationResource->setPropertyValue($this->notificationDateProp, date("Y-m-d H:i:s"));
        		}
        	}
        }
        
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method createNotification
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource connector
     * @param  Resource user
     * @param  Resource activityExecution
     * @param  Resource processExecution
     * @return core_kernel_classes_Resource
     */
    public function createNotification( core_kernel_classes_Resource $connector,  core_kernel_classes_Resource $user,  core_kernel_classes_Resource $activityExecution,  core_kernel_classes_Resource $processExecution = null)
    {
        $returnValue = null;

        
		
		$activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();

		if(is_null($processExecution)){
			$processExecution = $activityExecutionService->getRelatedProcessExecution($activityExecution);
		}
			
		$notification = $this->notificationClass->createInstance();
		$notification->setPropertyValue($this->notificationToProp, $user);
		$notification->setPropertyValue($this->notificationProcessExecProp, $processExecution);
		$notification->setPropertyValue($this->notificationConnectorProp, $connector);
		$notification->setPropertyValue($this->notificationSentProp, GENERIS_FALSE);
		$notification->setPropertyValue($this->notificationDateProp, date("Y-m-d H:i:s"));
			
		//get the message content from the connector
		$content = (string) $connector->getOnePropertyValue($this->connectorNotificationProp);
		if(strlen(trim($content)) > 0){
			$matches = array();
			$expr = "/{{((http|https|file|ftp):\/\/[\/.A-Za-z0-9_-]+#[A-Za-z0-9]+)}}/";
			if(preg_match_all($expr, $content, $matches)){
				if(isset($matches[1])){
					$termUris = $matches[1];
					$activity = $activityExecutionService->getExecutionOf($activityExecution);
					foreach ($termUris as $termUri) {
						$term = new core_kernel_rules_Term($termUri);
						$replacement = $term->evaluate(array(
							VAR_PROCESS_INSTANCE => $processExecution->getUri(),
							VAR_ACTIVITY_INSTANCE => $activityExecution->getUri(),
							VAR_ACTIVITY_DEFINITION => $activity->getUri(),
							VAR_CURRENT_USER => $user->getUri()
						));
						
						if($replacement instanceof core_kernel_classes_Resource 
							&& $replacement->getUri() == INSTANCE_TERM_IS_NULL){
							$replacement = '';
						}
						
						$content = str_replace('{{'.$termUri.'}}', $replacement, $content);
					}
					
					
				}
			}
		}
		
		if(empty($content)){
			throw new common_Exception('empty notification message');
		}
		$notification->setPropertyValue($this->notificationMessageProp, $content);
		$returnValue = $notification;
			
        

        return $returnValue;
    }

} /* end of class wfEngine_models_classes_NotificationService */

?>