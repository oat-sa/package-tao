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
 * TAO - wfAuthoring/helpers/Monitoring/class.VariablesAdapter.php
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
 * include tao_helpers_grid_Cell_SubgridAdapter
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/helpers/grid/Cell/class.SubgridAdapter.php');

/* user defined includes */
// section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:00000000000033F4-includes begin
// section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:00000000000033F4-includes end

/* user defined constants */
// section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:00000000000033F4-constants begin
// section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:00000000000033F4-constants end

/**
 * Short description of class wfAuthoring_helpers_Monitoring_VariablesAdapter
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfAuthoring
 * @subpackage helpers_Monitoring
 */
class wfAuthoring_helpers_Monitoring_VariablesAdapter
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

        // section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:00000000000033F8 begin
		
		$activityExecution = new core_kernel_classes_Resource($rowId);
		$activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();
		$variables = $activityExecutionService->getVariables($activityExecution);
		foreach($variables as $variableData){
			$returnValue[$variableData['propertyUri']] = $variableData;
		}
		
        // section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:00000000000033F8 end

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
        // section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:00000000000033FB begin
		$this->subgridOptions = array('excludedProperties' => $this->excludedProperties);
        // section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:00000000000033FB end
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
        // section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:00000000000033FE begin
		$this->subgridClass = 'wfAuthoring_helpers_Monitoring_ActivityVariablesGrid';
        // section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:00000000000033FE end
    }

} /* end of class wfAuthoring_helpers_Monitoring_VariablesAdapter */

?>