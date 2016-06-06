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

/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
$extpath = dirname(__FILE__).DIRECTORY_SEPARATOR;

return array(
    'name' => 'funcAcl',
    'label' => 'Functionality ACL',
	'description' => 'Functionality Access Control Layer',
    'license' => 'GPL-2.0',
    'version' => '2.7.2',
	'author' => 'Open Assessment Technologies, CRP Henri Tudor',
	'requires' => array(
	    'tao' => '>=2.7'
    ),
	'models' => array(
		'http://www.tao.lu/Ontologies/taoFuncACL.rdf'
	),
	'install' => array(
		'rdf' => array(
			dirname(__FILE__). '/models/ontology/taofuncacl.rdf'
		),
	    'php' => array(
	        dirname(__FILE__). '/scripts/install/setFuncAclImpl.php'
	    ),
	),
	'update' => 'oat\\funcAcl\\scripts\\update\\Updater',
	'managementRole' => 'http://www.tao.lu/Ontologies/taoFuncACL.rdf#FuncAclManagerRole',
    'acl' => array(
        array('grant', 'http://www.tao.lu/Ontologies/taoFuncACL.rdf#FuncAclManagerRole', array('ext'=>'funcAcl')),
    ),
	'optimizableClasses' => array(
		'http://www.tao.lu/Ontologies/taoFuncACL.rdf#Extension'
	),
	'constants' => array(
	
		# actions directory
		"DIR_ACTIONS" => $extpath."actions".DIRECTORY_SEPARATOR,
	
		# views directory
		"DIR_VIEWS" => $extpath."views".DIRECTORY_SEPARATOR,
	
		# default module name
		'DEFAULT_MODULE_NAME' => 'Main',
	
		#default action name
		'DEFAULT_ACTION_NAME' => 'index',
	
		#BASE PATH: the root path in the file system (usually the document root)
		'BASE_PATH' => $extpath,
	
		#BASE URL (usually the domain root)
		'BASE_URL' => ROOT_URL.'funcAcl/',
	
		#BASE WWW the web resources path
		'BASE_WWW' => ROOT_URL . 'funcAcl/views/',
	 
	 	#TPL PATH the path to the templates
	 	'TPL_PATH'	=> $extpath."views".DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR,
	 )
);
