<?php

/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * 
 */
$extpath = dirname(__FILE__).DIRECTORY_SEPARATOR;
$taopath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'tao'.DIRECTORY_SEPARATOR;
	
return array(
	'name' => 'taoAltResultStorage',
    'label' => 'Result storage key-value implementation',
	'description' => 'Implements Alternative Result storage results interface using persistencies',
	'version' => '1.0',
    'license' => 'GPL-2.0',
	'author' => 'Open Assessment Technologies',
	'requires' => array(
        'taoResultServer' => '2.6'
	),
	'models' => array(
		'http://www.tao.lu/Ontologies/taoAltResultStorage.rdf#'
        ),
	'install' => array('rdf' => array(
			dirname(__FILE__). '/models/ontology/taoAltResultStorage.rdf'
		)),
	'classLoaderPackages' => array( 
		dirname(__FILE__).'/actions/',
		dirname(__FILE__).'/helpers/'
	 ),
 	'constants' => array(
	 	# actions directory
		"DIR_ACTIONS"			=> $extpath."actions".DIRECTORY_SEPARATOR,
	
		# models directory
		"DIR_MODELS"			=> $extpath."models".DIRECTORY_SEPARATOR,
	
		# views directory
		"DIR_VIEWS"				=> $extpath."views".DIRECTORY_SEPARATOR,
	
		# helpers directory
		"DIR_HELPERS"			=> $extpath."helpers".DIRECTORY_SEPARATOR,
	
		# default module name
		'DEFAULT_MODULE_NAME'	=> 'taoAltResultStorage',
	
		#default action name
		'DEFAULT_ACTION_NAME'	=> 'index',
	
		#BASE PATH: the root path in the file system (usually the document root)
		'BASE_PATH'				=> $extpath,
	
		#BASE URL (usually the domain root)
		'BASE_URL'				=> ROOT_URL . '/taoAltResultStorage',
	
		#BASE WWW the web resources path
		'BASE_WWW'				=> ROOT_URL . '/taoAltResultStorage/views/',
	 
	  	#TAO extension Paths
		'TAOBASE_WWW'			=> ROOT_URL  . '/tao/views/',
	)
);