<?php
/**
 *   
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
	'name' => 'ltiDeliveryProvider',
    'label' => 'LTI Delivery Tool Provider',
	'description' => 'The LTI Delivery Tool Provider allows third party applications to embed deliveries created in Tao',
    'license' => 'GPL-2.0',
    'version' => '1.0',
	'author' => 'Open Assessment Technologies',
	'requires' => array(
	    'taoDelivery' => '>=2.6',
	    'taoLti' => '2.6',
        'taoLtiBasicOutcome' => '2.6'
	),
	'models' => array(
	 	'http://www.tao.lu/Ontologies/TAOLTI.rdf',
		'http://www.imsglobal.org/imspurl/lis/v1/vocab/membership'
	 ),
	'install' => array(
		'rdf' => array(
			dirname(__FILE__). '/install/ontology/deliverytool.rdf'
		)
	),
    'routes' => array(
        '/ltiDeliveryProvider' => 'oat\\ltiDeliveryProvider\\controller'
    ),
    'managementRole' => 'http://www.tao.lu/Ontologies/TAOLTI.rdf#LtiDeliveryProviderManagerRole',
    'acl' => array(
        array('grant', 'http://www.tao.lu/Ontologies/TAOLTI.rdf#LtiDeliveryProviderManagerRole', array('ext'=>'ltiDeliveryProvider')),
        array('grant', 'http://www.tao.lu/Ontologies/generis.rdf#AnonymousRole', array('ext'=>'ltiDeliveryProvider', 'mod' => 'DeliveryTool', 'act' => 'launch')),
        array('grant', 'http://www.tao.lu/Ontologies/TAOLTI.rdf#LtiBaseRole', array('ext'=>'ltiDeliveryProvider', 'mod' => 'DeliveryTool', 'act' => 'run')),
        array('grant', 'http://www.imsglobal.org/imspurl/lis/v1/vocab/membership#Learner', array('ext'=>'ltiDeliveryProvider', 'mod' => 'DeliveryRunner')),
        array('grant', 'http://www.imsglobal.org/imspurl/lis/v1/vocab/membership#Instructor', array('ext'=>'ltiDeliveryProvider', 'mod' => 'LinkConfiguration')),
        array('grant', 'http://www.imsglobal.org/imspurl/lis/v1/vocab/membership#Instructor', array('ext'=>'taoDelivery', 'mod'=>'Delivery', 'act'=>'getOntologyData'))
    ),
	'constants' => array(
	
		# views directory
		"DIR_VIEWS"				=> $extpath."views".DIRECTORY_SEPARATOR,
	
		# default module name
		'DEFAULT_MODULE_NAME'	=> 'Browser',
	
		#default action name
		'DEFAULT_ACTION_NAME'	=> 'index',
	
		#BASE PATH: the root path in the file system (usually the document root)
		'BASE_PATH'				=> $extpath ,
	
		#BASE URL (usually the domain root)
		'BASE_URL'				=> ROOT_URL . 'ltiDeliveryProvider/',
	
		#BASE WWW the web resources path
		'BASE_WWW'				=> ROOT_URL . 'ltiDeliveryProvider/views/',
	),
    'extra' => array(
        'structures' => dirname(__FILE__).DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'structures.xml',
    )
);
