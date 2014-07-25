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
 * TAO - wfEngine/helpers/class.ProcessUtil.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 16.03.2012, 11:56:26 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfEngine
 * @subpackage helpers
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BEA-includes begin
// section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BEA-includes end

/* user defined constants */
// section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BEA-constants begin
// section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BEA-constants end

/**
 * Short description of class wfEngine_helpers_ProcessUtil
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfEngine
 * @subpackage helpers
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

        // section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BF2 begin
		$serviceClass = new core_kernel_classes_Class(CLASS_SUPPORTSERVICES);
		$services = $serviceClass->searchInstances(array(PROPERTY_SUPPORTSERVICES_URL => $url), array('like' => false, 'recursive' => 1000));
		if(count($services)){
			$service = array_pop($services);
			if($service instanceof core_kernel_classes_Resource){
				$returnValue = $service;
			}
		}	
        
        // section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BF2 end

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

        // section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BF5 begin
		if(!is_null($resource)){
			$returnValue = $resource->hasType( new core_kernel_classes_Class(CLASS_ACTIVITIES));
		}
        // section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BF5 end

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

        // section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BFE begin
		if(!is_null($resource)){
			$returnValue = $resource->hasType( new core_kernel_classes_Class(CLASS_CONNECTORS));
		}
        // section 10-13-1-39--284957ac:12e4ca5284a:-8000:0000000000002BFE end

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

        // section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E9E begin
        foreach ($variables as $var) {
            $returnValue[$var->uri] = $var->value;
        }
        // section 127-0-1-1--7eb5a1dd:13214d5811e:-8000:0000000000002E9E end

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

        // section 127-0-1-1-3efeec8d:1361b13fcc8:-8000:00000000000038A6 begin
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
        // section 127-0-1-1-3efeec8d:1361b13fcc8:-8000:00000000000038A6 end

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
        // section 127-0-1-1-3efeec8d:1361b13fcc8:-8000:00000000000038AC begin
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
        // section 127-0-1-1-3efeec8d:1361b13fcc8:-8000:00000000000038AC end
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

        // section 127-0-1-1-3efeec8d:1361b13fcc8:-8000:00000000000038AE begin
		$next = wfEngine_models_classes_StepService::singleton()->getNextSteps($activity);
		$returnValue = empty($next);
        // section 127-0-1-1-3efeec8d:1361b13fcc8:-8000:00000000000038AE end

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

        // section 127-0-1-1-3efeec8d:1361b13fcc8:-8000:00000000000038B0 begin
        $returnValue = wfEngine_models_classes_ActivityService::singleton()->isInitial($activity);
        // section 127-0-1-1-3efeec8d:1361b13fcc8:-8000:00000000000038B0 end

        return (bool) $returnValue;
    }

} /* end of class wfEngine_helpers_ProcessUtil */

?>