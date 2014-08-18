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
class wfEngine_actions_WfApiVariable extends wfEngine_actions_WfApi {
    
	protected $variableService = null;
	protected $code = '';
	protected $value = '';
	protected $values;
	
	public function __construct()
	{
		parent::__construct();
		$this->values = array();
		$this->variableService = wfEngine_models_classes_VariableService::singleton();
		
		if(!$this->activityExecution->hasType(new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION))){
			$this->activityExecution = null;
			$this->setErrorMessage(__('The resource is not an activity execution'));
		}
		
		$code = urldecode($this->getRequestParameter('code'));
		if(!empty($code)){
			if(is_null($this->variableService->getProcessVariable($code))){
				$this->setErrorMessage(__('The variable with the code '.$code.' does not exists'));
			}else{
				$this->code = $code;
			}
		}else{
			$this->setErrorMessage(__('No variable code given'));
		}
		
		// don't use getRequestParameter since it modifies the params
		if (isset($_REQUEST['value'])) {
			$values = $_REQUEST['value'];
			if(is_array($values)){
				foreach($values as $value){
					$this->values[] = urldecode($value);
				}
			}else{
				$this->values[] = urldecode($values);
			}
		}
	}
	
	public function push()
	{
		if(!is_null($this->activityExecution) && !empty($this->code) && !empty($this->values)){
			foreach($this->values as $value){
				$this->setSuccess($this->variableService->push($this->code, $value, $this->activityExecution));
			}
		}
	}
	
	public function edit()
	{
		if(!is_null($this->activityExecution) && !empty($this->code) && !empty($this->values)){
			$this->setSuccess($this->variableService->edit($this->code, $this->values, $this->activityExecution));
		}
	}
	
	public function get()
	{
		if(!is_null($this->activityExecution) && !empty($this->code)){
			$value = $this->variableService->get($this->code, $this->activityExecution);
			
			if(!empty($value)){
				$this->setSuccess(true);
				$this->setData('values', $value);
			}
		}
	}
	
	public function remove()
	{
		if(!is_null($this->activityExecution) && !empty($this->code)){
			$this->setSuccess($this->variableService->remove($this->code, $this->activityExecution));
		}
	}
	
	public function getAllVariables()
	{
		$variables = $this->variableService->getAllVariables();
		if(!empty($variables)){
			$this->setSuccess(true);
			$this->setData('variables', $variables);
		}
		
	}
}
