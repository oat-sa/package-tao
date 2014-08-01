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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

$extpath = dirname(__FILE__).DIRECTORY_SEPARATOR;
$taopath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'tao'.DIRECTORY_SEPARATOR;

return array(
	'name' => 'taoQtiItem',
    'label' => 'QTI item model',
	'description' => 'TAO QTI item model',
    'license' => 'GPL-2.0',
    'version' => '2.6',
	'author' => 'Open Assessment Technologies',
	'requires' => array(
	    'taoItems' => '2.6',
	    'taoQtiCommon' => '2.6'
	),
	'models' => array(
		'http://www.tao.lu/Ontologies/TAOItem.rdf'
	),
	'install' => array(
		'rdf' => array(
			dirname(__FILE__). '/install/ontology/taoQti.rdf',
		    dirname(__FILE__). '/install/ontology/qtiItemRunner.rdf'
		),
		'checks' => array(
		    array('type' => 'CheckCustom', 'value' => array('id' => 'taoQtiItem_custom_mathjax', 'name' => 'mathjax', 'extension' => 'taoQtiItem', 'optional' => true))
		),
	),
	'local'	=> array(
		'php'	=> array(
			dirname(__FILE__).'/install/local/addQTIExamples.php'
		)
	),
    'autoload' => array (
        'psr-4' => array(
            'oat\\taoQtiItem\\' => dirname(__FILE__).DIRECTORY_SEPARATOR
        ),
        'psr-0' => array(
            'Sabberworm\\CSS' => __DIR__ . DIRECTORY_SEPARATOR . 'vendor'. DIRECTORY_SEPARATOR .'PHP-CSS-Parser'. DIRECTORY_SEPARATOR .'lib'. DIRECTORY_SEPARATOR,  
        ) 
    ),
    'routes' => array(
        '/taoQtiItem' => 'oat\\taoQtiItem\\controller'
    ),
	'managementRole' => 'http://www.tao.lu/Ontologies/TAOItem.rdf#QTIManagerRole',
    'acl' => array(
        array('grant', 'http://www.tao.lu/Ontologies/TAOItem.rdf#QTIManagerRole', array('ext'=>'taoQtiItem')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#DeliveryRole', array('ext'=>'taoQtiItem', 'mod' => 'QtiItemRunner')),
        array('grant', 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemsManagerRole', array('ext'=>'taoQtiItem', 'mod' => 'QtiAuthoring')),
        array('grant', 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemsManagerRole', array('ext'=>'taoQtiItem', 'mod' => 'QtiPreview'))
    ),    
	'constants' => array(
		# views directory
		"DIR_VIEWS"				=> $extpath."views".DIRECTORY_SEPARATOR,
	
		# default module name
		'DEFAULT_MODULE_NAME'	=> 'Main',
	
		#default action name
		'DEFAULT_ACTION_NAME'	=> 'index',
	
		#BASE PATH: the root path in the file system (usually the document root)
		'BASE_PATH'				=> $extpath,
	
		#BASE URL (usually the domain root)
		'BASE_URL'				=> ROOT_URL	.'taoQtiItem/',
	
		#BASE WWW the web resources path
		'BASE_WWW'				=> ROOT_URL	.'taoQtiItem/views/',
	
	 	#TAO extension Paths
		'TAOBASE_WWW'			=> ROOT_URL	.'tao/views/',
	)
);