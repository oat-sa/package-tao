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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *
 *
 */


/**
 * TAO - wfAuthoring/models/classes/class.ConnectorService.php
 *
 * Connector Services
 *
 * This file is part of TAO.
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package wfAuthoring
 * @subpackage models_classes
 */
class wfAuthoring_models_classes_ConnectorService
    extends wfEngine_models_classes_ConnectorService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method createConnector
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param core_kernel_classes_Resource sourceStep
     * @param string label
     * @throws Exception
     * @return core_kernel_classes_Resource
     */
    public function createConnector( core_kernel_classes_Resource $sourceStep, $label = '')
    {
        $returnValue = null;

		$label = empty($label) ? $sourceStep->getLabel()."_c" : $label;
		
		$connectorClass = new core_kernel_classes_Class(CLASS_CONNECTORS);
		$returnValue = $connectorClass->createInstance($label, "created by ProcessService.Class");
		
		if (is_null($returnValue)) {
			throw new Exception("the connector cannot be created for the activity {$sourceStep->getUri()}");
		}
		$activityService = wfEngine_models_classes_ActivityService::singleton();
		$connectorService = wfEngine_models_classes_ConnectorService::singleton();

		//associate the connector to the activity
		$sourceStep->setPropertyValue(new core_kernel_classes_Property(PROPERTY_STEP_NEXT), $returnValue);

		//set the activity reference of the connector:
		$activityRefProp = new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE);
		if($activityService->isActivity($sourceStep)){
			$returnValue->setPropertyValue($activityRefProp, $sourceStep);
		}elseif($connectorService->isConnector($sourceStep)){
			$returnValue->setPropertyValue($activityRefProp, $sourceStep->getUniquePropertyValue($activityRefProp));
		}else{
			throw new Exception("invalid resource type for the activity parameter: {$sourceStep->getUri()}");
		}

        return $returnValue;
    }

    /**
     * Short description of method createConditional
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource from
     * @param  Expression condition
     * @param  Resource then
     * @param  Resource else
     * @return core_kernel_classes_Resource
     */
    public function createConditional( core_kernel_classes_Resource $from,  core_kernel_rules_Expression $condition,  core_kernel_classes_Resource $then,  core_kernel_classes_Resource $else = null)
    {
        $returnValue = null;

		//Connector
		$connector = $this->createConnector($from);
		$this->setConnectorType($connector, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_CONDITIONAL));

		//Rule
		$authoringService = wfAuthoring_models_classes_ProcessService::singleton();
		$transitionRule = $this->createTransitionRule($connector, $condition);
		$transitionRule->editPropertyValues(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_THEN), $then);

		if (isset($else)) {
			$transitionRule->editPropertyValues(new core_kernel_classes_Property(PROPERTY_TRANSITIONRULES_ELSE), $else);
		}

		$returnValue = $connector;

        return $returnValue;
    }

    /**
     * Short description of method createTransitionRule
     *
     * @access private
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource connector
     * @param  Expression expression
     * @return core_kernel_classes_Resource
     */
    private function createTransitionRule( core_kernel_classes_Resource $connector,  core_kernel_rules_Expression $expression)
    {
        $returnValue = null;


        $transitionRule = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE));

		if (empty($transitionRule) || $transitionRule == null) {
			//create an instance of transition rule:
			$transitionRuleClass = new core_kernel_classes_Class(CLASS_TRANSITIONRULES);
			$transitionRule = $transitionRuleClass->createInstance();
			//Associate the newly created transition rule to the connector:
			$connector->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE), $transitionRule->getUri());
		}

		if (empty($expression)) {
			common_Logger::e('condition is not an instance of ressource : '.$expression);
		} else {
			//delete old condition:
			$oldCondition = $transitionRule->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_RULE_IF));
			if (!is_null($oldCondition)) {
				$this->deleteCondition($oldCondition);
			}
			$transitionRule->editPropertyValues(new core_kernel_classes_Property(PROPERTY_RULE_IF), $expression);
		}

		$returnValue = $transitionRule;

        return $returnValue;
    }

    /**
     * Short description of method createSequential
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource source
     * @param  Resource destination
     * @return core_kernel_classes_Resource
     */
    public function createSequential( core_kernel_classes_Resource $source,  core_kernel_classes_Resource $destination)
    {
        $returnValue = null;

		$returnValue = $this->createConnector($source);
		$this->setConnectorType($returnValue, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_SEQUENCE));
		$returnValue->setPropertyValue(new core_kernel_classes_Property(PROPERTY_STEP_NEXT), $destination);

        return $returnValue;
    }

    /**
     * Short description of method createJoin
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  array sources
     * @param  Resource destination
     * @return core_kernel_classes_Resource
     */
    public function createJoin($sources,  core_kernel_classes_Resource $destination)
    {
        $returnValue = null;

        foreach ($sources as $step) {
        	$followings = $step->getPropertyValues(new core_kernel_classes_Property(PROPERTY_STEP_NEXT));
        	if (count($followings) > 0) {
        		foreach ($followings as $followingUri) {
        			$following = new core_kernel_classes_Resource($followingUri);
        			if ($this->isConnector($following)) {
        				$this->delete($following);
        			} else {
        				throw new common_Exception('Step '.$step->getUri().' already has a non-connector attached');
        			}
        		}
        	}
		}
		
		$first = current($sources);
		$returnValue = $this->createConnector($first, "c_".$destination->getLabel());
		common_Logger::d('spawned connector '.$returnValue->getUri());
		$this->setConnectorType($returnValue, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_JOIN));
		
		$first->removePropertyValues(new core_kernel_classes_Property(PROPERTY_STEP_NEXT));
		$returnValue->setPropertyValue(new core_kernel_classes_Property(PROPERTY_STEP_NEXT), $destination);
		common_Logger::d('removed previous connections, added next');
		
		
		foreach ($sources as $activity) {
			$flow = new wfEngine_models_classes_ProcessFlow();
			$multiplicity = $flow->getCardinality($activity);
			$cardinality = wfEngine_models_classes_ActivityCardinalityService::singleton()->createCardinality($returnValue, $multiplicity);
			$activity->setPropertyValue(new core_kernel_classes_Property(PROPERTY_STEP_NEXT), $cardinality);
			common_Logger::d('spawned cardinality '.$cardinality->getUri().' with value '.$multiplicity);
		}


        return $returnValue;
    }

    /**
     * Short description of method createSplit
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource source
     * @param  array destinations
     * @return core_kernel_classes_Resource
     */
    public function createSplit( core_kernel_classes_Resource $source, $destinations)
    {
        $returnValue = null;

		$cardinalityService = wfEngine_models_classes_ActivityCardinalityService::singleton();
		foreach ($destinations as $destination) {
			$cardinalities[] = $cardinalityService->createCardinality($destination, 1);
		}
		
        $returnValue = $this->createConnector($source);
        $this->setConnectorType($returnValue, new core_kernel_classes_Resource(INSTANCE_TYPEOFCONNECTORS_PARALLEL));
		$returnValue->setPropertiesValues(array(
        	PROPERTY_STEP_NEXT => $cardinalities
        ));


        return $returnValue;
    }

    /**
     * Short description of method setSplitVariables
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource connector
     * @param  array variables
     * @return boolean
     */
    public function setSplitVariables( core_kernel_classes_Resource $connector, $variables)
    {
        $returnValue = (bool) false;


    	if($this->getType($connector)->getUri() != INSTANCE_TYPEOFCONNECTORS_PARALLEL){
    		throw new wfAuthoring_models_classes_ProcessAuthoringException('Called '.__FUNCTION__.' on non parallel connector');
    	}

    	$cardinalityService = wfEngine_models_classes_ActivityCardinalityService::singleton();
		foreach($this->getNextActivities($connector) as $cardinality){
			
			if($cardinalityService->isCardinality($cardinality)){
				
				//find the right cardinality resource (according to the activity defined in the connector):
				$activity = $cardinalityService->getDestination($cardinality);
				if(!is_null($activity) && isset($variables[$activity->getUri()])){
					common_Logger::i('found '.$cardinality->getUri());
					$returnValue = $cardinalityService->editSplitVariables($cardinality, $variables[$activity->getUri()]);
				}
			}
			
		}

        return (bool) $returnValue;
    }
    
	/**
     * sets the cardinality for the split connector of the specified
     * next steps
     * 
     * array in the form of stepUri => integer
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource connector
     * @param  array cardinalities
     */
    public function setSplitCardinality( core_kernel_classes_Resource $connector, $cardinalities)
    {
    	if($this->getType($connector)->getUri() != INSTANCE_TYPEOFCONNECTORS_PARALLEL){
    		throw new wfAuthoring_models_classes_ProcessAuthoringException('Called '.__FUNCTION__.' on non parallel connector '.$connector->getUri());
    	}

    	$cardinalityService = wfEngine_models_classes_ActivityCardinalityService::singleton();
		foreach($this->getNextActivities($connector) as $cardinality){
			
			if($cardinalityService->isCardinality($cardinality)){
				
				//find the right cardinality resource (according to the activity defined in the connector):
				$activity = $cardinalityService->getDestination($cardinality);
				if(!is_null($activity) && isset($cardinalities[$activity->getUri()])){
					$cardinalityService->editCardinality($cardinality, $cardinalities[$activity->getUri()]);
				}
			}
			
		}
    }
    
	/**
     * sets the cardinality for the join connector of the specified
     * previous steps
     * 
     * array in the form of stepUri => integer
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource connector
     * @param  array cardinalities
     */
    public function setJoinCardinality( core_kernel_classes_Resource $connector, $cardinalities)
    {
    	if($this->getType($connector)->getUri() != INSTANCE_TYPEOFCONNECTORS_JOIN){
    		throw new wfAuthoring_models_classes_ProcessAuthoringException('Called '.__FUNCTION__.' on the non join connector '.$connector->getUri());
    	}

    	$cardinalityService = wfEngine_models_classes_ActivityCardinalityService::singleton();
    	foreach($this->getPreviousSteps($connector) as $cardinality){
			
			if($cardinalityService->isCardinality($cardinality)){
				
				//find the right cardinality resource (according to the activity defined in the connector):
				$activity = $cardinalityService->getSource($cardinality);
				if(!is_null($activity) && isset($cardinalities[$activity->getUri()])){
					$cardinalityService->editCardinality($cardinality, $cardinalities[$activity->getUri()]);
				}
			}
			
		}
    }

    /**
     * Short description of method setConnectorType
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource connector
     * @param  Resource type
     * @return boolean
     */
    public function setConnectorType( core_kernel_classes_Resource $connector,  core_kernel_classes_Resource $type)
    {
        $returnValue = (bool) false;


        
        //@TODO: check range of type of connectors:
		$returnValue = $connector->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE), $type->getUri());
		


        return (bool) $returnValue;
    }

    /**
     * Short description of method delete
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource connector
     * @return boolean
     */
    public function delete( core_kernel_classes_Resource $connector)
    {
        $returnValue = (bool) false;


        $cardinalityService = wfEngine_models_classes_ActivityCardinalityService::singleton();
		
		if(!$this->isConnector($connector)){
			// throw new Exception("the resource in the parameter is not a connector: {$connector->getLabel()} ({$connector->getUri()})");
			return $returnValue;
		}
		
		//get the type of connector:
		$connectorType = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
		if(!is_null($connectorType) && $connectorType instanceof core_kernel_classes_Resource){
			if($connectorType->getUri() == INSTANCE_TYPEOFCONNECTORS_CONDITIONAL){
				//delete the related rule:
				$relatedRule = $this->getTransitionRule($connector);
				if(!is_null($relatedRule)){
					$processAuthoringService = wfAuthoring_models_classes_ProcessService::singleton();
					$processAuthoringService->deleteRule($relatedRule);
				}
			}
		}
		
		//delete cardinality resources if exists in previous activities:
		foreach($this->getPreviousActivities($connector) as $prevActivity){
			if($cardinalityService->isCardinality($prevActivity)){
				$prevActivity->delete();//delete the cardinality resource
			}
		}
		
		//manage the connection to the following activities
		$activityRef = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE))->getUri();
		foreach($this->getNextActivities($connector) as $nextActivity){
			
			$activity = null;
			
			if($cardinalityService->isCardinality($nextActivity)){
				try{
				$activity = $cardinalityService->getDestination($nextActivity);
				}catch(Exception $e){
					//the actiivty could be null if the reference have been removed...
				}
				
				$nextActivity->delete();//delete the cardinality resource
			}else{
				$activity = $nextActivity;
			}
			
			if(!is_null($activity) && $this->isConnector($activity)){
				$nextActivityRef = $activity->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE))->getUri();
				if($nextActivityRef == $activityRef){
					$this->delete($activity);//delete following connectors only if they have the same activity reference
				}
			}
		}
		
		//delete connector itself:
		$returnValue = $connector->delete(true);


        return (bool) $returnValue;
    }

} /* end of class wfAuthoring_models_classes_ConnectorService */

?>