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
 * Gives the activity monitoring Grid
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfAuthoring
 
 */
class wfAuthoring_helpers_Monitoring_CurrentActivitiesAdapter
    extends tao_helpers_grid_Cell_SubgridAdapter
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getSubgridRows
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string rowId
     * @return array
     */
    public function getSubgridRows($rowId)
    {
        $returnValue = array();

        
		$processExecutionService = wfEngine_models_classes_ProcessExecutionService::singleton();
		$processInstance = new core_kernel_classes_Resource($rowId);
		$currentActivityExecutions = $processExecutionService->getCurrentActivityExecutions($processInstance);
		$returnValue = array_keys($currentActivityExecutions);
        

        return (array) $returnValue;
    }

    /**
     * Short description of method initSubgridOptions
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function initSubgridOptions()
    {
        
		$this->subgridOptions = array('excludedProperties' => $this->excludedProperties);
        
    }

    /**
     * Short description of method initSubgridClass
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  subgridClass
     * @return mixed
     */
    public function initSubgridClass($subgridClass = '')
    {
        
		$this->subgridClass = 'wfAuthoring_helpers_Monitoring_ActivityMonitoringGrid';
		if(!empty($subgridClass)){
			if(class_exists($subgridClass)){
				$this->subgridClass = $subgridClass;
			}else{
				throw new common_Exception('The given subgrid class in argument is not valid : '.$subgridClass);
			}
		}
        
    }

} /* end of class wfAuthoring_helpers_Monitoring_CurrentActivitiesAdapter */

?>