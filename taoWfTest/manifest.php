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
	'name' => 'taoWfTest',
    'label' => 'Workflow test model',
	'description' => 'TAO Workflow Test definition',
    'license' => 'GPL-2.0',
    'version' => '2.5',
	'author' => 'Open Assessment Technologies',
	'requires' => array(
        'taoTests' => '2.6',
	    'wfEngine' => '*'
	),
	'models' => array(
		'http://www.tao.lu/Ontologies/TAOTest.rdf'
	),
	'install' => array(
		'rdf' => array(
		    dirname(__FILE__). '/models/ontology/wftest.rdf',
		    dirname(__FILE__). '/models/ontology/itemContainer.rdf'
		)
	),
    'managementRole' => 'http://www.tao.lu/Ontologies/TAOTest.rdf#WfTestManagerRole',
    'acl' => array(
        array('grant', 'http://www.tao.lu/Ontologies/TAOTest.rdf#WfTestManagerRole', array('ext'=>'taoWfTest')),
        array('grant', 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemsManagerRole', array('ext'=>'taoWfTest', 'mod' => 'Authoring')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#DeliveryRole', array('ext'=>'wfEngine', 'mod' => 'ProcessRunner'))
    ),
	'constants' => array(
		# actions directory
		"DIR_ACTIONS"			=> $extpath."actions".DIRECTORY_SEPARATOR,
	
		# views directory
		"DIR_VIEWS"				=> $extpath."views".DIRECTORY_SEPARATOR,
	
		# default module name
		'DEFAULT_MODULE_NAME'	=> 'Main',
	
		#default action name
		'DEFAULT_ACTION_NAME'	=> 'index',
	
		#BASE PATH: the root path in the file system (usually the document root)
		'BASE_PATH'				=> $extpath,
	
		#BASE URL (usually the domain root)
		'BASE_URL'				=> ROOT_URL	.'taoWfTest/',
	
		#BASE WWW the web resources path
		'BASE_WWW'				=> ROOT_URL	.'taoWfTest/views/',
	
	 	#TAO extension Paths
		'TAOBASE_WWW'			=> ROOT_URL	.'tao/views/',
	)
);