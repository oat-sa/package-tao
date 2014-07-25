<?php
/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
$extpath = dirname(__FILE__).DIRECTORY_SEPARATOR;
$taopath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'tao'.DIRECTORY_SEPARATOR;

return array(
	'name' => 'wfAuthoring',
	'description' => 'Workflow Authoring extension',
	'version' => '2.4',
	'author' => 'Open Assessment Technologies',
	'dependencies' => array('tao', 'wfEngine'),
	'install' => array(
		'checks' => array(
			array('type' => 'CheckFileSystemComponent', 'value' => array('id' => 'fs_wfAuthoring_includes', 'location' => 'wfAuthoring/includes', 'rights' => 'rw'))
		)
	),
	'managementRole' => 'http://www.tao.lu/middleware/wfEngine.rdf#WorkflowsManagerRole',
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
		'BASE_URL'				=> ROOT_URL	.'wfAuthoring/',
	
		#BASE WWW the web resources path
		'BASE_WWW'				=> ROOT_URL	.'wfAuthoring/views/',
	 
	 	#TAO extension Paths
		'TAOBASE_WWW'			=> ROOT_URL	.'tao/views/',
		'TAOVIEW_PATH'			=> $taopath	.'views'.DIRECTORY_SEPARATOR,
		'TAO_TPL_PATH'			=> $taopath	.'views'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR,
	
		#PROCESS BASE WWW the web path of the process authoring tool
		'PROCESS_BASE_WWW'		=> ROOT_URL	.'wfEngine/views/',
		'WFAUTHORING_SCRIPTS_URL'	=> ROOT_URL	.'wfAuthoring/views/js/authoring/',
		'WFAUTHORING_CSS_URL'	=> ROOT_URL	.'wfAuthoring/views/css/',
		'PROCESS_BASE_PATH'		=> ROOT_PATH.'wfEngine'.DIRECTORY_SEPARATOR,

		# Service mode
		# If set to true, the process dashboard (main view) and the
		# process creation feature are not available.
		'SERVICE_MODE'			=> false	
	)
);
?>