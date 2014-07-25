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
 * The wfEngine_models_classes_ProcessFlow class
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package wfEngine
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class wfEngine_models_classes_ProcessFlow{
    
	protected $jump = 0;
	public $checkedActivities = array();
	public $checkedConnectors = array();
	
	public function resetCheckedResources(){
		$this->checkedActivities = array();
		$this->checkedConnectors = array();
	}
	
	public function getCheckedActivities(){
		//return and ordered array of the sequence of activities that have been checked during the process flow analysis:
		return $this->checkedActivities;
	}
	
	public function findParallelFromActivityBackward(core_kernel_classes_Resource $activity){
	
		$returnValue = null;
		
		//put the activity being searched in an array to prevent searching from it again in case of back connection
		$this->checkedActivities[] = $activity->getUri();
		
		$connectorClass = new core_kernel_classes_Class(CLASS_CONNECTORS);
		$cardinalityClass = new core_kernel_classes_Class(CLASS_ACTIVITYCARDINALITY);
		
		$activityCardinalities = $cardinalityClass->searchInstances(array(PROPERTY_STEP_NEXT => $activity->getUri()), array('like' => false));//note: count()>1 only 
		$nextActivities = array_merge(array($activity->getUri()), array_keys($activityCardinalities));
		$previousConnectors = $connectorClass->searchInstances(array(PROPERTY_STEP_NEXT => $nextActivities), array('like' => false));//note: count()>1 only 
		foreach($previousConnectors as $connector){
		
			if(in_array($connector->getUri(), array_keys($this->checkedConnectors))){
				continue;
			}else{
				$this->checkedConnectors[$connector->getUri()] = $connector;
			}
		
			//get the type of the connector:
			$connectorType = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
			if($connectorType instanceof core_kernel_classes_Resource){
				
				switch($connectorType->getUri()){
					case INSTANCE_TYPEOFCONNECTORS_PARALLEL:{
						//parallel connector found:
						if($this->jump == 0){
							return $returnValue = $connector;
						}else{
							$this->jump --;
						}
						break;
					}
					case INSTANCE_TYPEOFCONNECTORS_JOIN:{
						//increment the class attribute $this->jump
						$this->jump ++;
					}
				}
			}
			
			//if the wanted parallel connector has not be found (i.e. no value returned so far):
			//get the previousActivityCollection and recursively execute the same function ON ONLY ONE of the previous branches (there would be several branches only in the case of a join, otherwise it should be one anyway:
			$previousActivity = $connector->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_ACTIVITYREFERENCE));
			//Note: the use of the property activity reference allow to jump to the "main" (in case of a join connector and successive conditionnal connectors) directly
			
			//if the previousActivity happens to have already been checked, jump it
			if(in_array($previousActivity->getUri(), $this->checkedActivities)){
				continue;
			}else{
				$parallelConnector = $this->findParallelFromActivityBackward($previousActivity);
				if($parallelConnector instanceof core_kernel_classes_Resource){
					//found it:
					if($this->jump != 0){
						throw new Exception('parallel connector returned while the "jump value" is not null ('.$this->jump.')');
					}
					return $returnValue = $parallelConnector;
				}
			}
			
		}
		
		return $returnValue;//null
	}
	
	public function findJoinFromActivityForward(core_kernel_classes_Resource $activity){
	
		$returnValue = null;
		
		//put the activity being searched in an array to prevent searching from it again in case of back connection
		$this->checkedActivities[] = $activity->getUri();
		
		$connectorClass = new core_kernel_classes_Class(CLASS_CONNECTORS);
		$connector = $activity->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_STEP_NEXT));
		if(!is_null($connector)){

			if(in_array($connector->getUri(), array_keys($this->checkedConnectors))){
				continue;
			}else{
				$this->checkedConnectors[$connector->getUri()] = $connector;
			}
		
			//get the type of the connector:
			$connectorType = $connector->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE));
			if($connectorType instanceof core_kernel_classes_Resource){
				
				switch($connectorType->getUri()){
					case INSTANCE_TYPEOFCONNECTORS_JOIN:{
						//parallel connector found:
						if($this->jump == 0){
							return $returnValue = $connector;
						}else{
							$this->jump --;
						}
						break;
					}
					case INSTANCE_TYPEOFCONNECTORS_PARALLEL:{
						//increment the class attribute $this->jump
						$this->jump ++;
						break;
					}
				}
			}
			
			//if the wanted join connector has not be found (i.e. no value returned so far):
			//get the nextActivitiesCollection and recursively execute the same function ON ONLY ONE of the next parallel branch, but both banches in case of a conditionnal connector
			$nextActivitiesCollection = $connector->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_STEP_NEXT));
			$cardinalityService = wfEngine_models_classes_ActivityCardinalityService::singleton();
			foreach($nextActivitiesCollection->getIterator() as $nextActivity){
				
				if($cardinalityService->isCardinality($nextActivity)){
					$nextActivity = $cardinalityService->getDestination($nextActivity);
				}
			
				//if the nextActivity happens to have already been checked, jump it
				if(in_array($nextActivity->getUri(), $this->checkedActivities)){
					continue;
				}else{
					$joinConnector = $this->findJoinFromActivityForward($nextActivity);
					if($joinConnector instanceof core_kernel_classes_Resource){
						//found it:
						if($this->jump != 0){
							throw new Exception('parallel connector returned while the "jump value" is not null ('.$this->jump.')');
						}
						return $returnValue = $joinConnector;
					}
				}
			}
			
		}
		
		return $returnValue;//null
	}
	
	public function getCardinality(core_kernel_classes_Resource $activity) {
		
		$returnValue = 1;
		
		// check multiplicity  (according to the cardinality defined in the related parallel connector):
		$parallelConnector = $this->findParallelFromActivityBackward($activity);
		
		if(!is_null($parallelConnector)){
			$cardinalityService = wfEngine_models_classes_ActivityCardinalityService::singleton();
			
			//count the number of time theprevious activity must be set as the previous activity of the join connector
			$cardinalities = $cardinalityService->getNextSteps($parallelConnector);
			foreach($cardinalities as $nextActivityCardinality){
				if(in_array($cardinalityService->getDestination($nextActivityCardinality)->getUri(), $this->getCheckedActivities())){
					return $cardinalityService->getCardinality($nextActivityCardinality);
				}
			}
			throw new common_exception_Error('parallel execution found by workflow '.$parallelConnector->getUri().' not in current execution path');			
		} else {
			// no parallel execution, so 1 execution
			common_Logger::i('no parallel connector found for '.$activity->getUri());			
		}
		
		return $returnValue;
	}
	
	
}
?>