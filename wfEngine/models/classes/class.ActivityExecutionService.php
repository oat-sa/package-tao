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

/**
 * This service enables you to manage, control, restrict the process activities
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 
 */
class wfEngine_models_classes_ActivityExecutionService
    extends tao_models_classes_GenerisService
        implements tao_models_classes_ServiceCacheInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute processExecutionProperty
     *
     * @access protected
     * @var Property
     */
    protected $processExecutionProperty = null;

    /**
     * Short description of attribute currentUserProperty
     *
     * @access protected
     * @var Property
     */
    protected $currentUserProperty = null;

    /**
     * Short description of attribute ACLModeProperty
     *
     * @access protected
     * @var Property
     */
    protected $ACLModeProperty = null;

    /**
     * Short description of attribute restrictedUserProperty
     *
     * @access protected
     * @var Property
     */
    protected $restrictedUserProperty = null;

    /**
     * Short description of attribute restrictedRoleProperty
     *
     * @access protected
     * @var Property
     */
    protected $restrictedRoleProperty = null;

    /**
     * Short description of attribute activityProperty
     *
     * @access protected
     * @var Property
     */
    protected $activityProperty = null;

    /**
     * Short description of attribute activityService
     *
     * @access protected
     * @var ActivityService
     */
    protected $activityService = null;

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

        
		if($this->cache){
			
			switch($methodName){
				case __CLASS__.'::getExecutionOf':
				case __CLASS__.'::getStatus':
				case __CLASS__.'::getActivityExecutionUser':
				case __CLASS__.'::getNonce':{
					if(isset($args[0]) && $args[0] instanceof core_kernel_classes_Resource){
						$activityExecution = $args[0];
						if(!isset($this->instancesCache[$activityExecution->getUri()])){
							$this->instancesCache[$activityExecution->getUri()] = array();
						}
						$this->instancesCache[$activityExecution->getUri()][$methodName] = $value;
						$returnValue = true;
					}
					break;
				}
				case __CLASS__.'::checkAcl':{
					if(count($args) == 3 
						&& $args[0] instanceof core_kernel_classes_Resource
						&& $args[1] instanceof core_kernel_classes_Resource
						&& $args[2] instanceof core_kernel_classes_Resource){
							$activityExecution = $args[0];
							$currentUser = $args[1];
							$processExecution = $args[2];

							if(!isset($this->instancesCache[$activityExecution->getUri()])){
								$this->instancesCache[$activityExecution->getUri()] = array();
							}
							if(!isset($this->instancesCache[$activityExecution->getUri()][$methodName])){
								$this->instancesCache[$activityExecution->getUri()][$methodName] = array();
							}
							$this->instancesCache[$activityExecution->getUri()][$methodName][$currentUser->getUri()] = (bool) $value;
							$returnValue = true;
					}
					break;
				}
			}
			
		}
        

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

        
		if($this->cache){
			switch($methodName){
				case __CLASS__.'::getExecutionOf':
				case __CLASS__.'::getStatus':
				case __CLASS__.'::getActivityExecutionUser':
				case __CLASS__.'::getNonce':{
					if(isset($args[0]) && $args[0] instanceof core_kernel_classes_Resource){
						$activityExecution = $args[0];
						if(isset($this->instancesCache[$activityExecution->getUri()])
						&& isset($this->instancesCache[$activityExecution->getUri()][$methodName])){

							$returnValue = $this->instancesCache[$activityExecution->getUri()][$methodName];

						}
					}
					break;
				}
				case __CLASS__.'::checkAcl':{
					if(count($args) == 3 
						&& $args[0] instanceof core_kernel_classes_Resource
						&& $args[1] instanceof core_kernel_classes_Resource
						&& $args[2] instanceof core_kernel_classes_Resource){
							$activityExecution = $args[0];
							$currentUser = $args[1];
							$processExecution = $args[2];

							if(!isset($this->instancesCache[$activityExecution->getUri()])){
								break;
							}
							if(!isset($this->instancesCache[$activityExecution->getUri()][$methodName])){
								break;
							}
							if(isset($this->instancesCache[$activityExecution->getUri()][$methodName][$currentUser->getUri()])){
								$returnValue = (bool) $this->instancesCache[$activityExecution->getUri()][$methodName][$currentUser->getUri()];
							}
					}
					break;
				}
			}
			
		}
        

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

        
		if($this->cache){
			
			if(empty($methodName)){
				$this->instancesCache = array();
				$returnValue = true;
			}

			switch($methodName){
				case __CLASS__.'::checkAcl': {
					if(count($args) == 0){
						foreach($this->instancesCache as $activityExecutionUri => $activityExecutionCache){
							if(isset($activityExecutionCache[$methodName])){
								unset($this->instancesCache[$activityExecutionUri][$methodName]);
								$returnValue = true;
							}
						}
					}else if(count($args) == 3 
						&& isset($args[0]) && $args[0] instanceof core_kernel_classes_Resource
						&& isset($args[1]) && $args[1] instanceof core_kernel_classes_Resource
						&& isset($args[2]) && $args[2] instanceof core_kernel_classes_Resource){
						
						$activityExecution = $args[0];
						$currentUser = $args[1];
						$processExecution = $args[2];

						if(!isset($this->instancesCache[$activityExecution->getUri()])){
							break;
						}
						if(!isset($this->instancesCache[$activityExecution->getUri()][$methodName])){
							break;
						}
						if(isset($this->instancesCache[$activityExecution->getUri()][$methodName][$currentUser->getUri()])){
							unset($this->instancesCache[$activityExecution->getUri()][$methodName][$currentUser->getUri()]);
							$returnValue = true;
						}
					}
					break;
				}
			}
		}
        

        return (bool) $returnValue;
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
        
		$this->instancesCache = array();
		$this->cache = true;
		
        $this->activityExecutionClass	= new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION);
		$this->activityExecutionStatusProperty = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_STATUS);
		$this->activityExecutionNonceProperty = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_NONCE);
		$this->activityExecutionPreviousProperty = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_PREVIOUS);
		$this->activityExecutionFollowingProperty = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_FOLLOWING);
		
    	$this->processExecutionProperty = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION);
        $this->currentUserProperty		= new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER);
    	$this->activityProperty			= new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_ACTIVITY);
    	$this->ACLModeProperty			= new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_ACL_MODE);
        $this->restrictedUserProperty	= new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_RESTRICTED_USER);
        $this->restrictedRoleProperty	= new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_RESTRICTED_ROLE);
		
		$this->processInstanceActivityExecutionsProperty = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_ACTIVITYEXECUTIONS); 
		
        $this->activityService          = wfEngine_models_classes_ActivityService::singleton();
        
    }

    /**
     * Get the list of available ACL modes
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return array
     */
    public function getAclModes()
    {
        $returnValue = array();

        
		//deprecated, use activityService instead
        $aclModeClass = new core_kernel_classes_Class(CLASS_ACL_MODES);
        foreach($aclModeClass->getInstances() as $mode){
        	$returnValue[$mode->getUri()] = $mode;
        }
        
        

        return (array) $returnValue;
    }

    /**
     * Define the ACL mode of an activity
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activity
     * @param  Resource mode
     * @param  Resource target
     * @return core_kernel_classes_Resource
     */
    public function setAcl( core_kernel_classes_Resource $activity,  core_kernel_classes_Resource $mode,  core_kernel_classes_Resource $target = null)
    {
        $returnValue = null;

        
        //deprecated, use actiivytService instead:
        //check the kind of resources
        if($this->getClass($activity)->getUri() != CLASS_ACTIVITIES){
        	throw new Exception("Activity must be an instance of the class Activities");
        }
        if(!in_array($mode->getUri(), array_keys($this->getAclModes()))){
        	throw new Exception("Unknow acl mode");
        }
        
        //set the ACL mode
        $properties = array(
        	PROPERTY_ACTIVITIES_ACL_MODE	=> $mode->getUri()
        );
        
        switch($mode->getUri()){
        	case INSTANCE_ACL_ROLE:
        	case INSTANCE_ACL_ROLE_RESTRICTED_USER:
        	case INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED:{
        		if(is_null($target)){
        			throw new Exception("Target must reference a role resource");
        		}
        		$properties[PROPERTY_ACTIVITIES_RESTRICTED_ROLE] = $target->getUri();
        		break;
        	}	
        	case INSTANCE_ACL_USER:{
        		if(is_null($target)){
        			throw new Exception("Target must reference a user resource");
        		}
        		$properties[PROPERTY_ACTIVITIES_RESTRICTED_USER] = $target->getUri();
        		break;
			}	
        }
        
        //bind the mode and the target (user or role) to the activity
        $returnValue = $this->bindProperties($activity, $properties);
        
        

        return $returnValue;
    }

    /**
     * get the execution of this activity for the user
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activity
     * @param  Resource currentUser
     * @param  Resource processExecution
     * @return core_kernel_classes_Resource
     */
    public function getExecution( core_kernel_classes_Resource $activity,  core_kernel_classes_Resource $currentUser,  core_kernel_classes_Resource $processExecution)
    {
        $returnValue = null;

        
        
        if(!is_null($activity) && !is_null($currentUser) && !is_null($processExecution)){
        	
        	$filters = array(
        		PROPERTY_ACTIVITY_EXECUTION_ACTIVITY 			=> $activity->getUri(),
        		$this->currentUserProperty->getUri()			=> $currentUser->getUri(),
        		$this->processExecutionProperty->getUri()	=> $processExecution->getUri()
        	);
        	$clazz = new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION);
        	$options = array('recursive'	=> 0, 'like' => false);
			
			foreach($clazz->searchInstances($filters, $options) as $activityExecution){
				$returnValue = $activityExecution;
				break;
			}
        }
        

        return $returnValue;
    }

    /**
     * get the executions of this activity
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activity
     * @param  Resource processExecution
     * @return array
     */
    public function getExecutions( core_kernel_classes_Resource $activity,  core_kernel_classes_Resource $processExecution)
    {
        $returnValue = array();

        
        
		if(!is_null($activity) &&  !is_null($processExecution)){
          	
        	$filters = array(
        		PROPERTY_ACTIVITY_EXECUTION_ACTIVITY 			=> $activity->getUri(),
        		$this->processExecutionProperty->getUri()	=> $processExecution->getUri()
        	);
        	$clazz = new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION);
        	$options = array('recursive'	=> 0, 'like' => false);
			
			foreach($clazz->searchInstances($filters, $options) as $activityExecution){
				$returnValue[$activityExecution->getUri()] = $activityExecution;
			}
        }
        
        

        return (array) $returnValue;
    }

    /**
     * Check the ACL of a user for a given activity.
     * It returns false if the user cannot access the activity.
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @param  Resource currentUser
     * @param  Resource processExecution
     * @return boolean
     */
    public function checkAcl( core_kernel_classes_Resource $activityExecution,  core_kernel_classes_Resource $currentUser,  core_kernel_classes_Resource $processExecution = null)
    {
        $returnValue = (bool) false;

        

        if(!is_null($activityExecution) && !is_null($currentUser)){
        	
			if(is_null($processExecution)){
				$processExecution = $this->getRelatedProcessExecution($activityExecution);
			}
			
			//get cached value:
			$cachedValue = $this->getCache(__METHOD__, array($activityExecution, $currentUser, $processExecution));
			if(!is_null($cachedValue) && is_bool($cachedValue)){
//				var_dump('ACL results from cache '.$activityExecution->getUri().' '.$currentUser->getLabel());
				$returnValue = $cachedValue;
				return $returnValue;
			}
			
        	//activity and current must be set to the activty execution otherwise a common Exception is thrown
        	$modeUri = $activityExecution->getOnePropertyValue($this->ACLModeProperty);
        	
        	if(is_null($modeUri)){
				
        		$returnValue = true;	//if no mode defined, the activity is allowed
				
        	}else{
				
        		switch($modeUri->getUri()){
        			
        			//check if th current user is the restricted user
        			case INSTANCE_ACL_USER:{
        				
        				$activityUser = $this->getRestrictedUser($activityExecution);
        				if(!is_null($activityUser)){
	        				if($activityUser->getUri() == $currentUser->getUri()) {
	        					$returnValue = true;
	        				}
        				}
        				break;
        			}
        			//check if the current user has the restricted role
        			case INSTANCE_ACL_ROLE:{
        				
        				$userService 		= tao_models_classes_UserService::singleton();
        				$activityRole 		= $this->getRestrictedRole($activityExecution);
        				$returnValue		= $userService->userHasRoles($currentUser, $activityRole);
        				break;	
        			}
        			//check if the current user has the restricted role and is the restricted user
        			case INSTANCE_ACL_ROLE_RESTRICTED_USER:{
						
        				//check if an activity execution already exists for the current activity or if there are several in parallel, check if there is one spot available. If so create the activity execution:
						//need to know the current process execution, from it, get the process definition and the number of activity executions associated to it.
						//from the process definition get the number of allowed activity executions for this activity definition (normally only 1 but can be more, for a parallel connector)
						$userService = tao_models_classes_UserService::singleton();
        				$activityRole = $this->getRestrictedRole($activityExecution);
        				
        				if (true === $userService->userHasRoles($currentUser, $activityRole)){
        					
        					$assignedUser = $this->getActivityExecutionUser($activityExecution);
        					
							if(is_null($assignedUser) || $assignedUser->getUri() == $currentUser->getUri()){
								$returnValue = true;
							}
        				}
        				break;	
        			}	
        			//check if the current user has the restricted role and is the restricted user based on the previous activity with the given role
        			case INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED:{
        				
        				$userService = tao_models_classes_UserService::singleton();
        				$activityRole = $this->getRestrictedRole($activityExecution);
        				
        				if (true === $userService->userHasRoles($currentUser, $activityRole)) {
        							
							$roleSearchPattern = array();
							$roleSearchPattern[] = $activityRole->getUri();
							$relatedProcessVariable = $this->getRestrictedRole($activityExecution, false);
							if(!is_null($relatedProcessVariable) && $relatedProcessVariable->getUri() != $activityRole->getUri()){
								$roleSearchPattern[] = $relatedProcessVariable->getUri();
							}
							
							//search for a past activity execution that has the the right role:
							$activityExecutionsClass = new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION);
							$pastActivityExecutions = $activityExecutionsClass->searchInstances(array(
								PROPERTY_ACTIVITY_EXECUTION_ACTIVITY => $this->getExecutionOf($activityExecution)->getUri(),
								PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION => $processExecution->getUri(),
								PROPERTY_ACTIVITY_EXECUTION_ACL_MODE => INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED,
								PROPERTY_ACTIVITY_EXECUTION_RESTRICTED_ROLE => $roleSearchPattern
							), array(
								'like' => false
							));
							
							$count = count($pastActivityExecutions);
							if($count > 0){
								foreach ($pastActivityExecutions as $pastActivityExecution) {
									$pastUser = $this->getActivityExecutionUser($pastActivityExecution);
									if (!is_null($pastUser)){
										if($pastUser->getUri() == $currentUser->getUri()){
											$returnValue = true; //user's activity execution
										}
										break(2);
									}else{
										continue;
									}
								}
								$returnValue = true;//no user has taken it
							}else{
								//throw exception here, since there should be at least the current acitivty exec here
								throw new wfEngine_models_classes_ProcessExecutionException('cannot even found a single activity execution that for the inherited role');
							}
							break;
						}
						
						break;
					}
					//special case for deliveries
					case INSTANCE_ACL_ROLE_RESTRICTED_USER_DELIVERY:{
						
						$userService = tao_models_classes_UserService::singleton();
						
						$activity = $this->getExecutionOf($activityExecution);
						if($this->activityService->isInitial($activity)){
	        				$activityRole = $this->getRestrictedRole($activityExecution);
	        				$returnValue = $userService->userHasRoles($currentUser, $activityRole);
						}else{
							$process = $processExecution->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_EXECUTIONOF));
							if(!is_null($process)){
								$processDefinitionService = wfEngine_models_classes_ProcessDefinitionService::singleton();
								foreach($processDefinitionService->getRootActivities($process) as $initialActivity){
									if(!is_null($this->getExecution($initialActivity, $currentUser, $processExecution))){
										$returnValue = true;
									}
									break;
								}
							}
						}
						break;
					}
        		}
        	}
			
			//set cached value:
			if (is_null($cachedValue) || !is_bool($cachedValue)) {
				$this->setCache(__METHOD__, array($activityExecution, $currentUser, $processExecution), $returnValue);
			}
        }
			
        

        return (bool) $returnValue;
    }

    /**
     * Get the estimated number of execution of this activity
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activity
     * @return int
     */
    public function getEstimatedExecutionCount( core_kernel_classes_Resource $activity)
    {
        $returnValue = (int) 0;

        
        
        $processFlow = new wfEngine_models_classes_ProcessFlow();
   		$parallelConnector = $processFlow->findParallelFromActivityBackward($activity);
   		if(!is_null($parallelConnector)){
			 $returnValue = count($parallelConnector->getPropertyValues(new core_kernel_classes_Property(PROPERTY_STEP_NEXT)));
   		}
   		else{
   			$returnValue = 1;
   		}
   		
        

        return (int) $returnValue;
    }

    /**
     * Short description of method remove
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @return boolean
     */
    public function remove( core_kernel_classes_Resource $processExecution)
    {
        $returnValue = (bool) false;

        
        
    	if(!is_null($processExecution)){
          	$activityExecClass = new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION);
			$activityExecutions = $activityExecClass->searchInstances(array(PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION => $processExecution->getUri()), array('like' => false, 'recursive' => 0));
        	foreach($activityExecutions as $activityExecution){
				$activityExecution->delete();
        	}
        }
        
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method getVariables
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @return array
     */
    public function getVariables( core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = array();

        
		$variableService = wfEngine_models_classes_VariableService::singleton();
		$activityExecutionVariables = $activityExecution->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_VARIABLES));
		if(!is_null($activityExecutionVariables)){
			$tokenVarKeys = @unserialize($activityExecutionVariables);
			if($tokenVarKeys !== false){
				if(is_array($tokenVarKeys)){
					foreach($tokenVarKeys as $key){
						$processVariable = $variableService->getProcessVariable($key);
						if(!is_null($processVariable)){
							$property = new core_kernel_classes_Property($processVariable->getUri());
							$returnValue[] = array(
									'code'			=> $key,
									'propertyUri'	=> $property->getUri(),
									'value'			=> $activityExecution->getPropertyValues($property)
								);
						}
					}
				}
			}
        }
		
        

        return (array) $returnValue;
    }

    /**
     * Short description of method getExecutionOf
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @return core_kernel_classes_Resource
     */
    public function getExecutionOf( core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = null;

        
		$returnValue = $this->getCache(__METHOD__, array($activityExecution));
		if(empty($returnValue)){
			$returnValue = $activityExecution->getUniquePropertyValue($this->activityProperty);
			if(!is_null($returnValue)){
				$this->setCache(__METHOD__, array($activityExecution), $returnValue);
			}
		}
        

        return $returnValue;
    }

    /**
     * Short description of method getRelatedProcessExecution
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @return core_kernel_classes_Resource
     */
    public function getRelatedProcessExecution( core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = null;

        
		//use get one property value for better performance:
		$returnValue = $activityExecution->getOnePropertyValue($this->processExecutionProperty);
		if(is_null($returnValue)){
			throw new wfEngine_models_classes_ProcessExecutionException('no process execution found for the activity execution '.$activityExecution->getUri());
		}
		
        

        return $returnValue;
    }

    /**
     * Short description of method setStatus
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @param  string status
     * @return boolean
     */
    public function setStatus( core_kernel_classes_Resource $activityExecution, $status)
    {
        $returnValue = (bool) false;

        
		
		if (!empty($status)){
			if($status instanceof core_kernel_classes_Resource){
				switch($status->getUri()){
					case INSTANCE_PROCESSSTATUS_RESUMED:
					case INSTANCE_PROCESSSTATUS_STARTED:
					case INSTANCE_PROCESSSTATUS_FINISHED:
					case INSTANCE_PROCESSSTATUS_PAUSED:
					case INSTANCE_PROCESSSTATUS_CLOSED:{
						$returnValue = $activityExecution->editPropertyValues($this->activityExecutionStatusProperty, $status->getUri());
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
					$returnValue = $activityExecution->editPropertyValues($this->activityExecutionStatusProperty, $status->getUri());
				}
			}
			
			if($returnValue){
				$this->setCache(__CLASS__.'::getStatus', array($activityExecution), $status);
			}
		}
		
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method createActivityExecution
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityDefinition
     * @param  Resource processExecution
     * @return core_kernel_classes_Resource
     */
    public function createActivityExecution( core_kernel_classes_Resource $activityDefinition,  core_kernel_classes_Resource $processExecution)
    {
        $returnValue = null;

        
		
		$activityExecution = $this->activityExecutionClass->createInstance();//do not create label! useless for activity exec management: to identify an activity execution, use time and executionOf property
		$activityExecution->setPropertyValue($this->activityProperty, $activityDefinition->getUri());
		
		//apply activity ACL from definition:
		$this->applyAclDefinitionToExecution($activityDefinition, $activityExecution);
		
		//add bijective relation for performance optimization (not modifiable)
		$activityExecution->setPropertyValue($this->processExecutionProperty, $processExecution->getUri());
		$activityExecution->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_TIME_CREATED), time());
		if($processExecution->setPropertyValue($this->processInstanceActivityExecutionsProperty, $activityExecution->getUri())){
			$returnValue = $activityExecution;
		}
        

        return $returnValue;
    }

    /**
     * Short description of method setActivityExecutionUser
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @param  Resource user
     * @param  boolean forced
     * @return boolean
     */
    public function setActivityExecutionUser( core_kernel_classes_Resource $activityExecution,  core_kernel_classes_Resource $user, $forced = false)
    {
        $returnValue = (bool) false;

        
		
		if($forced){
			$returnValue = $activityExecution->editPropertyValues($this->currentUserProperty, $user->getUri());
		}else{
			$currentUser = $activityExecution->getOnePropertyValue($this->currentUserProperty);
			if(!is_null($currentUser)){
				$errorMessage = "the activity execution {$activityExecution->getLabel()}({$activityExecution->getUri()}) has already been assigned to the user {$user->getLabel()}({$user->getUri()})";
				throw new wfEngine_models_classes_ProcessExecutionException($errorMessage);
			}else{
				$returnValue = $activityExecution->editPropertyValues($this->currentUserProperty, $user->getUri());
			}
		}
		
		if($returnValue){
			$this->setCache(__CLASS__.'::getActivityExecutionUser', array($activityExecution), $user);
		}
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method finish
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @return boolean
     */
    public function finish( core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = (bool) false;

        
		$returnValue = $this->setStatus($activityExecution, 'finished');
		
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method isFinished
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @return boolean
     */
    public function isFinished( core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = (bool) false;

        
		$status = $activityExecution->getOnePropertyValue($this->activityExecutionStatusProperty);
		if(!is_null($status)){
			if($status->getUri() == INSTANCE_PROCESSSTATUS_FINISHED){
				$returnValue = true;
			}
		}
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method resume
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @return boolean
     */
    public function resume( core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = (bool) false;

        
		$returnValue = $this->setStatus($activityExecution, 'resumed');
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method getActivityExecutionUser
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @return core_kernel_classes_Resource
     */
    public function getActivityExecutionUser( core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = null;

        
		$cacheValue = $this->getCache(__METHOD__, array($activityExecution));
		if(!is_null($cacheValue)){
			$returnValue = $cacheValue;
		}else{
			$returnValue = $activityExecution->getOnePropertyValue($this->currentUserProperty);
			$this->setCache(__METHOD__, array($activityExecution), $returnValue);
		}
		
        

        return $returnValue;
    }

    /**
     * Short description of method getStatus
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @return core_kernel_classes_Resource
     */
    public function getStatus( core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = null;

        
		$returnValue = $this->getCache(__METHOD__, array($activityExecution));
		if(empty($returnValue)){
			
			$status = $activityExecution->getOnePropertyValue($this->activityExecutionStatusProperty);
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
			
			$this->setCache(__METHOD__, array($activityExecution), $returnValue);
		}
        

        return $returnValue;
    }

    /**
     * Short description of method moveForward
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @param  Resource connector
     * @param  array nextActivities
     * @param  Resource processExecution
     * @return array
     */
    public function moveForward( core_kernel_classes_Resource $activityExecution,  core_kernel_classes_Resource $connector, $nextActivities,  core_kernel_classes_Resource $processExecution)
    {
        $returnValue = array();

        
		
		if(!is_null($activityExecution) && !is_null($connector) && !is_null($processExecution)){
            
			$processExecutionService = wfEngine_models_classes_ProcessExecutionService::singleton();
			$connectorService = wfEngine_models_classes_ConnectorService::singleton();
			
			$oldActivityExecutions = array();//holds the old activity executions to be removed from the current process activity executions at the end of the function
            switch($connectorService->getType($connector)->getUri()){

                /// SEQUENCE & CONDITIONAL ///
                case INSTANCE_TYPEOFCONNECTORS_SEQUENCE:
                case INSTANCE_TYPEOFCONNECTORS_CONDITIONAL:{
                     
                    if(count($nextActivities) == 0){
                        throw new wfEngine_models_classes_ProcessExecutionException("No next activity defined");
                    }
                    if(count($nextActivities) > 1){
                        throw new wfEngine_models_classes_ProcessExecutionException("Too many next activities, only one is required after a conditional or a sequence connector");
                    }
                    $nextActivity = $nextActivities[0];
                    
					$newActivityExecution = $this->duplicateActivityExecutionVariables($activityExecution, $nextActivity, $processExecution);
					if(!is_null($newActivityExecution)){
						//set backward and forward property values:
						$activityExecution->setPropertyValue($this->activityExecutionFollowingProperty, $newActivityExecution->getUri());
						$newActivityExecution->setPropertyValue($this->activityExecutionPreviousProperty, $activityExecution->getUri());
						$returnValue[$newActivityExecution->getUri()] = $newActivityExecution;
						
						$oldActivityExecutions = array($activityExecution->getUri() => $activityExecution);
					}
		
                    break;
                }
				/// PARALLEL ///
                case INSTANCE_TYPEOFCONNECTORS_PARALLEL:{
                    
					$splitVariablesArray = $this->getSplitVariables($activityExecution, $connector);
					
					foreach($nextActivities as $nextActivity){
						
						$splitVariables = array();
						if(count($splitVariablesArray) > 0){
							if(isset($splitVariablesArray[$nextActivity->getUri()])){
								$splitVariables = array_shift($splitVariablesArray[$nextActivity->getUri()]);
							}
						}
						
						$newActivityExecution = $this->createActivityExecution($nextActivity, $processExecution);
						$variableMerged = $this->mergeActivityExecutionVariables($newActivityExecution, array($activityExecution->getUri() => $activityExecution), $processExecution, $splitVariables);
						if($variableMerged){
							$activityExecution->setPropertyValue($this->activityExecutionFollowingProperty, $newActivityExecution->getUri());
							$newActivityExecution->setPropertyValue($this->activityExecutionPreviousProperty, $activityExecution->getUri());
							$returnValue[$newActivityExecution->getUri()] = $newActivityExecution;
						}
					}
					
					$oldActivityExecutions = array($activityExecution->getUri() => $activityExecution);
					
                    break;
                }
				/// JOIN ///
                case INSTANCE_TYPEOFCONNECTORS_JOIN:{
					
					$cardinalityService = wfEngine_models_classes_ActivityCardinalityService::singleton();
					
                    if(count($nextActivities) == 0){
                        throw new wfEngine_models_classes_ProcessExecutionException("No next activity defined");
                    }
                    if(count($nextActivities) > 1){
                        throw new wfEngine_models_classes_ProcessExecutionException("Too many next activities, only one is allowed after a join connector");
                    }
                    $nextActivity = current($nextActivities);
                    
					//get the activity around the connector
		            $activityResourceArray = array();
					foreach ($connectorService->getPreviousActivities($connector) as $activityCardinality) {
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
					
                    foreach($activityResourceArray as $activityDefinitionUri => $count){
                        //compare with execution and get tokens:
						$activityDefinition = new core_kernel_classes_Resource($activityDefinitionUri);
                        $previousActivityExecutions = $processExecutionService->getCurrentActivityExecutions($processExecution, $activityDefinition);
                        if(count($previousActivityExecutions) == $count){
                            foreach($previousActivityExecutions as $previousActivityExecution){
                                $oldActivityExecutions[$previousActivityExecution->getUri()] = $previousActivityExecution;
                            }
                        }else{
                            throw new wfEngine_models_classes_ProcessExecutionException("the number of activity execution does not correspond to the join connector definition (".count($previousActivityExecutions)." against {$count})");
                        }
						unset($activityDefinition);
                    }
                    	
                    //create the token for next activity
					$newActivityExecution = $this->createActivityExecution($nextActivity, $processExecution);
                    $variableMerged = $this->mergeActivityExecutionVariables($newActivityExecution, $oldActivityExecutions, $processExecution);
                    if($variableMerged){
						foreach ($oldActivityExecutions as $oldActivityExecution){
							$oldActivityExecution->setPropertyValue($this->activityExecutionFollowingProperty, $newActivityExecution->getUri());
							$newActivityExecution->setPropertyValue($this->activityExecutionPreviousProperty, $oldActivityExecution->getUri());
						}
						$returnValue[$newActivityExecution->getUri()] = $newActivityExecution;
					}
                    break;
				}
                default:
                	throw new common_exception_Error('Unknown connectortype for connector '.$connector->getUri());
            }
			
			if(!empty($returnValue)){
				//set the process' current activity executions:
				$processExecutionService->removeCurrentActivityExecutions($processExecution, $oldActivityExecutions);
				$processExecutionService->setCurrentActivityExecutions($processExecution, $returnValue);
			}
			
        }
		//do not forget to set current activity exec after this method execution to 
		
		
        

        return (array) $returnValue;
    }

    /**
     * Short description of method jump
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @param  Resource nextActivity
     * @param  Resource processExecution
     * @return core_kernel_classes_Resource
     */
    public function jump( core_kernel_classes_Resource $activityExecution,  core_kernel_classes_Resource $nextActivity,  core_kernel_classes_Resource $processExecution)
    {
        $returnValue = null;

        
        

        return $returnValue;
    }

    /**
     * Short description of method moveBackward
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @param  Resource processExecution
     * @param  array revertOptions
     * @return array
     */
    public function moveBackward( core_kernel_classes_Resource $activityExecution,  core_kernel_classes_Resource $processExecution, $revertOptions = array())
    {
        $returnValue = array();

        
		$processExecutionService = wfEngine_models_classes_ProcessExecutionService::singleton();
		
		//fill options:
		$revert = isset($revertOptions['revert'])?(bool)$revertOptions['revert']:true;
		$newVariables = (isset($revertOptions['newVariables']) && is_array($revertOptions['newVariables']))?$revertOptions['newVariables']:array();
		$notResumed = (isset($revertOptions['notResumed']) && is_array($revertOptions['notResumeds']))?$revertOptions['notResumed']:array();
		
		//check if the previous connector is not parallel:
		$previousActivityExecutions = array();
		$previous = $activityExecution->getPropertyValues($this->activityExecutionPreviousProperty);
		$count = count($previous);
		for($i=0; $i<$count; $i++){
			if(common_Utils::isUri($previous[$i])){
				$prevousActivityExecution = new core_kernel_classes_Resource($previous[$i]);
				if(count($prevousActivityExecution->getPropertyValues($this->activityExecutionFollowingProperty)) == 1){
					$previousActivityExecutions[$prevousActivityExecution->getUri()] = $prevousActivityExecution;
				}else{
					return false;//forbidden to go backward of a parallel connector
				}
			}
		}
		
		if(!empty($previousActivityExecutions)){
			//set the process' current activity executions:
			$processExecutionService->removeCurrentActivityExecutions($processExecution, array($activityExecution));
			
			//not performance efficient:
//			$activityExecution->delete();
//			$processExecution->removePropertyValues(
//				new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_ACTIVITYEXECUTIONS),
//				array(
//					'like' => false,
//					'pattern' => array()
//				));
			//instead, set the current activity execution as closed (i.e. invalidate the path):		
			$this->setStatus($activityExecution, 'closed');
			
			foreach($previousActivityExecutions as $previousActivityExecution) {
				
				$previousActivityExecution->removePropertyValues($this->activityExecutionFollowingProperty);
				
				//manage the additional option to full revert (default behaviour), full merge or partial merge (i.e. redifine some process variable values)
				if(!$revert){
					$overwritingVariables = array();
					if(isset($newVariables[$previousActivityExecution->getUri()])){
						$overwritingVariables = $newVariables[$previousActivityExecution->getUri()];
					}
					$this->mergeActivityExecutionVariables($previousActivityExecution, array($activityExecution), $processExecution, $overwritingVariables);
				}
				
				//change the status of the activity executions to 'paused' by default or nothing if not required (parallel branch):
				if(!isset($notResumed[$previousActivityExecution->getUri()])){
					$this->setStatus($previousActivityExecution, 'paused');
				}
				
			}
			
			if($processExecutionService->setCurrentActivityExecutions($processExecution, $previousActivityExecutions)){
				$returnValue = $previousActivityExecutions;
			}
			
		}
			
        

        return (array) $returnValue;
    }

    /**
     * Short description of method duplicateActivityExecutionVariables
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource oldActivityExecution
     * @param  Resource newActivityDefinition
     * @param  Resource processExecution
     * @return core_kernel_classes_Resource
     */
    public function duplicateActivityExecutionVariables( core_kernel_classes_Resource $oldActivityExecution,  core_kernel_classes_Resource $newActivityDefinition,  core_kernel_classes_Resource $processExecution)
    {
        $returnValue = null;

        
		
		$excludedProperties = array(
			RDFS_LABEL,
			PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER,
			PROPERTY_ACTIVITY_EXECUTION_ACTIVITY,
			PROPERTY_ACTIVITY_EXECUTION_CTX_RECOVERY,
			PROPERTY_ACTIVITY_EXECUTION_PREVIOUS,
			PROPERTY_ACTIVITY_EXECUTION_FOLLOWING,
			PROPERTY_ACTIVITY_EXECUTION_STATUS,
			PROPERTY_ACTIVITY_EXECUTION_TIME_CREATED,
			PROPERTY_ACTIVITY_EXECUTION_TIME_STARTED,
			PROPERTY_ACTIVITY_EXECUTION_TIME_LASTACCESS,
			PROPERTY_ACTIVITY_EXECUTION_ACL_MODE,
			PROPERTY_ACTIVITY_EXECUTION_RESTRICTED_USER,
			PROPERTY_ACTIVITY_EXECUTION_RESTRICTED_ROLE
		);
		
		$newActivityExecution = $oldActivityExecution->duplicate($excludedProperties);
		$newActivityExecution->setPropertyValue($this->activityProperty, $newActivityDefinition->getUri());
		$newActivityExecution->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_TIME_CREATED), time());
		
		//apply activity ACL from definition:
		$this->applyAclDefinitionToExecution($newActivityDefinition, $newActivityExecution);
		
		if($processExecution->setPropertyValue($this->processInstanceActivityExecutionsProperty, $newActivityExecution->getUri())){
			$returnValue = $newActivityExecution;
		}
		
        

        return $returnValue;
    }

    /**
     * Short description of method mergeActivityExecutionVariables
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource newActivityExecution
     * @param  array currentActivityExecutions
     * @param  Resource processExecution
     * @param  array newVariables
     * @return core_kernel_classes_Resource
     */
    public function mergeActivityExecutionVariables( core_kernel_classes_Resource $newActivityExecution, $currentActivityExecutions,  core_kernel_classes_Resource $processExecution = null, $newVariables = array())
    {
        $returnValue = null;

        
		
		if(is_null($processExecution)){
			$processExecution = $this->getRelatedProcessExecution($newActivityExecution);
		}
		
		//get tokens variables
        $allVars = array();
        foreach($currentActivityExecutions as $i => $currentActivityExec){
            $allVars[$i] = $this->getVariables($currentActivityExec);
        }

        //merge the variables
        $mergedVars = array();
        foreach($allVars as $tokenVars){
            foreach($tokenVars as $tokenVar){
                $code = $tokenVar['code'];
                foreach($tokenVar['value'] as $value){
                    if(array_key_exists($code, $mergedVars)){
                        if(is_array($mergedVars[$code])){
                            $alreadyExists = false;
                            foreach($mergedVars[$code] as $tValue){
                                if($tValue instanceof core_kernel_classes_Resource && $value instanceof core_kernel_classes_Resource){
                                    if($tValue->getUri() == $value->getUri()){
                                        $alreadyExists = true;
                                        break;
                                    }
                                }
                                else if($tValue == $value){
                                    $alreadyExists = true;
                                    break;
                                }
                            }
                            if(!$alreadyExists){
                                $mergedVars[$code][] = $value;
                            }
                        }
                        else{
                            $tValue = $mergedVars[$code];
                            if($tValue instanceof core_kernel_classes_Resource && $value instanceof core_kernel_classes_Resource){
                                if($tValue->getUri() != $value->getUri()){
                                    $mergedVars[$code] = array($tValue, $value);
                                }
                            }
                            else if($tValue != $value){
                                $mergedVars[$code] = array($tValue, $value);
                            }
                        }
                    }
                    else{
                        $mergedVars[$code] = $value;
                    }
                }
            }
        }
		
		if(!empty($newVariables)){
			$mergedVars = array_merge($mergedVars, $newVariables);
		}
		
        //store merged values:
        if(count($mergedVars) > 0){
			
			$variableService = wfEngine_models_classes_VariableService::singleton();
            $keys = array();
			//TODO: use Resource::setPropertyValues() here when implemented to improve performance:
            foreach($mergedVars as $code => $values){
				$processVariable = $variableService->getProcessVariable($code);
                if(!is_null($processVariable)){
					if(is_array($values)){
						foreach($values as $value){
							$newActivityExecution->setPropertyValue(new core_kernel_classes_Property($processVariable->getUri()), $value);
						}
					}
					else{
						$newActivityExecution->setPropertyValue(new core_kernel_classes_Property($processVariable->getUri()), $values);
					}
                }
                $keys[] = $code;
            }
            
            $newActivityExecution->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_VARIABLES), serialize($keys));
        }
		
		$returnValue =  true;
		
        

        return $returnValue;
    }

    /**
     * Short description of method generateActivityExecutionLabel
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityDefinition
     * @return string
     */
    public function generateActivityExecutionLabel( core_kernel_classes_Resource $activityDefinition)
    {
        $returnValue = (string) '';

        
		$returnValue = 'Execution of '.$activityDefinition->getLabel().' at '.date('d-m-Y G:i:s');//d-m-Y G:i:s u
        

        return (string) $returnValue;
    }

    /**
     * Short description of method createNonce
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @param  string nonce
     * @return string
     */
    public function createNonce( core_kernel_classes_Resource $activityExecution, $nonce = '')
    {
        $returnValue = (string) '';

        
		//tip : to be executed after a transition :
		$nonce = trim($nonce);
		if(empty($nonce)){
//			$nonce = (string)time();
			$nonce = (string)uniqid(time().'n');
		}
		
		if($activityExecution->editPropertyValues($this->activityExecutionNonceProperty, $nonce)){
			$returnValue = $nonce;
			$this->setCache(__CLASS__.'::getNonce', array($activityExecution), (string) $returnValue);
		}
        

        return (string) $returnValue;
    }

    /**
     * Short description of method checkNonce
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @param  string nonce
     * @return boolean
     */
    public function checkNonce( core_kernel_classes_Resource $activityExecution, $nonce)
    {
        $returnValue = (bool) false;

        
		$nonce = trim($nonce);
		if($nonce == $this->getNonce($activityExecution)){
			$returnValue = true;
		}
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method getNonce
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @return string
     */
    public function getNonce( core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = (string) '';

        
		
		$cachedValue = $this->getCache(__METHOD__, array($activityExecution));
		if(!is_null($cachedValue) && is_bool($cachedValue)){
			$returnValue = $cachedValue;
			return $returnValue;
		}
			
		$nonce = $activityExecution->getOnePropertyValue($this->activityExecutionNonceProperty);
		if(!is_null($nonce) && $nonce instanceof core_kernel_classes_Literal){
			$returnValue = $nonce->literal;
			if(!empty($returnValue)){
				$this->setCache(__METHOD__, array($activityExecution), $returnValue);
			}
		}
        

        return (string) $returnValue;
    }

    /**
     * Short description of method getRestrictedRole
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @param  boolean evaluateValue
     * @return core_kernel_classes_Resource
     */
    public function getRestrictedRole( core_kernel_classes_Resource $activityExecution, $evaluateValue = true)
    {
        $returnValue = null;

        
		$activityRole = $activityExecution->getOnePropertyValue($this->restrictedRoleProperty);
		if($evaluateValue){
			if($activityRole instanceof core_kernel_classes_Resource){
				$variableService = wfEngine_models_classes_VariableService::singleton();
				if($variableService->isProcessVariable($activityRole)){
					$actualValue = $activityExecution->getOnePropertyValue(new core_kernel_classes_Property($activityRole->getUri()));
					if(!is_null($actualValue) && $actualValue instanceof core_kernel_classes_Resource){
						$returnValue = $actualValue;
					}
				}else{
					//consider it as the role:
					$returnValue = $activityRole;
				}
			}
		}else{
			$returnValue = $activityRole;
		}
        

        return $returnValue;
    }

    /**
     * Short description of method getRestrictedUser
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @param  boolean evaluateValue
     * @return core_kernel_classes_Resource
     */
    public function getRestrictedUser( core_kernel_classes_Resource $activityExecution, $evaluateValue = true)
    {
        $returnValue = null;

        
		$activityUser = $activityExecution->getOnePropertyValue($this->restrictedUserProperty);
		if($evaluateValue){
			if($activityUser instanceof core_kernel_classes_Resource){
				$variableService = wfEngine_models_classes_VariableService::singleton();
				if($variableService->isProcessVariable($activityUser)){
					$actualValue = $activityExecution->getOnePropertyValue(new core_kernel_classes_Property($activityUser->getUri()));
					if(!is_null($actualValue) && $actualValue instanceof core_kernel_classes_Resource){
						$returnValue = $actualValue;
					}
				}else{
					//consider it as the role:
					$returnValue = $activityUser;
				}
			}
		}else{
			$returnValue = $activityUser;
		}
        

        return $returnValue;
    }

    /**
     * Short description of method getAclMode
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @return core_kernel_classes_Resource
     */
    public function getAclMode( core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = null;

        
		$aclMode = $activityExecution->getOnePropertyValue($this->ACLModeProperty);
		if($aclMode instanceof core_kernel_classes_Resource){
			$activityService = wfEngine_models_classes_ActivityService::singleton();
			if (array_key_exists($aclMode->getUri(), $activityService->getAclModes())) {
				$returnValue = $aclMode;
			}
		}
        

        return $returnValue;
    }

    /**
     * Short description of method applyAclDefinitionToExecution
     *
     * @access protected
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityDefinition
     * @param  Resource activityExecution
     * @return boolean
     */
    protected function applyAclDefinitionToExecution( core_kernel_classes_Resource $activityDefinition,  core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = (bool) false;

        
		$ACLmode = $activityDefinition->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ACL_MODE));
		if(!is_null($ACLmode)){
			switch($ACLmode->getUri()){
				case INSTANCE_ACL_USER:{
					$user = $activityDefinition->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_RESTRICTED_USER));
					if(!is_null($user)){
						$activityExecution->setPropertyValue($this->ACLModeProperty, $ACLmode);
						$returnValue = $activityExecution->setPropertyValue($this->restrictedUserProperty, $user);
					}
					break;
				}
				case INSTANCE_ACL_ROLE:
				case INSTANCE_ACL_ROLE_RESTRICTED_USER:
				case INSTANCE_ACL_ROLE_RESTRICTED_USER_INHERITED:
				case INSTANCE_ACL_ROLE_RESTRICTED_USER_DELIVERY:{
					$role = $activityDefinition->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_RESTRICTED_ROLE));
					if(!is_null($role)){
						$activityExecution->setPropertyValue($this->ACLModeProperty, $ACLmode);
						$returnValue = $activityExecution->setPropertyValue($this->restrictedRoleProperty, $role);
					}
					break;
				}
			}
		}
        

        return (bool) $returnValue;
    }

    /**
     * Return dispatched process variables values, by activity definiiton
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @param  Resource connector
     * @return array
     */
    public function getSplitVariables( core_kernel_classes_Resource $activityExecution,  core_kernel_classes_Resource $connector)
    {
        $returnValue = array();

        
		$connectorService = wfEngine_models_classes_ConnectorService::singleton();
		$cardinalityService = wfEngine_models_classes_ActivityCardinalityService::singleton();
		
		$allSplitVariables = array();
		foreach($connectorService->getNextActivities($connector) as $cardinality){
			if($cardinalityService->isCardinality($cardinality)){
				$splitVars = $cardinalityService->getSplitVariables($cardinality);
				$activity = $cardinalityService->getDestination($cardinality);
				
				if(!is_null($activity) && !empty($splitVars)) {
					$allSplitVariables[$activity->getUri()] = $splitVars;
				}
			}
		}
		
        $codeProperty = new core_kernel_classes_Property(PROPERTY_PROCESSVARIABLES_CODE);
		
		foreach($allSplitVariables as $activityUri => $splitVariables){
			
			$returnValue[$activityUri] = array();
			
			foreach($splitVariables as $splitVariable){
				if($splitVariable instanceof core_kernel_classes_Resource){
					$codeLiteral = $splitVariable->getOnePropertyValue($codeProperty);
					if (!is_null($codeLiteral) && $codeLiteral instanceof core_kernel_classes_Literal) {
						$code = $codeLiteral->literal;
						$serialisedValues = $activityExecution->getOnePropertyValue(new core_kernel_classes_Property($splitVariable->getUri()));
						if (!empty($serialisedValues) && $serialisedValues instanceof core_kernel_classes_Literal) {
							$values = unserialize($serialisedValues);
							if ($values && is_array($values) && !empty($values)) {
								$count = count($values);
								for ($i = 0; $i < $count; $i++) {
									if (!isset($returnValue[$activityUri][$i])) {
										$returnValue[$activityUri][$i] = array();
									}
									$returnValue[$activityUri][$i][$code] = $values[$i];
								}
							}
						}
					}
				}
			}
			
			
		}

        

        return (array) $returnValue;
    }

    /**
     * Short description of method getPrevious
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @return array
     */
    public function getPrevious( core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = array();

        
		
		$previous = $activityExecution->getPropertyValues($this->activityExecutionPreviousProperty);
		$countPrevious = count($previous);
		for($j=0; $j<$countPrevious; $j++){
			if(common_Utils::isUri($previous[$j])){
				$returnValue[$previous[$j]] = new core_kernel_classes_Resource($previous[$j]);
			}
		}
				
        

        return (array) $returnValue;
    }

    /**
     * Short description of method getFollowing
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource activityExecution
     * @return array
     */
    public function getFollowing( core_kernel_classes_Resource $activityExecution)
    {
        $returnValue = array();

        
		
		$following = $activityExecution->getPropertyValues($this->activityExecutionFollowingProperty);
		$countFollowing = count($following);
		for($k=0; $k<$countFollowing; $k++){
			if(common_Utils::isUri($following[$k])){
				$returnValue[$following[$k]] = new core_kernel_classes_Resource($following[$k]);
			}
		}
		
        

        return (array) $returnValue;
    }

}