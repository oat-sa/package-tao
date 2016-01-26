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
	'name' => 'taoDevTools',
	'label' => 'Development Tools',
    'description' => 'Developer tools that can assist you in creating new extensions, run scripts, destroy your install',
    'license' => 'GPL-2.0',
    'version' => '2.8',
	'author' => 'Open Assessment Technologies',
	'requires' => array(
	    'tao' => '>=2.7.0'
	),
    'managementRole' => 'http://www.tao.lu/Ontologies/TAO.rdf#TaoDevToolsRole',
    'acl' => array(
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#SysAdminRole', array('ext'=>'taoDevTools')),
    ),
    'uninstall' => array(
    ),
    'update' => 'oat\\taoDevTools\\scripts\\update\\Updater',
    'routes' => array(
        '/taoDevTools' => 'oat\\taoDevTools\\actions'
    ),    
	'constants' => array(
		# actions directory
		"DIR_ACTIONS"			=> $extpath."actions".DIRECTORY_SEPARATOR,
	
		# views directory
		"DIR_VIEWS"				=> $extpath."views".DIRECTORY_SEPARATOR,
	
		# default module name
		'DEFAULT_MODULE_NAME'	=> 'Groups',
	
		#default action name
		'DEFAULT_ACTION_NAME'	=> 'index',
	
		#BASE URL (usually the domain root)
		'BASE_URL'				=> ROOT_URL .'taoDevTools/',
	
		#BASE WWW the web resources path
		'BASE_WWW'				=> ROOT_URL .'taoDevTools/views/',
	)
);
