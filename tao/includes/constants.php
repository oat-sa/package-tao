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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 *
 * Constant for TAO
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 *
 *
 */
#TAO version number
define('TAO_VERSION', '2.5.6');

#TAO version label
define('TAO_VERSION_NAME', 'v2.5.6');

#the name to display
define('PRODUCT_NAME', 'TAO');

#TAO release status, use to add specific footer to TAO, available alpha, beta, demo, stable
define('TAO_RELEASE_STATUS', 'stable');

#TAO default character encoding (mainly used with multi-byte string functions).
define('TAO_DEFAULT_ENCODING', 'UTF-8');

$todefine = array(
	'TAO_OBJECT_CLASS' 					=> 'http://www.tao.lu/Ontologies/TAO.rdf#TAOObject',
	'TAO_GROUP_CLASS' 					=> 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Group',
	'TAO_ITEM_CLASS' 					=> 'http://www.tao.lu/Ontologies/TAOItem.rdf#Item',
	'TAO_RESULT_CLASS' 					=> 'http://www.tao.lu/Ontologies/TAOResult.rdf#Result',
	'TAO_SUBJECT_CLASS' 				=> 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject',
	'TAO_TEST_CLASS' 					=> 'http://www.tao.lu/Ontologies/TAOTest.rdf#Test',
	'TAO_DELIVERY_CLASS' 				=> 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery',
	'RDFS_TYPE'							=> 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type',
	'GENERIS_RESOURCE'					=> 'http://www.tao.lu/Ontologies/generis.rdf#generis_Ressource',
	'INSTANCE_BOOLEAN_TRUE'				=> 'http://www.tao.lu/Ontologies/generis.rdf#True',
	'INSTANCE_BOOLEAN_FALSE'			=> 'http://www.tao.lu/Ontologies/generis.rdf#False',
	'TAO_LIST_CLASS'					=> 'http://www.tao.lu/Ontologies/TAO.rdf#List',
	'TAO_LIST_LEVEL_PROP'				=> 'http://www.tao.lu/Ontologies/TAO.rdf#level',
	'TAO_GUIORDER_PROP'					=> 'http://www.tao.lu/Ontologies/TAO.rdf#TAOGUIOrder',
	'CLASS_LANGUAGES'					=> 'http://www.tao.lu/Ontologies/TAO.rdf#Languages',
	'INSTANCE_ROLE_GLOBALMANAGER'		=> 'http://www.tao.lu/Ontologies/TAO.rdf#GlobalManagerRole',
	'INSTANCE_ROLE_TAOMANAGER'			=> 'http://www.tao.lu/Ontologies/TAO.rdf#TaoManagerRole',
    'INSTANCE_ROLE_SYSADMIN'			=> 'http://www.tao.lu/Ontologies/TAO.rdf#SysAdminRole',     
	'INSTANCE_ROLE_BACKOFFICE'			=> 'http://www.tao.lu/Ontologies/TAO.rdf#BackOfficeRole',
	'INSTANCE_ROLE_FRONTOFFICE'			=> 'http://www.tao.lu/Ontologies/TAO.rdf#FrontOfficeRole',
	'INSTANCE_ROLE_SERVICE'				=> 'http://www.tao.lu/Ontologies/TAO.rdf#ServiceRole',
	'INSTANCE_ROLE_WORKFLOW'  			=> 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowRole',
	'INSTANCE_ROLE_DELIVERY'			=> 'http://www.tao.lu/Ontologies/TAO.rdf#DeliveryRole',
	'CLASS_WORKFLOWUSER' 				=> 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowUser',
	'TAO_INSTALLATOR'  					=> 'http://www.tao.lu/Ontologies/TAO.rdf#installator',
	'PROPERTY_WIDGET_CALENDAR'			=> 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Calendar',
	'PROPERTY_WIDGET_TEXTBOX'			=> 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextBox',
	'PROPERTY_WIDGET_TEXTAREA'			=> 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextArea',
	'PROPERTY_WIDGET_HTMLAREA'			=> 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#HTMLArea',
	'PROPERTY_WIDGET_PASSWORD'			=> 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Password',
	'PROPERTY_WIDGET_HIDDENBOX'			=> 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#HiddenBox',
	'PROPERTY_WIDGET_RADIOBOX'			=> 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#RadioBox',
	'PROPERTY_WIDGET_COMBOBOX'			=> 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ComboBox',
	'PROPERTY_WIDGET_CHECKBOX'			=> 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#CheckBox',
	'PROPERTY_WIDGET_FILE'				=> 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#AsyncFile',
	'PROPERTY_WIDGET_VERSIONEDFILE'		=> 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#VersionedFile',
	'PROPERTY_TAO_PROPERTY'				=> 'http://www.tao.lu/Ontologies/TAO.rdf#TAOProperty',
	'PROPERTY_LANGUAGE_USAGES'			=> 'http://www.tao.lu/Ontologies/TAO.rdf#LanguageUsages',
	'CLASS_LANGUAGES_USAGES'			=> 'http://www.tao.lu/Ontologies/TAO.rdf#LanguagesUsages',
	'INSTANCE_LANGUAGE_USAGE_GUI'		=> 'http://www.tao.lu/Ontologies/TAO.rdf#LanguageUsageGUI',
	'INSTANCE_LANGUAGE_USAGE_DATA'		=> 'http://www.tao.lu/Ontologies/TAO.rdf#LanguageUsageData',
    'CLASS_PROCESS_EXECUTIONS'			=> 'http://www.tao.lu/middleware/taoqual.rdf#i119010455660544',
	'FUNCACL_NS'						=> 'http://www.tao.lu/Ontologies/taoFuncACL.rdf',
	'INSTANCE_ROLE_BASEACCESS'			=> 'http://www.tao.lu/Ontologies/TAO.rdf#BaseAccessRole',
	'CLASS_ACL_EXTENSION'				=> 'http://www.tao.lu/Ontologies/taoFuncACL.rdf#Extension',
	'PROPERTY_ACL_EXTENSION_ID'			=> 'http://www.tao.lu/Ontologies/taoFuncACL.rdf#Identifier',
	'PROPERTY_ACL_GRANTACCESS'			=> 'http://www.tao.lu/Ontologies/taoFuncACL.rdf#GrantAccess',
	'CLASS_ACL_MODULE'					=> 'http://www.tao.lu/Ontologies/taoFuncACL.rdf#Module',
	'PROPERTY_ACL_MODULE_ID'			=> 'http://www.tao.lu/Ontologies/taoFuncACL.rdf#moduleIdentifier',
	'PROPERTY_ACL_MODULE_EXTENSION'		=> 'http://www.tao.lu/Ontologies/taoFuncACL.rdf#moduleExtension',
	'PROPERTY_ACL_MODULE_GRANTACCESS'	=> 'http://www.tao.lu/Ontologies/taoFuncACL.rdf#grantAccessModule',
	'CLASS_ACL_ACTION'					=> 'http://www.tao.lu/Ontologies/taoFuncACL.rdf#Action',
	'PROPERTY_ACL_ACTION_ID'			=> 'http://www.tao.lu/Ontologies/taoFuncACL.rdf#actionIdentifier',
	'PROPERTY_ACL_ACTION_MEMBEROF'		=> 'http://www.tao.lu/Ontologies/taoFuncACL.rdf#actionMemberOf',
	'PROPERTY_ACL_ACTION_GRANTACCESS'	=> 'http://www.tao.lu/Ontologies/taoFuncACL.rdf#grantAccessAction',
	'CLASS_MANAGEMENTROLE'				=> 'http://www.tao.lu/Ontologies/TAO.rdf#ManagementRole',
	'CLASS_WORKERROLE'					=> 'http://www.tao.lu/Ontologies/TAO.rdf#WorkerRole',
	'CLASS_TAO_USER'					=> 'http://www.tao.lu/Ontologies/TAO.rdf#User',
	
	'CLASS_OAUTH_CONSUMER' 				=> 'http://www.tao.lu/Ontologies/TAO.rdf#OauthConsumer',
	'PROPERTY_OAUTH_KEY'			    => 'http://www.tao.lu/Ontologies/TAO.rdf#OauthKey',
	'PROPERTY_OAUTH_SECRET'             => 'http://www.tao.lu/Ontologies/TAO.rdf#OauthSecret',
    'PROPERTY_OAUTH_CALLBACK'             => 'http://www.tao.lu/Ontologies/TAO.rdf#OauthCallbackUrl',
    
	'CLASS_GENERIS_COMMENT'				=> 'http://www.tao.lu/Ontologies/generis.rdf#comment',
	'PROPERTY_GENERIS_RESOURCE_COMMENT'	=> 'http://www.tao.lu/Ontologies/generis.rdf#generisRessourceComment',
	'PROPERTY_COMMENT_AUTHOR'		=> 'http://www.tao.lu/Ontologies/generis.rdf#commentAuthor',
	'PROPERTY_COMMENT_TIMESTAMP'	=> 'http://www.tao.lu/Ontologies/generis.rdf#commentTimestamp',
    
    // @todo properly migrate service and service calls to tao
    'CLASS_CALLOFSERVICES'							=> 'http://www.tao.lu/middleware/wfEngine.rdf#ClassCallOfServices',
    'PROPERTY_CALLOFSERVICES_SERVICEDEFINITION'		=> 'http://www.tao.lu/middleware/wfEngine.rdf#PropertyCallOfServicesServiceDefinition',
    'PROPERTY_CALLOFSERVICES_ACTUALPARAMETEROUT'	=> 'http://www.tao.lu/middleware/wfEngine.rdf#PropertyCallOfServicesActualParameterOut',
    'PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN'		=> 'http://www.tao.lu/middleware/wfEngine.rdf#PropertyCallOfServicesActualParameterin',
    'PROPERTY_CALLOFSERVICES_TOP'					=> 'http://www.tao.lu/middleware/wfEngine.rdf#PropertyCallOfServicesTop',
    'PROPERTY_CALLOFSERVICES_LEFT'					=> 'http://www.tao.lu/middleware/wfEngine.rdf#PropertyCallOfServicesLeft',
    'PROPERTY_CALLOFSERVICES_WIDTH'					=> 'http://www.tao.lu/middleware/wfEngine.rdf#PropertyCallOfServicesWidth',
    'PROPERTY_CALLOFSERVICES_HEIGHT'				=> 'http://www.tao.lu/middleware/wfEngine.rdf#PropertyCallOfServicesHeight',
    
    'CLASS_ACTUALPARAMETER'							=> 'http://www.tao.lu/middleware/wfEngine.rdf#ClassActualParameters',
    'PROPERTY_ACTUALPARAMETER_PROCESSVARIABLE'		=> 'http://www.tao.lu/middleware/wfEngine.rdf#PropertyActualParametersProcessVariable',
    'PROPERTY_ACTUALPARAMETER_CONSTANTVALUE'		=> 'http://www.tao.lu/middleware/wfEngine.rdf#PropertyActualParametersConstantValue',
    'PROPERTY_ACTUALPARAMETER_FORMALPARAMETER'		=> 'http://www.tao.lu/middleware/wfEngine.rdf#PropertyActualParametersFormalParameter',
    
    'CLASS_SERVICESDEFINITION'						=> 'http://www.tao.lu/middleware/wfEngine.rdf#ClassServiceDefinitions',
    'PROPERTY_SERVICESDEFINITION_FORMALPARAMOUT'	=> 'http://www.tao.lu/middleware/wfEngine.rdf#PropertyServiceDefinitionsFormalParameterOut',
    'PROPERTY_SERVICESDEFINITION_FORMALPARAMIN' 	=> 'http://www.tao.lu/middleware/wfEngine.rdf#PropertyServiceDefinitionsFormalParameterIn',
    
    'CLASS_SUPPORTSERVICES'							=> 'http://www.tao.lu/middleware/wfEngine.rdf#ClassSupportServices',
    'PROPERTY_SUPPORTSERVICES_URL'					=> 'http://www.tao.lu/middleware/wfEngine.rdf#PropertySupportServicesUrl',
    
    'CLASS_WEBSERVICES'								=> 'http://www.tao.lu/middleware/wfEngine.rdf#ClassWebServices',
    
    'CLASS_FORMALPARAMETER'							=> 'http://www.tao.lu/middleware/wfEngine.rdf#ClassFormalParameters',
    'PROPERTY_FORMALPARAMETER_DEFAULTCONSTANTVALUE' => 'http://www.tao.lu/middleware/wfEngine.rdf#PropertyFormalParametersDefaultConstantValue',
    'PROPERTY_FORMALPARAMETER_DEFAULTPROCESSVARIABLE'=>'http://www.tao.lu/middleware/wfEngine.rdf#PropertyFormalParametersDefaultProcessVariable',
    'PROPERTY_FORMALPARAMETER_NAME'					=> 'http://www.tao.lu/middleware/wfEngine.rdf#PropertyFormalParametersName',

);
