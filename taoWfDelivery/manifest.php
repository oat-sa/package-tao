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
 * 
 */

$extpath = dirname(__FILE__).DIRECTORY_SEPARATOR;
$taopath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'tao'.DIRECTORY_SEPARATOR;

return array(
	'name' => 'taoWfDelivery',
	'description' => 'TAO Workflow Delivery Model',
	'version' => '2.4',
	'author' => 'Open Assessment Technologies',
	'dependencies' => array('taoDelivery', 'wfAuthoring'),
	'models' => array(
		'http://www.tao.lu/Ontologies/TAODelivery.rdf'
	),
	'install' => array(
		'rdf' => array(
				dirname(__FILE__). '/models/ontology/model.rdf',
		)
	),
	'constants' => array(
		# actions directory
		"DIR_ACTIONS"			=> $extpath."actions".DIRECTORY_SEPARATOR,
	
		# views directory
		"DIR_VIEWS"				=> $extpath."views".DIRECTORY_SEPARATOR,
	
		# default module name
		'DEFAULT_MODULE_NAME'	=> 'DeliveryServerAuthentification',
	
		#default action name
		'DEFAULT_ACTION_NAME'	=> 'index',
	
		#BASE PATH: the root path in the file system (usually the document root)
		'BASE_PATH'				=> $extpath,
	
		#BASE URL (usually the domain root)
		'BASE_URL'				=> ROOT_URL . 'taoWfDelivery/',
	
		#BASE WWW the web resources path
		'BASE_WWW'				=> ROOT_URL . 'taoWfDelivery/views/',
	
	 	#TAO extension Paths
		'TAOBASE_WWW'			=> ROOT_URL  . 'tao/views/',
		'TAOVIEW_PATH'			=> $taopath.'views'.DIRECTORY_SEPARATOR,
		'TAO_TPL_PATH'			=> $taopath.'views'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR,
	
		#PROCESS BASE WWW the web path of the process authoring tool
		'PROCESS_BASE_WWW'		=> ROOT_URL. 'wfEngine/views/',
		'WFAUTHORING_CSS_URL'	=> ROOT_URL. 'wfAuthoring/views/css/',
		'WFAUTHORING_SCRIPTS_URL'	=> ROOT_URL. 'wfAuthoring/views/js/authoring/',
		'PROCESS_BASE_PATH'		=> ROOT_PATH.'wfAuthoring'.DIRECTORY_SEPARATOR,
		'PROCESS_TPL_PATH'		=> ROOT_PATH.'wfAuthoring'.DIRECTORY_SEPARATOR
									.'views'.DIRECTORY_SEPARATOR
									.'templates'.DIRECTORY_SEPARATOR
									.'authoring'.DIRECTORY_SEPARATOR,
	
		# Next/Previous button usable or not.
		'USE_NEXT'				=> true,
		'USE_PREVIOUS'			=> true,
		'FORCE_NEXT'			=> true,

		# Maximum Compilation Time of a Test before Timeout
		'TEST_COMPILATION_TIME'	=> 300,
	)
);
?>