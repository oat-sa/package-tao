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
 * WFEngine API
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package wfEngine
 
 *
 */
class wfEngine_actions_WfApiActivityExecution extends wfEngine_actions_WfApi {
    
	public function __construct()
	{
		parent::__construct();
		if(!$this->activityExecution->hasType(new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION))){
			$this->activityExecution = null;
			$this->setErrorMessage(__('The resource is not an activity execution'));
		}
		
		$this->activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();
	}
	
	public function assign()
	{
		if(!is_null($this->activityExecution)){
			$userUri = urldecode($this->getRequestParameter('userUri'));
			if (!empty($userUri) && common_Utils::isUri($userUri)) {
				$user = new core_kernel_classes_Resource($userUri);
				$this->setSuccess($this->activityExecutionService->setActivityExecutionUser($this->activityExecution, $user, true));
			}else{
				$this->setErrorMessage('no user given');
			}
		}
	}
	
	public function next()
	{
		if(!is_null($this->activityExecution)){
			if(is_null($this->processExecution)){
				$this->processExecution = $this->activityExecutionService->getRelatedProcessExecution($this->activityExecution);
			}
			$currentActivityExecutions = $this->processExecutionService->performTransition($this->processExecution, $this->activityExecution);
			if($currentActivityExecutions!==false){
				$this->setSuccess(true);
				$this->setData('currentActivityExecutions', $currentActivityExecutions);
			}
		}
	}
    
	public function previous()
	{
		if(!is_null($this->activityExecution)){
			if(is_null($this->processExecution)){
				$this->processExecution = $this->activityExecutionService->getRelatedProcessExecution($this->activityExecution);
			}
			$currentActivityExecutions = $this->processExecutionService->performBackwardTransition($this->processExecution, $this->activityExecution);
			if($currentActivityExecutions!==false){
				$this->setSuccess(true);
				$this->setData('currentActivityExecutions', $currentActivityExecutions);
			}
		}
	}
	
}
