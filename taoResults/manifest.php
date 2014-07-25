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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

$extpath = dirname(__FILE__).DIRECTORY_SEPARATOR;
$taopath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'tao'.DIRECTORY_SEPARATOR;

return array(
	'name' => 'taoResults',
    'label' => 'Result storage ontology',
	'description' => 'TAO Results extension',
    'license' => 'GPL-2.0',
    'version' => '2.6',
	'author' => 'Open Assessment Technologies, CRP Henri Tudor',
    //taoItems is only needed for the item model property retrieval
	'requires' => array(
	    'taoResultServer'  => '2.6',
        'taoItems' => '*'
    ),
	'models' => array(
		'http://www.tao.lu/Ontologies/TAOResult.rdf'
	),
	'install' => array(
		'rdf' => array(
			dirname(__FILE__). '/models/ontology/taoresult.rdf'
		),
		'checks' => array(
			array('type' => 'CheckFileSystemComponent', 'value' => array('id' => 'fs_taoResults_views_genpics', 'location' => 'taoResults/views/genpics', 'rights' => 'rw'))
		),
	    'php' => array(
            dirname(__FILE__). '/scripts/install/postInstall.php'
        )
	),
	'managementRole' => 'http://www.tao.lu/Ontologies/TAOResult.rdf#ResultsManagerRole',
    'acl' => array(
        array('grant', 'http://www.tao.lu/Ontologies/TAOResult.rdf#ResultsManagerRole', array('ext'=>'taoResults'))
    ),'optimizableClasses' => array(
/*			'http://www.tao.lu/Ontologies/TAOResult.rdf#Result',
			'http://www.tao.lu/Ontologies/TAOResult.rdf#Variable',*/
			'http://www.tao.lu/Ontologies/TAOResult.rdf#ResponseVariable',
			'http://www.tao.lu/Ontologies/TAOResult.rdf#OutcomeVariable',
                        'http://www.tao.lu/Ontologies/TAOResult.rdf#TraceVariable',
                        'http://www.tao.lu/Ontologies/TAOResult.rdf#ItemResult'/*
 * 
 */
	),
	'constants' => array(
	 	# actions directory
	 	"DIR_ACTIONS"			=> $extpath."actions".DIRECTORY_SEPARATOR,
	
		# views directory
		"DIR_VIEWS"				=> $extpath."views".DIRECTORY_SEPARATOR,
	
		# default module name
		'DEFAULT_MODULE_NAME'	=> 'Results',
	
		#default action name
		'DEFAULT_ACTION_NAME'	=> 'index',
	
		#BASE PATH: the root path in the file system (usually the document root)
		'BASE_PATH'				=> $extpath,
	
		#BASE URL (usually the domain root)
		'BASE_URL'				=> ROOT_URL	.'taoResults/',
	
		#BASE WWW the web resources path
		'BASE_WWW'				=> ROOT_URL .'taoResults/views/',

		#the path were the event logs are saved
		'EVENT_LOG_PATH'		=> ROOT_PATH.'taoResults/data',

	 	#TAO extension Paths
		'TAOBASE_WWW'			=> ROOT_URL	.'tao/views/',
	)
);