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
    'label' => 'Item core extension',
	'description' => 'TAO Items extension',
    'license' => 'GPL-2.0',
    'version' => '2.6.4',
	'author' => 'Open Assessment Technologies, CRP Henri Tudor',
	'requires' => array(
	    'tao' => '>=2.7'
    ),
	'models' => array(
		'http://www.tao.lu/Ontologies/TAOItem.rdf'
	),
	'install' => array(
		'rdf' => array(
		    dirname(__FILE__). '/models/ontology/taoitem.rdf',
		    dirname(__FILE__). '/models/ontology/taoItemRunner.rdf',
		    dirname(__FILE__). '/models/ontology/indexation.rdf'
		),
		'php'	=> array(
			dirname(__FILE__).'/scripts/install/addLocalSource.php',
		)
	),
    'update' => 'taoItems_scripts_update_Updater',
	'managementRole' => 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemsManagerRole',
    'acl' => array(
        array('grant', 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemsManagerRole', array('ext'=>'taoItems')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#DeliveryRole', array('ext'=>'taoItems', 'mod' => 'ItemRunner')),
    ),    
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
	)
);
