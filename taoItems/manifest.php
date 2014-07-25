<?php
/*  
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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
$extpath = dirname(__FILE__).DIRECTORY_SEPARATOR;
$taopath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'tao'.DIRECTORY_SEPARATOR;

return array(
	'name' => 'taoItems',
	'description' => 'the TAO Items extension provides the item creation, authoring and managment',
	'version' => '2.4',
	'author' => 'Open Assessment Technologies, CRP Henri Tudor',
	'dependencies' => array('tao'),
	'models' => array(
		'http://www.tao.lu/Ontologies/TAOItem.rdf'
	),
	'install' => array(
		'rdf' => array(
			dirname(__FILE__). '/models/ontology/aclrole.rdf',
		    dirname(__FILE__). '/models/ontology/taoitem.rdf',
		    dirname(__FILE__). '/models/ontology/taoItemRunner.rdf'
		),
		'checks' => array(
			array('type' => 'CheckFileSystemComponent', 'value' => array('id' => 'fs_taoItems_data', 'location' => 'taoItems/data/itemdata', 'rights' => 'rw')),
			array('type' => 'CheckFileSystemComponent', 'value' => array('id' => 'fs_taoItems_includes', 'location' => 'taoItems/includes', 'rights' => 'rw'))
		),
		'php'	=> array(
			dirname(__FILE__).'/scripts/install/addLocalSource.php',
		)
	),
	'managementRole' => 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemsManagerRole',
	'optimizableClasses' => array(
			'http://www.tao.lu/Ontologies/TAOItem.rdf#Item',
			'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModels',
			'http://www.tao.lu/Ontologies/TAOItem.rdf#DeliveryTarget',
			'http://www.tao.lu/Ontologies/TAOItem.rdf#ModelStatus'
	),
	'constants' => array(
		# actions directory
		"DIR_ACTIONS"			=> $extpath."actions".DIRECTORY_SEPARATOR,
	
		# views directory
		"DIR_VIEWS"				=> $extpath."views".DIRECTORY_SEPARATOR,
	
		# default module name
		'DEFAULT_MODULE_NAME'	=> 'Items',
	
		#default action name
		'DEFAULT_ACTION_NAME'	=> 'index',
	
		#BASE PATH: the root path in the file system (usually the document root)
		'BASE_PATH'				=> $extpath,
	
		#BASE URL (usually the domain root)
		'BASE_URL'				=> ROOT_URL	.'taoItems/',
	
		#BASE WWW the web resources path
		'BASE_WWW'				=> ROOT_URL	.'taoItems/views/',
	
		#BASE DATA the path where items are stored
		'BASE_DATA'				=> $extpath.'data'.DIRECTORY_SEPARATOR,
	
		#BASE PREVIEW the path where items are compiled for preview
		'BASE_PREVIEW'			=> $extpath.'views'.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR,

		#BASE PREVIEW URL the url pointing at where items can be previewed
		'BASE_PREVIEW_URL'		=> ROOT_URL.'taoItems/views/runtime/',
	 
		#WWW PATH the path where view medias (templates, img) are stored.
		'WWW_PATH'				=> $extpath.'views'.DIRECTORY_SEPARATOR,	
		
	 	#TAO extension Paths
		'TAOBASE_WWW'			=> ROOT_URL	.'tao/views/',
		'TAOVIEW_PATH'			=> $taopath	.'views'.DIRECTORY_SEPARATOR,
		'TAO_TPL_PATH'			=> $taopath	.'views'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR,
	
		'USE_CACHED_XSL'		=> false
	)
);
?>