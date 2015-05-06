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
class wfEngine_actions_WfApiProcessDefinition extends wfEngine_actions_WfApi {
    
	protected $processDefinitionService = null;
	protected $processDefinition = null;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->processDefinitionService = wfEngine_models_classes_ProcessDefinitionService::singleton();
		
		$processDefinitionUri = urldecode($this->getRequestParameter('processDefinitionUri'));
		if(!empty($processDefinitionUri) && common_Utils::isUri($processDefinitionUri)){
			$process = new core_kernel_classes_Resource($processDefinitionUri);
			if($process->hasType(new core_kernel_classes_Class(CLASS_PROCESS))){
				$this->processDefinition = $process;
			}else{
				$this->setErrorMessage(__('The resource is not a process definition'));
			}
		}
		else{
			$this->setErrorMessage(__('No process definition uri given'));
		}
	}
	
	public function getName()
	{
		if(!is_null($this->processDefinition)){
			$label = $this->processDefinition->getLabel();
			$this->setSuccess(true);
			$this->setData('name', $label);
		}
	}
	
	public function initExecution()
	{
		if(!is_null($this->processDefinition)){

			$postName = urldecode($this->getRequestParameter('name'));
			$name = empty($postName)?__('execution of').' '.$this->processDefinition->getLabel():$postName;

			$postComment = urldecode($this->getRequestParameter('comment'));
			$comment = empty($postComment)?__('created by').' '.__CLASS__.' on '.date('c'):$postComment;
			
			$variables = array();
			$postVariables = urldecode($this->getRequestParameter('variables'));
			if (is_array($postVariables) && !empty($postVariables)) {
				$variables = $postVariables;
			}
			
			$processExecution = $this->processExecutionService->createProcessExecution($this->processDefinition, $name, $comment, $variables);
			if(!is_null($processExecution)){
				$this->setSuccess(true);
				$this->setData('processExecutionUri', $processExecution->getUri());
			}
			else{
				$this->setErrorMessage(__('Cannot create process execution'));
			}
		}
	}
	
}