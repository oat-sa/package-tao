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
 * TAO - wfAuthoring/helpers/Monitoring/class.ActivityVariablesGrid.php
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
// section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:0000000000003403-includes begin
// section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:0000000000003403-includes end

/* user defined constants */
// section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:0000000000003403-constants begin
// section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:0000000000003403-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfAuthoring
 * @subpackage helpers_Monitoring
 */
class wfAuthoring_helpers_Monitoring_ActivityVariablesGrid
    extends tao_helpers_grid_GridContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method initGrid
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return boolean
     */
    public function initGrid()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:0000000000003406 begin
		
		if(is_array($this->data)){
			
			parent::initGrid();
			
		}else if($this->data instanceof core_kernel_classes_Resource && $this->data->hasType(new core_kernel_classes_Class(CLASS_ACTIVITY_EXECUTION))){
			
			$activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();
			$variables = $activityExecutionService->getVariables($this->data);
			foreach ($variables as $variableData) {
				$returnValue[$variableData['propertyUri']] = $variableData;
			}
			
		}else{
			throw new common_Exception('the data is not an array of variables, nor an activity execution resource');
		}
		
        // section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:0000000000003406 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method initColumns
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return boolean
     */
    public function initColumns()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:000000000000340C begin
		
		$this->grid->addColumn('code', __('Code'));
		$this->grid->addColumn('value', __('Value'));
		
        // section 127-0-1-1--5e069f0e:133a7dcfc6a:-8000:000000000000340C end

        return (bool) $returnValue;
    }

} /* end of class wfAuthoring_helpers_Monitoring_ActivityVariablesGrid */

?>