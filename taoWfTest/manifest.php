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
	'description' => 'TAO Workflow Test definition',
	'version' => '2.5',
	'author' => 'Open Assessment Technologies',
	'dependencies' => array('taoTests', 'wfEngine'),
	'models' => array(
		'http://www.tao.lu/Ontologies/TAOTest.rdf'
	),
	'install' => array(
		'rdf' => array(
			dirname(__FILE__). '/models/ontology/aclrole.rdf',
		    dirname(__FILE__). '/models/ontology/wftest.rdf',
		    dirname(__FILE__). '/models/ontology/itemContainer.rdf'
		)
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
	
		#BASE DATA the path where items are stored
		'BASE_DATA'				=> $extpath.'data'.DIRECTORY_SEPARATOR,
	
		#BASE PREVIEW the path where items are compiled for preview
		'BASE_PREVIEW'			=> $extpath.'views'.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR,

		#BASE PREVIEW URL the url pointing at where items can be previewed
		'BASE_PREVIEW_URL'		=> ROOT_URL.'taoItems/views/runtime/',
	 
	 	#TAO extension Paths
		'TAOBASE_WWW'			=> ROOT_URL	.'tao/views/',
		'TAOVIEW_PATH'			=> $taopath	.'views'.DIRECTORY_SEPARATOR,
		'TAO_TPL_PATH'			=> $taopath	.'views'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR,
	
	)
);
?>