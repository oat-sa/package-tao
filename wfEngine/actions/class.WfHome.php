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
class wfEngine_actions_WfHome extends wfEngine_actions_WfModule
{

	/**
	 * 
	 * Main page of wfEngine containning 2 sections : 
	 *  - Processes Execution in progress or just started
	 *  - Processes Definition user may instanciate
	 * 
	 * @return void
	 */	
	public function index()
	{
		
		//init required services
		$activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();
		$processExecutionService = wfEngine_models_classes_ProcessExecutionService::singleton();
		$processDefinitionService = wfEngine_models_classes_ProcessDefinitionService::singleton();
		$userService = wfEngine_models_classes_UserService::singleton();
		
		//get current user:
		$currentUser = $userService->getCurrentUser();
                
		//init variable that save data to be used in the view
		$processViewData 	= array();
		
		$userViewData = wfEngine_helpers_UsersHelper::buildCurrentUserForView();
		$this->setData('userViewData', $userViewData);
		
		//list of available process executions:
		$processInstancesClass = new core_kernel_classes_Class(CLASS_PROCESSINSTANCES);
		$processExecutions = $processInstancesClass->getInstances();
		
		foreach ($processExecutions as $processExecution){
			
			if(!is_null($processExecution) && $processExecution instanceof core_kernel_classes_Resource){
				
				try{
					$processDefinition = $processExecutionService->getExecutionOf($processExecution);
				}catch(wfEngine_models_classes_ProcessExecutionException $e){
					$processDefinition = null;
					$processExecutionService->deleteProcessExecution($processExecution);
					continue;
				}
				$processStatus = $processExecutionService->getStatus($processExecution);
				if(is_null($processStatus) || !$processStatus instanceof core_kernel_classes_Resource){
					continue;
				}
					
				$currentActivities = array();
				// Bypass ACL Check if possible...
				if ($processStatus->getUri() == INSTANCE_PROCESSSTATUS_FINISHED) {
					$processViewData[] = array(
						'type' 			=> $processDefinition->getLabel(),
						'label' 		=> $processExecution->getLabel(),
						'uri' 			=> $processExecution->getUri(),
						'activities'	=> array(array('label' => '', 'uri' => '', 'may_participate' => false, 'finished' => true, 'allowed'=> true)),
						'status'		=> $processStatus
					);
					continue;

				}else{

					$currentActivityExecutions = $processExecutionService->getCurrentActivityExecutions($processExecution);
					
					foreach ($currentActivityExecutions as $uri => $currentActivityExecution){

						$isAllowed = $activityExecutionService->checkAcl($currentActivityExecution, $currentUser, $processExecution);
						
						$activityExecFinishedByUser = false;
						$assignedUser = $activityExecutionService->getActivityExecutionUser($currentActivityExecution);
						if(!is_null($assignedUser) && $assignedUser->getUri() == $currentUser->getUri()){
							$activityExecFinishedByUser = $activityExecutionService->isFinished($currentActivityExecution);
						}
						
						$currentActivity = $activityExecutionService->getExecutionOf($currentActivityExecution);
						
						$currentActivities[] = array(
							'label'				=> $currentActivity->getLabel(),
							'uri' 				=> $uri,
							'may_participate'	=> ($processStatus->getUri() != INSTANCE_PROCESSSTATUS_FINISHED && $isAllowed),
							'finished'			=> ($processStatus->getUri() == INSTANCE_PROCESSSTATUS_FINISHED),
							'allowed'			=> $isAllowed,
							'activityEnded'		=> $activityExecFinishedByUser
						);
					}

					$processViewData[] = array(
						'type' 			=> $processDefinition->getLabel(),
						'label' 		=> $processExecution->getLabel(),
						'uri' 			=> $processExecution->getUri(),
						'activities'	=> $currentActivities,
						'status'		=> $processStatus
					);
					
				}
			}
		}
		
		//list of available process definitions:
		$processDefinitionClass = new core_kernel_classes_Class(CLASS_PROCESS);
		$availableProcessDefinitions = $processDefinitionClass->getInstances();

		//filter process that can be initialized by the current user (2nd check...)
		$authorizedProcessDefinitions = array();
		foreach($availableProcessDefinitions as $processDefinition){
			if($processDefinitionService->checkAcl($processDefinition, $currentUser)){
				$authorizedProcessDefinitions[] = $processDefinition;
			}
		}
		
		$this->setData('availableProcessDefinition', $authorizedProcessDefinitions);
		$this->setData('processViewData', $processViewData);
		$this->setView('main.tpl');
	}

}
?>