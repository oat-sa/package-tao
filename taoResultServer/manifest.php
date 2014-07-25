<?php

/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * 
 */
$extpath = dirname(__FILE__).DIRECTORY_SEPARATOR;
$taopath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'tao'.DIRECTORY_SEPARATOR;
	
return array(
	'name' => 'taoResultServer',
	'description' => 'Results Server management and exposed interfaces for results data submission',
	'version' => '1.0',
	'author' => 'Open Assessment Technologies',
    //taoResults may be needed for the taoResults taoResultServerModel that uses taoResults db storage
	'dependencies' => array('tao'),
	'models' => array(
		'http://www.tao.lu/Ontologies/TAOResultServer.rdf#'
	),
	'install' => array(
        'rdf' => array(
			dirname(__FILE__). '/models/ontology/taoResultServer.rdf',
            dirname(__FILE__). '/models/ontology/aclrole.rdf'
		)
    ),
    'managementRole' => 'http://www.tao.lu/Ontologies/TAOResultServer.rdf#ResultServerRole',
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
		'DEFAULT_MODULE_NAME'	=> 'Result',
	
		#default action name
		'DEFAULT_ACTION_NAME'	=> 'index',
	
		#BASE PATH: the root path in the file system (usually the document root)
		'BASE_PATH'				=> $extpath,
	
		#BASE URL (usually the domain root)
		'BASE_URL'				=> ROOT_URL . '/taoResultServer',
	
		#BASE WWW the web resources path
		'BASE_WWW'				=> ROOT_URL . '/taoResultServer/views/',
	 
	  	#TAO extension Paths
		'TAOBASE_WWW'			=> ROOT_URL  . '/tao/views/',
		'TAOVIEW_PATH'			=> $taopath.'views'.DIRECTORY_SEPARATOR,
		'TAO_TPL_PATH'			=> $taopath.'views'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR,
	)
);
?>