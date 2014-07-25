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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

return array(
	'name' => 'complex',
	'description' => 'complex testing manifest',
	'version' => '1.0',
	'author' => 'TAO Team',
	'dependencies' => array('taoItemBank', 'taoDocuments'),
	'models' => array(
		'http://www.tao.lu/Ontologies/taoFuncACL.rdf',
		'http://www.tao.lu/Ontologies/taoItemBank.rdf'
	),
	'install' => array(
		'rdf' => array(
				array('ns' => 'http://www.tao.lu/Ontologies/taoFuncACL.rdf', 'file' => '/extension/path/models/ontology/taofuncacl.rdf'),
				array('ns' => 'http://www.tao.lu/Ontologies/taoItemBank.rdf', 'file' => '/extension/path/models/ontology/taoitembank.rdf')
		),
		'checks' => array(
			array('type' => 'CheckPHPRuntime', 'value' => array('id' => 'php_runtime', 'min' => '5.3', 'max' => '5.3.18')),
			array('type' => 'CheckPHPExtension', 'value' => array('id' => 'ext_pdo', 'name' => 'PDO')),
			array('type' => 'CheckPHPExtension', 'value' => array('id' => 'ext_svn','name' => 'svn', 'optional' => true)),
			array('type' => 'CheckPHPExtension', 'value' => array('id' => 'ext_suhosin','name' => 'suhosin', 'optional' => true)),
			array('type' => 'CheckPHPINIValue', 'value' => array('id' => 'ini_register_globals', 'name' => 'register_globals', 'value' => "0")),
			array('type' => 'CheckFileSystemComponent', 'value' => array('id' => 'fs_root','location' => '.', 'rights' => 'rw', 'name' => 'fs_root')),
		)
	),
	 'constants' => array(
		 // web services
         'WS_ENDPOINT_TWITTER' => 'http://twitter.com/statuses/',
         'WS_ENDPOINT_FACEBOOK' => 'http://api.facebook.com/restserver.php'
	 ),
	 'optimizableClasses' => array(
	 	'http://www.linkeddata.org/ontologies/data.rdf#myClass1',
	 	'http://www.linkeddata.org/ontologies/data.rdf#myClass2'
	 ),
	 'optimizableProperties' => array(
	 	'http://www.linkeddata.org/ontologies/props.rdf#myProp1',
	 	'http://www.linkeddata.org/ontologies/props.rdf#myProp2'
	 )
);
?>