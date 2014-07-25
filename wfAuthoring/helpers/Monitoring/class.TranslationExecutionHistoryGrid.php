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
 * Short description of class wfAuthoring_helpers_Monitoring_ExecutionHistoryGrid
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 
 */
class wfAuthoring_helpers_Monitoring_TranslationExecutionHistoryGrid
    extends wfAuthoring_helpers_Monitoring_TranslationActivityMonitoringGrid
{
    /**
     * Short description of method __construct
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource processExecution
     * @param  array options
     * @return mixed
     */
    public function __construct( core_kernel_classes_Resource $processExecution, $options = array())
    {
        
		$processExecutionService = wfEngine_models_classes_ProcessExecutionService::singleton();
		$this->activityExecutions = $processExecutionService->getExecutionHistory($processExecution);
		parent::__construct($this->activityExecutions, $options);
        
    }

}

?>