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
	'label' => 'Workflow authoring tool',
	'description' => 'Workflow Authoring extension',
    'license' => 'GPL-2.0',
    'version' => '2.6',
	'author' => 'Open Assessment Technologies',
	'requires' => array(
	    'tao' => '2.6.*',
	    'wfEngine' => '2.6.*'
	),
	'install' => array(
	),
	'uninstall' => array(
	),
    'managementRole' => 'http://www.tao.lu/middleware/wfEngine.rdf#WfAuthoringManagerRole',
    'acl' => array(
        array('grant', 'http://www.tao.lu/middleware/wfEngine.rdf#WfAuthoringManagerRole', array('ext'=>'wfAuthoring'))
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
		'BASE_URL'				=> ROOT_URL	.'wfAuthoring/',
	
		#BASE WWW the web resources path
		'BASE_WWW'				=> ROOT_URL	.'wfAuthoring/views/',
	 
	 	#TAO extension Paths
		'TAOBASE_WWW'			=> ROOT_URL	.'tao/views/',
	)
);