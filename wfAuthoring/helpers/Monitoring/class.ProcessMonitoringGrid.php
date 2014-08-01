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
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfAuthoring
 
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
        

        return (bool) $returnValue;
    }

} /* end of class wfAuthoring_helpers_Monitoring_ProcessMonitoringGrid */

?>