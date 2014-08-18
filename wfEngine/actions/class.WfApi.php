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
class wfEngine_actions_WfApi extends tao_actions_Api {
    
	protected $processExecutionService = null;
	protected $activityExecutionService = null;
	protected $processExecution = null;
	protected $activityExecution = null;
	
	public function __construct()
	{
		parent::__construct();
		$this->processExecutionService = wfEngine_models_classes_ProcessExecutionService::singleton();
		$this->activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();
		
		//validate ALL posted values:
		$processExecutionUri = urldecode($this->getRequestParameter('processExecutionUri'));
		if(!empty($processExecutionUri) && common_Utils::isUri($processExecutionUri)){
			$this->processExecution = new core_kernel_classes_Resource($processExecutionUri);
		}
		
		$activityExecutionUri = urldecode($this->getRequestParameter('activityExecutionUri'));
		if(!empty($activityExecutionUri) && common_Utils::isUri($activityExecutionUri)){
			$this->activityExecution = new core_kernel_classes_Resource($activityExecutionUri);
		}
		
		$this->setSuccess(false);
	}
	
	public function __destruct()
	{
		echo json_encode($this->output);	
	}
			

	public function setData($key, $value) {
		
//		parent::setData($key, $value);//no view needed for WfApi actions
		if(!isset($this->output['data'])){
			$this->output['data'] = array();
		}
		$this->output['data'][$key] = $value;
		
	}
	

	protected function setSuccess($success){

		$this->output['success'] = (bool) $success;
		
	}
	
	protected function setErrorMessage($message, $code = 0)
	{
		if(!isset($this->output['error'])){
			$this->output['error'] = array();
		}
		$this->output['error'][] = $message;
	}
	
	public function getCurrentActivityExecution()
	{
		$returnValue = null;
		
		if(!is_null($this->processExecution)){
			
			$currentActivityExecutions = $this->processExecutionService->getCurrentActivityExecutions($this->processExecution);
			if(is_null($this->activityExecution)){
				if(count($currentActivityExecutions) == 1){
					$returnValue = reset($currentActivityExecutions);
				}else{
					$this->setErrorMessage('There are more than one current activity executions');
				}
			}else{
				if(!isset($currentActivityExecutions[$this->activityExecution->getUri()])){
					$returnValue = $this->activityExecution;
				}else{
					$this->setErrorMessage('The current activity execution is not among the current ones of the process execution');
				}
			}
		}
		
		return $returnValue;
	}
	
}