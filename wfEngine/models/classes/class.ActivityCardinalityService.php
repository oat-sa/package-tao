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

error_reporting(E_ALL);

/**
 * TAO - wfEngine/models/classes/class.ActivityCardinalityService.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 24.10.2012, 14:16:58 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include wfEngine_models_classes_StepService
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('wfEngine/models/classes/class.StepService.php');

/* user defined includes */
// section 127-0-1-1-6eb1148b:132b4a0f8d0:-8000:000000000000304E-includes begin
// section 127-0-1-1-6eb1148b:132b4a0f8d0:-8000:000000000000304E-includes end

/* user defined constants */
// section 127-0-1-1-6eb1148b:132b4a0f8d0:-8000:000000000000304E-constants begin
// section 127-0-1-1-6eb1148b:132b4a0f8d0:-8000:000000000000304E-constants end

/**
 * Short description of class wfEngine_models_classes_ActivityCardinalityService
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfEngine
 * @subpackage models_classes
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

        // section 127-0-1-1-6eb1148b:132b4a0f8d0:-8000:000000000000304F begin
		
		if(!empty($object) && $object instanceof core_kernel_classes_Resource){
			$returnValue = $object->hasType(new core_kernel_classes_Class(CLASS_ACTIVITYCARDINALITY));
		}
		
        // section 127-0-1-1-6eb1148b:132b4a0f8d0:-8000:000000000000304F end

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

        // section 127-0-1-1-6eb1148b:132b4a0f8d0:-8000:0000000000003052 begin
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
		
        // section 127-0-1-1-6eb1148b:132b4a0f8d0:-8000:0000000000003052 end

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

        // section 127-0-1-1-6eb1148b:132b4a0f8d0:-8000:0000000000003058 begin
		$returnValue = $this->getDestination($activityCardinality);
        // section 127-0-1-1-6eb1148b:132b4a0f8d0:-8000:0000000000003058 end

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

        // section 127-0-1-1-6eb1148b:132b4a0f8d0:-8000:000000000000305B begin
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
        // section 127-0-1-1-6eb1148b:132b4a0f8d0:-8000:000000000000305B end

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
        // section 127-0-1-1-6eb1148b:132b4a0f8d0:-8000:0000000000003064 begin
        // section 127-0-1-1-6eb1148b:132b4a0f8d0:-8000:0000000000003064 end
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

        // section 127-0-1-1-6eb1148b:132b4a0f8d0:-8000:0000000000003067 begin
		//TODO: to be cached
		if(!is_null($activityCardinality)){
			$returnValue = $activityCardinality->editPropertyValues(
				new core_kernel_classes_Property(PROPERTY_ACTIVITYCARDINALITY_CARDINALITY),
				intval($cardinality)
			);
		}
        // section 127-0-1-1-6eb1148b:132b4a0f8d0:-8000:0000000000003067 end

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

        // section 127-0-1-1-2c295278:132fc7ce41a:-8000:00000000000030D2 begin
		$returnValue = $activityCardinality->editPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITYCARDINALITY_SPLITVARIABLES), $variables);
        // section 127-0-1-1-2c295278:132fc7ce41a:-8000:00000000000030D2 end

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

        // section 127-0-1-1-2c295278:132fc7ce41a:-8000:00000000000030D7 begin
		
		foreach($activityCardinality->getPropertyValues(new core_kernel_classes_Property(PROPERTY_ACTIVITYCARDINALITY_SPLITVARIABLES)) as $splitVariableUri){
			if(common_Utils::isUri($splitVariableUri)){
				$returnValue[$splitVariableUri] = new core_kernel_classes_Resource($splitVariableUri);
			}
		}
		
        // section 127-0-1-1-2c295278:132fc7ce41a:-8000:00000000000030D7 end

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

        // section 10-30-1--78-1db73770:13a8c80f2ee:-8000:0000000000003B9A begin

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
        // section 10-30-1--78-1db73770:13a8c80f2ee:-8000:0000000000003B9A end

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

        // section 10-30-1--78-5ba9bc3b:13a91b2b409:-8000:0000000000003BB5 begin
        $sources = $this->getPreviousSteps($cardinality);
        if (count($sources) <> 1) {
        	throw new wfEngine_models_classes_ProcessDefinitonException('Cardinality '.$cardinality->getUri().' has '.count($sources).' sources');
        }
        $returnValue = current($sources);
        // section 10-30-1--78-5ba9bc3b:13a91b2b409:-8000:0000000000003BB5 end

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

        // section 10-30-1--78-5ba9bc3b:13a91b2b409:-8000:0000000000003BB8 begin
        $destination = $this->getNextSteps($cardinality);
        if (count($destination) <> 1) {
        	throw new wfEngine_models_classes_ProcessDefinitonException('Cardinality '.$cardinality->getUri().' has '.count($destination).' destinations');
        }
        $returnValue = current($destination);
        // section 10-30-1--78-5ba9bc3b:13a91b2b409:-8000:0000000000003BB8 end

        return $returnValue;
    }

} /* end of class wfEngine_models_classes_ActivityCardinalityService */

?>