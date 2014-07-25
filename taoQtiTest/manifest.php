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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
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
	'name' => 'taoQtiTest',
	'description' => 'the TAO QTI test implementation',
	'version' => '2.5',
	'author' => 'Open Assessment Technologies',
	'dependencies' => array('taoTests', 'taoQTI', 'taoQtiCommon'),
	'models' => array(
		'http://www.tao.lu/Ontologies/TAOTest.rdf'
	),
	'install' => array(
		'rdf' => array(
			dirname(__FILE__) . '/models/ontology/qtitest.rdf',
		    dirname(__FILE__) . '/models/ontology/taoQtiTestItemRunner.rdf',
		    dirname(__FILE__) . '/models/ontology/aclrole.rdf'
		),
	    'checks' => array(
	        array('type' => 'CheckFileSystemComponent', 'value' => array('id' => 'fs_taoQtiTest_data_testdata', 'location' => 'taoQtiTest/data/testdata', 'rights' => 'rw')),
	    ),
		'php'	=> array(
			dirname(__FILE__) . '/scripts/install/addQtiTestFolder.php'
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
		'BASE_URL'				=> ROOT_URL	.'taoQtiTest/',
	
		#BASE WWW the web resources path
		'BASE_WWW'				=> ROOT_URL	.'taoQtiTest/views/',
	
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
	                
	    # taoQtiTest configuration
        'QTITEST_RESULT_SERVER'             => ROOT_URL . 'taoQtiTest/TestRunner/',
	    'QTITEST_RESULT_SERVER_CONFIG_KEY'  => 'qtiTestResultServerUri'
	)
);
?>