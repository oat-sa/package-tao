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
 * Service interface to save and retrieve recovery contexts
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package wfEngine
 
 *
 */
class wfEngine_actions_RecoveryContext extends tao_actions_Api {
	
	/**
	 * Retrieve the current context
	 */
	public function retrieve(){
		$session = PHPSession::singleton();
		
		if(!$session->hasAttribute('activityExecutionUri')){
			throw new common_exception_Error('missing activityExecutionUri in Session during RecoveryContext::retrieve()');
		}
			
		$activityExecutionUri = $session->getAttribute('activityExecutionUri');
		$activityExecution = new core_kernel_classes_Resource($activityExecutionUri);
		$recoveryService = wfEngine_models_classes_RecoveryService::singleton();
		$context = $recoveryService->getContext($activityExecution, 'any');//get the first, no null context!
		echo json_encode($context);
	}
	
	/**
	 * Save a context in the current activity execution
	 */
	public function save(){
		$session = PHPSession::singleton();
		if(!$session->hasAttribute('activityExecutionUri')){
			throw new common_exception_Error('missing activityExecutionUri in Session during RecoveryContext::retrieve()');
		}
		if(!$this->hasRequestParameter('context')){
			throw new common_exception_MissingParameter('context');
		}
		
		$saved = false;
		$activityExecutionUri = $session->getAttribute('activityExecutionUri');
		if(!empty($activityExecutionUri)){
			$activityExecution = new core_kernel_classes_Resource($activityExecutionUri);
			$recoveryService = wfEngine_models_classes_RecoveryService::singleton();

			$context = $this->getRequestParameter('context');
			if(is_array($context)){
				if(count($context) > 0){
					$saved = $recoveryService->saveContext($activityExecution, $context);						
				}
			}
			else if (is_null($context) || $context == 'null'){
				//if the data sent are null [set context to null], we remove it  
				$saved = $recoveryService->removeContext($activityExecution);
			}
		}
		echo json_encode(array('saved' => $saved));
	}
	
}
?>