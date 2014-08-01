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

/**
 * ProcessBrowser Controller provide actions that navigate along a process
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package wfEngine
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

error_reporting(E_ALL);

class wfEngine_actions_ProcessBrowser extends wfEngine_actions_WfModule{
	
	protected $processExecution = null;
	protected $activityExecution = null;
	protected $processExecutionService = null;
	protected $activityExecutionService = null;
	protected $activityExecutionNonce = false;
	protected $autoRedirecting = false;
	
	public function __construct(){
		
		parent::__construct();
		
		$this->processExecutionService = wfEngine_models_classes_ProcessExecutionService::singleton();
		$this->activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();
		
		//validate ALL posted values:
		$processExecutionUri = urldecode($this->getRequestParameter('processUri'));
		if(!empty($processExecutionUri) && common_Utils::isUri($processExecutionUri)){
			$processExecution = new core_kernel_classes_Resource($processExecutionUri);
			//check that the process execution is not finished or closed here:
			if($this->processExecutionService->isFinished($processExecution)){

			    common_Logger::w('Cannot browse a finished process execution');
				$this->redirectToMain();
				
			}else{
			    
				$this->processExecution = $processExecution;
				
				$activityExecutionUri = urldecode($this->getRequestParameter('activityUri'));
				
				if(!empty($activityExecutionUri) && common_Utils::isUri($activityExecutionUri)){
					
					$activityExecution = new core_kernel_classes_Resource($activityExecutionUri);
					$currentActivityExecutions = $this->processExecutionService->getCurrentActivityExecutions($this->processExecution);
					
					//check if it is a current activity exec:
					if(array_key_exists($activityExecutionUri, $currentActivityExecutions)){
						
						$this->activityExecution = $activityExecution;

						//if ok, check the nonce:
						$nc = $this->getRequestParameter('nc');
						if($this->activityExecutionService->checkNonce($this->activityExecution, $nc)){
							$this->activityExecutionNonce = true;
						}else{
							$this->activityExecutionNonce = false;
						}
						
					}else{
					    //the provided activity execution is no longer the current one (link may be outdated).
						//the user is redirected to the current activity execution if allowed, 
						//or redirected to "main" if there are more than one allowed current activity execution or none
                        common_Logger::w('The provided activity execution is no longer the current one');
					    $this->autoRedirecting = true;
					}
				}
			}
		}
		
	}
	
	protected function autoredirectToIndex(){
		
		if(is_null($this->processExecution)){
			$this->redirectToMain();
			return;
		}
		//user data for browser view
		$userViewData = wfEngine_helpers_UsersHelper::buildCurrentUserForView(); 
		$this->setData('userViewData', $userViewData);
		$this->setData('processExecutionUri', urlencode($this->processExecution->getUri()));
		
		$this->setView('auto_redirecting.tpl');
		
		return;
	}
	
	protected function redirectToIndex(){
		
		if(ENABLE_HTTP_REDIRECT_PROCESS_BROWSER){
			$parameters = array();
			if(!empty($this->activityExecution)){
				$parameters['activityExecutionUri'] = urlencode($this->activityExecution->getUri());
			}
			$this->redirect(tao_helpers_Uri::url('index', 'ProcessBrowser', null, $parameters));
		}else{
			$this->index();
		}
		
	}
	
	protected function redirectToMain(){
		$this->removeSessionAttribute("processUri");
		$this->redirect(tao_helpers_Uri::url('index', 'WfHome'));
	}
	
	public function index(){
		
		if(is_null($this->processExecution)){
			common_Logger::w('ProcessBrowser invoked without processExecution');
			$this->redirectToMain();
			return;
		}
		
		if($this->autoRedirecting){
		    $this->autoredirectToIndex();
			return;
		}
		
		/*
		 * @todo: clean usage
		 * known use of Session::setAttribute("processUri") in:
		 * - taoDelivery_actions_ItemDelivery::runner()
		 * - tao_actions_Api::createAuthEnvironment()
		 */
		$this->setSessionAttribute("processUri", $this->processExecution->getUri());
		
		//user data for browser view
		$userViewData = wfEngine_helpers_UsersHelper::buildCurrentUserForView(); 
		$this->setData('userViewData', $userViewData);
		$browserViewData = array(); // general data for browser view.
		
		//init services:
		$userService = wfEngine_models_classes_UserService::singleton();
		$activityService = wfEngine_models_classes_ActivityService::singleton();
		$interactiveServiceService = wfEngine_models_classes_InteractiveServiceService::singleton();
		
		//get current user:
		$currentUser = $userService->getCurrentUser();
		if(is_null($currentUser)){
			throw new wfEngine_models_classes_ProcessExecutionException("No current user found!");
		}
		
		//get activity execution from currently available process definitions:
		$currentlyAvailableActivityExecutions = $this->processExecutionService->getAvailableCurrentActivityExecutions($this->processExecution, $currentUser, true);
		$activityExecution = null;
		
		if(count($currentlyAvailableActivityExecutions) == 0){
		    common_Logger::w('No available current activity exec found: no permission or issue in process execution');
			$this->pause();
			return;
		}else{
			if(!is_null($this->activityExecution) && $this->activityExecution instanceof core_kernel_classes_Resource){
				foreach($currentlyAvailableActivityExecutions as $availableActivityExec){
					if($availableActivityExec->getUri() == $this->activityExecution->getUri()){
						$activityExecution = $this->processExecutionService->initCurrentActivityExecution($this->processExecution, $this->activityExecution, $currentUser);
						break;
					}
				}
				if(is_null($activityExecution)){
					//invalid choice of activity execution:
					$this->activityExecution = null;
//					$invalidActivity = new core_kernel_classes_Resource($activityUri);
//					throw new wfEngine_models_classes_ProcessExecutionException("invalid choice of activity definition in process browser {$invalidActivity->getLabel()} ({$invalidActivity->getUri()}). \n<br/> The link may be outdated.");
					$this->autoredirectToIndex();
					return;
				}
			}else{
				if(count($currentlyAvailableActivityExecutions) == 1){
					$activityExecution = $this->processExecutionService->initCurrentActivityExecution($this->processExecution, reset($currentlyAvailableActivityExecutions), $currentUser);
					if(is_null($activityExecution)){
						throw new wfEngine_models_classes_ProcessExecutionException('cannot initiate the activity execution of the unique next activity definition');
					}
				}else{
					//count > 1:
					//parallel branch, ask the user to select activity to execute:
					common_Logger::i('Ask the user to select activity');
					$this->pause();
					return;
				}
			}
		}
		
		if(!is_null($activityExecution)){
			
			$this->activityExecution = $activityExecution;
			
			$browserViewData[''] = $this->processExecution->getUri();
			$browserViewData['activityExecutionUri'] = $activityExecution->getUri();
			$this->activityExecutionService->createNonce($this->activityExecution);
			$browserViewData['activityExecutionNonce'] = $this->activityExecutionService->getNonce($activityExecution);
			
			//get interactive services (call of services):
			$activityDefinition = $this->activityExecutionService->getExecutionOf($activityExecution);
			$interactiveServices = $activityService->getInteractiveServices($activityDefinition);
			$services = array();
			foreach($interactiveServices as $interactiveService){
			    
			    $serviceCallModel = tao_models_classes_service_ServiceCall::fromResource($interactiveService);
			    $vars = $serviceCallModel->getRequiredVariables();
			    $parameters = array();
			    foreach ($vars as $variable) {
			        $key = (string)$variable->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_PROCESSVARIABLES_CODE));
			        $value = $activityExecution->getOnePropertyValue(new core_kernel_classes_Property($variable));
			        if ($value instanceof core_kernel_classes_Resource) {
			            $parameters[$key] = $value->getUri();
			        } elseif ($value instanceof core_kernel_classes_Literal) {
			            $parameters[$key] = (string)$value;
			        }
			    }
			    $serviceCallId = $activityExecution->getUri() . (count($interactiveServices) == 1 ? '' : $interactiveService->getUri());
			    $jsServiceApi = tao_helpers_ServiceJavascripts::getServiceApi($serviceCallModel, $serviceCallId, $parameters);
				$services[] = array(
					'style'		=> $interactiveServiceService->getStyle($interactiveService),
				    'api'      => $jsServiceApi
				);
			}
			$this->setData('services', $services);
			
			//set activity control:
			$controls = $activityService->getControls($activityDefinition);
			$browserViewData['controls'] = array(
				'backward' 	=> isset($controls[INSTANCE_CONTROL_BACKWARD])?(bool)$controls[INSTANCE_CONTROL_BACKWARD]:false,
				'forward'	=> isset($controls[INSTANCE_CONTROL_FORWARD])?(bool)$controls[INSTANCE_CONTROL_FORWARD]:false,
			);
		
			// If paused, resume it:
			if ($this->processExecutionService->isFinished($this->processExecution)){
				$this->processExecutionService->resume($this->processExecution);
			}
			
			//get process definition:
			$processDefinition = $this->processExecutionService->getExecutionOf($this->processExecution);
			
			// Browser view main data.
			$browserViewData['processLabel'] 			= $processDefinition->getLabel();
			$browserViewData['processExecutionLabel']	= $this->processExecution->getLabel();
			$browserViewData['activityLabel'] 			= $activityDefinition->getLabel();
			$browserViewData['processUri']				= $this->processExecution->getUri();
			$browserViewData['active_Resource']			="'".$activityDefinition->getUri()."'" ;
			$browserViewData['isInteractiveService'] 	= true;
			$this->setData('browserViewData', $browserViewData);
					
			$this->setData('activity', $activityDefinition);
			
			/* <DEBUG> :populate the debug widget */
			if(DEBUG_MODE){
				
				$this->setData('debugWidget', DEBUG_MODE);
				
				$servicesResources = array();
				foreach($services as $service){
				    $servicesResource = $service;
				    $servicesResource['input'] = $interactiveServiceService->getInputValues($interactiveService, $activityExecution);
				    $servicesResource['output'] = $interactiveServiceService->getOutputValues($interactiveService, $activityExecution);
					$servicesResources[] = $servicesResource;
				}
				$variableService = wfEngine_models_classes_VariableService::singleton();
				$this->setData('debugData', array(
						'Activity' => $activityDefinition,
						'ActivityExecution' => $activityExecution,
						'CurrentActivities' => $currentlyAvailableActivityExecutions,
						'Services' => $servicesResources,
						'VariableStack' => $variableService->getAll()
				));
			}
			/* </DEBUG> */

			$this->setData('activityExecutionUri',		$browserViewData['activityExecutionUri']);
			$this->setData('processUri',				$browserViewData['processUri']);
			$this->setData('activityExecutionNonce',	$browserViewData['activityExecutionNonce']);
                        
                        $this->setData('client_config_url', $this->getClientConfigUrl());
			$this->setView('process_browser.tpl');
		}
	}

	public function back(){
		
		if(is_null($this->processExecution) || is_null($this->activityExecution) || !$this->activityExecutionNonce){
			$this->redirectToIndex();
			return;
		}
		
		$previousActivityExecutions = $this->processExecutionService->performBackwardTransition($this->processExecution, $this->activityExecution);
		
		//reinitiate nonce:
		$this->activityExecutionService->createNonce($this->activityExecution);
		
		if($previousActivityExecutions === false || count($previousActivityExecutions) == 0 ){
			
			if ($this->processExecutionService->isFinished($this->processExecution)) {
				$this->redirectToMain();
			} else if ($this->processExecutionService->isPaused($this->processExecution)) {
				$this->pause();
			}
			
		}else{
			
			//look if the next activity execs are from the same definition:
			if(count($previousActivityExecutions) == 1){
				
				$this->activityExecution = reset($previousActivityExecutions);
				
			}else if(count($previousActivityExecutions) > 1){
				
				//check if it is the executions of a single actiivty or not:
				$activityDefinition = null;
				foreach($previousActivityExecutions as $previousActivityExecution){
					if(is_null($activityDefinition)){
						$activityDefinition = $this->activityExecutionService->getExecutionOf($previousActivityExecution);
					}else{
						if($activityDefinition->getUri() != $this->activityExecutionService->getExecutionOf($previousActivityExecution)->getUri()){
							break;
						}
					}
				}
				$this->activityExecution = reset($previousActivityExecutions);
				
			}
			$this->redirectToIndex();
		}
	}

	public function next(){
		
		if(is_null($this->processExecution) || is_null($this->activityExecution) || !$this->activityExecutionNonce){
			$this->redirectToIndex();
			return;
		}
		
		$nextActivityExecutions = $this->processExecutionService->performTransition($this->processExecution, $this->activityExecution);
		
		//reinitiate nonce:
		$this->activityExecutionService->createNonce($this->activityExecution);
		
		if($nextActivityExecutions === false || count($nextActivityExecutions) == 0 ){
			
			if ($this->processExecutionService->isFinished($this->processExecution)) {
				$this->finish();
			} elseif ($this->processExecutionService->isPaused($this->processExecution)) {
				$this->pause();
			}
			
		}else{
			//look if the next activity execs are from the same definition:
			if(count($nextActivityExecutions) == 1){
				
				$this->activityExecution = reset($nextActivityExecutions);
				
			}else if(count($nextActivityExecutions) > 1){
				
				//check if it is the executions of a single actiivty or not:
				$activityDefinition = null;
				foreach($nextActivityExecutions as $nextActivityExecution){
					if(is_null($activityDefinition)){
						$activityDefinition = $this->activityExecutionService->getExecutionOf($nextActivityExecution);
					}else{
						if($activityDefinition->getUri() != $this->activityExecutionService->getExecutionOf($nextActivityExecution)->getUri()){
							break;
						}
					}
				}
				$this->activityExecution = reset($nextActivityExecutions);
				
			}
			$this->redirectToIndex();
		}
	}
	
	protected function finish(){
        $this->redirectToMain();
    }

	public function callback(){
		
	}

	public function pause(){
		
		if(!is_null($this->processExecution)){
			if(!$this->processExecutionService->isPaused($this->processExecution)){
				$this->processExecutionService->pause($this->processExecution);
				//set the current activity execution to pause too...
			}
		}
		
		$this->redirectToMain();
	}

}
?>