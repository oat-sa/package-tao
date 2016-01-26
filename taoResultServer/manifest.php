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
    'label' => 'Result core extension',
	'description' => 'Results Server management and exposed interfaces for results data submission',
    'license' => 'GPL-2.0',
    'version' => '2.8.2',
	'author' => 'Open Assessment Technologies',
    //taoResults may be needed for the taoResults taoResultServerModel that uses taoResults db storage
	'requires' => array(
	    'tao' => '>=2.7.0'
	),
	'models' => array(
		'http://www.tao.lu/Ontologies/TAOResultServer.rdf#'
	),
	'install' => array(
        'rdf' => array(
			dirname(__FILE__). '/models/ontology/taoResultServer.rdf'
		)
    ),
    'update' => 'taoResultServer_scripts_update_Updater',

    'managementRole' => 'http://www.tao.lu/Ontologies/TAOResultServer.rdf#ResultServerRole',
    'acl' => array(
        array('grant', 'http://www.tao.lu/Ontologies/TAOResultServer.rdf#ResultServerRole', array('ext'=>'taoResultServer')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#DeliveryRole', array('ext'=>'taoResultServer', 'mod' => 'ResultServerStateFull')),
        array('grant', 'http://www.tao.lu/Ontologies/generis.rdf#AnonymousRole', array('ext'=>'taoResultServer', 'mod' => 'RestResultServer'))
        
    ),
 	'constants' => array(
	 	# actions directory
		"DIR_ACTIONS"			=> $extpath."actions".DIRECTORY_SEPARATOR,
	
		# views directory
		"DIR_VIEWS"				=> $extpath."views".DIRECTORY_SEPARATOR,
	
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
	)
);
