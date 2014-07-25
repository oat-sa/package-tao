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
define('NS_WFENGINE', 'http://www.tao.lu/middleware/wfEngine.rdf');
define('NS_RULES', 'http://www.tao.lu/middleware/Rules.rdf');
$todefine = array(
	'ENABLE_HTTP_REDIRECT_PROCESS_BROWSER' 			=> false,
	
	'VAR_PROCESS_INSTANCE' 							=> NS_RULES . '#VarProcessInstance',
	'VAR_ACTIVITY_INSTANCE' 						=> NS_RULES . '#VarActivityInstance',
	'VAR_ACTIVITY_DEFINITION'						=> NS_RULES . '#VarActivityDefinition',
	'VAR_CURRENT_USER'								=> NS_RULES . '#VarCurrentUser',
	
	'CLASS_PROCESS'									=> NS_WFENGINE . '#ClassProcessDefinitions',
	'PROPERTY_PROCESS_VARIABLES'					=> NS_WFENGINE . '#PropertyProcessVariables',
	'PROPERTY_PROCESS_DIAGRAMDATA'					=> NS_WFENGINE . '#PropertyProcessDiagramData',
	'PROPERTY_PROCESS_ACTIVITIES'					=> NS_WFENGINE . '#PropertyProcessActivities',
	'PROPERTY_PROCESS_ROOT_ACTIVITIES'				=> NS_WFENGINE . '#PropertyProcessRootActivities',
	'PROPERTY_PROCESS_INIT_RESTRICTED_USER'			=> NS_WFENGINE . '#PropertyProcessInitRestrictedUser',
	'PROPERTY_PROCESS_INIT_RESTRICTED_ROLE'			=> NS_WFENGINE . '#PropertyProcessInitRestrictedRole',
	'PROPERTY_PROCESS_INIT_ACL_MODE'				=> NS_WFENGINE . '#PropertyProcessInitAccesControlMode',//!!!
	
	'CLASS_PROCESSINSTANCES'						=> NS_WFENGINE . '#ClassProcessInstances',
	'PROPERTY_PROCESSINSTANCES_STATUS'				=> NS_WFENGINE . '#PropertyProcessInstancesStatus',
	'PROPERTY_PROCESSINSTANCES_EXECUTIONOF'			=> NS_WFENGINE . '#PropertyProcessInstancesExecutionOf',
	'PROPERTY_PROCESSINSTANCES_CURRENTACTIVITYEXECUTIONS'=> NS_WFENGINE . '#PropertyProcessInstancesCurrentActivityExecutions',
	'PROPERTY_PROCESSINSTANCES_ACTIVITYEXECUTIONS'	=> NS_WFENGINE . '#PropertyProcessInstancesActivityExecutions',
	'PROPERTY_PROCESSINSTANCES_TIME_STARTED'		=> NS_WFENGINE . '#PropertyProcessInstancesTimeStarted',
	
	'INSTANCE_PROCESSSTATUS_RESUMED'				=> NS_WFENGINE . '#InstanceStatusResumed',
	'INSTANCE_PROCESSSTATUS_STARTED'				=> NS_WFENGINE . '#InstanceStatusStarted',
	'INSTANCE_PROCESSSTATUS_FINISHED'				=> NS_WFENGINE . '#InstanceStatusFinished',
	'INSTANCE_PROCESSSTATUS_PAUSED'					=> NS_WFENGINE . '#InstanceStatusPaused',
	'INSTANCE_PROCESSSTATUS_CLOSED'					=> NS_WFENGINE . '#InstanceStatusClosed',
	'INSTANCE_PROCESSSTATUS_STOPPED'				=> NS_WFENGINE . '#InstanceStatusStopped',
	
	'CLASS_PROCESSVARIABLES'						=> NS_WFENGINE . '#ClassProcessVariables',
	'PROPERTY_PROCESSVARIABLES_CODE'				=> NS_WFENGINE . '#PropertyCode',
	
	'CLASS_STEP'									=> NS_WFENGINE . '#ClassStep',
	'PROPERTY_STEP_NEXT'							=> NS_WFENGINE  .'#PropertyStepNext',

	'CLASS_ACTIVITIES'								=> NS_WFENGINE . '#ClassActivities',
	'PROPERTY_ACTIVITIES_INTERACTIVESERVICES'		=> NS_WFENGINE . '#PropertyActivitiesInteractiveServices',
	'PROPERTY_ACTIVITIES_RESTRICTED_USER'			=> NS_WFENGINE . '#PropertyActivitiesRestrictedUser',
	'PROPERTY_ACTIVITIES_RESTRICTED_ROLE'			=> NS_WFENGINE . '#PropertyActivitiesRestrictedRole',
	'PROPERTY_ACTIVITIES_ACL_MODE'					=> NS_WFENGINE . '#PropertyActivitiesAccessControlMode',
	'PROPERTY_ACTIVITIES_ISHIDDEN'					=> NS_WFENGINE . '#PropertyActivitiesHidden',
	'PROPERTY_ACTIVITIES_ISINITIAL'					=> NS_WFENGINE . '#PropertyActivitiesInitial',
	'PROPERTY_ACTIVITIES_CONTROLS'					=> NS_WFENGINE  .'#PropertyActivitiesControls',

	'CLASS_ACTIVITYCARDINALITY'						=> NS_WFENGINE . '#ClassActivityCardinality',
	'PROPERTY_ACTIVITYCARDINALITY_ACTIVITY'			=> NS_WFENGINE . '#PropertyActivityCardinalityActivity',
	'PROPERTY_ACTIVITYCARDINALITY_CARDINALITY'		=> NS_WFENGINE . '#PropertyActivityCardinalityCardinality',
	'PROPERTY_ACTIVITYCARDINALITY_SPLITVARIABLES'	=> NS_WFENGINE  .'#PropertyActivityCardinalitySplitVariables',
	
	'CLASS_CONTROLS'								=> NS_WFENGINE  .'#ClassControls',
	'INSTANCE_CONTROL_BACKWARD'						=> NS_WFENGINE  .'#InstanceControlsBackward',
	'INSTANCE_CONTROL_FORWARD'						=> NS_WFENGINE  .'#InstanceControlsForward',
	
	'CLASS_CONNECTORS'								=> NS_WFENGINE . '#ClassConnectors',
	'PROPERTY_CONNECTORS_TRANSITIONRULE'			=> NS_WFENGINE . '#PropertyConnectorsTransitionRule',
	'PROPERTY_CONNECTORS_ACTIVITYREFERENCE'			=> NS_WFENGINE . '#PropertyConnectorsActivityReference',
	'PROPERTY_CONNECTORS_TYPE' 						=> NS_WFENGINE . '#PropertyConnectorsType',
	'PROPERTY_CONNECTORS_NOTIFY'					=> NS_WFENGINE  .'#PropertyConnectorsNotificationModes',
	'PROPERTY_CONNECTORS_USER_NOTIFIED'				=> NS_WFENGINE  .'#PropertyConnectorsNotifiedUser',
	'PROPERTY_CONNECTORS_ROLE_NOTIFIED'				=> NS_WFENGINE  .'#PropertyConnectorsNotifiedRole',
	'PROPERTY_CONNECTORS_NOTIFICATION_MESSAGE'		=> NS_WFENGINE  .'#PropertyConnectorsNotificationMessage',
	
	'CLASS_TYPEOFCONNECTORS'						=> NS_WFENGINE . '#ClassTypeOfConnectors',
	'INSTANCE_TYPEOFCONNECTORS_CONDITIONAL'			=> NS_WFENGINE . '#InstanceTypeOfConnectorsConditional',
	'INSTANCE_TYPEOFCONNECTORS_SEQUENCE'			=> NS_WFENGINE . '#InstanceTypeOfConnectorsSequence',
	'INSTANCE_TYPEOFCONNECTORS_PARALLEL'			=> NS_WFENGINE . '#InstanceTypeOfConnectorsParallel',
	'INSTANCE_TYPEOFCONNECTORS_JOIN'				=> NS_WFENGINE . '#InstanceTypeOfConnectorsJoin',
	'INSTANCE_TYPEOFCONNECTORS_SWITCH'				=> NS_WFENGINE . '#InstanceTypeOfConnectorsSwitch',
	
	'CLASS_TRANSITIONRULES'							=> NS_WFENGINE . '#ClassTransitionRules',
	'PROPERTY_TRANSITIONRULES_THEN'					=> NS_WFENGINE . '#PropertyTransitionRulesThen',
	'PROPERTY_TRANSITIONRULES_ELSE'					=> NS_WFENGINE . '#PropertyTransitionRulesElse',
	
	'CLASS_NOTIFICATION_MODE' 						=> NS_WFENGINE  .'#ClassNotificationMode',
	'INSTANCE_NOTIFY_USER' 							=> NS_WFENGINE  .'#InstanceNotifyUser',
	'INSTANCE_NOTIFY_NEXT'	 						=> NS_WFENGINE  .'#InstanceNotifyNextActivityUsers',
	'INSTANCE_NOTIFY_THEN'							=> NS_WFENGINE  .'#InstanceNotifyThenActivityUsers',
	'INSTANCE_NOTIFY_ELSE'							=> NS_WFENGINE  .'#InstanceNotifyElseActivityUsers',
	'INSTANCE_NOTIFY_PREVIOUS' 						=> NS_WFENGINE  .'#InstanceNotifyPreviousActivityUsers',
	'INSTANCE_NOTIFY_ROLE' 							=> NS_WFENGINE  .'#InstanceNotifyRole',

	'CLASS_NOTIFICATION' 							=> NS_WFENGINE  .'#ClassNotification',
	'PROPERTY_NOTIFICATION_TO' 						=> NS_WFENGINE  .'#PropertyNotificationTo',
	'PROPERTY_NOTIFICATION_CONNECTOR' 				=> NS_WFENGINE  .'#PropertyNotificationConnector',
	'PROPERTY_NOTIFICATION_PROCESS_EXECUTION' 		=> NS_WFENGINE  .'#PropertyNotificationProcessExecution',
	'PROPERTY_NOTIFICATION_SENT' 					=> NS_WFENGINE  .'#PropertyNotificationSent',
	'PROPERTY_NOTIFICATION_DATE' 					=> NS_WFENGINE  .'#PropertyNotificationDate',
	'PROPERTY_NOTIFICATION_MESSAGE'					=> NS_WFENGINE  .'#PropertyNotificationMessage',

    // moved constants of service definitions and calls to tao
	
	'CLASS_ACTIVITY_EXECUTION' 						=> NS_WFENGINE . '#ClassActivityExecutions',
	'PROPERTY_ACTIVITY_EXECUTION_ACTIVITY'			=> NS_WFENGINE . '#PropertyActivityExecutionsExecutionOf',
	'PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER'		=> NS_WFENGINE . '#PropertyActivityExecutionsCurrentUser',
	'PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION' 	=> NS_WFENGINE . '#PropertyActivityExecutionsProcessExecution',
	'PROPERTY_ACTIVITY_EXECUTION_CTX_RECOVERY'		=> NS_WFENGINE . '#PropertyActivityExecutionsContextRecovery',
	'PROPERTY_ACTIVITY_EXECUTION_VARIABLES'			=> NS_WFENGINE  .'#PropertyActivityExecutionsHasVariables',
	'PROPERTY_ACTIVITY_EXECUTION_PREVIOUS'			=> NS_WFENGINE  .'#PropertyActivityExecutionsPreviousActivityExecutions',
	'PROPERTY_ACTIVITY_EXECUTION_FOLLOWING'			=> NS_WFENGINE  .'#PropertyActivityExecutionsFollowingActivityExecutions',
	'PROPERTY_ACTIVITY_EXECUTION_STATUS'			=> NS_WFENGINE  .'#PropertyActivityExecutionsStatus',
	'PROPERTY_ACTIVITY_EXECUTION_TIME_CREATED'		=> NS_WFENGINE . '#PropertyActivityExecutionsTimeCreated',
	'PROPERTY_ACTIVITY_EXECUTION_TIME_STARTED'		=> NS_WFENGINE . '#PropertyActivityExecutionsTimeStarted',
	'PROPERTY_ACTIVITY_EXECUTION_TIME_LASTACCESS'	=> NS_WFENGINE . '#PropertyActivityExecutionsTimeLastAccess',
	'PROPERTY_ACTIVITY_EXECUTION_NONCE'				=> NS_WFENGINE . '#PropertyActivityExecutionsNonce',
	'PROPERTY_ACTIVITY_EXECUTION_ACL_MODE'			=> NS_WFENGINE . '#PropertyActivityExecutionsAccessControlMode',
	'PROPERTY_ACTIVITY_EXECUTION_RESTRICTED_USER'	=> NS_WFENGINE . '#PropertyActivityExecutionsRestrictedUser',
	'PROPERTY_ACTIVITY_EXECUTION_RESTRICTED_ROLE'	=> NS_WFENGINE . '#PropertyActivityExecutionsRestrictedRole',
	
	'CLASS_ACL_MODES'								=> NS_WFENGINE . '#ClassAccessControlModes',
	'INSTANCE_ACL_ROLE'								=> NS_WFENGINE . '#PropertyAccessControlModesRole',//to be renamed to InstanceAccessControlModes...
	'INSTANCE_ACL_ROLE_RESTRICTED_USER'				=> NS_WFENGINE . '#PropertyAccessControlModesRoleRestrictedUser',
	'INSTANCE_ACL_USER'								=> NS_WFENGINE . '#PropertyAccessControlModesUser',
	'INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED'	=> NS_WFENGINE . '#PropertyAccessControlModesRoleRestrictedUserInherited',
	'INSTANCE_ACL_ROLE_RESTRICTED_USER_DELIVERY'    => NS_WFENGINE . '#PropertyAccessControlModesRoleRestrictedUserInheritedDelivery',
	
	'CLASS_TOKEN'									=> NS_WFENGINE  .'#ClassTokens',
	'PROPERTY_TOKEN_VARIABLE'						=> NS_WFENGINE  .'#PropertyTokensVariable',
	'PROPERTY_TOKEN_ACTIVITY'						=> NS_WFENGINE  .'#PropertyTokensActivity',
	'PROPERTY_TOKEN_ACTIVITYEXECUTION'				=> NS_WFENGINE  .'#PropertyTokensActivityExecution',
	'PROPERTY_TOKEN_CURRENTUSER'					=> NS_WFENGINE  .'#PropertyTokensCurrentUser',
    
    'INSTANCE_SERVICE_PROCESSRUNNER'                => NS_WFENGINE  .'#ProcessDefinitionRunner',
    'INSTANCE_FORMALPARAM_PROCESSDEFINITION'        => NS_WFENGINE  .'#FormalParamProcessDefinition',
    'INSTANCE_FORMALPARAM_PROCESSVARIABLES'         => NS_WFENGINE  .'#FormalParamProcessVariables',
);
?>