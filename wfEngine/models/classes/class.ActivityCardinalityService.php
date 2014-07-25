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
 * Short description of class wfEngine_models_classes_ActivityCardinalityService
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfEngine
 
 */
class wfEngine_models_classes_ActivityCardinalityService
    extends wfEngine_models_classes_StepService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method isCardinality
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string object
     * @return boolean
     */
    public function isCardinality($object)
    {
        $returnValue = (bool) false;

        
		
		if(!empty($object) && $object instanceof core_kernel_classes_Resource){
			$returnValue = $object->hasType(new core_kernel_classes_Class(CLASS_ACTIVITYCARDINALITY));
		}
		
        

        return (bool) $returnValue;
    }

    /**
     * creates a new cardinality resource that links to "step"
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource step
     * @param  int cardinality
     * @return core_kernel_classes_Resource
     */
    public function createCardinality( core_kernel_classes_Resource $step, $cardinality = 1)
    {
        $returnValue = null;

        
		$cardinalityValue = null;
		if(is_numeric($cardinality)){
			$cardinalityValue = intval($cardinality);
		}else if($cardinality instanceof core_kernel_classes_Resource || common_Utils::isUri($cardinality)){
			$cardinalityValue = $cardinality;
		}
		
		if(!is_null($cardinalityValue)){
			$class = new core_kernel_classes_Class(CLASS_ACTIVITYCARDINALITY);
			$returnValue = $class->createInstanceWithProperties(array(
				PROPERTY_STEP_NEXT	=> $step,
				PROPERTY_ACTIVITYCARDINALITY_CARDINALITY => $cardinalityValue
			));
		}
		
        

        return $returnValue;
    }

    /**
     * please use getSource/get Destination instead
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @deprecated
     * @param  Resource activityCardinality
     * @return core_kernel_classes_Resource
     */
    public function getActivity( core_kernel_classes_Resource $activityCardinality)
    {
        $returnValue = null;

        
		$returnValue = $this->getDestination($activityCardinality);
        

        return $returnValue;
    }

    /**
     * Short description of method getCardinality
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activityCardinality
     * @param  Resource activityExecution
     * @return mixed
     */
    public function getCardinality( core_kernel_classes_Resource $activityCardinality,  core_kernel_classes_Resource $activityExecution = null)
    {
        $returnValue = null;

        
		//TODO: to be cached
		if(!is_null($activityCardinality)){
			
			$variableService = wfEngine_models_classes_VariableService::singleton();
			
			$cardinality = $activityCardinality->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTIVITYCARDINALITY_CARDINALITY));
			if($cardinality instanceof core_kernel_classes_Literal && is_numeric((string)$cardinality)){
				
				$returnValue = intval((string)$cardinality);
				
			}else if($cardinality instanceof core_kernel_classes_Resource && $variableService->isProcessVariable($cardinality)){
				
				//consider it as a process variable:
				if(is_null($activityExecution)){
					$returnValue = $cardinality;
				}else{
					//we want to retrieve the value in the context of execution
					$cardinalityValue = $activityExecution->getOnePropertyValue(new core_kernel_classes_Property($cardinality->getUri()));
					if($cardinalityValue instanceof core_kernel_classes_Literal){
						$returnValue = intval((string)$cardinalityValue);
					}else{
						throw new wfEngine_models_classes_ProcessExecutionException('cardinality must be of a numeric type in a process variable');
					}
				}
				
			}else{
				throw new wfEngine_models_classes_ProcessDefinitonException('cardinality must be a integer or a process variable of an integer');
			}
		}
        

        return $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        
        
    }

    /**
     * Short description of method editCardinality
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activityCardinality
     * @param  int cardinality
     * @return boolean
     */
    public function editCardinality( core_kernel_classes_Resource $activityCardinality, $cardinality = 1)
    {
        $returnValue = (bool) false;

        
		//TODO: to be cached
		if(!is_null($activityCardinality)){
			$returnValue = $activityCardinality->editPropertyValues(
				new core_kernel_classes_Property(PROPERTY_ACTIVITYCARDINALITY_CARDINALITY),
				intval($cardinality)
			);
		}
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method editSplitVariables
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activityCardinality
     * @param  array variables
     * @return boolean
     */
    public function editSplitVariables( core_kernel_classes_Resource $activityCardinality, $variables)
    {
        $returnValue = (bool) false;

        
		$returnValue = $activityCardinality->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITYCARDINALITY_SPLITVARIABLES), $variables);
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method getSplitVariables
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activityCardinality
     * @return array
     */
    public function getSplitVariables( core_kernel_classes_Resource $activityCardinality)
    {
        $returnValue = array();

        
		
		foreach($activityCardinality->getPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITYCARDINALITY_SPLITVARIABLES)) as $splitVariableUri){
			if(common_Utils::isUri($splitVariableUri)){
				$returnValue[$splitVariableUri] = new core_kernel_classes_Resource($splitVariableUri);
			}
		}
		
        

        return (array) $returnValue;
    }

    /**
     * Returns the ActivityCardinality that links the specified steps
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource source
     * @param  Resource destination
     * @return core_kernel_classes_Resource
     */
    public function getCardinalityResource( core_kernel_classes_Resource $source,  core_kernel_classes_Resource $destination)
    {
        $returnValue = null;

        

    	// @TODO consider other properties in case of chained connectors
    	$propNextStep = new core_kernel_classes_Property(PROPERTY_STEP_NEXT);
    	
		$candidates = $source->getPropertyValues($propNextStep);
		foreach ($candidates as $candidate) {
			if ($this->isCardinality($candidate)) {
				$candDest = $candidate->getUniquePropertyValue($propNextStep);
				if ($candDest->getUri() == $destination->getUri()) {
					$returnValue = $candidate;
					break;
				}
			}
		}
        

        return $returnValue;
    }

    /**
     * Short description of method getSource
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource cardinality
     * @return core_kernel_classes_Resource
     */
    public function getSource( core_kernel_classes_Resource $cardinality)
    {
        $returnValue = null;

        
        $sources = $this->getPreviousSteps($cardinality);
        if (count($sources) <> 1) {
        	throw new wfEngine_models_classes_ProcessDefinitonException('Cardinality '.$cardinality->getUri().' has '.count($sources).' sources');
        }
        $returnValue = current($sources);
        

        return $returnValue;
    }

    /**
     * Short description of method getDestination
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource cardinality
     * @return core_kernel_classes_Resource
     */
    public function getDestination( core_kernel_classes_Resource $cardinality)
    {
        $returnValue = null;

        
        $destination = $this->getNextSteps($cardinality);
        if (count($destination) <> 1) {
        	throw new wfEngine_models_classes_ProcessDefinitonException('Cardinality '.$cardinality->getUri().' has '.count($destination).' destinations');
        }
        $returnValue = current($destination);
        

        return $returnValue;
    }

} /* end of class wfEngine_models_classes_ActivityCardinalityService */

?>