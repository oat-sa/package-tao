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
	'label' => 'LTI library',
    'description' => 'TAO LTI library and helpers',
    'license' => 'GPL-2.0',
    'version' => '1.0',
	'author' => 'Open Assessment Technologies SA',
	'requires' => array(
	   'tao' => '>=2.7.0'
	),
	'models' => array(
	 	'http://www.tao.lu/Ontologies/TAOLTI.rdf',
	 	'http://www.imsglobal.org/imspurl/lis/v1/vocab/person',
	 	'http://www.imsglobal.org/imspurl/lis/v1/vocab/membership'
	 ),
	'install' => array(
		'rdf' => array(
			dirname(__FILE__). '/models/ontology/lti.rdf',
			dirname(__FILE__). '/models/ontology/roledefinition.rdf',
			dirname(__FILE__). '/models/ontology/ltiroles_person.rdf',
			dirname(__FILE__). '/models/ontology/ltiroles_membership.rdf'
		)
	),
    'managementRole' => 'http://www.tao.lu/Ontologies/TAOLTI.rdf#LtiManagerRole',
    'acl' => array(
        array('grant', 'http://www.tao.lu/Ontologies/TAOLTI.rdf#LtiManagerRole', array('ext'=>'taoLti')),
        array('grant', 'http://www.tao.lu/Ontologies/generis.rdf#AnonymousRole',array('ext'=>'taoLti','mod' => 'CookieUtils')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#BaseUserRole', array('ext'=>'taoLti','mod' => 'LtiConsumer', 'act' => 'call')),
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
	)
);
