<?php

/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * 
 */
$extpath = dirname(__FILE__).DIRECTORY_SEPARATOR;
$taopath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'tao'.DIRECTORY_SEPARATOR;
	
return array(
	'name' => 'taoResultsUdp',
    'label' => 'Result Storage UDP',
	'description' => 'Not intended for production.
        Implements the results storage.to be uded as documentation reference ',
    'license' => 'GPL-2.0',
    'version' => '1.0',
	'author' => 'Open Assessment Technologies',
	'requires' => array(
	    'taoResultServer' => '2.6'
	),
	'models' => array(
        'http://www.tao.lu/Ontologies/taoResultsUdp.rdf#'
    ),
	'install' => array('rdf' => array(
			dirname(__FILE__). '/models/ontology/taoResultsUdp.rdf'
	)),
 	'constants' => array(
	 
		# models directory
		"DIR_MODELS"			=> $extpath."models".DIRECTORY_SEPARATOR,
	
		# default module name
		'DEFAULT_MODULE_NAME'	=> 'taoResultsUdp',

		#BASE PATH: the root path in the file system (usually the document root)
		'BASE_PATH'				=> $extpath,
	
		#BASE URL (usually the domain root)
		'BASE_URL'				=> ROOT_URL . '/taoResultsUdp',
	
		#BASE WWW the web resources path
		'BASE_WWW'				=> ROOT_URL . '/taoResultsUdp/views/',
	 
	  	#TAO extension Paths
		'TAOBASE_WWW'			=> ROOT_URL  . '/tao/views/',
		
	)
);
?>
