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
/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
$extpath = dirname(__FILE__).DIRECTORY_SEPARATOR;
$taopath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'tao'.DIRECTORY_SEPARATOR;

return array(
	'name' => 'wfEngine',
	'description' => 'Workflow Engine extension',
	'version' => '2.4',
	'author' => 'Open Assessment Technologies, CRP Henri Tudor',
	'dependencies' => array('tao'),
	'models' => array(
		'http://www.tao.lu/middleware/wfEngine.rdf'
	),
	'install' => array(
		'rdf' => array(
			dirname(__FILE__). '/models/ontology/wfengine.rdf',
			dirname(__FILE__). '/models/ontology/aclrole.rdf',
		    dirname(__FILE__). '/models/ontology/wfRunner.rdf',
		),
		'php' => array(
			//dirname(__FILE__). '/scripts/importSas.php',
		),
		'checks' => array(
			array('type' => 'CheckFileSystemComponent', 'value' => array('id' => 'fs_wfEngine_includes', 'location' => 'wfEngine/includes', 'rights' => 'rw'))
		),
	),
	'managementRole' => 'http://www.tao.lu/middleware/wfEngine.rdf#WorkflowsManagerRole',
	'optimizableClasses' => array(
		'http://www.tao.lu/middleware/wfEngine.rdf#ClassProcessDefinitionResources',
		'http://www.tao.lu/middleware/wfEngine.rdf#ClassProcessExecutionResources',
		'http://www.tao.lu/middleware/wfEngine.rdf#ClassServicesResources',
		'http://www.tao.lu/middleware/wfEngine.rdf#ClassActivityExecutions',
		'http://www.tao.lu/middleware/wfEngine.rdf#ClassAccessControlModes'
	),
	'optimizableProperties' => array(
		'http://www.tao.lu/middleware/wfEngine.rdf#PropertyActualParametersFormalParameter',
		'http://www.tao.lu/middleware/wfEngine.rdf#PropertySupportServicesUrl',
		'http://www.tao.lu/middleware/wfEngine.rdf#PropertyConnectorsType',
		'http://www.tao.lu/middleware/wfEngine.rdf#PropertyConnectorsActivityReference',
		'http://www.tao.lu/middleware/wfEngine.rdf#PropertyProcessInstancesStatus',
		'http://www.tao.lu/middleware/wfEngine.rdf#PropertyProcessInstancesExecutionOf',
		'http://www.tao.lu/middleware/wfEngine.rdf#PropertyActivityExecutionsStatus',
		'http://www.tao.lu/middleware/wfEngine.rdf#PropertyActivityExecutionsExecutionOf',
		'http://www.tao.lu/middleware/wfEngine.rdf#PropertyActivityExecutionsCurrentUser',
		'http://www.tao.lu/middleware/wfEngine.rdf#PropertyActivityExecutionsProcessExecution'
	),
	'constants' => array(
		# actions directory
		"DIR_ACTIONS"			=> $extpath."actions".DIRECTORY_SEPARATOR,
	
		# views directory
		"DIR_VIEWS"				=> $extpath."views".DIRECTORY_SEPARATOR,
	
		# default module name
		'DEFAULT_MODULE_NAME'	=> 'Authentication',
	
		#default action name
		'DEFAULT_ACTION_NAME'	=> 'index',
	
		#BASE PATH: the root path in the file system (usually the document root)
		'BASE_PATH'				=> $extpath ,
	
		#BASE URL (usually the domain root)
		'BASE_URL'				=> ROOT_URL	.'wfEngine/',
	
		#BASE WWW the web resources path
		'BASE_WWW'				=> ROOT_URL	.'wfEngine/views/',
			
		#WWW PATH the path where view medias (templates, img) are stored.
		'WWW_PATH'				=> $extpath.'views'.DIRECTORY_SEPARATOR,
	 
	 	#TAO extension Paths
		'TAOBASE_WWW'			=> ROOT_URL	.'tao/views/',
		'TAOVIEW_PATH'			=> $taopath	.'views'.DIRECTORY_SEPARATOR,
		'TAO_TPL_PATH'			=> $taopath	.'views'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR,
	
		#PROCESS BASE WWW the web path of the process authoring tool
		'PROCESS_BASE_WWW'		=> ROOT_URL	.'wfEngine/views/',
		'PROCESS_BASE_PATH'		=> ROOT_PATH.'wfEngine'.DIRECTORY_SEPARATOR,
									
		# Process Browser page title.
		'PROCESS_BROWSER_TITLE'	=> 'Process BrowserEngine',
	
		# Next/Previous button usable or not.
		'USE_NEXT'				=> true,
		'USE_PREVIOUS'			=> true,
		'FORCE_NEXT'			=> true,
	
		# Keyboard enabled or not.
		'USE_KEYBOARD'			=> true,
	
		# Service mode
		# If set to true, the process dashboard (main view) and the
		# process creation feature are not available.
		'SERVICE_MODE'			=> false	
	)
);
?>