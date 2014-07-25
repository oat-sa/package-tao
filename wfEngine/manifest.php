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
 * Copyright (c) 2007-2010 (original work) Public Research Centre Henri Tudor & University of Luxembourg) (under the project TAO-QUAL);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
$extpath = dirname(__FILE__).DIRECTORY_SEPARATOR;
$taopath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'tao'.DIRECTORY_SEPARATOR;

return array(
    'name' => 'wfEngine',
    'label' => 'Workflow engine',
	'description' => 'Workflow Engine extension',
    'license' => 'GPL-2.0',
	'version' => '2.6',
	'author' => 'Open Assessment Technologies, CRP Henri Tudor',
	'requires' => array(
	    'tao' => '>=2.4'
    ),
	'models' => array(
		'http://www.tao.lu/middleware/wfEngine.rdf'
	),
	'install' => array(
		'rdf' => array(
			dirname(__FILE__). '/models/ontology/wfengine.rdf',
		    dirname(__FILE__). '/models/ontology/wfRunner.rdf',
		),
		'php' => array(
			//dirname(__FILE__). '/scripts/importSas.php',
		)
	),
	'managementRole' => 'http://www.tao.lu/middleware/wfEngine.rdf#WorkflowsManagerRole',
    'acl' => array(
        array('grant', 'http://www.tao.lu/middleware/wfEngine.rdf#WorkflowsManagerRole', array('ext'=>'wfEngine')),
        array('grant', 'http://www.tao.lu/Ontologies/generis.rdf#AnonymousRole', array('ext'=>'wfEngine', 'mod' => 'Authentication')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowRole', array('ext'=>'wfEngine', 'mod' => 'WfHome')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowRole', array('ext'=>'wfEngine', 'mod' => 'ProcessBrowser')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowRole', array('ext'=>'wfEngine', 'mod' => 'ProcessInstanciation')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowRole', array('ext'=>'taoItems', 'mod' => 'SaSItems')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowRole', array('ext'=>'taoSubjects', 'mod' => 'SaSSubjects')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowRole', array('ext'=>'taoSubjects', 'mod' => 'SasSubjectsImport')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowRole', array('ext'=>'taoTests', 'mod' => 'SaSTests')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowRole', array('ext'=>'taoGroups', 'mod' => 'SaSGroups')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowRole', array('ext'=>'taoDelivery', 'mod' => 'SaSResultServer')),
        array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowRole', array('ext'=>'taoDelivery', 'mod' => 'SaSDelivery')),
	    array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowRole', array('ext'=>'tao', 'mod' => 'WebService')),
	    array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowRole', array('ext'=>'taoResults', 'mod' => 'SaSResults')),
	    array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowRole', array('ext'=>'tao', 'mod' => 'SaSUsers')),
	    array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowRole', array('ext'=>'tao', 'mod' => 'File')),
	    array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowRole', array('ext'=>'wfEngine', 'mod' => 'WfApiProcessExecution')),
	    array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowRole', array('ext'=>'wfEngine', 'mod' => 'WfApiActivityExecution')),
	    array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowRole', array('ext'=>'wfEngine', 'mod' => 'WfApiProcessDefinition')),
	    array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowRole', array('ext'=>'wfEngine', 'mod' => 'WfApiProcessDefinition')),
	    array('grant', 'http://www.tao.lu/Ontologies/TAO.rdf#WorkflowRole', array('ext'=>'wfEngine', 'mod' => 'RecoveryContext'))
	),
	'optimizableClasses' => array(
		'http://www.tao.lu/middleware/wfEngine.rdf#ClassProcessDefinitionResources',
		'http://www.tao.lu/middleware/wfEngine.rdf#ClassProcessExecutionResources',
		'http://www.tao.lu/middleware/wfEngine.rdf#ClassServicesResources',
		'http://www.tao.lu/middleware/wfEngine.rdf#ClassActivityExecutions',
		'http://www.tao.lu/middleware/wfEngine.rdf#ClassAccessControlModes'
	),
	'optimizableProperties' => array(
		'http://www.tao.lu/middleware/wfEngine.rdf#PropertyActualParametersFormalParameter',
		'http://www.tao.lu/middleware/wfEngine.rdf#PropertySupportServicesUrl',
		'http://www.tao.lu/middleware/wfEngine.rdf#PropertyConnectorsType',
		'http://www.tao.lu/middleware/wfEngine.rdf#PropertyConnectorsActivityReference',
		'http://www.tao.lu/middleware/wfEngine.rdf#PropertyProcessInstancesStatus',
		'http://www.tao.lu/middleware/wfEngine.rdf#PropertyProcessInstancesExecutionOf',
		'http://www.tao.lu/middleware/wfEngine.rdf#PropertyActivityExecutionsStatus',
		'http://www.tao.lu/middleware/wfEngine.rdf#PropertyActivityExecutionsExecutionOf',
		'http://www.tao.lu/middleware/wfEngine.rdf#PropertyActivityExecutionsCurrentUser',
		'http://www.tao.lu/middleware/wfEngine.rdf#PropertyActivityExecutionsProcessExecution'
	),
	'constants' => array(
		# actions directory
		"DIR_ACTIONS"			=> $extpath."actions".DIRECTORY_SEPARATOR,
	
		# views directory
		"DIR_VIEWS"				=> $extpath."views".DIRECTORY_SEPARATOR,
	
		# default module name
		'DEFAULT_MODULE_NAME'	=> 'Authentication',
	
		#default action name
		'DEFAULT_ACTION_NAME'	=> 'index',
	
		#BASE PATH: the root path in the file system (usually the document root)
		'BASE_PATH'				=> $extpath ,
	
		#BASE URL (usually the domain root)
		'BASE_URL'				=> ROOT_URL	.'wfEngine/',
	
		#BASE WWW the web resources path
		'BASE_WWW'				=> ROOT_URL	.'wfEngine/views/',
			
	 	#TAO extension Paths
		'TAOBASE_WWW'			=> ROOT_URL	.'tao/views/',
	)
);