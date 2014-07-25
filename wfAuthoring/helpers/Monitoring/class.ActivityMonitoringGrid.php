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
 * TAO - wfAuthoring/helpers/Monitoring/class.ActivityMonitoringGrid.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 29.10.2012, 09:08:10 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfAuthoring
 * @subpackage helpers_Monitoring
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_helpers_grid_GridContainer
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/helpers/grid/class.GridContainer.php');

/* user defined includes */
// section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003359-includes begin
// section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003359-includes end

/* user defined constants */
// section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003359-constants begin
// section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003359-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfAuthoring
 * @subpackage helpers_Monitoring
 */
class wfAuthoring_helpers_Monitoring_ActivityMonitoringGrid
    extends tao_helpers_grid_GridContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute activityExecutions
     *
     * @access protected
     * @var array
     */
    protected $activityExecutions = array();

    // --- OPERATIONS ---

    /**
     * Short description of method initColumns
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return boolean
     */
    protected function initColumns()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--521607b6:1338265e839:-8000:000000000000335E begin
		$excludedProperties = (is_array($this->options) && isset($this->options['excludedProperties']))?$this->options['excludedProperties']:array();
		$columnNames = (is_array($this->options) && isset($this->options['columnNames']))?$this->options['columnNames']:array();
		
		$activityProperties = array(
			PROPERTY_ACTIVITY_EXECUTION_ACTIVITY => __('Label'),
			PROPERTY_ACTIVITY_EXECUTION_STATUS => __('Status'),
			PROPERTY_ACTIVITY_EXECUTION_TIME_CREATED => __('Process Definition'),
			PROPERTY_ACTIVITY_EXECUTION_TIME_STARTED => __('Current Activities'),
			PROPERTY_ACTIVITY_EXECUTION_TIME_LASTACCESS => __('Started Time'),
			PROPERTY_ACTIVITY_EXECUTION_ACL_MODE => __('Access Control Mode'),
			PROPERTY_ACTIVITY_EXECUTION_RESTRICTED_USER => __('Restricted to'),
			PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER => __('Current User'),
			PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION => __('Process Execution')
		);
		
		$propertyUris = array();
		
		foreach($activityProperties as $activityPropertyUri => $label){
			if(!isset($excludedProperties[$activityPropertyUri])){
				if(isset($columnNames[$activityPropertyUri]) && !empty($columnNames[$activityPropertyUri])){
					$label = $columnNames[$activityPropertyUri];
				}
				$this->grid->addColumn($activityPropertyUri, $label);
				$propertyUris[] = $activityPropertyUri;
			}
		}
		
		$returnValue = $this->grid->setColumnsAdapter(
			$propertyUris,
			new wfAuthoring_helpers_Monitoring_ActivityPropertiesAdapter(array('excludedProperties' => $excludedProperties))
		);
		
		$this->grid->addColumn('variables', __('Variables'));
		$returnValue = $this->grid->setColumnsAdapter('variables', new wfAuthoring_helpers_Monitoring_VariablesAdapter());
		
        // section 127-0-1-1--521607b6:1338265e839:-8000:000000000000335E end

        return (bool) $returnValue;
    }

} /* end of class wfAuthoring_helpers_Monitoring_ActivityMonitoringGrid */

?>