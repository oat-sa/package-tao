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
 * Gives the activity monitoring Grid
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfAuthoring
 * @subpackage helpers_Monitoring
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_helpers_grid_Cell_SubgridAdapter
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/helpers/grid/Cell/class.SubgridAdapter.php');

/* user defined includes */
// section 127-0-1-1--715d45eb:13387d0ab1e:-8000:0000000000003355-includes begin
// section 127-0-1-1--715d45eb:13387d0ab1e:-8000:0000000000003355-includes end

/* user defined constants */
// section 127-0-1-1--715d45eb:13387d0ab1e:-8000:0000000000003355-constants begin
// section 127-0-1-1--715d45eb:13387d0ab1e:-8000:0000000000003355-constants end

/**
 * Gives the activity monitoring Grid
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfAuthoring
 * @subpackage helpers_Monitoring
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

        // section 127-0-1-1-72bb438:1338cba5f73:-8000:000000000000339D begin
		$processExecutionService = wfEngine_models_classes_ProcessExecutionService::singleton();
		$processInstance = new core_kernel_classes_Resource($rowId);
		$currentActivityExecutions = $processExecutionService->getCurrentActivityExecutions($processInstance);
		$returnValue = array_keys($currentActivityExecutions);
        // section 127-0-1-1-72bb438:1338cba5f73:-8000:000000000000339D end

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
        // section 127-0-1-1-72bb438:1338cba5f73:-8000:000000000000339F begin
		$this->subgridOptions = array('excludedProperties' => $this->excludedProperties);
        // section 127-0-1-1-72bb438:1338cba5f73:-8000:000000000000339F end
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
        // section 127-0-1-1-72bb438:1338cba5f73:-8000:00000000000033A1 begin
		$this->subgridClass = 'wfAuthoring_helpers_Monitoring_ActivityMonitoringGrid';
		if(!empty($subgridClass)){
			if(class_exists($subgridClass)){
				$this->subgridClass = $subgridClass;
			}else{
				throw new common_Exception('The given subgrid class in argument is not valid : '.$subgridClass);
			}
		}
        // section 127-0-1-1-72bb438:1338cba5f73:-8000:00000000000033A1 end
    }

} /* end of class wfAuthoring_helpers_Monitoring_CurrentActivitiesAdapter */

?>