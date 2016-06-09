<?php

/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * 
 */
$extpath = dirname(__FILE__).DIRECTORY_SEPARATOR;
$taopath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'tao'.DIRECTORY_SEPARATOR;
	
return array(
	'name' => 'taoLtiBasicOutcome',
    'label' => 'Result storage for LTI',
	'description' => 'Implements the LTI basic outcome engine for LTI Result Server',
    'license' => 'GPL-2.0',
    'version' => '2.6.2',
	'author' => 'Open Assessment Technologies',
	'requires' => array(
        'taoResultServer' => '>=2.6',
        'taoLti' => '*'
	),
	'models' => array(
		'http://www.tao.lu/Ontologies/taoLtiBasicOutcome.rdf#'
        ),
	'install' => array('rdf' => array(
			dirname(__FILE__). '/models/ontology/taoLtiBasicOutcome.rdf'
		)),
	'update' => 'taoLtiBasicOutcome_scripts_update_Updater',
 	'constants' => array(
	 	# actions directory
		"DIR_ACTIONS"			=> $extpath."actions".DIRECTORY_SEPARATOR,
	
		# views directory
		"DIR_VIEWS"				=> $extpath."views".DIRECTORY_SEPARATOR,
	
		# default module name
		'DEFAULT_MODULE_NAME'	=> 'taoLtiBasicOutcome',
	
		#default action name
		'DEFAULT_ACTION_NAME'	=> 'index',
	
		#BASE PATH: the root path in the file system (usually the document root)
		'BASE_PATH'				=> $extpath,
	
		#BASE URL (usually the domain root)
		'BASE_URL'				=> ROOT_URL . '/taoLtiBasicOutcome',
	
		#BASE WWW the web resources path
		'BASE_WWW'				=> ROOT_URL . '/taoLtiBasicOutcome/views/',
	 
	  	#TAO extension Paths
		'TAOBASE_WWW'			=> ROOT_URL  . '/tao/views/',
	)
);