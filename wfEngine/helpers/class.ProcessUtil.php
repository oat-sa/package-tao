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
 * Short description of class wfEngine_helpers_ProcessUtil
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfEngine
 
 */
class wfEngine_helpers_ProcessUtil
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getServiceDefinition
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string url
     * @return core_kernel_classes_Resource
     */
    public static function getServiceDefinition($url)
    {
        $returnValue = null;

        
		$serviceClass = new core_kernel_classes_Class(CLASS_SUPPORTSERVICES);
		$services = $serviceClass->searchInstances(array(PROPERTY_SUPPORTSERVICES_URL => $url), array('like' => false, 'recursive' => 1000));
		if(count($services)){
			$service = array_pop($services);
			if($service instanceof core_kernel_classes_Resource){
				$returnValue = $service;
			}
		}	
        
        

        return $returnValue;
    }

    /**
     * Short description of method isActivity
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public static function isActivity( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        
		if(!is_null($resource)){
			$returnValue = $resource->hasType( new core_kernel_classes_Class(CLASS_ACTIVITIES));
		}
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method isConnector
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public static function isConnector( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        
		if(!is_null($resource)){
			$returnValue = $resource->hasType( new core_kernel_classes_Class(CLASS_CONNECTORS));
		}
        

        return (bool) $returnValue;
    }

    /**
     * Organize Process Variable into an array
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array variables
     * @return array
     */
    public static function processVarsToArray($variables)
    {
        $returnValue = array();

        
        foreach ($variables as $var) {
            $returnValue[$var->uri] = $var->value;
        }
        

        return (array) $returnValue;
    }

    /**
     * Returns the activityExecutions of a ProcessInstance
     * in order of execution
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource process
     * @return array
     */
    public static function getActivityExecutions( core_kernel_classes_Resource $process)
    {
        $returnValue = array();

        
        $prop = new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_ACTIVITYEXECUTIONS);
        $activities = $process->getPropertyValues($prop);
        
        $nextmap = array();
        $previous = new core_kernel_classes_Property(PROPERTY_ACTIVITY_EXECUTION_PREVIOUS);
        $ordered = array();
        foreach ($activities as $activity) {
        	$activityRessource = new core_kernel_classes_Resource($activity);
        	$predecessor = $activityRessource->getOnePropertyValue($previous);
        	if (is_null($predecessor)) {
        		$currenturi = $activity;
        		$returnValue[] = new core_kernel_classes_Resource($activity);
        	} else {
        		$nextmap[$predecessor->getUri()] = $activity; 
        	} 
        }
        
        while (!empty($nextmap)) {
        	$nexturi = $nextmap[$currenturi];
        	$returnValue[] = new core_kernel_classes_Resource($nexturi);
        	unset($nextmap[$currenturi]);
        	$currenturi = $nexturi;
        }
        

        return (array) $returnValue;
    }

    /**
     * Short description of method checkType
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Class clazz
     * @return mixed
     */
    public static function checkType( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $clazz)
    {
        
    	if(!is_null($resource) && !is_null($clazz)){	
			foreach($resource->getTypes() as $type){
				if($type instanceof core_kernel_classes_Class){
					if($type->equals($clazz)){
						$returnValue = true;
						break;
					}
				}
			}
		}
        
    }

    /**
     * Short description of method isActivityFinal
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activity
     * @return boolean
     */
    public static function isActivityFinal( core_kernel_classes_Resource $activity)
    {
        $returnValue = (bool) false;

        
		$next = wfEngine_models_classes_StepService::singleton()->getNextSteps($activity);
		$returnValue = empty($next);
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method isActivityInitial
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource activity
     * @return boolean
     */
    public static function isActivityInitial( core_kernel_classes_Resource $activity)
    {
        $returnValue = (bool) false;

        
        $returnValue = wfEngine_models_classes_ActivityService::singleton()->isInitial($activity);
        

        return (bool) $returnValue;
    }

} /* end of class wfEngine_helpers_ProcessUtil */

?>