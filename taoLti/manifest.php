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
$taopath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'tao'.DIRECTORY_SEPARATOR;

return array(
	'name' => 'taoLti',
	'description' => 'TAO LTI Tool library',
	'version' => '0.9',
	'author' => 'Open Assessment Technologies SA',
	'dependencies' => array('tao'),
	'models' => array(
	 	'http://www.tao.lu/Ontologies/TAOLTI.rdf',
	 	'http://www.imsglobal.org/imspurl/lis/v1/vocab/person',
	 	'http://www.imsglobal.org/imspurl/lis/v1/vocab/membership'
	 ),
	'install' => array(
		'checks' => array(
			array('type' => 'CheckFileSystemComponent', 'value' => array('id' => 'fs_taoLti_includes', 'location' => 'taoLti/includes', 'rights' => 'rw'))
		),
		'rdf' => array(
			dirname(__FILE__). '/models/ontology/lti.rdf',
			dirname(__FILE__). '/models/ontology/roledefinition.rdf',
			dirname(__FILE__). '/models/ontology/ltiroles_person.rdf',
			dirname(__FILE__). '/models/ontology/ltiroles_membership.rdf'
		)
	),
	'constants' => array(
		# actions directory
		"DIR_ACTIONS"			=> $extpath."actions".DIRECTORY_SEPARATOR,
	
		# views directory
		"DIR_VIEWS"				=> $extpath."views".DIRECTORY_SEPARATOR,
	
		# default module name
		'DEFAULT_MODULE_NAME'	=> 'Browser',
	
		#default action name
		'DEFAULT_ACTION_NAME'	=> 'index',
	
		#BASE PATH: the root path in the file system (usually the document root)
		'BASE_PATH'				=> $extpath ,
	
		#BASE URL (usually the domain root)
		'BASE_URL'				=> ROOT_URL . 'taoLti/',
	
		#BASE WWW the web resources path
		'BASE_WWW'				=> ROOT_URL . 'taoLti/views/',
	 
	
		#TAO extension Paths
		'TAOBASE_WWW'			=> ROOT_URL  . 'tao/views/',
		'TAOVIEW_PATH'			=> $taopath.'views'.DIRECTORY_SEPARATOR,
		'TAO_TPL_PATH'			=> $taopath.'views'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR,

	)
);
?>