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
class wfEngine_actions_WfApiProcessExecution extends wfEngine_actions_WfApi {
    
	
	public function __construct()
	{
		
		parent::__construct();
		
		if(!$this->processExecution->hasType(new core_kernel_classes_Class(CLASS_PROCESSINSTANCES))){
			$this->processExecution = null;
			$this->setErrorMessage(__('The resource is not a process execution'));
		}
	}
	
	public function delete()
	{
		if(!is_null($this->processExecution)){
			$this->setSuccess($this->processExecutionService->deleteProcessExecution($this->processExecution, true));
		}
		
	}
	
	public function pause()
	{
		if(!is_null($this->processExecution)){
			$this->setSuccess($this->processExecutionService->pause($this->processExecution));
		}
	}
	
	public function resume()
	{
		if(!is_null($this->processExecution)){
			$this->setSuccess($this->processExecutionService->resume($this->processExecution));
		}
	}
	
	public function cancel()
	{
		if(!is_null($this->processExecution)){
			$this->setSuccess($this->processExecutionService->finish($this->processExecution));
		}
	}
	
	public function next()
	{
		if(!is_null($this->processExecution)){
			$activityExecution = $this->getCurrentActivityExecution();
			if(!is_null($activityExecution)){
				$currentActivityExecutions = $this->processExecutionService->performTransition($this->processExecution, $activityExecution);
				if(!empty($currentActivityExecutions)){
					$this->setSuccess(true);
					$this->setData('currentActivityExecutions', $currentActivityExecutions);
				}
				
			}
		}
	}
    
	public function previous()
	{
		if(!is_null($this->processExecution)){
			$activityExecution = $this->getCurrentActivityExecution();
			if(!is_null($activityExecution)){
				$currentActivityExecutions = $this->processExecutionService->performBackwardTransition($this->processExecution, $activityExecution);
				if(!empty($currentActivityExecutions)){
					$this->setSuccess(true);
					$this->setData('currentActivityExecutions', $currentActivityExecutions);
				}
			}
		}
	}
	
}