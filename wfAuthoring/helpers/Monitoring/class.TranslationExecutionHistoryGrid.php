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
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage helpers_Monitoring
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}
/**
 * Short description of class wfAuthoring_helpers_Monitoring_ExecutionHistoryGrid
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage helpers_Monitoring
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
        // section 127-0-1-1-41d91020:13392d7ae4a:-8000:00000000000033A5 begin
		$processExecutionService = wfEngine_models_classes_ProcessExecutionService::singleton();
		$this->activityExecutions = $processExecutionService->getExecutionHistory($processExecution);
		parent::__construct($this->activityExecutions, $options);
        // section 127-0-1-1-41d91020:13392d7ae4a:-8000:00000000000033A5 end
    }

}

?>