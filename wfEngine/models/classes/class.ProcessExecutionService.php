<?php
/**  
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
 * Manage the particular executions of a process definition.
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */
class wfEngine_models_classes_ProcessExecutionService
    extends tao_models_classes_GenerisService
        implements tao_models_classes_ServiceCacheInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method setCache
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string methodName
     * @param  array args
     * @param  array value
     * @return boolean
     */
    public function setCache($methodName, $args = array(), $value = array())
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-3a6b44f1:1326d50ba09:-8000:00000000000065CB begin
		if($this->cache){
			
			switch($methodName){
				case __CLASS__.'::getExecutionOf':
				case __CLASS__.'::getStatus':{
					if(isset($args[0]) && $args[0] instanceof core_kernel_classes_Resource){
						$processExecution = $args[0];
						if(!isset($this->instancesCache[$processExecution->getUri()])){
							$this->instancesCache[$processExecution->getUri()] = array();
						}
						$this->instancesCache[$processExecution->getUri()][$methodName] = $value;
						$returnValue = true;
					}
					break;
				}
				case __CLASS__.'::getCurrentActivityExecutions':{
					if(count($args) == 1 && isset($args[0]) && $args[0] instanceof core_kernel_classes_Resource){
						$processExecution = $args[0];
						if(!isset($this->instancesCache[$processExecution->getUri()])){
							$this->instancesCache[$processExecution->getUri()] = array();
						}
						$this->instancesCache[$processExecution->getUri()][$methodName] = $value;
						$returnValue = true;
					}
					break;
				}
			}
		}
        // section 127-0-1-1-3a6b44f1:1326d50ba09:-8000:00000000000065CB end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getCache
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string methodName
     * @param  array args
     * @return mixed
     */
    public function getCache($methodName, $args = array())
    {
        $returnValue = null;

        // section 127-0-1-1-3a6b44f1:1326d50ba09:-8000:00000000000065D0 begin
		if($this->cache){
			switch($methodName){
				case __CLASS__.'::getCurrentActivityExecutions':{
					if(count($args) != 1){
						//only allow the simplest version of the method
						break;
					}
				}
				case __CLASS__.'::getExecutionOf':
				case __CLASS__.'::getStatus':{
					if(isset($args[0]) && $args[0] instanceof core_kernel_classes_Resource){
						$processExecution = $args[0];
						if(isset($this->instancesCache[$processExecution->getUri()])
						&& isset($this->instancesCache[$processExecution->getUri()][$methodName])){

							$returnValue = $this->instancesCache[$processExecution->getUri()][$methodName];

						}
					}
					break;
				}
		  }
		}
		
        // section 127-0-1-1-3a6b44f1:1326d50ba09:-8000:00000000000065D0 end

        return $returnValue;
    }

    /**
     * Short description of method clearCache
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string methodName
     * @param  array args
     * @return boolean
     */
    public function clearCache($methodName = '', $args = array())
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-3a6b44f1:1326d50ba09:-8000:00000000000065D4 begin
		if($this->cache){
			
			if(empty($methodName)){
				$this->instancesCache = array();
				$returnValue = true;
			}

			switch($methodName){
				case __CLASS__.'::getCurrentActivityExecutions': {
					if (count($args) == 1 && isset($args[0]) && $args[0] instanceof core_kernel_classes_Resource) {
						$processExecution = $args[0];
						if(isset($this->instancesCache[$processExecution->getUri()])
						&& $this->instancesCache[$processExecution->getUri()][$methodName]){
							unset($this->instancesCache[$processExecution->getUri()][$methodName]);
							$returnValue = true;
						}
					}else if(count($args) == 2 
						&& isset($args[0]) && $args[0] instanceof core_kernel_classes_Resource
						&& isset($args[1]) && is_array($args[1])){

						$processExecution = $args[0];
						if(isset($this->instancesCache[$processExecution->getUri()])
							&& isset($this->instancesCache[$processExecution->getUri()][$methodName])){

							foreach($args[1] as $activityExecution) {
								if($activityExecution instanceof core_kernel_classes_Resource){
									if(isset($this->instancesCache[$processExecution->getUri()][$methodName][$activityExecution->getUri()])){
										unset($this->instancesCache[$processExecution->getUri()][$methodName][$activityExecution->getUri()]);
									}
								}
							}
							unset($this->instancesCache[$processExecution->getUri()][$methodName]);
							$returnValue = true;
						}
					}
					break;
				}
			}
		}
        // section 127-0-1-1-3a6b44f1:1326d50ba09:-8000:00000000000065D4 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method deleteProcessExecution
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @param  boolean finishedOnly
     * @return boolean
     */
    public function deleteProcessExecution( core_kernel_classes_Resource $processExecution, $finishedOnly = false)
    {
        $returnValue = (bool) false;

        // section 10-50-1-116-185ba8ba:12f4978614f:-8000:0000000000002D5F begin
		if($processExecution->hasType($this->processInstancesClass)){
		
			if($finishedOnly){
				if(!$this->isFinished($processExecution)){
					return $returnValue;
				}
			}
			
			$allActivityExecutions = $processExecution->getPropertyValues($this->processInstancesActivityExecutionsProp);
			$count = count($allActivityExecutions);
			for($i=0;$i<$count;$i++){
				$uri = $allActivityExecutions[$i];
				if(common_Utils::isUri($uri)){
					$activityExecution = new core_kernel_classes_Resource($uri);
					$activityExecution->delete();//no need for the second param to "true" since all the related resources are going to be deleted in this method
				}
			}
			
			$returnValue = $processExecution->delete();
		}
        // section 10-50-1-116-185ba8ba:12f4978614f:-8000:0000000000002D5F end

        return (bool) $returnValue;
    }

    /**
     * Short description of method deleteProcessExecutions
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  array processExecutions
     * @param  boolean finishedOnly
     * @return boolean
     */
    public function deleteProcessExecutions($processExecutions = array(), $finishedOnly = false)
    {
        $returnValue = (bool) false;

        // section 10-50-1-116-185ba8ba:12f4978614f:-8000:0000000000002D68 begin
		if(is_array($processExecutions)){
			if(empty($processExecutions)){
				$activityExecutionClass = new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION);
				//get all instances!
				foreach($this->processInstancesClass->getInstances(false) as $processInstance){
					if($finishedOnly){
						if(!$this->isFinished($processInstance)) {
						    continue;
						}
					}
					$processExecutions[] = $processInstance;
				}
				
				$execToDelete = array();
				
				foreach($processExecutions as $processExecution){ 
						
					$allActivityExecutions = $processExecution->getPropertyValues($this->processInstancesActivityExecutionsProp);
					$count = count($allActivityExecutions);
					for($i = 0 ; $i < $count; $i++){
						$uri = $allActivityExecutions[$i];
						if(common_Utils::isUri($uri)){
							$execToDelete[] = $uri;
						}
					}
				}
				
				$this->processInstancesClass->deleteInstances($processExecutions);
				$activityExecutionClass->deleteInstances($execToDelete);
				
			}
			
			foreach($processExecutions as $processExecution){
				if(!is_null($processExecution) && $processExecution instanceof core_kernel_classes_Resource){
					$returnValue = $this->deleteProcessExecution($processExecution, $finishedOnly);
				}
			}
		}
		else{
			throw new InvalidArgumentException('$processExecutions must be an array.');
		}
        // section 10-50-1-116-185ba8ba:12f4978614f:-8000:0000000000002D68 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isFinished
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @return boolean
     */
    public function isFinished( core_kernel_classes_Resource $processExecution)
    {
        $returnValue = (bool) false;

        // section 10-50-1-116-185ba8ba:12f4978614f:-8000:0000000000002D78 begin
		$returnValue = $this->checkStatus($processExecution, 'finished');
        // section 10-50-1-116-185ba8ba:12f4978614f:-8000:0000000000002D78 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getProcessExecutionsByDefinition
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processDefinition
     * @return array
     */
    public function getProcessExecutionsByDefinition( core_kernel_classes_Resource $processDefinition)
    {
        $returnValue = array();

        // section 127-0-1-1-7c36bc99:13092a153cd:-8000:0000000000003B8C begin
        if(!is_null($processDefinition)){
                $processInstancesClass = new core_kernel_classes_Class(CLASS_PROCESSINSTANCES);
                $returnValue = $processInstancesClass->searchInstances(array(PROPERTY_PROCESSINSTANCES_EXECUTIONOF => $processDefinition->getUri()));
        }
        // section 127-0-1-1-7c36bc99:13092a153cd:-8000:0000000000003B8C end

        return (array) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 127-0-1-1-7c36bc99:13092a153cd:-8000:0000000000003B9A begin
		
        parent::__construct();
		
		$this->instancesCache = array();
		$this->cache = true;
		
		$this->instanceProcessFinished = new core_kernel_classes_Resource(INSTANCE_PROCESSSTATUS_FINISHED);
		$this->instanceProcessResumed = new core_kernel_classes_Resource(INSTANCE_PROCESSSTATUS_RESUMED);
		
        $this->processInstancesClass = new core_kernel_classes_Class(CLASS_PROCESSINSTANCES);
        $this->processInstancesStatusProp = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_STATUS);
		$this->processInstancesExecutionOfProp = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_EXECUTIONOF);
		$this->processInstancesCurrentActivityExecutionsProp = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_CURRENTACTIVITYEXECUTIONS);
		$this->processInstancesActivityExecutionsProp = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_ACTIVITYEXECUTIONS);
		
		$this->processVariablesCodeProp = new core_kernel_classes_Property(PROPERTY_PROCESSVARIABLES_CODE);
		
		$this->activityExecutionsClass = new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION);
		$this->activityExecutionsProcessExecutionProp = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION);
		
		$this->activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();
        // section 127-0-1-1-7c36bc99:13092a153cd:-8000:0000000000003B9A end
    }

    /**
     * Short description of method createProcessExecution
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processDefinition
     * @param  string name
     * @param  string comment
     * @param  array variablesValues
     * @return core_kernel_classes_Resource
     */
    public function createProcessExecution( core_kernel_classes_Resource $processDefinition, $name, $comment = '', $variablesValues = array())
    {
        $returnValue = null;

        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F51 begin
		common_Logger::i('Creating processexecution for '.$processDefinition->getUri());
        
        if(empty($comment)){
			$comment = "create by processExecutionService on ".date("d-m-Y H:i:s");
		}
		$processInstance = $this->processInstancesClass->createInstance($name, $comment);
		$this->setStatus($processInstance, 'started');
		$processInstance->setPropertyValue($this->processInstancesExecutionOfProp, $processDefinition);
		
		$processDefinitionService = wfEngine_models_classes_ProcessDefinitionService::singleton();
		$initialActivities = $processDefinitionService->getRootActivities($processDefinition);
		
		if(!count($initialActivities)){
			
			//manage special case of empty process:
			$this->setStatus($processInstance, 'finished');
			$processInstance->setComment('empty process execution of '.$processDefinition->getLabel());
			$returnValue = $processInstance;
			
		}else{
		
			$activityExecutions = array();
			foreach ($initialActivities as $activity){
				$activityExecution = $this->activityExecutionService->createActivityExecution($activity, $processInstance);
				if(!is_null($activityExecution)){
					$activityExecutions[$activityExecution->getUri()] = $activityExecution;
				}
			}

			//foreach first tokens, assign the user input prop values:
			$codes = array();
			foreach($variablesValues as $uri => $value) {
				// have to skip name because doesnt work like other variables
				if($uri != RDFS_LABEL && common_Utils::isUri($uri)) {

					$property = new core_kernel_classes_Property($uri);

					//assign property values to them:
					foreach($activityExecutions as $activityExecution){
						$activityExecution->setPropertyValue($property, $value);
					}

					//prepare the array of codes to be inserted as the "variables" property of the current token
					$code = $property->getUniquePropertyValue($this->processVariablesCodeProp);
					$codes[] = (string) $code;

				}
			}

			//set serialized codes array into variable property:
			$propActivityExecutionVariables = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_VARIABLES);
			foreach($activityExecutions as $activityExecution){
				$activityExecution->setPropertyValue($propActivityExecutionVariables, serialize($codes)); 
			}

			if($this->setCurrentActivityExecutions($processInstance, $activityExecutions)){
				$returnValue = $processInstance;
			}
			
		}
		
        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F51 end

        return $returnValue;
    }

    /**
     * Short description of method isPaused
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @return boolean
     */
    public function isPaused( core_kernel_classes_Resource $processExecution)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F63 begin
		$returnValue = $this->checkStatus($processExecution, 'paused');
        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F63 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isClosed
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @return boolean
     */
    public function isClosed( core_kernel_classes_Resource $processExecution)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F66 begin
		$returnValue = $this->checkStatus($processExecution, 'closed');
        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F66 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method pause
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @return boolean
     */
    public function pause( core_kernel_classes_Resource $processExecution)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F69 begin
		$returnValue = $this->setStatus($processExecution, 'paused');
        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F69 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method resume
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @return boolean
     */
    public function resume( core_kernel_classes_Resource $processExecution)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F6C begin
		$returnValue = $this->setStatus($processExecution, 'resumed');
        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F6C end

        return (bool) $returnValue;
    }

    /**
     * Short description of method finish
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @return boolean
     */
    public function finish( core_kernel_classes_Resource $processExecution)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F70 begin
		
		$returnValue = $this->setStatus($processExecution, 'finished');
		
        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F70 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method close
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @return boolean
     */
    public function close( core_kernel_classes_Resource $processExecution)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F76 begin
		
		$returnValue = $this->setStatus($processExecution, 'closed');
		
		//delete process execution data: activity executions, tokens and remove all process execution properties but label, comment and status (+serialize the execution path?)
		//implementation...
		//remove the current tokens
		$this->removeCurrentActivityExecutions($processExecution);
		
        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F76 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setStatus
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @param  string status
     * @return boolean
     */
    public function setStatus( core_kernel_classes_Resource $processExecution, $status)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F79 begin

		if (!empty($status)){
			if($status instanceof core_kernel_classes_Resource){
				switch($status->getUri()){
					case INSTANCE_PROCESSSTATUS_RESUMED:
					case INSTANCE_PROCESSSTATUS_STARTED:
					case INSTANCE_PROCESSSTATUS_FINISHED:
					case INSTANCE_PROCESSSTATUS_PAUSED:
					case INSTANCE_PROCESSSTATUS_CLOSED:{
						$returnValue = $processExecution->editPropertyValues($this->processInstancesStatusProp, $status->getUri());
						break;
					}
				}
			}else if(is_string($status)){
				$status = strtolower(trim($status));
				switch($status){
					case 'resumed':{
						$status = new core_kernel_classes_Resource(INSTANCE_PROCESSSTATUS_RESUMED);
						break;
					}
					case 'started':{
						$status = new core_kernel_classes_Resource(INSTANCE_PROCESSSTATUS_STARTED);
						break;
					}
					case 'finished':{
						$status = new core_kernel_classes_Resource(INSTANCE_PROCESSSTATUS_FINISHED);
						break;
					}
					case 'paused':{
						$status = new core_kernel_classes_Resource(INSTANCE_PROCESSSTATUS_PAUSED);
						break;
					}
					case 'closed':{
						$status = new core_kernel_classes_Resource(INSTANCE_PROCESSSTATUS_CLOSED);
						break;
					}
				}
				if($status instanceof core_kernel_classes_Resource){
					$returnValue = $processExecution->editPropertyValues($this->processInstancesStatusProp, $status->getUri());
				}
			}
			
			if($returnValue){
				$this->setCache(__CLASS__.'::getStatus', array($processExecution), $status);
			}
		}
		
        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F79 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getStatus
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @return core_kernel_classes_Resource
     */
    public function getStatus( core_kernel_classes_Resource $processExecution)
    {
        $returnValue = null;

        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F7D begin
		$returnValue = $this->getCache(__METHOD__, array($processExecution));
		if(empty($returnValue)){
			
			$status = $processExecution->getOnePropertyValue($this->processInstancesStatusProp);
			if (!is_null($status)){
				switch($status->getUri()){
					case INSTANCE_PROCESSSTATUS_RESUMED:
					case INSTANCE_PROCESSSTATUS_STARTED:
					case INSTANCE_PROCESSSTATUS_FINISHED:
					case INSTANCE_PROCESSSTATUS_PAUSED:
					case INSTANCE_PROCESSSTATUS_CLOSED:{
						$returnValue = $status;
						break;
					}
				}

			}
			
			$this->setCache(__METHOD__, array($processExecution), $returnValue);
		}
		
        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F7D end

        return $returnValue;
    }

    /**
     * Short description of method checkStatus
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @param  string status
     * @return boolean
     */
    public function checkStatus( core_kernel_classes_Resource $processExecution, $status)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F80 begin
		
		$processStatus = $this->getStatus($processExecution);
		
		if(!is_null($processStatus)){
			if ($status instanceof core_kernel_classes_Resource) {
				if ($processStatus->getUri() == $status->getUri()) {
					$returnValue = true;
				}
			} else if (is_string($status)) {
				
				switch ($processStatus->getUri()){
					case INSTANCE_PROCESSSTATUS_RESUMED: {
						$returnValue = (strtolower($status) == 'resumed');
						break;
					}
					case INSTANCE_PROCESSSTATUS_STARTED: {
						$returnValue = (strtolower($status) == 'started');
						break;
					}
					case INSTANCE_PROCESSSTATUS_FINISHED: {
						$returnValue = (strtolower($status) == 'finished');
						break;
						}
					case INSTANCE_PROCESSSTATUS_PAUSED: {
						$returnValue = (strtolower($status) == 'paused');
						break;
					}
					case INSTANCE_PROCESSSTATUS_CLOSED: {
						$returnValue = (strtolower($status) == 'closed');
						break;
					}
				}
				
			}
		}
		
        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F80 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method performTransition
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @param  Resource activityExecution
     * @return mixed
     */
    public function performTransition( core_kernel_classes_Resource $processExecution,  core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = null;

        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F84 begin
		$session = PHPSession::singleton();
        
		$session->setAttribute("activityExecutionUri", $activityExecution->getUri());
		
		//check if the transition is possible, e.g. process is not finished
		if($this->isFinished($processExecution)){
			return false;
		}
		
		//init the services
		$activityDefinitionService	= wfEngine_models_classes_ActivityService::singleton();
		$connectorService			= wfEngine_models_classes_ConnectorService::singleton();
		$userService 				= wfEngine_models_classes_UserService::singleton();
		$notificationService 		= wfEngine_models_classes_NotificationService::singleton();
		
		$currentUser = $userService->getCurrentUser();
		
		//set the activity execution of the current user as finished:
		if($activityExecution->exists()){
			$this->activityExecutionService->finish($activityExecution);
		}else{
			throw new Exception("cannot find the activity execution of the current activity {$activityBeforeTransition->getUri()} in perform transition");
		}
		
		$activityBeforeTransition = $this->activityExecutionService->getExecutionOf($activityExecution);
		$nextConnector = $activityDefinitionService->getUniqueNextConnector($activityBeforeTransition);
		if (wfEngine_models_classes_ActivityCardinalityService::singleton()->isCardinality($nextConnector)) {
			$nextConnector = wfEngine_models_classes_ActivityCardinalityService::singleton()->getDestination($nextConnector);
		}
		
		$newActivities = array();
		if(!is_null($nextConnector)){
			$newActivities = $this->getNewActivities($processExecution, $activityExecution, $nextConnector);
		}else{
			//final activity:
			$this->finish($processExecution);
			return array();
		}
		
		if($newActivities === false){
			//means that the process must be paused before transition: transition condition not fullfilled
			$this->pause($processExecution);
			return false;
		}
		
		// The actual transition starts here:
		$newActivityExecutions = array();
		
		if(!is_null($nextConnector)){
			
			//trigger the forward transition:
			$newActivityExecutions = $this->activityExecutionService->moveForward($activityExecution, $nextConnector, $newActivities, $processExecution);
			
			//trigger the notifications
			$notificationService->trigger($nextConnector, $activityExecution, $processExecution);
			
		}
		
		//transition done from here: now get the following activities:
		
		//if the connector is not a parallel one, let the user continue in his current branch and prevent the pause:
		$uniqueNextActivityExecution = null;
		if(!is_null($nextConnector)){
			if($connectorService->getType($nextConnector)->getUri() != INSTANCE_TYPEOFCONNECTORS_PARALLEL){
				
				if(count($newActivityExecutions) == 1){
					//TODO: could do a double check here: if($newActivities[0] is one of the activty found in the current tokens):
					
					if($this->activityExecutionService->checkAcl(reset($newActivityExecutions), $currentUser, $processExecution)){
						$uniqueNextActivityExecution = reset($newActivityExecutions);
					}
				}
			}
		}
		
		
		$setPause = true;
		$authorizedActivityExecutions = array();
		
		if (!count($newActivities) || $activityDefinitionService->isFinal($activityBeforeTransition)){
			//there is no following activity so the process ends here:
			$this->finish($processExecution);
			return array();
		}elseif(!is_null($uniqueNextActivityExecution)){
			//we are certain that the next activity would be for the user so return it:
			$authorizedActivityExecutions[$uniqueNextActivityExecution->getUri()] = $uniqueNextActivityExecution;
			$setPause = false;
		}else{
			
			foreach ($newActivityExecutions as $activityExecutionAfterTransition){
				//check if the current user is allowed to execute the activity
				if($this->activityExecutionService->checkAcl($activityExecutionAfterTransition, $currentUser, $processExecution)){
					$authorizedActivityExecutions[$activityExecutionAfterTransition->getUri()] = $activityExecutionAfterTransition;
					$setPause = false;
				}
				else{
					continue;
				}
			}
			
		}
		
		$returnValue = array();
		//finish actions on the authorized acitivty definitions
		foreach($authorizedActivityExecutions as $uri => $activityExecutionAfterTransition){
			
			// Last but not least ... is the next activity a machine activity ?
			// if yes, we perform the transition.
			/*
			 * @todo to be tested
			 */
			$activityAfterTransition = $this->activityExecutionService->getExecutionOf($activityExecutionAfterTransition);
			if ($activityDefinitionService->isHidden($activityAfterTransition)){
				//required to create an activity execution here with:
				
				$currentUser = $userService->getCurrentUser();
				if(is_null($currentUser)){
					throw new wfEngine_models_classes_ProcessExecutionException("No current user found!");
				}
				
				$activityExecutionResource = $this->initCurrentActivityExecution($processExecution, $activityExecutionAfterTransition, $currentUser, true);//force execution of the ghost actiivty
				//service not executed? use curl request?
				if(!is_null($activityExecutionResource)){
					$followingActivityExecutions = $this->performTransition($processExecution, $activityExecutionResource);
					if(is_array($followingActivityExecutions)){
						foreach ($followingActivityExecutions as $followingActivityExec) {
							$returnValue[$followingActivityExec->getUri()] = $followingActivityExec;
						}
					}
				}else{
					throw new wfEngine_models_classes_ProcessExecutionException('the activity execution cannot be created for the hidden activity');
				}
				
			}else{
				$returnValue[$uri] = $activityExecutionAfterTransition;
			}
		}
		
		if($setPause){
			$this->pause($processExecution);
		}else if(!$this->isFinished($processExecution)){
			$this->resume($processExecution);
		}		
		
        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F84 end

        return $returnValue;
    }

    /**
     * Short description of method performBackwardTransition
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @param  Resource activityExecution
     * @param  revertOptions
     * @return mixed
     */
    public function performBackwardTransition( core_kernel_classes_Resource $processExecution,  core_kernel_classes_Resource $activityExecution, $revertOptions = array())
    {
        $returnValue = null;

        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F88 begin
		
		//check if the transition is possible, e.g. process is not finished
		if($this->isFinished($processExecution)){
			return false;
		}
		
		$activityService = wfEngine_models_classes_ActivityService::singleton();
			
		$newActivityExecutions = $this->activityExecutionService->moveBackward($activityExecution, $processExecution, $revertOptions);
		$count = count($newActivityExecutions);
		
		$returnValue = array();
		if(is_array($newActivityExecutions) && $count){
			//see if needs to go back again
			foreach($newActivityExecutions as $newActivityExecution){
				$newActivityDefinition = $this->activityExecutionService->getExecutionOf($newActivityExecution);
				if($activityService->isHidden($newActivityDefinition) && !$activityService->isInitial($newActivityDefinition)){
					$newNewActivityExecutions = $this->performBackwardTransition($processExecution, $newActivityExecution);
					unset($newActivityExecutions[$newActivityExecution->getUri()]);
					foreach($newNewActivityExecutions as $newNewActivityExec){
						$returnValue[$newNewActivityExec->getUri()] = $newNewActivityExec;
					}
				}else{
					$returnValue[$newActivityExecution->getUri()] = $newActivityExecution; 
				}
			}
			
			if($count == 1){
				$this->resume($processExecution);
			}else{
				$this->pause($processExecution);
			}
		}else{
			$returnValue = false;
		}
		
        // section 127-0-1-1-7a69d871:1322a76df3c:-8000:0000000000002F88 end

        return $returnValue;
    }

    /**
     * Short description of method getNewActivities
     *
     * @access protected
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @param  Resource activityExecution
     * @param  Resource currentConnector
     * @return mixed
     */
    protected function getNewActivities( core_kernel_classes_Resource $processExecution,  core_kernel_classes_Resource $activityExecution,  core_kernel_classes_Resource $currentConnector)
    {
        $returnValue = null;

        // section 127-0-1-1--4b38ca35:1323a4c748d:-8000:0000000000002F87 begin
		
		$connectorService = wfEngine_models_classes_ConnectorService::singleton();
		$activityService = wfEngine_models_classes_ActivityService::singleton();
		
		$returnValue = array();
		if(is_null($currentConnector)){
			return $returnValue;//certainly the last activity
		}
		
		$connectorType = $connectorService->getType($currentConnector);
		if(!($connectorType instanceof core_kernel_classes_Resource)){
			throw new common_Exception('Connector type must be a Resource');
		}
		
		switch ($connectorType->getUri()) {
			case INSTANCE_TYPEOFCONNECTORS_CONDITIONAL:{
				
				$returnValue = $this->getConditionalConnectorNewActivities($processExecution, $activityExecution, $currentConnector);
				
				break;
			}
			case INSTANCE_TYPEOFCONNECTORS_PARALLEL:{
				
				$returnValue = $this->getSplitConnectorNewActivities($activityExecution, $currentConnector);
				
				break;
			}
			case INSTANCE_TYPEOFCONNECTORS_JOIN:{
				
				$returnValue = $this->getJoinConnectorNewActivities($processExecution, $activityExecution, $currentConnector);
				
				break;
			}
			case INSTANCE_TYPEOFCONNECTORS_SEQUENCE:
			default:{
				
				//considered as a sequential connector
				$newActivities = $connectorService->getNextActivities($currentConnector);
				if(count($newActivities)){
					foreach ($newActivities as $nextActivity){

						if($activityService->isActivity($nextActivity)){
							$returnValue[]= $nextActivity;
						}else if($connectorService->isConnector($nextActivity)){
							$returnValue = $this->getNewActivities($processExecution, $activityExecution, $nextActivity);
						}

						if(!empty($returnValue)){
							break;//since it is a sequential one, stop at the first valid loop:
						}
					}
				}
				break;
			}
		}
		
        // section 127-0-1-1--4b38ca35:1323a4c748d:-8000:0000000000002F87 end

        return $returnValue;
    }

    /**
     * Short description of method getConditionalConnectorNewActivities
     *
     * @access protected
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @param  Resource activityExecution
     * @param  Resource conditionalConnector
     * @return array
     */
    protected function getConditionalConnectorNewActivities( core_kernel_classes_Resource $processExecution,  core_kernel_classes_Resource $activityExecution,  core_kernel_classes_Resource $conditionalConnector)
    {
        $returnValue = array();

        // section 127-0-1-1--4b38ca35:1323a4c748d:-8000:0000000000002F8B begin
		
		$activityService = wfEngine_models_classes_ActivityService::singleton();
		$connectorService = wfEngine_models_classes_ConnectorService::singleton();
		$transitionRuleService = wfEngine_models_classes_TransitionRuleService::singleton();
		
		$transitionRule = $connectorService->getTransitionRule($conditionalConnector);
		if(is_null($transitionRule)){
			return $returnValue;
		}
		
		$evaluationResult = $transitionRuleService->getExpression($transitionRule)->evaluate(array(VAR_PROCESS_INSTANCE=>$activityExecution->getUri()));
//		var_dump('transition rule '.$transitionRule->getLabel(), $evaluationResult);
		
		if ($evaluationResult){
			// next activities = THEN
			$thenActivity = $transitionRuleService->getThenActivity($transitionRule);
			if(!is_null($thenActivity)){
				if($activityService->isActivity($thenActivity)){
					$thenActivity->getLabel();
					$returnValue[] = $thenActivity;
				}else if($activityService->isConnector($thenActivity)){
					$returnValue = $this->getNewActivities($processExecution, $activityExecution, $thenActivity);
				}
			}else{
				throw new wfEngine_models_classes_ProcessDefinitonException('no "then" activity found for the transition rule '.$transitionRule->getUri());
			}
//			var_dump('then', $returnValue);
		}else{
			// next activities = ELSE
			$elseActivity = $transitionRuleService->getElseActivity($transitionRule);
			if(!is_null($elseActivity)){
				if($activityService->isActivity($elseActivity)){
					$elseActivity->getLabel();
					$returnValue[] = $elseActivity;
				}else{
					$returnValue = $this->getNewActivities($processExecution, $activityExecution, $elseActivity);
				}
			}else{
				throw new wfEngine_models_classes_ProcessDefinitonException('no "else" activity found for the transition rule '.$transitionRule->getUri());
			}
//			var_dump('else', $returnValue);
		}
		
        // section 127-0-1-1--4b38ca35:1323a4c748d:-8000:0000000000002F8B end

        return (array) $returnValue;
    }

    /**
     * Short description of method getJoinConnectorNewActivities
     *
     * @access protected
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @param  Resource activityExecution
     * @param  Resource joinConnector
     * @return mixed
     */
    protected function getJoinConnectorNewActivities( core_kernel_classes_Resource $processExecution,  core_kernel_classes_Resource $activityExecution,  core_kernel_classes_Resource $joinConnector)
    {
        $returnValue = null;

        // section 127-0-1-1--4b38ca35:1323a4c748d:-8000:0000000000002F8F begin
		
		$connectorService = wfEngine_models_classes_ConnectorService::singleton();
		$cardinalityService = wfEngine_models_classes_ActivityCardinalityService::singleton();
		
		$returnValue = false;
		$completed = false;
				
		//count the number of each different activity definition that has to be done parallely:
		$activityResourceArray = array();
		$prevActivites = $connectorService->getPreviousActivities($joinConnector);
		$countPrevActivities = count($prevActivites);
		foreach ($prevActivites as $activityCardinality) {
			if($cardinalityService->isCardinality($activityCardinality)){
				$activity = $cardinalityService->getSource($activityCardinality);
				try{
					$count = $cardinalityService->getCardinality($activityCardinality, $activityExecution);
				}catch(wfEngine_models_classes_ProcessExecutionException $e){
					$count = 0;
				}
				$activityResourceArray[$activity->getUri()] = $count;
			}
		}
		//TODO: implement the case of successive merging: A & B merging to C, D & E merging F and C & F merging to G...

		$debug = array();
		//count finished activity execution by activity definition
		foreach($activityResourceArray as $activityDefinitionUri => $count){
			
			$activityDefinition = new core_kernel_classes_Resource($activityDefinitionUri);
			$debug[$activityDefinitionUri] = array();
			$activityExecutionArray = array();
			
			//get all activity execution for the current activity definition and for the current process execution indepedently from the user (which is not known at the authoring time)
			$activityExecutions = $this->getCurrentActivityExecutions($processExecution, $activityDefinition);
			foreach($activityExecutions as $activityExecutionResource){
				
				if($this->activityExecutionService->isFinished($activityExecutionResource)){
					//a finished activity execution for the process execution
					$activityExecutionArray[] = $activityExecutionResource;
				}else{
					$completed = false;
					break(2); //leave the $completed value as false, no neet to continue
				}
				
			}

			$debug[$activityDefinitionUri]['activityExecutionArray'] = $activityExecutionArray;
			common_Logger::d($activityDefinitionUri.' has cardinality '.$count.' and was executed '.count($activityExecutionArray));
			

			if(count($activityExecutionArray) == $count){
				//ok for this activity definiton, continue to the next loop
				$completed = true;
			}else{
				$completed = false;
				break;
			}
		}
		
		if($completed){
			//get THE (unique) next activity
			$returnValue = $connectorService->getNextActivities($joinConnector);//normally, should be only ONE, so could actually break after the first loop
		}else{
			//pause, do not allow transition so return boolean false
			$returnValue = false;
		}
		
        // section 127-0-1-1--4b38ca35:1323a4c748d:-8000:0000000000002F8F end

        return $returnValue;
    }

    /**
     * Short description of method getExecutionOf
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @return core_kernel_classes_Resource
     */
    public function getExecutionOf( core_kernel_classes_Resource $processExecution)
    {
        $returnValue = null;

        // section 127-0-1-1--42c550f9:1323e0e4fe5:-8000:0000000000002FB6 begin
		
		$returnValue = $this->getCache(__METHOD__, array($processExecution));
		if(empty($returnValue)){
			try{
				$returnValue = $processExecution->getUniquePropertyValue($this->processInstancesExecutionOfProp);
			}catch(common_Exception $e){
				throw new wfEngine_models_classes_ProcessExecutionException('No empty value allowed for the property "execution of"');
			}
			
			if(!empty($returnValue)) {
			    $this->setCache(__METHOD__, array($processExecution), $returnValue);
			}
		}
		
        // section 127-0-1-1--42c550f9:1323e0e4fe5:-8000:0000000000002FB6 end

        return $returnValue;
    }

    /**
     * Short description of method setCurrentActivityExecutions
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @param  array activityExecutions
     * @return boolean
     */
    public function setCurrentActivityExecutions( core_kernel_classes_Resource $processExecution, $activityExecutions)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--6e0edde7:13247ef74e0:-8000:0000000000002FC7 begin
		
		if(!is_null($processExecution)){
            if(!is_array($activityExecutions) && !empty($activityExecutions) && $activityExecutions instanceof core_kernel_classes_Resource){
                $activityExecutions = array($activityExecutions);
            }
			if(is_array($activityExecutions)){
				foreach($activityExecutions as $activityExecution){
					$returnValue = $processExecution->setPropertyValue($this->processInstancesCurrentActivityExecutionsProp, $activityExecution->getUri());
				}
				//associative array mendatory in cache!
				$this->setCache(__CLASS__.'::getCurrentActivityExecutions', array($processExecution), $activityExecutions);
			}
        }
		
        // section 127-0-1-1--6e0edde7:13247ef74e0:-8000:0000000000002FC7 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getCurrentActivityExecutions
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @param  Resource activityDefinition
     * @param  string user
     * @return array
     */
    public function getCurrentActivityExecutions( core_kernel_classes_Resource $processExecution,  core_kernel_classes_Resource $activityDefinition = null, $user = null)
    {
        $returnValue = array();

        // section 127-0-1-1--6e0edde7:13247ef74e0:-8000:0000000000002FCD begin
		
		$allCurrentActivityExecutions = array();
		
		$cachedValues = $this->getCache(__METHOD__,array($processExecution));
		if(!is_null($cachedValues)){
			$allCurrentActivityExecutions = $cachedValues;
		}else{
			$currentActivityExecutions = $processExecution->getPropertyValues($this->processInstancesCurrentActivityExecutionsProp);
			$count = count($currentActivityExecutions);
			for($i=0;$i<$count;$i++){
				$uri = $currentActivityExecutions[$i];
				if(common_Utils::isUri($uri)){
					$allCurrentActivityExecutions[$uri] = new core_kernel_classes_Resource($uri);
				}
			}
			$this->setCache(__METHOD__,array($processExecution), $allCurrentActivityExecutions);
		}
		
		if(is_null($activityDefinition) && is_null($user)){
			
			$returnValue = $allCurrentActivityExecutions;
			
		}else{
			//search by criteria:
			$propertyFilter = array(PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION =>	$processExecution->getUri());
			if(!is_null($activityDefinition)){
				$propertyFilter[PROPERTY_ACTIVITY_EXECUTION_ACTIVITY] = $activityDefinition->getUri();
			}
			if(!is_null($user) && $user instanceof core_kernel_classes_Resource){
				$propertyFilter[PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER] = $user->getUri();
			}
				
			$foundActivityExecutions = $this->activityExecutionsClass->searchInstances($propertyFilter, array('like' => false, 'recursive' => false));
			$returnValue = array_intersect_key($allCurrentActivityExecutions, $foundActivityExecutions);
			
			//special case:check if we want an 'empty-user' activityExecution:
			if(!is_null($activityDefinition) && is_string($user) && empty($user)){
				
				foreach($returnValue as $uri => $currentActivityExecution){
					if(!is_null($this->activityExecutionService->getActivityExecutionUser($currentActivityExecution))){
						unset($returnValue[$uri]);
					}
				}
			}
		}
        
		
        // section 127-0-1-1--6e0edde7:13247ef74e0:-8000:0000000000002FCD end

        return (array) $returnValue;
    }

    /**
     * Create or retrieve the current activity execution of a process execution
     * a given activity definition and a user
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @param  Resource activityExecution
     * @param  Resource user
     * @param  boolean bypassACL
     * @return core_kernel_classes_Resource
     */
    public function initCurrentActivityExecution( core_kernel_classes_Resource $processExecution,  core_kernel_classes_Resource $activityExecution,  core_kernel_classes_Resource $user, $bypassACL = false)
    {
        $returnValue = null;

        // section 127-0-1-1--6e0edde7:13247ef74e0:-8000:0000000000002FD5 begin
		if(!is_null($processExecution) && !is_null($activityExecution) && !is_null($user)){
             
			$assignedUser = $this->activityExecutionService->getActivityExecutionUser($activityExecution);
			
			if(!is_null($assignedUser) && $assignedUser->getUri() == $user->getUri()){
				
				$this->activityExecutionService->setStatus($activityExecution, 'resumed');
				$returnValue = $activityExecution;

			}else if($bypassACL || $this->activityExecutionService->checkAcl($activityExecution, $user, $processExecution)){
				
				//force assignation to the user:
				if ($this->activityExecutionService->setActivityExecutionUser($activityExecution, $user, true)) {
					$this->activityExecutionService->setStatus($activityExecution, 'started');
					$returnValue = $activityExecution;
				}
				
			}
					
			//set in the session the current activity uri
			if(!is_null($returnValue)){
				//set time properties:
				$propStartedTime = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_TIME_STARTED);
				$propLastTime = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_TIME_LASTACCESS);
				
				if(is_null($activityExecution->getOnePropertyValue($propStartedTime))){
					$activityExecution->setPropertyValue($propStartedTime, time());
				}
				$activityExecution->editPropertyValues($propLastTime, time());
				
				$session = PHPSession::singleton();
				$session->setAttribute("activityExecutionUri", $returnValue->getUri());//for variable service only?
			}
			
        }
        // section 127-0-1-1--6e0edde7:13247ef74e0:-8000:0000000000002FD5 end

        return $returnValue;
    }

    /**
     * Short description of method getAvailableCurrentActivityDefinitions
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @param  Resource currentUser
     * @param  boolean checkACL
     * @return array
     */
    public function getAvailableCurrentActivityDefinitions( core_kernel_classes_Resource $processExecution,  core_kernel_classes_Resource $currentUser, $checkACL = false)
    {
        $returnValue = array();

        // section 127-0-1-1--6e0edde7:13247ef74e0:-8000:0000000000002FE5 begin
		
		//old method to return available current definition:
		$currentActivityExecutions = $this->getCurrentActivityExecutions($processExecution);
		$propActivityExecutionCurrentUser = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER);
		foreach($currentActivityExecutions as $currentActivityExecution){
			$ok = false;
			$activityDefinition = null;
			$assignedUser = $currentActivityExecution->getOnePropertyValue($propActivityExecutionCurrentUser);
			if(!is_null($assignedUser)){
				if($assignedUser->getUri() == $currentUser->getUri()){
					$ok = true;
				}
			}else{
				if($checkACL){
					$activityDefinition = $this->activityExecutionService->getExecutionOf($currentActivityExecution);
					if($this->activityExecutionService->checkACL($activityDefinition, $currentUser, $processExecution)){
						$ok = true;
					}
				}else{
					$ok = true;
				}
			}
			
			if($ok){
				if(is_null($activityDefinition)){
					$activityDefinition = $this->activityExecutionService->getExecutionOf($currentActivityExecution);
				}
				$returnValue[$activityDefinition->getUri()] = $activityDefinition;
			}
		}
		
		//suggestion: check ACL on the return values of this method
		
        // section 127-0-1-1--6e0edde7:13247ef74e0:-8000:0000000000002FE5 end

        return (array) $returnValue;
    }

    /**
     * Short description of method removeCurrentActivityExecutions
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @param  array activityExecutions
     * @return boolean
     */
    public function removeCurrentActivityExecutions( core_kernel_classes_Resource $processExecution, $activityExecutions = array())
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--5016dfa1:1324df105c5:-8000:0000000000003022 begin
		
		if(!is_null($processExecution)){
            if(!is_array($activityExecutions) && !empty($activityExecutions) && $activityExecutions instanceof core_kernel_classes_Resource){
                $activityExecutions = array($activityExecutions);
            }
			if(is_array($activityExecutions)){
				if(empty($activityExecutions)){
					$returnValue = $processExecution->removePropertyValues($this->processInstancesCurrentActivityExecutionsProp);
					if($returnValue){
						$this->clearCache(__CLASS__.'::getCurrentActivityExecutions', array($processExecution));
					}
				
				}else{
					$removePattern = array();
					foreach($activityExecutions as $activityExecution){
						$removePattern[] = $activityExecution->getUri();
					}
					
					$returnValue = $processExecution->removePropertyValues($this->processInstancesCurrentActivityExecutionsProp, array(
						'like' => false,
						'pattern' => $removePattern
					));
					
					if($returnValue){
						$this->clearCache(__CLASS__.'::getCurrentActivityExecutions', array($processExecution, $activityExecutions));
					}
				}
				
				if($returnValue){
					$this->clearCache(__CLASS__.'::getCurrentActivityExecutions', array($processExecution, $activityExecutions));
				}
			}
        }
		
        // section 127-0-1-1--5016dfa1:1324df105c5:-8000:0000000000003022 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getAllActivityExecutions
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processInstance
     * @return array
     */
    public function getAllActivityExecutions( core_kernel_classes_Resource $processInstance)
    {
        $returnValue = array();

        // section 127-0-1-1--1e75179b:1325dc5c4e1:-8000:0000000000003012 begin
		
		$previousProperty = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_PREVIOUS);
		$followingProperty = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_FOLLOWING);
		$recoveryService = wfEngine_models_classes_RecoveryService::singleton();
					
		$currentActivityExecutions = $this->getCurrentActivityExecutions($processInstance);
		
		$allActivityExecutions = $processInstance->getPropertyValues($this->processInstancesActivityExecutionsProp);
		$count = count($allActivityExecutions);
		for($i=0;$i<$count;$i++){
			$uri = $allActivityExecutions[$i];
			if(common_Utils::isUri($uri)){
				$activityExecution = new core_kernel_classes_Resource($uri);
				$activityDefinition = $this->activityExecutionService->getExecutionOf($activityExecution);
				$previousArray = array();
				$followingArray = array();

				$previous = $activityExecution->getPropertyValues($previousProperty);
				$countPrevious = count($previous);
				for($j=0; $j<$countPrevious; $j++){
					if(common_Utils::isUri($previous[$j])){
						$prevousActivityExecution = new core_kernel_classes_Resource($previous[$j]);
						$previousArray[] = $prevousActivityExecution->getUri();
					}
				}

				$following = $activityExecution->getPropertyValues($followingProperty);
				$countFollowing = count($following);
				for($k=0; $k<$countFollowing; $k++){
					if(common_Utils::isUri($following[$k])){
						$followingActivityExecution = new core_kernel_classes_Resource($following[$k]);
						$followingArray[] = $followingActivityExecution->getUri();
					}
				}
				$user = $this->activityExecutionService->getActivityExecutionUser($activityExecution);
				$status = $this->activityExecutionService->getStatus($activityExecution);
				$aclMode = $this->activityExecutionService->getAclMode($activityExecution);
				$restrictedRole = $this->activityExecutionService->getRestrictedRole($activityExecution);
				$restrictedUser = $this->activityExecutionService->getRestrictedUser($activityExecution);
				
				$returnValue[$uri] = array(
					'executionOf' => $activityDefinition->getLabel().' ('.$activityDefinition->getUri().')',
					'user' => (is_null($user))?'none':$user->getLabel().' ('.$user->getUri().')',
					'status' => (is_null($status))?'none':$status->getLabel(),
					'createdOn' => date('d-m-Y G:i:s', (string)$activityExecution->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_TIME_CREATED))),
					'current' => array_key_exists($activityExecution->getUri(), $currentActivityExecutions),
					'previous' => $previousArray,
					'following' => $followingArray,
					'context' => $recoveryService->getContext($activityExecution, ''),
					'nonce' => $this->activityExecutionService->getNonce($activityExecution),
					'ACLmode' => (is_null($aclMode))?'none':$aclMode->getLabel(),
					'restrictedRole' => (is_null($restrictedRole))?'none':$restrictedRole->getLabel(),
					'restrictedUser' => (is_null($restrictedUser))?'none':$restrictedUser->getLabel(),
					'variables' => $this->activityExecutionService->getVariables($activityExecution)
				);
			}
		}
		
		ksort($returnValue);
        // section 127-0-1-1--1e75179b:1325dc5c4e1:-8000:0000000000003012 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getSplitConnectorNewActivities
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @param  Resource currentConnector
     * @return array
     */
    public function getSplitConnectorNewActivities( core_kernel_classes_Resource $activityExecution,  core_kernel_classes_Resource $currentConnector)
    {
        $returnValue = array();

        // section 127-0-1-1-6eb1148b:132b4a0f8d0:-8000:000000000000306D begin
		$connectorService = wfEngine_models_classes_ConnectorService::singleton();
		$cardinalityService = wfEngine_models_classes_ActivityCardinalityService::singleton();
		
		foreach($connectorService->getNextActivities($currentConnector) as $cardinality){
			if($cardinalityService->isCardinality($cardinality)){
				$activity = $cardinalityService->getDestination($cardinality);
				if (!is_null($activity)) {
					try{
						$count = $cardinalityService->getCardinality($cardinality, $activityExecution);
					}catch(wfEngine_models_classes_ProcessExecutionException $e){
						$count = 0;//in case the parallel variable is not set
					}
					for ($i = 0; $i < $count; $i++) {
						$returnValue[] = $activity;
					}
				}
			}
			
		}
		
        // section 127-0-1-1-6eb1148b:132b4a0f8d0:-8000:000000000000306D end

        return (array) $returnValue;
    }

    /**
     * Short description of method getAvailableCurrentActivityExecutions
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @param  Resource currentUser
     * @return array
     */
    public function getAvailableCurrentActivityExecutions( core_kernel_classes_Resource $processExecution,  core_kernel_classes_Resource $currentUser)
    {
        $returnValue = array();

        // section 127-0-1-1--1b682bf3:132cdc3fef4:-8000:000000000000307A begin
		
		$currentActivityExecutions = $this->getCurrentActivityExecutions($processExecution);
		$propActivityExecutionCurrentUser = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER);
		foreach($currentActivityExecutions as $currentActivityExecution){
			$ok = false;
			$assignedUser = $this->activityExecutionService->getActivityExecutionUser($currentActivityExecution);
			if(!is_null($assignedUser)){
				$ok = ($assignedUser->getUri() == $currentUser->getUri());
			}else{
				$ok = $this->activityExecutionService->checkACL($currentActivityExecution, $currentUser, $processExecution);
			}
			
			if($ok){
				$returnValue[$currentActivityExecution->getUri()] = $currentActivityExecution;
			}
		}	
        // section 127-0-1-1--1b682bf3:132cdc3fef4:-8000:000000000000307A end

        return (array) $returnValue;
    }

    /**
     * To build the audit trail of a process execution.
     * Return the list of all activity executions, ordered by creation time,
     * with their row data.
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @param  boolean withData
     * @return array
     */
    public function getExecutionHistory( core_kernel_classes_Resource $processExecution, $withData = false)
    {
        $returnValue = array();

        // section 127-0-1-1--32040631:1334998261c:-8000:0000000000003246 begin
		
		$previousProperty = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_PREVIOUS);
		$followingProperty = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_FOLLOWING);
		$recoveryService = wfEngine_models_classes_RecoveryService::singleton();
					
		$currentActivityExecutions = $this->getCurrentActivityExecutions($processExecution);
		
		$creationTime = array();
		$unorderedActivityExecutions = array();
		
		$allActivityExecutions = $processExecution->getPropertyValues($this->processInstancesActivityExecutionsProp);
		$count = count($allActivityExecutions);
		for($i=0;$i<$count;$i++){
			
			$uri = $allActivityExecutions[$i];
			if(common_Utils::isUri($uri)){
				
				$activityExecution = new core_kernel_classes_Resource($uri);
				$createdOn = (string)$activityExecution->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_TIME_CREATED));
				
				if($withData){
					
					$previousArray = array();
					$followingArray = array();

					$previous = $activityExecution->getPropertyValues($previousProperty);
					$countPrevious = count($previous);
					for($j=0; $j<$countPrevious; $j++){
						if(common_Utils::isUri($previous[$j])){
							$prevousActivityExecution = new core_kernel_classes_Resource($previous[$j]);
							$previousArray[] = $prevousActivityExecution->getUri();
						}
					}

					$following = $activityExecution->getPropertyValues($followingProperty);
					$countFollowing = count($following);
					for($k=0; $k<$countFollowing; $k++){
						if(common_Utils::isUri($following[$k])){
							$followingActivityExecution = new core_kernel_classes_Resource($following[$k]);
							$followingArray[] = $followingActivityExecution->getUri();
						}
					}
				
					$unorderedActivityExecutions[$uri] = array(
						'activityExecution' => $activityExecution,
						'executionOf' => $this->activityExecutionService->getExecutionOf($activityExecution),
						'createdOn' => date('d-m-Y G:i:s', $createdOn),
						'current' => array_key_exists($activityExecution->getUri(), $currentActivityExecutions),
						'status' => $this->activityExecutionService->getStatus($activityExecution),

						'ACLmode' => $this->activityExecutionService->getAclMode($activityExecution),
						'restrictedRole' => $this->activityExecutionService->getRestrictedRole($activityExecution),
						'restrictedUser' => $this->activityExecutionService->getRestrictedUser($activityExecution),
						'user' => $this->activityExecutionService->getActivityExecutionUser($activityExecution),

						'previous' => $previousArray,
						'following' => $followingArray,
						'nonce' => $this->activityExecutionService->getNonce($activityExecution),

						'context' => $recoveryService->getContext($activityExecution, ''),
						'variables' => $this->activityExecutionService->getVariables($activityExecution)
					);
					
				}else{
					$unorderedActivityExecutions[$uri] = $activityExecution->getUri();
				}
				
				$creationTime[$uri] = $createdOn;
			}
		}
		
		asort($creationTime);
		foreach($creationTime as $uri => $time){
			$returnValue[] = $unorderedActivityExecutions[$uri];
		}
		
        // section 127-0-1-1--32040631:1334998261c:-8000:0000000000003246 end

        return (array) $returnValue;
    }

    /**
     * Short description of method undoForwardTransition
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @param  Resource activityExecution
     * @return boolean
     */
    public function undoForwardTransition( core_kernel_classes_Resource $processExecution,  core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-6e321c4d:13349bf5055:-8000:000000000000324A begin
		
		$allowed = false;
		
		$currentActivityExecutions = $this->getCurrentActivityExecutions($processExecution);
		$followings = $this->activityExecutionService->getFollowing($activityExecution);
		$restoringActivityExecutions = array();
		$restoringActivityExecutions[$activityExecution->getUri()] = $activityExecution;
		
		if(count($followings)){
			
			foreach($followings as $followingActivityExecution){
				
				if(!in_array($followingActivityExecution->getUri(), array_keys($currentActivityExecutions))) {
					common_Logger::w($followingActivityExecution->getUri().' not in currentActivityExecutions (count '.count($currentActivityExecutions).')');
					$allowed = false;
					break;
				}
				
				//check that no following activity has been taken:
				$user = $this->activityExecutionService->getActivityExecutionUser($followingActivityExecution);
				if(is_null($user)){
					$allowed = true;
					$restoringActivityExecutions = array_merge($restoringActivityExecutions, $this->activityExecutionService->getPrevious($followingActivityExecution));
					continue;
				} else {
					common_Logger::w($followingActivityExecution->getUri().' has been taken by user '.$user->getUri());
					$allowed = false;
					break;
				}
			}
			
		} else {
			common_Logger::w($activityExecution->getUri().' has no following activities, undo impossible');
		}
		
		if ($allowed) {
			//move the current activity pointer:
			//of the branch uniquely:
			if(!empty($followings)){
				$this->removeCurrentActivityExecutions($processExecution, $followings);
				foreach ($followings as $following) {
					$this->activityExecutionService->setStatus($following, 'closed'); //invalidate the path:
				}
			}
			
			//undo the following activity executions assignation to *all* activity exec to be restored:
			foreach($restoringActivityExecutions as $restoringActivityExecution){
				$restoringActivityExecution->removePropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_FOLLOWING));
			}
			
			$this->activityExecutionService->resume($activityExecution);

			$returnValue = $this->setCurrentActivityExecutions($processExecution, $restoringActivityExecutions);
		}
		
        // section 127-0-1-1-6e321c4d:13349bf5055:-8000:000000000000324A end

        return (bool) $returnValue;
    }

} /* end of class wfEngine_models_classes_ProcessExecutionService */

?>