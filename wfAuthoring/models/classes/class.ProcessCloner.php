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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */


/**
 * Short description of class wfAuthoring_models_classes_ProcessCloner
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfAuthoring
 
 */
class wfAuthoring_models_classes_ProcessCloner
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * Short description of attribute authoringService
     *
     * @access protected
     * @var ProcessService
     */
    protected $authoringService = null;

    /**
     * Short description of attribute currentActivity
     *
     * @access protected
     * @var Resource
     */
    protected $currentActivity = null;

    /**
     * Short description of attribute clonedProcess
     *
     * @access protected
     * @var Resource
     */
    protected $clonedProcess = null;

    /**
     * Short description of attribute cloneLabel
     *
     * @access protected
     * @var string
     */
    protected $cloneLabel = '';

    /**
     * Short description of attribute clonedActivities
     *
     * @access protected
     * @var array
     */
    protected $clonedActivities = array();

    /**
     * Short description of attribute clonedConnectors
     *
     * @access protected
     * @var array
     */
    protected $clonedConnectors = array();

    /**
     * Short description of attribute waitingConnectors
     *
     * @access protected
     * @var array
     */
    protected $waitingConnectors = array();

    /**
     * Short description of attribute debugClonedActivities
     *
     * @access public
     * @var array
     */
    public $debugClonedActivities = array();

    /**
     * Short description of attribute debugClonedConnectors
     *
     * @access public
     * @var array
     */
    public $debugClonedConnectors = array();

    /**
     * Short description of attribute activityService
     *
     * @access protected
     * @var ActivityService
     */
    protected $activityService = null;

    /**
     * Short description of attribute connectorService
     *
     * @access protected
     * @var ConnectorService
     */
    protected $connectorService = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string cloneLabel
     */
    public function __construct($cloneLabel = '')
    {
        
		$this->cloneLabel = $cloneLabel;
		$this->authoringService = wfAuthoring_models_classes_ProcessService::singleton();
		$this->activityService = wfEngine_models_classes_ActivityService::singleton();
		$this->connectorService = wfEngine_models_classes_ConnectorService::singleton();		
		$this->initCloningVariables();
        
    }

    /**
     * Short description of method addClonedActivity
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource newActivityIn
     * @param  Resource oldActivity
     * @param  array newActivityOut
     * @return mixed
     */
    public function addClonedActivity( core_kernel_classes_Resource $newActivityIn,  core_kernel_classes_Resource $oldActivity = null, $newActivityOut = null)
    {
        
		if(is_null($newActivityOut)) {
		    $newActivityOut = $newActivityIn;
		}
		
		if(!is_null($oldActivity)){
			//set the in:
			$this->clonedActivities[$oldActivity->getUri()]['in'] = $newActivityIn->getUri();
			//debug:
			$this->setDebugClonedActivities($oldActivity);
				
			//set the out:
			if($newActivityOut instanceof core_kernel_classes_Resource){
			
				$this->clonedActivities[$oldActivity->getUri()]['out'] = $newActivityOut->getUri();
				//debug:
				$this->setDebugClonedActivities($newActivityOut);
				
			}else if(is_array($newActivityOut)){
				//debug
				$this->clonedActivities[$oldActivity->getUri()]['out'] = array();
				foreach($newActivityOut as $aNewActivityOut){
					if($aNewActivityOut instanceof core_kernel_classes_Resource) {
						$this->clonedActivities[$oldActivity->getUri()]['out'][] = $aNewActivityOut->getUri();
						$this->setDebugClonedActivities($aNewActivityOut);
					}
				}
				
			}
		}else{
			$this->clonedActivities[] = $newActivityIn->getUri();
		}
		
		$this->setDebugClonedActivities($newActivityIn);
        
    }

    /**
     * Short description of method addClonedConnector
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource oldConnector
     * @param  Resource newConnector
     * @return mixed
     */
    public function addClonedConnector( core_kernel_classes_Resource $oldConnector,  core_kernel_classes_Resource $newConnector)
    {
        
		$this->clonedConnectors[$oldConnector->getUri()] = $newConnector->getUri();
        
    }

    /**
     * Short description of method cloneActivity
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activity
     * @return core_kernel_classes_Resource
     */
    protected function cloneActivity( core_kernel_classes_Resource $activity)
    {
        $returnValue = null;

        
		if($this->activityService->isActivity($activity)){
			$activityClone = $this->cloneWfResource(
				$activity, 
				new core_kernel_classes_Class(CLASS_ACTIVITIES),
				array(
					PROPERTY_ACTIVITIES_INTERACTIVESERVICES,
					PROPERTY_STEP_NEXT
			));
			
			if(!is_null($activityClone)){
				//clone the interactive service:
				$services = $this->authoringService->getServicesByActivity($activity);
				foreach($services as $service){
					$serviceClone = $this->cloneInteractiveService($service);
					if(!is_null($serviceClone)){
						$activityClone->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES), $serviceClone->getUri());
					}else{
						throw new Exception("the interactive service cannot be cloned: {$service->getLabel()}({$service->getUri()}) for the activity {$activityClone->getLabel()}({$activityClone->getUri()}) ");
					}
					
				}
				
				//TODO: the related rules, when implementation has been confirmed
				
				$returnValue = $activityClone;
			}
			
		}
        

        return $returnValue;
    }

    /**
     * Short description of method cloneConnector
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource connector
     * @return core_kernel_classes_Resource
     */
    protected function cloneConnector( core_kernel_classes_Resource $connector)
    {
        $returnValue = null;

        
		if($this->connectorService->isConnector($connector)){
			$connectorClone = $this->cloneWfResource(
				$connector, 
				new core_kernel_classes_Class(CLASS_CONNECTORS),
				array(
					PROPERTY_CONNECTORS_TRANSITIONRULE,
					PROPERTY_STEP_NEXT,
					PROPERTY_CONNECTORS_ACTIVITYREFERENCE
			));
			
			$this->updateWaitingConnector($connector, $connectorClone);
			
			//set activity reference:
			$propActivityRef = new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE);
			$oldReferenceActivity = $connector->getUniquePropertyValue($propActivityRef);
			
			$newReferenceActivity = $this->getClonedActivity($oldReferenceActivity, 'out');
			
			if(!is_null($newReferenceActivity)){
				if(is_array($newReferenceActivity)){
					$newReferenceActivity = $newReferenceActivity[0];
				}
				if(!$newReferenceActivity instanceof $newReferenceActivity){
					throw new Exception("the cloned reference activity found is not a resource!");
				}
				$connectorClone->setPropertyValue($propActivityRef, $newReferenceActivity->getUri());
			}else{
				//echo 'oldReferenceActivity label: '.$oldReferenceActivity->getLabel();
				//print_r($this);
				throw new common_exception_Error("Clone of ".$oldReferenceActivity->getUri()." cannot be found");
			}
			
			$connectorType = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
			if(!is_null($connectorType)){
				switch($connectorType->getUri()){
					case INSTANCE_TYPEOFCONNECTORS_CONDITIONAL:{
					
						$transitionRule = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
						
						$transitionRuleClone= null;
						if(!is_null($transitionRule)){
							//required to recreate the rule:
							$if = $transitionRule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_RULE_IF));
							if(!is_null($if)){
								$transitionRuleClone = $this->authoringService->createTransitionRule($connectorClone, $if->getLabel());
							}
						}
						if(is_null($transitionRuleClone)){
							$transitionRuleClone = $this->authoringService->createTransitionRule($connectorClone);
							if(is_null($transitionRuleClone)) {
							    throw new Exception("the transition rule of the cloned connector cannot be created");
							}
						}
						
						$transitionRuleActivityProperties = array(
							'then' => new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN),
							'else' => new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_ELSE)
						);
						
						foreach($transitionRuleActivityProperties as $activityType => $connectorActivityProperty){
							$activity = $transitionRule->getOnePropertyValue($connectorActivityProperty);
							if(!is_null($activity)){
								
								$newPropActivity = $this->getNewActivityFromOldActivity($activity, $oldReferenceActivity, $activityType, $connectorClone);
								if(!is_null($newPropActivity)){
									if(is_array($newPropActivity)){
										foreach($newPropActivity as $activityResource){
											if($activityResource instanceof core_kernel_classes_Resource){
												$transitionRuleClone->setPropertyValue($connectorActivityProperty, $activityResource->getUri());
												$activityResource->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_FALSE);
											}
										}
									}else if($newPropActivity instanceof core_kernel_classes_Resource){
										$transitionRuleClone->setPropertyValue($connectorActivityProperty, $newPropActivity->getUri());
										$newPropActivity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_FALSE);
									}
									
								}
							}
						}
						// break;//do not break!
					}
					case INSTANCE_TYPEOFCONNECTORS_SEQUENCE:
					case INSTANCE_TYPEOFCONNECTORS_PARALLEL:
					case INSTANCE_TYPEOFCONNECTORS_JOIN:{
						
						$connectorActivityProperties = array(
							'next' => new core_kernel_classes_Property(PROPERTY_STEP_NEXT)
						);
						
						foreach($connectorActivityProperties as $activityType => $connectorActivityProperty){
							$activities = $connector->getPropertyValuesCollection($connectorActivityProperty);
							$newPropActivitiesUris = array();
							
							foreach($activities->getIterator() as $activity){
								if(!is_null($activity)){
									
									/*
									* "new prop acitivy" can be:
									* 1 - an activity resource
									* 2 - an array of activity resources
									* 3 - a connector resource
									*/
									$newPropActivity = $this->getNewActivityFromOldActivity($activity, $oldReferenceActivity, $activityType, $connectorClone);
									
									if(!is_null($newPropActivity)){
										if($activityType == 'next'){
											if($newPropActivity instanceof core_kernel_classes_Resource){
												$newPropActivitiesUris[] = $newPropActivity->getUri();
												$newPropActivity->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL), GENERIS_FALSE);
											}else{
												throw new Exception('the next activity must be a single activity resource');
											}
										}else if($activityType == 'prev'){
											//prev:
											if($newPropActivity instanceof core_kernel_classes_Resource){
												$newPropActivitiesUris[] = $newPropActivity->getUri();
											}else if(is_array($newPropActivity)){
												$count = 0;
												$sequentialConnectorType = new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE);
												foreach($newPropActivity as $inputActivity){
													if($count == 0){
														$newPropActivitiesUris[] = $inputActivity->getUri();
													}else{
														//required to build a new sequential connector:
														$sequentialConnector = $this->authoringService->createConnector($inputActivity);
														// $this->authoringService->setConnectorType($sequentialConnector, $sequentialConnectorType);
														// $newPropActivitiesUris[] = $sequentialConnector->getUri();
														$this->authoringService->createSequenceActivity($sequentialConnector, $connectorClone);
													}
													$count++;
												}
											}else{
												throw new Exception('the next activity must be a single activity resource');
											}
										}else{
											throw new Exception('unknown connection type in connector clone: '.$activityType);
										}
									}
									
								}
							}
							$connectorClone->editPropertyValues($connectorActivityProperty, $newPropActivitiesUris);
						}
						break;
					}
				}
			}
			
			$this->addClonedConnector($connector, $connectorClone);
			$returnValue = $connectorClone;
		}
        

        return $returnValue;
    }

    /**
     * Short description of method cloneInteractiveService
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource service
     * @return core_kernel_classes_Resource
     */
    protected function cloneInteractiveService( core_kernel_classes_Resource $service)
    {
        $returnValue = null;

        
		$classCallOfServices = new core_kernel_classes_Class(CLASS_CALLOFSERVICES);
		$classActualParam = new core_kernel_classes_Class(CLASS_ACTUALPARAMETER);
		$propActualParamIn = new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN);
		
		$serviceClone = $this->cloneWfResource($service, $classCallOfServices, array($propActualParamIn->getUri(), PROPERTY_CALLOFSERVICES_ACTUALPARAMETEROUT));
		if(!is_null($serviceClone)){
			foreach($service->getPropertyValuesCollection($propActualParamIn)->getIterator() as $actualParamIn){
				if($actualParamIn instanceof core_kernel_classes_Resource){
					$actualParamInClone = $this->cloneWfResource($actualParamIn, $classActualParam);
					if(!is_null($actualParamInClone)){
						$serviceClone->setPropertyValue($propActualParamIn, $actualParamInClone->getUri());
					}else{
						throw new Exception('an input actual parameter cannot be clonned');
					}
				}
				
			}
			
			$returnValue = $serviceClone;
		}
        

        return $returnValue;
    }

    /**
     * Short description of method cloneProcess
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource process
     * @return core_kernel_classes_Resource
     */
    public function cloneProcess( core_kernel_classes_Resource $process)
    {
        $returnValue = null;

        
        common_Logger::i('Cloning '.$process->getUri());
		$processClone = $this->cloneWfResource($process, new core_kernel_classes_Class(CLASS_PROCESS), array(PROPERTY_PROCESS_ACTIVITIES, PROPERTY_PROCESS_DIAGRAMDATA));
		
		if(is_null($processClone)){
			throw new wfEngine_models_classes_ProcessDefinitonException("unable to clone process instance ".$process->getUri());
		}
		$this->clonedProcess = $processClone;
		
		$this->cloneProcessContent($process);
		
		foreach($this->getClonedActivities() as $activityClone){
			$processClone->setPropertyValue(new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES), $activityClone->getUri());
		}
		
		$returnValue = $processClone;
        

        return $returnValue;
    }

    /**
     * Short description of method cloneProcessSegment
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource process
     * @param  boolean addTransitionalActivity
     * @return core_kernel_classes_Array
     */
    protected function cloneProcessSegment( core_kernel_classes_Resource $process, $addTransitionalActivity = false)
    {
        $returnValue = null;

        
		$steps = $this->cloneProcessContent($process);
		
		$in = array();
		$out = array();
		foreach($steps as $activity){
			if (!$this->activityService->isActivity($activity)) {
				continue;
			}
			if(wfEngine_models_classes_ActivityService::singleton()->isInitial($activity)){
				$in[] = $activity;
			}
			$next = $activity->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_STEP_NEXT));
			if(is_null($next)){
				$out[] = $activity;
			}
		}	
		
		if (count($in) != 1) {
			throw new common_exception_Error('Unsupported nr of initial activities '.count($in).' for test '.$process->getUri());
		}

		$initialActivity = array_shift($in);
		$newFinalActivities = $out;

		if(is_null($initialActivity)){
			throw new Exception('no initial activity found to the defined process segment');
		}
		if(empty($newFinalActivities)){
			//TODO: check that every connector has a following activity
			throw new Exception('no terminal activity found to the defined process segment');
		}
		
		$newInitialActivity = $initialActivity;
		if($addTransitionalActivity){
			//echo "adding transitionnal actiivties";
			//init the required properties:
			$propInitial = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL);
			$propHidden = new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISHIDDEN);
			$activityClass = new core_kernel_classes_Class(CLASS_ACTIVITIES);
			
			//build the $firstActivity:
			$firstActivity = $activityClass->createInstance("process_start ({$process->getLabel()})", "created by ProcessCloner.Class");
			$firstActivity->editPropertyValues($propInitial, GENERIS_TRUE);//do set it here, the property will be modified automatically by create "following" activity
			$firstActivity->editPropertyValues($propHidden, GENERIS_TRUE);
			$connector = $this->authoringService->createConnector($firstActivity);
			
			//get the clone of the intiial acitivty:
			if(is_null($newInitialActivity)){
				throw new Exception("the intial activity has not been cloned: {$initialActivity->getLabel()}({$initialActivity->getUri()})");
			}
			$this->authoringService->createSequenceActivity($connector, $newInitialActivity);//this function also automatically set the former $iniitalAcitivty to "not initial"
			//TODO: rename the function createSequenceActivity to addSequenceActivity, clearer that way
			
			//build the last activity:
			$lastActivity = $activityClass->createInstance("process_end ({$process->getLabel()})", "created by ProcessCloner.Class");
			$lastActivity->editPropertyValues($propHidden, GENERIS_TRUE);
			foreach($newFinalActivities as $newActivity){
				
				//TODO: determine if there is need for merging multiple instances of a parallelized activity that has not been merged 
				$connector = $this->authoringService->createConnector($newActivity);
				
				$this->authoringService->createSequenceActivity($connector, $lastActivity);
			}
			
			$newInitialActivity = $firstActivity;
			$newFinalActivities = $lastActivity;
			
			$this->addClonedActivity($firstActivity);
			$this->addClonedActivity($lastActivity);
		}
		
		$returnValue = array(
			'in' => $newInitialActivity,
			'out' => $newFinalActivities
		);
        

        return $returnValue;
    }

    /**
     * Short description of method cloneWfResource
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource instance
     * @param  Class clazz
     * @param  array forbiddenProperties
     * @param  string newLabel
     * @return core_kernel_classes_Resource
     */
    protected function cloneWfResource( core_kernel_classes_Resource $instance,  core_kernel_classes_Class $clazz, $forbiddenProperties = array(), $newLabel = '')
    {
        $returnValue = null;

        
		
		$returnValue = $instance->duplicate($forbiddenProperties);
	   
		if(!is_null($returnValue)){
			$cloneLabel = empty($newLabel)? $instance->getLabel().$this->cloneLabel:$newLabel;
			$returnValue->setLabel($cloneLabel);
		}
		
        

        return $returnValue;
    }

    /**
     * Short description of method cloneProcessContent
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource process
     * @return array
     */
    private function cloneProcessContent( core_kernel_classes_Resource $process)
    {
        $returnValue = array();

        
    	
        // first pass: clone activities and connectors
        
		//get all activity processes and clone them:
		$activities = $this->authoringService->getActivitiesByProcess($process);
		foreach($activities as $activityUri => $activity){
			$activityClone = $this->cloneActivity($activity);
			if(!is_null($activityClone)){
				$returnValue[] = $activityClone;
				$this->addClonedActivity($activityClone, $activity);
				common_Logger::d('Cloned A '.$activity->getUri().' to '.$activityClone->getUri());
			}else{
				throw new Exception("the activity '{$activity->getLabel()}'({$activity->getUri()}) cannot be cloned");
			}
		}
		
		//reloop for connectors this time:
		foreach($activities as $activityUri => $activity){
			$this->currentActivity = $activity;
			$connectors = $this->authoringService->getConnectorsByActivity($activity, array('next'));
			
			foreach($connectors['next'] as $connector){
				$clone = $this->cloneConnector($connector);
				$returnValue[] = $clone;
				
				common_Logger::d('Cloned C '.$connector->getUri().' to '.$clone->getUri());
			}
		}
		/*
		if(!empty($this->waitingConnectors)){
			//update the remaing connectors:
			foreach($this->clonedConnectors as $oldConnectorUri => $newConnectorUri){
				$this->updateWaitingConnector(new core_kernel_classes_Resource($oldConnectorUri), new core_kernel_classes_Resource($newConnectorUri));
			}
		}
		*/
		
		// second pass, link the clones using the originals as model
		
		foreach($this->authoringService->getActivitiesByProcess($process) as  $activity){
			$this->linkClonedStep($activity);
			$connectors = $this->authoringService->getConnectorsByActivity($activity, array('next'));
			foreach($connectors['next'] as $connector){
				$this->linkClonedStep($connector);
			}
		}
        

        return (array) $returnValue;
    }

    /**
     * Short description of method getClonedActivities
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getClonedActivities()
    {
        $returnValue = array();

        
		foreach($this->clonedActivities as $newActivityIO){
			if(is_array($newActivityIO)){
				foreach(array('in', 'out') as $interface){
					if(is_array($newActivityIO[$interface])){
						foreach($newActivityIO[$interface] as $activityUri){
							$activity = new core_kernel_classes_Resource($activityUri);
							if($this->activityService->isActivity($activity)){
								$returnValue[$activity->getUri()] = $activity;
							}
						}
					}
					else{
						$activity = new core_kernel_classes_Resource($newActivityIO[$interface]);
						if($this->activityService->isActivity($activity)){
							$returnValue[$activity->getUri()] = $activity;
						}
					}
				}
			}else if(is_string($newActivityIO)){
				$activity = new core_kernel_classes_Resource($newActivityIO);
				if($this->activityService->isActivity($activity)){
					$returnValue[$activity->getUri()] = $activity;
				}
				
			}
			
		}
        

        return (array) $returnValue;
    }

    /**
     * Short description of method getClonedActivity
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource oldActivity
     * @param  string InOut
     * @return core_kernel_classes_Resource
     */
    public function getClonedActivity( core_kernel_classes_Resource $oldActivity, $InOut = 'in')
    {
        $returnValue = null;

        
		$InOut = strtolower($InOut);
		if(in_array($InOut, array('in', 'out')) && isset($this->clonedActivities[$oldActivity->getUri()])){
			if(isset($this->clonedActivities[$oldActivity->getUri()][$InOut])){
				$activities = $this->clonedActivities[$oldActivity->getUri()][$InOut];
				if(is_array($activities)){
					$returnValue = array();
					foreach($activities as $activityUri){
						$returnValue[] = new core_kernel_classes_Resource($activityUri);
					}
				}else if(is_string($activities)){
					$returnValue = new core_kernel_classes_Resource($activities);
				}
				else{
					throw new common_Exception("unkown type in getClonedActivity array ({$activities})");
				}
				
			}
		}
        

        return $returnValue;
    }

    /**
     * Short description of method getClonedConnector
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource oldConnector
     * @return core_kernel_classes_Resource
     */
    public function getClonedConnector( core_kernel_classes_Resource $oldConnector)
    {
        $returnValue = null;

        
		if(isset($this->clonedConnectors[$oldConnector->getUri()])){
			$returnValue = new core_kernel_classes_Resource($this->clonedConnectors[$oldConnector->getUri()]);
		}
        

        return $returnValue;
    }

    /**
     * Short description of method getClonedConnectors
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getClonedConnectors()
    {
        $returnValue = array();

        
		foreach($this->clonedConnectors as $connectorUri){
			$connector = new core_kernel_classes_Resource($connectorUri);
			if($this->connectorService->isConnector($connector)){
				$returnValue[$connector->getUri()] = $connector;
			}
		}
        

        return (array) $returnValue;
    }

    /**
     * Short description of method getCloneLabel
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getCloneLabel()
    {
        $returnValue = (string) '';

        
		$returnValue = $this->cloneLabel;
        

        return (string) $returnValue;
    }

    /**
     * Short description of method getNewActivityFromOldActivity
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource oldActivity
     * @param  Resource oldReferenceActivity
     * @param  string connectionType
     * @param  Resource clonedConnector
     * @return core_kernel_classes_Resource
     */
    protected function getNewActivityFromOldActivity( core_kernel_classes_Resource $oldActivity,  core_kernel_classes_Resource $oldReferenceActivity, $connectionType,  core_kernel_classes_Resource $clonedConnector)
    {
        $returnValue = null;

        
		$activity = $oldActivity;
		$activityIO = '';
		switch($connectionType){
			case 'next':
			case 'then':
			case 'else':{
				//explanation: we are looking for the activity than is in the property "next activity" so it is the activity entering point that should be considered
				$activityIO = 'in';
				break;
			}
			case 'prev':{
				$activityIO = 'out';
				break;
			}
			default:{
				throw new Exception("unknown connectionType");
			}
		}
		//note: most of the time, $this->clonedActivities[$activity->getUri()]['in'] = $this->clonedActivities[$activity->getUri()]['out']
		
		if(!is_null($activity) && !is_null($oldReferenceActivity)){
			if($this->activityService->isActivity($activity)){
				$newActivity = $this->getClonedActivity($activity, $activityIO);
				if(!is_null($newActivity)){
					$returnValue = $newActivity;
					//note: works for parallel activity too, where multiple branch is created a parallelized branch
				}else{
					//must have been cloned!
					// print_r($this->clonedActivities);
					throw new common_exception_Error("the activity {$activity->getLabel()} ({$activity->getUri()}) has not been cloned!");
				}
			}else if($this->connectorService->isConnector($activity)){
				$newConnector = $this->getClonedConnector($activity);
				if(!is_null($newConnector)){
					//it is a reference to a connector with another activity reference and it has been cloned already
					$returnValue = $newConnector;
				}else{
					//not cloned yet:
					//clone it only if the reference id is the current activity
					//OR if the previous activities of a split connector:
					if($oldReferenceActivity->getUri() == $activity->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE))->getUri() && $activityIO=='in'){
						//recursively clone it
						
						$nextConnectorClone = $this->cloneConnector($activity);
						
						// $this->setWaitingConnector($activity, 'prev', $nextConnectorClone);//important to set the connector as a required one
						// if(!$this->updateWaitingConnector($activity, $nextConnectorClone)){
							// throw new Exception("the next connector clone cannot be updated");
						// }
						
						if(!is_null($nextConnectorClone)){
							
							$returnValue = $nextConnectorClone;
						}else{
							throw new Exception("the next connector cannot be cloned");
						}
					}else{
						//it is a connector of another activityReference branch and it is not cloned yet, so set it as such:
						//put in the waiting list:
						$this->setWaitingConnector($activity, $connectionType, $clonedConnector);
					}
				}
			}
		}
        

        return $returnValue;
    }

    /**
     * Short description of method initCloningVariables
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function initCloningVariables()
    {
        
		$this->currentActivity = null;
		$this->clonedProcess = null;
		$this->clonedActivities = array();
		$this->clonedConnectors = array();
		$this->waitingConnectors = array();
        
    }

    /**
     * Short description of method revertCloning
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function revertCloning()
    {
        
		if(!is_null($this->clonedProcess) && $this->clonedProcess instanceof core_kernel_classes_Resource){
			$this->authoringService->deleteProcess($this->clonedProcess);
		}
		
		foreach($this->getClonedActivities() as $activity){
			$this->authoringService->deleteActivity($activity);
		}
		
		$this->initCloningVariables();
        
    }

    /**
     * Short description of method setCloneLabel
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string cloneLabel
     */
    public function setCloneLabel($cloneLabel = '')
    {
        
		$this->cloneLabel = $cloneLabel;
        
    }

    /**
     * Short description of method setDebugClonedActivities
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activity
     * @return mixed
     */
    public function setDebugClonedActivities( core_kernel_classes_Resource $activity)
    {
        
		if(!is_null($activity)){
			$this->debugClonedActivities[$activity->getUri()] = $activity->getLabel();
		}
        
    }

    /**
     * Short description of method setWaitingConnector
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource waitingOldConnectorToBeCloned
     * @param  string connectionType
     * @param  Resource clonedConnectorToUpdate
     * @return mixed
     */
    protected function setWaitingConnector( core_kernel_classes_Resource $waitingOldConnectorToBeCloned, $connectionType,  core_kernel_classes_Resource $clonedConnectorToUpdate)
    {
        
		
		$authorizedConnectionTypes = array('next', 'then', 'else');
		
		if(!in_array($connectionType, $authorizedConnectionTypes)){
			throw new Exception("unavailable connection type");
		}
		if(!isset($this->waitingConnectors[$waitingOldConnectorToBeCloned->getUri()])){
			foreach($authorizedConnectionTypes as $authorizedConnectionType){
				$this->waitingConnectors[$waitingOldConnectorToBeCloned->getUri()][$authorizedConnectionType] = array();
			}
			
		}
		$this->waitingConnectors[$waitingOldConnectorToBeCloned->getUri()][$connectionType][] = $clonedConnectorToUpdate;
		
        
    }

    /**
     * Short description of method updateWaitingConnector
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource expectedConnector
     * @param  Resource expectedConnectorClone
     * @return boolean
     */
    protected function updateWaitingConnector( core_kernel_classes_Resource $expectedConnector,  core_kernel_classes_Resource $expectedConnectorClone)
    {
        $returnValue = (bool) false;

        
		
		//check if it is in the waiting expectedConnector list:
		$activityPropertiesMap = array(
			'next' => new core_kernel_classes_Property(PROPERTY_STEP_NEXT),
			'then' => new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN),
			'else' => new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_ELSE)
		);

		if(isset($this->waitingConnectors[$expectedConnector->getUri()])){
			
			foreach($this->waitingConnectors[$expectedConnector->getUri()] as $connectionType=>$connectors){
				if(isset($activityPropertiesMap[$connectionType])){
					
					$connectorProperty = $activityPropertiesMap[$connectionType];
					
					foreach($connectors as $aConnector){
						switch($connectionType){
							case 'next':{
								$aConnector->setPropertyValue($connectorProperty, $expectedConnectorClone->getUri());
								$returnValue = true;
								break;
							}
							case 'then':
							case 'else':{
								//property of the transition rule:
								$transitionRule = $aConnector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));
								if(!is_null($transitionRule)){
									//transition rule copied
									$transitionRule->setPropertyValue($connectorProperty, $expectedConnectorClone->getUri());
									$returnValue = true;
								}else{
									throw new Exception("the transition rule does not exist anymore");
								}
								break;
							}
						}
					}
				}else{
					throw new common_exception_Error('unknown connection type :'.$connectionType);
				}
			}
			
			unset($this->waitingConnectors[$expectedConnector->getUri()]);
			
		}
		
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method mapClonedResources
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array resources
     * @param  boolean in
     * @return array
     */
    private function mapClonedResources($resources, $in = true)
    {
        $returnValue = array();

        
        foreach ($resources as $res) {
        	$clone = $this->getClonedConnector($res);
        	if (!is_null($clone)) {
        		$returnValue[] = $clone;
        		continue;
        	}
        	$clone = $this->getClonedActivity($res, $in ? 'in' : 'out');
        	if (!is_null($clone)) {
        		$returnValue[] = $clone;
        		continue;
        	}
        	var_dump($this->clonedActivities);
        	throw new common_exception_Error('Could not find clone of '.$res->getUri().($in ? ' in' : ' out'));
        }
        

        return (array) $returnValue;
    }

    /**
     * Short description of method linkClonedStep
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource original
     * @return mixed
     */
    protected function linkClonedStep( core_kernel_classes_Resource $original)
    {
        
        $nextProp = new core_kernel_classes_Property(PROPERTY_STEP_NEXT);
		$arr = $this->mapClonedResources(array($original), false);
		$clone = current($arr);
		$resources = array();
		foreach ($original->getPropertyValues($nextProp) as $uri) {
			$resources[] = new core_kernel_classes_Resource($uri);
		}
		$mappedValues = $this->mapClonedResources($resources, true);
		$clone->editPropertyValues($nextProp, $mappedValues);
        
    }

} /* end of class wfAuthoring_models_classes_ProcessCloner */

?>