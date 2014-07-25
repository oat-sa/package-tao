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
class wfAuthoring_helpers_Monitoring_ProcessPropertiesAdapter
    extends tao_helpers_grid_Cell_Adapter
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string rowId
     * @param  string columnId
     * @param  string data
     * @return mixed
     */
    public function getValue($rowId, $columnId, $data = null)
    {
        $returnValue = null;

        
		
		if(isset($this->data[$rowId])){
			
			//return values:
			if(isset($this->data[$rowId][$columnId])){
				$returnValue = $this->data[$rowId][$columnId];
			}
			
		}else{
		
			if(common_Utils::isUri($rowId)){
				
				$excludedProperties = (is_array($this->options) && isset($this->options['excludedProperties']))?$this->options['excludedProperties']:array();
				$processExecutionService = wfEngine_models_classes_ProcessExecutionService::singleton();
				$processInstance = new core_kernel_classes_Resource($rowId);
				$this->data[$rowId] = array();
				
				if(!in_array(RDFS_LABEL, $excludedProperties)){
					$this->data[$rowId][RDFS_LABEL] = $processInstance->getLabel();
				}
				
				if(!in_array(PROPERTY_PROCESSINSTANCES_STATUS, $excludedProperties)){
					$status = $processExecutionService->getStatus($processInstance);
					$this->data[$rowId][PROPERTY_PROCESSINSTANCES_STATUS] = is_null($status)?'n/a':$status->getLabel();
				}
				
				if(!in_array(PROPERTY_PROCESSINSTANCES_EXECUTIONOF, $excludedProperties)){
					$executionOf = $processExecutionService->getExecutionOf($processInstance);
					$this->data[$rowId][PROPERTY_PROCESSINSTANCES_EXECUTIONOF] = is_null($executionOf)?'n/a':$executionOf->getLabel();
				}
				
				if(!in_array(PROPERTY_PROCESSINSTANCES_TIME_STARTED, $excludedProperties)){
					$time = (string) $processInstance->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_PROCESSINSTANCES_TIME_STARTED));
					$this->data[$rowId][PROPERTY_PROCESSINSTANCES_TIME_STARTED] = !empty($time)?date('d-m-Y G:i:s', $time):'n/a';;
				}
				
//				if(!in_array(PROPERTY_PROCESSINSTANCES_CURRENTACTIVITYEXECUTIONS, $excludedProperties)){
//					$currentActivityExecutions = $processExecutionService->getCurrentActivityExecutions($processInstance);
//					$this->data[$rowId][PROPERTY_PROCESSINSTANCES_CURRENTACTIVITYEXECUTIONS] = new wfAuthoring_helpers_Monitoring_ActivityMonitoringGrid(array_keys($currentActivityExecutions));
//				}

				if(isset($this->data[$rowId][$columnId])){
					$returnValue = $this->data[$rowId][$columnId];
				}
			}
			
		}
        

        return $returnValue;
    }

} /* end of class wfAuthoring_helpers_Monitoring_ProcessPropertiesAdapter */

?>