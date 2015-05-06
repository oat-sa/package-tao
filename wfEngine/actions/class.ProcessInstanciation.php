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
 * Copyright (c) 2007-2010 (original work) Public Research Centre Henri Tudor & University of Luxembourg) (under the project TAO-QUAL);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
class wfEngine_actions_ProcessInstanciation extends wfEngine_actions_WfModule{
	
	
	public function authoring($processDefinitionUri){
		
		// This action is not available when running
		// the service mode !

		$processDefinitionUri = urldecode($processDefinitionUri);

		$userViewData 		= wfEngine_helpers_UsersHelper::buildCurrentUserForView();
		$this->setData('userViewData',$userViewData);

		$processAuthoringData 	= array();
		$processAuthoringData['processUri'] 	= $processDefinitionUri;
		$processAuthoringData['processLabel']	= "Process' variables initialization";
		$processAuthoringData['variables']		= array();

		// Process variables retrieving.
		$processDefinitionService = wfEngine_models_classes_ProcessDefinitionService::singleton();
		$variables = $processDefinitionService->getProcessVars(new core_kernel_classes_Resource($processDefinitionUri));

		foreach ($variables as $key => $variable){

			$name 			= $variable['name'];
			$propertyKey	= $key;//urlencode?

			$processAuthoringData['variables'][] = array(
				'name'	=> $name,															
				'key'	=> $propertyKey
			);
		}

		$this->setData('processAuthoringData', $processAuthoringData);
		$this->setView('process_initialization.tpl');
	}
	
	public function initProcessExecution($posted){
		
		//set_time_limit(200);
	    helpers_TimeOutHelper::setTimeOutLimit(helpers_TimeOutHelper::LONG);
	    
		$processExecutionService = wfEngine_models_classes_ProcessExecutionService::singleton();
		$activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();
		
		$processDefinitionUri = urldecode($posted['executionOf']);
		$processDefinition = new core_kernel_classes_Resource($processDefinitionUri);

		$processExecName = $posted["variables"][RDFS_LABEL];
		$processExecComment = 'Created in Processes server on ' . date(DATE_ISO8601);
		$processVariables = $posted["variables"];

		$newProcessExecution = $processExecutionService->createProcessExecution($processDefinition, $processExecName, $processExecComment, $processVariables);
		
		//create nonce to initial activity executions:
		foreach($processExecutionService->getCurrentActivityExecutions($newProcessExecution) as $initialActivityExecution){
			$activityExecutionService->createNonce($initialActivityExecution);
		}
		helpers_TimeOutHelper::reset();
		$param = array('processUri' => urlencode($newProcessExecution->getUri()));
		$this->redirect(tao_helpers_Uri::url('index', 'ProcessBrowser', null, $param));

	}
}
?>