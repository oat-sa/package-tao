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
 * TAO - wfAuthoring/helpers/Monitoring/class.ProcessMonitoringGrid.php
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
// section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032D7-includes begin
// section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032D7-includes end

/* user defined constants */
// section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032D7-constants begin
// section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032D7-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfAuthoring
 * @subpackage helpers_Monitoring
 */
class wfAuthoring_helpers_Monitoring_ProcessMonitoringGrid
    extends tao_helpers_grid_GridContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute processExecutions
     *
     * @access protected
     * @var array
     */
    protected $processExecutions = array();

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

        // section 127-0-1-1--521607b6:1338265e839:-8000:000000000000335C begin
		
		$excludedProperties = (is_array($this->options) && isset($this->options['excludedProperties']))?$this->options['excludedProperties']:array();
		$columnNames = (is_array($this->options) && isset($this->options['columnNames']))?$this->options['columnNames']:array();
		
		$processProperties = array(
			RDFS_LABEL => __('Label'),
			PROPERTY_PROCESSINSTANCES_STATUS => __('Status'),
			PROPERTY_PROCESSINSTANCES_EXECUTIONOF => __('Process Definition'),
			PROPERTY_PROCESSINSTANCES_TIME_STARTED => __('Started Time')
		);
		
		$propertyUris = array();
		
		foreach($processProperties as $processPropertyUri => $label){
			if(!isset($excludedProperties[$processPropertyUri])){
				$this->grid->addColumn($processPropertyUri, $label);
				$propertyUris[] = $processPropertyUri;
			}
		}
		
		$returnValue = $this->grid->setColumnsAdapter(
			$propertyUris,
			new wfAuthoring_helpers_Monitoring_ProcessPropertiesAdapter(array('excludedProperties' => $excludedProperties))
		);
		
		$this->initCurrentActivityColumn();
        // section 127-0-1-1--521607b6:1338265e839:-8000:000000000000335C end

        return (bool) $returnValue;
    }

    /**
     * Can be easily extended to adapt the current activity executions column
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return boolean
     */
    protected function initCurrentActivityColumn()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--715d45eb:13387d0ab1e:-8000:0000000000003364 begin

        /*$subGridAdapterOptions = array('excludedProperties' => $this->excludedProperties);
        if(isset($this->options['columns']) 
        	&& isset($this->options['columns'][PROPERTY_PROCESSINSTANCES_CURRENTACTIVITYEXECUTIONS])
        	&& isset($this->options['columns'][PROPERTY_PROCESSINSTANCES_CURRENTACTIVITYEXECUTIONS]['columns']))
        {
        	$subGridAdapterOptions = array_merge($subGridAdapterOptions, $this->options['columns'][PROPERTY_PROCESSINSTANCES_CURRENTACTIVITYEXECUTIONS]['columns']);	
        }*/
        
		$this->grid->addColumn(PROPERTY_PROCESSINSTANCES_CURRENTACTIVITYEXECUTIONS, __('Current Activities'));
		$returnValue = $this->grid->setColumnsAdapter(
			PROPERTY_PROCESSINSTANCES_CURRENTACTIVITYEXECUTIONS,
			new wfAuthoring_helpers_Monitoring_CurrentActivitiesAdapter()
		);	
        // section 127-0-1-1--715d45eb:13387d0ab1e:-8000:0000000000003364 end

        return (bool) $returnValue;
    }

} /* end of class wfAuthoring_helpers_Monitoring_ProcessMonitoringGrid */

?>