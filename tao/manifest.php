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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */


/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
$extpath = dirname(__FILE__).DIRECTORY_SEPARATOR;

return array(
    'name' => 'tao',
	'label' => 'Tao base',
	'description' => 'TAO meta-extension',
    'license' => 'GPL-2.0',
    'version' => '2.7.13',
	'author' => 'Open Assessment Technologies, CRP Henri Tudor',
    'requires' => array(
	   'generis' => '>=2.7.1'
    ),
    'models' => array(
        'http://www.tao.lu/Ontologies/TAO.rdf',
        'http://www.tao.lu/middleware/wfEngine.rdf'
    ),
	'install' => array(
		'rdf' => array(
            dirname(__FILE__). '/models/ontology/tao.rdf',
            dirname(__FILE__). '/models/ontology/taoaclrole.rdf',
            dirname(__FILE__). '/models/ontology/oauth.rdf',
            dirname(__FILE__). '/models/ontology/webservice.rdf',
            dirname(__FILE__). '/models/ontology/services.rdf',
            dirname(__FILE__). '/models/ontology/indexation.rdf',
            dirname(__FILE__). '/models/ontology/model.rdf'
		),
		'checks' => array(
				array('type' => 'CheckPHPRuntime', 'value' => array('id' => 'tao_php_runtime', 'min' => '5.3')),
				array('type' => 'CheckPHPRuntime', 'value' => array('id' => 'tao_php_runtime53', 'min' => '5.3', 'max' => '5.3.x', 'silent' => true)),
				array('type' => 'CheckPHPExtension', 'value' => array('id' => 'tao_extension_pdo', 'name' => 'PDO')),
				array('type' => 'CheckPHPExtension', 'value' => array('id' => 'tao_extension_curl', 'name' => 'curl')),
				array('type' => 'CheckPHPExtension', 'value' => array('id' => 'tao_extension_zip', 'name' => 'zip')),
				array('type' => 'CheckPHPExtension', 'value' => array('id' => 'tao_extension_json', 'name' => 'json')),
				array('type' => 'CheckPHPExtension', 'value' => array('id' => 'tao_extension_spl', 'name' => 'spl')),
				array('type' => 'CheckPHPExtension', 'value' => array('id' => 'tao_extension_dom', 'name' => 'dom')),
				array('type' => 'CheckPHPExtension', 'value' => array('id' => 'tao_extension_mbstring', 'name' => 'mbstring')),
				array('type' => 'CheckPHPExtension', 'value' => array('id' => 'tao_extension_suhosin', 'name' => 'suhosin', 'silent' => true)),
				array('type' => 'CheckPHPINIValue', 'value' => array('id' => 'tao_ini_magic_quotes_gpc', 'name' => 'magic_quotes_gpc', 'value' => '0', 'dependsOn' => array('tao_php_runtime53'))),
				array('type' => 'CheckPHPINIValue', 'value' => array('id' => 'tao_ini_register_globals', 'name' => 'register_globals', 'value' => '0', 'dependsOn' => array('tao_php_runtime53'))),
				array('type' => 'CheckPHPINIValue', 'value' => array('id' => 'tao_ini_safe_mode', 'name' => 'safe_mode', 'value' => '0', 'dependsOn' => array('tao_php_runtime53'))),
				array('type' => 'CheckPHPINIValue', 'value' => array('id' => 'tao_ini_suhosin_post_max_name_length', 'name' => 'suhosin.post.max_name_length', 'value' => '128', 'dependsOn' => array('tao_extension_suhosin'))),
				array('type' => 'CheckPHPINIValue', 'value' => array('id' => 'tao_ini_suhosin_request_max_varname_length', 'name' => 'suhosin.request.max_varname_length', 'value' => '128', 'dependsOn' => array('tao_extension_suhosin'))),
				array('type' => 'CheckFileSystemComponent', 'value' => array('id' => 'fs_data', 'location' => 'data', 'rights' => 'rw')),
		        array('type' => 'CheckFileSystemComponent', 'value' => array('id' => 'fs_generis_common_conf', 'location' => 'config', 'rights' => 'rw')),
		        array('type' => 'CheckFileSystemComponent', 'value' => array('id' => 'fs_tao', 'location' => 'tao', 'rights' => 'rw')),
				array('type' => 'CheckFileSystemComponent', 'value' => array('id' => 'fs_tao_includes', 'location' => 'tao/includes', 'rights' => 'rw')),
				array('type' => 'CheckFileSystemComponent', 'value' => array('id' => 'fs_tao_locales', 'location' => 'tao/locales', 'rights' => 'r')),
				array('type' => 'CheckFileSystemComponent', 'value' => array('id' => 'fs_tao_client_locales', 'location' => 'tao/views/locales', 'rights' => 'rw')),
                array('type' => 'CheckCustom', 'value' => array('id' => 'tao_custom_not_nginx', 'name' => 'not_nginx', 'extension' => 'tao', "optional" => true)),
				array('type' => 'CheckCustom', 'value' => array('id' => 'tao_custom_mod_rewrite', 'name' => 'mod_rewrite', 'extension' => 'tao', 'dependsOn' => array('tao_custom_not_nginx'))),
				array('type' => 'CheckCustom', 'value' => array('id' => 'tao_custom_database_drivers', 'name' => 'database_drivers', 'extension' => 'tao'))
		),
		'php' => array(
			dirname(__FILE__).'/scripts/install/addFileUploadSource.php',
			dirname(__FILE__).'/scripts/install/setSimpleAccess.php',
		    dirname(__FILE__).'/scripts/install/setupServiceFileStorage.php',
		    dirname(__FILE__).'/scripts/install/setServiceState.php',
		    dirname(__FILE__).'/scripts/install/setJsConfig.php'
		)
	),
    'update' => 'oat\\tao\\scripts\\update\\Updater',
	'optimizableClasses' => array(
		'http://www.tao.lu/Ontologies/TAO.rdf#Languages',
		'http://www.tao.lu/Ontologies/TAO.rdf#LanguageUsages'
	),
	'managementRole' => 'http://www.tao.lu/Ontologies/TAO.rdf#TaoManagerRole',
    'acl' => array(
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#TaoManagerRole',    array('ext'=>'tao')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#BaseUserRole',      array('ext'=>'tao','mod' => 'ServiceModule')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#BaseUserRole',      array('ext'=>'tao','mod' => 'File', 'act' => 'accessFile')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#BackOfficeRole',    array('ext'=>'tao','mod' => 'Main', 'act' => 'index')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#BackOfficeRole',    array('ext'=>'tao','mod' => 'Main', 'act' => 'getSectionActions')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#BackOfficeRole',    array('ext'=>'tao','mod' => 'Main', 'act' => 'getSectionTrees')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#BackOfficeRole',    array('ext'=>'tao','mod' => 'Users', 'act' => 'checkLogin')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#BackOfficeRole',    array('ext'=>'tao','mod' => 'UserSettings')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#BackOfficeRole',    array('ext'=>'tao','mod' => 'GenerisTree')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#BackOfficeRole',    array('ext'=>'tao','mod' => 'Main', 'act' => 'index')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#BackOfficeRole',    array('act' => 'tao_actions_Lock@locked')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#BackOfficeRole',    array('act' => 'tao_actions_Lock@release')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#LockManagerRole',   array('act' => 'tao_actions_Lock@forceRelease')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#PropertyManagerRole', array('controller' => 'tao_actions_Lists')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#PropertyManagerRole', array('controller' => 'tao_actions_PropertiesAuthoring')),
        array('grant', 'http://www.tao.lu/Ontologies/generis.rdf#AnonymousRole', array('ext'=>'tao','mod' => 'Main', 'act' => 'entry')),
        array('grant', 'http://www.tao.lu/Ontologies/generis.rdf#AnonymousRole', array('ext'=>'tao','mod' => 'Main', 'act' => 'login')),
        array('grant', 'http://www.tao.lu/Ontologies/generis.rdf#AnonymousRole', array('ext'=>'tao','mod' => 'Main', 'act' => 'logout')),
        array('grant', 'http://www.tao.lu/Ontologies/generis.rdf#AnonymousRole', array('ext'=>'tao','mod' => 'AuthApi')),
        array('grant', 'http://www.tao.lu/Ontologies/generis.rdf#AnonymousRole', array('ext'=>'tao','mod' => 'ClientConfig'))
    ),
	'constants' => array(

		# actions directory
		"DIR_ACTIONS" => $extpath."actions".DIRECTORY_SEPARATOR,
	
		# views directory
		"DIR_VIEWS" => $extpath."views".DIRECTORY_SEPARATOR,
	
		# default module name
		'DEFAULT_MODULE_NAME' => 'Main',
	
		#default action name
		'DEFAULT_ACTION_NAME' => 'index',
	
		#BASE PATH: the root path in the file system (usually the document root)
		'BASE_PATH' => $extpath,
	
		#BASE URL (usually the domain root)
		'BASE_URL' => ROOT_URL.'tao/',
	
		#BASE WWW the web resources path
		'BASE_WWW' => ROOT_URL . 'tao/views/',
	 
	 	#TPL PATH the path to the templates
	 	'TPL_PATH'	=> $extpath."views".DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR,
	
		#STUFF that belongs in TAO
		'TAOBASE_WWW' => ROOT_URL . 'tao/views/'
	 ),
    'extra' => array(
        'structures' => $extpath.'actions'.DIRECTORY_SEPARATOR.'structures.xml',
    )
);
