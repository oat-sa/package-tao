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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */

/**
 * Short description of class wfAuthoring_models_classes_ProcessTreeService
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfAuthoring
 
 */
class wfAuthoring_models_classes_ProcessTreeService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute addedConnectors
     *
     * @access protected
     * @var array
     */
    protected $addedConnectors = array(array());

    /**
     * Short description of attribute currentActivity
     *
     * @access protected
     * @var Resource
     */
    protected $currentActivity = null;

    /**
     * Short description of attribute currentConnector
     *
     * @access protected
     * @var Resource
     */
    protected $currentConnector = null;

    /**
     * Short description of attribute currentProcess
     *
     * @access protected
     * @var Resource
     */
    protected $currentProcess = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource currentProcess
     * @return wfAuthoring_models_classes_ProcessTreeService
     */
    public function __construct( core_kernel_classes_Resource $currentProcess = null)
    {
        $returnValue = null;

        
        $this->currentProcess = $currentProcess;
        

        return $returnValue;
    }

    /**
     * Short description of method activityNode
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activity
     * @param  string nodeClass
     * @param  boolean goto
     * @param  array portInfo
     * @param  string labelSuffix
     * @return array
     */
    public function activityNode( core_kernel_classes_Resource $activity, $nodeClass = '', $goto = false, $portInfo = array(), $labelSuffix = '')
    {
        $returnValue = array();

        
		$class = '';
		$linkAttribute = 'id';
		          
		$activityService = wfEngine_models_classes_ActivityService::singleton();
		$connectorService = wfEngine_models_classes_ConnectorService::singleton();
		
		if($activityService->isActivity($activity)){
			$class = 'node-activity';
		}elseif($connectorService->isConnector($activity)){
			$class = 'node-connector';
		}else{
			return $returnValue;//unknown type
		}
		
		if($goto){
			$class .= "-goto";
			$linkAttribute = "rel";
		}
		
		if(empty($portInfo)){
			$portInfo = array(
				'id' => 0,
				'label' => 'next',
				'multiplicity' => 1,
			);
		}else{
			if(!isset($portInfo['id'])){
				$portInfo['id'] = 0;
			}
			if(!isset($portInfo['id'])){
				$portInfo['label'] = 'next';
			}
			if(!isset($portInfo['id'])){
				$portInfo['multiplicity'] = 1;
			}
		}
		
		$returnValue = array(
			'data' => $activity->getLabel().' '.$labelSuffix,
			'attributes' => array(
				$linkAttribute => tao_helpers_Uri::encode($activity->getUri()),
				'class' => $class
			),
			'port' => $nodeClass,
			'portData' => $portInfo
		);
		
		$returnValue = self::addNodePrefix($returnValue, $nodeClass);
        

        return (array) $returnValue;
    }

    /**
     * Short description of method activityTree
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource process
     * @return array
     */
    public function activityTree( core_kernel_classes_Resource $process)
    {
        $returnValue = array();

        
        $this->currentActivity = null;
		
		if(empty($process) && !empty($this->currentProcess)){
			$process = $this->currentProcess;
		}
		if(empty($process)){
			throw new Exception("no process instance to populate the activity tree");
			return $data;
		}
		
		//initiate the return data value:
		$data = array(
			'data' => __("Process Tree:").' '.$process->getLabel(),
			'attributes' => array(
				'id' => 'node-process-root',
				'class' => 'node-process-root',
				'rel' => tao_helpers_Uri::encode($process->getUri())
			),
			'children' => array()
		);
		
		//instanciate the processAuthoring service
		$processAuthoringService = wfAuthoring_models_classes_ProcessService::singleton();
	
		$activities = array();
		$activities = $processAuthoringService->getActivitiesByProcess($process);
		// throw new Exception(var_dump($activities));
		foreach($activities as $activity){
			
			$this->currentActivity = $activity;
			$this->addedConnectors = array();//required to prevent cyclic connexion between connectors of a given activity
			$initial = false;
			$last = false;
			
			$activityData = array();
			$activityData = $this->activityNode(
				$activity,
				'next',
				false
			);//default value will do
						
			//get connectors
			$connectors = $processAuthoringService->getConnectorsByActivity($activity);
			
			//following nodes:
			if(!empty($connectors['next'])){
				//connector following the current activity: there should be only one
				foreach($connectors['next'] as $connector){
					$this->currentConnector = $connector;
					$activityData['children'][] = $this->connectorNode($connector, '', true);
				}
			}else{
				// throw new Exception("no connector associated to the activity: {$activity->getUri()}");
				//Simply not add a connector here: this should be considered as the last activity:
				$last = true;				
			}
			
			//check if it is the first activity node:
			
			$isIntial = $activity->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_ISINITIAL));
			if(!is_null($isIntial) && $isIntial instanceof core_kernel_classes_Resource){
				if($isIntial->getUri() == GENERIS_TRUE){
					$initial =true;
				}
			}
			
			if($initial){
				$activityData = $this->addNodeClass($activityData, "node-activity-initial");
				if($last){
					$activityData = $this->addNodeClass($activityData, 'node-activity-last');	
					$activityData = $this->addNodeClass($activityData, "node-activity-unique");
				}
			}elseif($last){
				$activityData = $this->addNodeClass($activityData, 'node-activity-last');	
			}
			
			
			//get interactive services
			$services = null;
			$services = $activity->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_ACTIVITIES_INTERACTIVESERVICES));
			foreach($services->getIterator() as $service){
				if($service instanceof core_kernel_classes_Resource){
					$activityData['children'][] = array(
						'data' => $service->getLabel(),
						'attributes' => array(
							'id' => tao_helpers_Uri::encode($service->getUri()),
							'class' => 'node-interactive-service'
						)
					);
				}
			}
			
			//add children here
			if($initial){
				array_unshift($data["children"],$activityData);
			}else{
				$data["children"][] = $activityData;
			}
			
		}
		
		$returnValue = $data;
        

        return (array) $returnValue;
    }

    /**
     * Short description of method addNodeClass
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array nodeData
     * @param  string newClass
     * @return array
     */
    public function addNodeClass($nodeData = array(), $newClass = '')
    {
        $returnValue = array();

        
        if(isset($nodeData['attributes']['class']) && !empty($newClass)){
			$nodeData['attributes']['class'] .= " ".$newClass;
			
			//set specific option
			if($newClass == 'node-activity-initial'){
				$nodeData['isInitial'] = true; 
			}
			if($newClass == 'node-activity-last'){
				$nodeData['isLast'] = true; 
			}
		}
		$returnValue = $nodeData;
        

        return (array) $returnValue;
    }

    /**
     * Short description of method addNodePrefix
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array node
     * @param  string prefix
     * @return array
     */
    public function addNodePrefix($node, $prefix = '')
    {
        $returnValue = array();

        
        $newNode = $node;
		$labelPrefix = '';
		switch(strtolower($prefix)){
			case 'prev':
				break;
			case 'next':
				break;
			case 'if':
				$labelPrefix = __('If').' ';
				break;	
			case 'then':
				$labelPrefix = __('Then').' ';
				break;
			case 'else':
				$labelPrefix = __('Else').' ';
				break;
		}
		if(!empty($labelPrefix)){
			$newNode['data'] = $labelPrefix.$node['data'];
		}
		$returnValue = $newNode;
        

        return (array) $returnValue;
    }

    /**
     * Short description of method connectorNode
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource connector
     * @param  string nodeClass
     * @param  boolean recursive
     * @return array
     */
    public function connectorNode( core_kernel_classes_Resource $connector, $nodeClass = '', $recursive = false)
    {
        $returnValue = array();

        
		$connectorData = array();
		$connectorService = wfEngine_models_classes_ConnectorService::singleton();			
//		$activityService = wfEngine_models_classes_ActivityService::singleton();
		
		//type of connector:
		//if not null, get the information on the next activities. Otherwise, return an "empty" connector node, indicating that the node has just been created, i.e. at the same time as an activity
		$connectorType = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE), false);
		if(is_null($connectorType)){
			//create default connector node:
			$returnValue = $this->addNodePrefix($this->defaultConnectorNode($connector),$nodeClass);
			return $returnValue;
		} else {
			
			//if it is a conditional type
			if( $connectorType->getUri() == INSTANCE_TYPEOFCONNECTORS_CONDITIONAL){
				
				//get the rule
				$connectorRule = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE), false);
				if(!is_null($connectorRule)){
					//continue getting connector data: 
					$connectorData[] = $this->conditionNode($connectorRule);
					
					//get the "THEN"
					$then = $connectorRule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN), false);
					if(!is_null($then)){
						$portData = array(
							'id' => 0,
							'label' => 'then',
							'multiplicity' => 1
						);
						if($connectorService->isConnector($then)){
							$connectorActivityReference = $then->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE))->getUri();
							if( ($connectorActivityReference == $this->currentActivity->getUri()) && !in_array($then->getUri(), $this->addedConnectors) ){
								if($recursive){
									$connectorData[] = $this->connectorNode($then, 'then', true, $portData);
								}else{
									$connectorData[] = $this->activityNode($then, 'then', false, $portData);
								}
							}else{
								$connectorData[] = $this->activityNode($then, 'then', true, $portData);
							}
						}else{
							$connectorData[] = $this->activityNode($then, 'then', true, $portData);
						}
					}
					
					//same for the "ELSE"
					$else = $connectorRule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_ELSE), false);
					if(!is_null($else)){
						$portData = array(
							'id' => 1,
							'label' => 'else',
							'multiplicity' => 1
						);
						if($connectorService->isConnector($else)){
							$connectorActivityReference = $else->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE))->getUri();
							if( ($connectorActivityReference == $this->currentActivity->getUri()) && !in_array($else->getUri(), $this->addedConnectors) ){
								if($recursive){
									$connectorData[] = $this->connectorNode($else, 'else', true, $portData);
								}else{
									$connectorData[] = $this->activityNode($else, 'else', false, $portData);
								}
							}else{
								$connectorData[] = $this->activityNode($else, 'else', true, $portData);
							}
						}else{
							$connectorData[] = $this->activityNode($else, 'else', true, $portData);
						}
					}
				}
				
			}elseif($connectorType->getUri() == INSTANCE_TYPEOFCONNECTORS_SEQUENCE){
				
				$next = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_STEP_NEXT), false);
				if(!is_null($next)){
					$connectorData[] = $this->activityNode($next, 'next', true);//the default portData array will do
				}
				
			}elseif($connectorType->getUri() == INSTANCE_TYPEOFCONNECTORS_PARALLEL){
				
				$cardinalityService = wfEngine_models_classes_ActivityCardinalityService::singleton();
				$variableService = wfEngine_models_classes_VariableService::singleton();
				$nextActivitiesCollection = $connector->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_STEP_NEXT));
				$portId = 0;
				foreach($nextActivitiesCollection->getIterator() as $nextActivity){
					
					if($cardinalityService->isCardinality($nextActivity)){
						
						$activity = $cardinalityService->getDestination($nextActivity);
						$cardinality = $cardinalityService->getCardinality($nextActivity);
						$number = ($cardinality instanceof core_kernel_classes_Resource)?'^'.$variableService->getCode($cardinality):$cardinality;
						$connectorData[] = $this->activityNode(
							$activity, 'next', true,
							array(
									'id' => $portId,
									'multiplicity' => $number,
									'label' => $activity->getLabel()
								),
							"(count : $number)"
						);
						$portId++;
					}
					
				}
				
			}elseif($connectorType->getUri() == INSTANCE_TYPEOFCONNECTORS_JOIN){
				
				$next = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_STEP_NEXT), false);
				if(!is_null($next)){
					$connectorData[] = $this->activityNode($next, 'next', true);//the default portData array will do
				}
				
			}else{
				throw new Exception("unknown connector type: {$connectorType->getLabel()} for connector {$connector->getUri()}");
			}
			
			if(empty($portInfo)){
				$portInfo = array(
					'id' => 0,
					'label' => 'next',
					'multiplicity' => 1,
				);
			}else{
				if(!isset($portInfo['id'])){
					$portInfo['id'] = 0;
				}
				if(!isset($portInfo['id'])){
					$portInfo['label'] = 'next';
				}
				if(!isset($portInfo['id'])){
					$portInfo['multiplicity'] = 1;
				}
			}
			
			//add to data
			$returnValue = array(
				'data' => $connectorType->getLabel().":".$connector->getLabel(),
				'attributes' => array(
					'id' => tao_helpers_Uri::encode($connector->getUri()),
					'class' => 'node-connector'
				),
				'type' => trim(strtolower($connectorType->getLabel())),
				'port' => $nodeClass,
				'portData' => $portInfo
			);
			$returnValue = self::addNodePrefix($returnValue, $nodeClass);
			
			if(!empty($connectorData)){
				$returnValue['children'] = $connectorData;
			}
			
			$this->addedConnectors[] = $connector->getUri();
		}
        

        return (array) $returnValue;
    }

    /**
     * Short description of method defaultConnectorNode
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource connector
     * @param  boolean prev
     * @return array
     */
    public function defaultConnectorNode( core_kernel_classes_Resource $connector, $prev = false)
    {
        $returnValue = array();

        
        $returnValue = array(
			'data' => __("type??").":".$connector->getLabel()
		);
		
		if(!$prev){
			$returnValue['attributes'] = array(
				'id' => tao_helpers_Uri::encode($connector->getUri()),
				'class' => 'node-connector'
			);
		}else{
			$returnValue['attributes'] = array(
				'class' => 'node-connector-prev'
			);
		}
        

        return (array) $returnValue;
    }

    /**
     * The method creates the array representation of the condition
     * of a rule node to fill the jsTree
     * (could be inferenceRule or transitionRule)
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource rule
     * @return array
     */
    public function conditionNode( core_kernel_classes_Resource $rule)
    {
        $returnValue = array();

        
        $nodeData = array();
		
		if(!is_null($rule)) {
			$data='';
			$if = $rule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_RULE_IF));
			if(!is_null($if)){
				$data = $if->getLabel();
			}else{
				$data = __("(still undefined)");
			}
			$nodeData = array(
				'data' => $data,
				'attributes' => array(
					'id' => tao_helpers_Uri::encode($rule->getUri()),
					'class' => 'node-rule'
				)
			);
			
			$returnValue = self::addNodePrefix($nodeData, 'if');
		}
        

        return (array) $returnValue;
    }

} /* end of class wfAuthoring_models_classes_ProcessTreeService */

?>