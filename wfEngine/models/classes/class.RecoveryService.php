<?php
/**
 *   
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
 * Short description of class wfEngine_models_classes_RecoveryService
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 
 */
class wfEngine_models_classes_RecoveryService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute contextRecoveryProperty
     *
     * @access protected
     * @var Property
     */
    protected $contextRecoveryProperty = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        
        
    	$this->contextRecoveryProperty = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_CTX_RECOVERY);
    	
        
    }

    /**
     * Short description of method saveContext
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @param  array context
     * @return boolean
     */
    public function saveContext( core_kernel_classes_Resource $activityExecution, $context)
    {
        $returnValue = (bool) false;

        
        
        if(!is_null($activityExecution) && is_array($context)){
        	$returnValue = $activityExecution->editPropertyValues($this->contextRecoveryProperty, serialize($context));
        }
        
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method getContext
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @param  string level
     * @return array
     */
    public function getContext( core_kernel_classes_Resource $activityExecution, $level = '')
    {
        $returnValue = array();

        
        
         if(!is_null($activityExecution)){
			 $theActivityExecution = null;
			 $level = strtolower(trim($level));
			 if($level == ''){
				 $theActivityExecution = $activityExecution;
			 }else{
				 //offer the option to retrieve the context at the nth execution:
				 $activityExecutions = $this->getUserActivityExecutionsStack($activityExecution);
				 
				 $count = count($activityExecutions);
				 if($level == '*' || $level == 'all'){
					 for($i = 0; $i < $count; $i++) {
						 $contextData = (string)$activityExecutions[$i]['resource']->getOnePropertyValue($this->contextRecoveryProperty);
						if(!empty($contextData)){
							$returnValue[] = unserialize($contextData);
						}else{
							$returnValue[] = array();
						}
					 }
					 
					 return (array) $returnValue;
					 
				 }else if($level == 'any'){
					 
					 for($i = $count-1; $i >= 0; $i--) {
						$contextData = (string)$activityExecutions[$i]['resource']->getOnePropertyValue($this->contextRecoveryProperty);
						if(!empty($contextData)){
							$returnValue = unserialize($contextData);
							return (array) $returnValue;
						}
					 }
					 
					 return (array) $returnValue;
				 }else{
					 $level = intval($level);
					 if($level < 0){
						 $level = $count - $level;
					 }
					 
					 if($level >= 0 && isset($activityExecutions[$level])){
						 $theActivityExecution = $activityExecutions[$level]['resource'];
					 }
				 }
			 }
			 
			 
         	$contextData = (string)$theActivityExecution->getOnePropertyValue($this->contextRecoveryProperty);
         	if(!empty($contextData)){
         		$returnValue = unserialize($contextData);
         	}
         }
        
        
        

        return (array) $returnValue;
    }

    /**
     * Short description of method removeContext
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @param  string level
     * @return boolean
     */
    public function removeContext( core_kernel_classes_Resource $activityExecution, $level = '')
    {
        $returnValue = (bool) false;

        
        
    	if(!is_null($activityExecution)){
         	$returnValue = $activityExecution->removePropertyValues($this->contextRecoveryProperty);
         }
        
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method getUserActivityExecutionsStack
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @param  Resource user
     * @return array
     */
    public function getUserActivityExecutionsStack( core_kernel_classes_Resource $activityExecution,  core_kernel_classes_Resource $user = null)
    {
        $returnValue = array();

        
		
		$activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();
		if(is_null($user)){
			$user = $activityExecutionService->getActivityExecutionUser($activityExecution);
		}
		if(!is_null($user)){
			$processExecution = $activityExecutionService->getRelatedProcessExecution($activityExecution);
			$activityDefinition = $activityExecutionService->getExecutionOf($activityExecution);
			$activityExecutionClass = new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION);
			$activityExecutions = $activityExecutionClass->searchInstances(
				array(
					PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION => $processExecution->getUri(),
					PROPERTY_ACTIVITY_EXECUTION_ACTIVITY => $activityDefinition->getUri(),
					PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER => $user->getUri()
				),
				array(
					'like' => false,
					'recursive' => 0
				)
			);
			
			$sortedArray = array();
			foreach($activityExecutions as $activityExec){
				$time = time();
				$sortKey = $time;
				
				//compare them 
				while(isset($sortedArray[$sortKey])){
					$sortKey++;
				}
				
				$sortedArray[$sortKey] = array(
					'resource' => $activityExec,
					'time' => $time
				);
			}
			
			ksort($sortedArray);
			
			foreach($sortedArray as $sortedData){
				$returnValue[] = $sortedData;
			}
		}
		
        

        return (array) $returnValue;
    }

} /* end of class wfEngine_models_classes_RecoveryService */

?>