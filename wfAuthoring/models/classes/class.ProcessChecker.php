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
 */



/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every servcie instances.
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfAuthoring
 
 */
class wfAuthoring_models_classes_ProcessChecker
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute process
     *
     * @access protected
     * @var Resource
     */
    protected $process = null;

    /**
     * Short description of attribute authoringService
     *
     * @access protected
     * @var Resource
     */
    protected $authoringService = null;

    /**
     * Short description of attribute initialActivities
     *
     * @access protected
     * @var array
     */
    protected $initialActivities = array();

    /**
     * Short description of attribute isolatedActivities
     *
     * @access protected
     * @var array
     */
    protected $isolatedActivities = array();

    /**
     * Short description of attribute isolatedConnectors
     *
     * @access protected
     * @var array
     */
    protected $isolatedConnectors = array();

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
     * @param  Resource process
     * @return mixed
     */
    public function __construct( core_kernel_classes_Resource $process)
    {
        
		$this->process = $process;
		$this->activityService = wfEngine_models_classes_ActivityService::singleton();
		$this->connectorService = wfEngine_models_classes_ConnectorService::singleton();
		$this->authoringService = wfAuthoring_models_classes_ProcessService::singleton();
		parent::__construct();
        
    }

    /**
     * Short description of method check
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array checkList
     * @return boolean
     */
    public function check($checkList = array())
    {
        $returnValue = (bool) false;

        
		$classMethods = get_class_methods(get_class($this));
		$checkFunctions = array();
		foreach($classMethods as $functionName){
			if(preg_match('/^check(.)+/', $functionName)){
				$checkFunctions[] = $functionName;
			}
		}
		
		if(!empty($checkList)){
			$checkFunctions = array_intersect($checkFunctions, $checkList);
		}
		
		foreach($checkFunctions as $function){
			if(method_exists($this, $function)){
				$returnValue = $this->$function();
				if(!$returnValue) {
				    break;
				}
			}
		}
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method checkInitialActivity
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  int number
     * @return boolean
     */
    public function checkInitialActivity($number = 0)
    {
        $returnValue = (bool) false;

        
		$number = intval($number);
		$this->initialActivities = array();
		
		$process = $this->process;
		$count = 0;
		foreach($this->authoringService->getActivitiesByProcess($process) as $activity){
			
			if($this->activityService->isInitial($activity)){
				$this->initialActivities[$activity->getUri()] = $activity;
				$count++;
				if($number && ($count>$number)){
					// throw new wfEngine_models_classes_QTI_ProcessDefinitionException('too many initial activity');
					$returnValue = false;
					break;
				}
			}
		}
		
		if($number){
			$returnValue = ($count==$number)?true:false;
		}else{
			//number == 0 means at least one
			$returnValue = ($count>0)?true:false;
		}
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method checkNoIsolatedActivity
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return boolean
     */
    public function checkNoIsolatedActivity()
    {
        $returnValue = (bool) false;

        
		$returnValue = true;//need to be initiated as true
		
		$this->isolatedActivities = array();
		
		$connectorsClass = new core_kernel_classes_Class(CLASS_CONNECTORS);
		$process = $this->process;
		foreach($this->authoringService->getActivitiesByProcess($process) as $activity){
			if(!$this->activityService->isInitial($activity)){
				//should have a previous activity:
				$connectors = $connectorsClass->searchInstances(array(PROPERTY_STEP_NEXT => $activity->getUri()), array('like'=>false, 'recursive' => 0));
				if(empty($connectors)){
					$returnValue = false;
					$this->isolatedActivities[$activity->getUri()] = $activity;
				}
			}
		}
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method checkNoIsolatedConnector
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return boolean
     */
    public function checkNoIsolatedConnector()
    {
        $returnValue = (bool) false;

        
		$returnValue = true;//need to be initiated as true
		
		$this->isolatedConnectors = array();
		
		$process = $this->process;
		foreach($this->authoringService->getActivitiesByProcess($process) as $activity){
			$nextConnectors = $this->authoringService->getConnectorsByActivity($activity, array('next'));
			foreach($nextConnectors['next'] as $connector){
				
				$returnValue = false;
				if(!$this->isIsolatedConnector($connector)){
					$returnValue = true;
				}
				
			}
		}
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method getInitialActivities
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getInitialActivities()
    {
        $returnValue = array();

        
		$returnValue = $this->initialActivities;
        

        return (array) $returnValue;
    }

    /**
     * Short description of method getIsolatedActivities
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getIsolatedActivities()
    {
        $returnValue = array();

        
		$returnValue = $this->isolatedActivities;
        

        return (array) $returnValue;
    }

    /**
     * Short description of method getIsolatedConnectors
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getIsolatedConnectors()
    {
        $returnValue = array();

        
		$returnValue = $this->isolatedConnectors;
        

        return (array) $returnValue;
    }

    /**
     * Short description of method isIsolatedConnector
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource connector
     * @return boolean
     */
    public function isIsolatedConnector( core_kernel_classes_Resource $connector)
    {
        $returnValue = (bool) false;

        
		$returnValue = true;//need to be initiated as true
		
		$propNextActivities = new core_kernel_classes_Property(PROPERTY_STEP_NEXT);
		foreach($connector->getPropertyValuesCollection($propNextActivities)->getIterator() as $nextActivityOrConnector){
			
			if($this->activityService->isActivity($nextActivityOrConnector)){
				$returnValue = false;
			}else if($this->connectorService->isConnector($nextActivityOrConnector)){
				$isolated = $this->isIsolatedConnector($nextActivityOrConnector);
				if($returnValue){
					$returnValue = $isolated;
				}
			}else{
				throw new common_exception_Error('the next acitivty of "'.$connector->getUri().'" is neither an activity nor a connector');
			}
		}
		if($returnValue){
			$this->isolatedConnectors[$connector->getUri()] = $connector; 
		}
        

        return (bool) $returnValue;
    }

} /* end of class wfAuthoring_models_classes_ProcessChecker */

?>