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
 * Connector Services
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfEngine
 
 */
class wfEngine_models_classes_ConnectorService
    extends wfEngine_models_classes_StepService
        implements tao_models_classes_ServiceCacheInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method setCache
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string methodName
     * @param  array args
     * @param  array value
     * @return boolean
     */
    public function setCache($methodName, $args = array(), $value = array())
    {
        $returnValue = (bool) false;

        
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method getCache
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string methodName
     * @param  array args
     * @return mixed
     */
    public function getCache($methodName, $args = array())
    {
        $returnValue = null;

        
        

        return $returnValue;
    }

    /**
     * Short description of method clearCache
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string methodName
     * @param  array args
     * @return boolean
     */
    public function clearCache($methodName = '', $args = array())
    {
        $returnValue = (bool) false;

        
        

        return (bool) $returnValue;
    }

    /**
     * Check if the resource is a connector
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource connector
     * @return boolean
     */
    public function isConnector( core_kernel_classes_Resource $connector)
    {
        $returnValue = (bool) false;

        
        if(!is_null($connector)){
            $returnValue = $connector->hasType( new core_kernel_classes_Class(CLASS_CONNECTORS));
        }
        

        return (bool) $returnValue;
    }

    /**
     * retrieve connector nexts activities
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource connector
     * @param  boolean followCardinalities
     * @return array
     */
    public function getNextActivities( core_kernel_classes_Resource $connector, $followCardinalities = false)
    {
        $returnValue = array();

        
        foreach ($this->getNextSteps($connector) as $next) {
        	$returnValue[$next->getUri()] = $next;
        }
        

        return (array) $returnValue;
    }

    /**
     * retrieve connector previous activities
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource connector
     * @param  boolean followCardinalities
     * @return array
     */
    public function getPreviousActivities( core_kernel_classes_Resource $connector, $followCardinalities = false)
    {
        $returnValue = array();

        
        $steps = $this->getPreviousSteps($connector);
        if (!$followCardinalities) {
        	$returnValue = $steps;
        } else { 
        	$cardService = wfEngine_models_classes_ActivityCardinalityService::singleton();
        	foreach ($steps as $cand) {
        		if ($cardService->isCardinality($cand)) {
        			// no recursion nescessary since cardinalities cannnot follow each other
        			foreach ($this->getPreviousSteps($cand) as $orig) {
        				$returnValue[] = $orig;
        			}
        		} else {
        			$returnValue[] = $cand;
        		}
        	}
        }
        

        return (array) $returnValue;
    }

    /**
     * retrive type of Connector Conditionnal, Sequestionnal Parallele...
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource connector
     * @return core_kernel_classes_Resource
     */
    public function getType( core_kernel_classes_Resource $connector)
    {
        $returnValue = null;

        
       	$connTypeProp = new core_kernel_classes_Property(PROPERTY_CONNECTORS_TYPE);
       	try{
       	    $returnValue = $connector->getUniquePropertyValue($connTypeProp);
       	}
       	catch (common_Exception $e) {
			throw new wfEngine_models_classes_ProcessDefinitonException('Exception when retreiving connector type ' . $connector->getUri());
       	}
        

        return $returnValue;
    }

    /**
     * Short description of method getTransitionRule
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource connector
     * @return core_kernel_classes_Resource
     */
    public function getTransitionRule( core_kernel_classes_Resource $connector)
    {
        $returnValue = null;

        
        $ruleProp = new core_kernel_classes_Property(PROPERTY_CONNECTORS_TRANSITIONRULE);
        $returnValue = $connector->getOnePropertyValue($ruleProp);
        

        return $returnValue;
    }

    /**
     * Short description of method getAllConnectorTypes
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getAllConnectorTypes()
    {
        $returnValue = array();

        
        throw new common_exception_Error(__METHOD__.' not yet implemented');
        

        return (array) $returnValue;
    }

} /* end of class wfEngine_models_classes_ConnectorService */

?>