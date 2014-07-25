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
 * TAO - wfAuthoring/helpers/Monitoring/class.ActivityPropertiesAdapter.php
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
 * include tao_helpers_grid_Cell_Adapter
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/helpers/grid/Cell/class.Adapter.php');

/* user defined includes */
// section 127-0-1-1-6c609706:1337d294662:-8000:000000000000334E-includes begin
// section 127-0-1-1-6c609706:1337d294662:-8000:000000000000334E-includes end

/* user defined constants */
// section 127-0-1-1-6c609706:1337d294662:-8000:000000000000334E-constants begin
// section 127-0-1-1-6c609706:1337d294662:-8000:000000000000334E-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfAuthoring
 * @subpackage helpers_Monitoring
 */
class wfAuthoring_helpers_Monitoring_ActivityPropertiesAdapter
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

        // section 127-0-1-1-6c609706:1337d294662:-8000:000000000000334F begin
		if (isset($this->data[$rowId])) {

			//return values:
			if (isset($this->data[$rowId][$columnId])) {
				$returnValue = $this->data[$rowId][$columnId];
			}
			
		} else {

//			'PROPERTY_ACTIVITY_EXECUTION_CTX_RECOVERY' => NS_WFENGINE . '#PropertyActivityExecutionsContextRecovery',
//			'PROPERTY_ACTIVITY_EXECUTION_VARIABLES' => NS_WFENGINE .'#PropertyActivityExecutionsHasVariables',
//			'PROPERTY_ACTIVITY_EXECUTION_PREVIOUS' => NS_WFENGINE .'#PropertyActivityExecutionsPreviousActivityExecutions',
//			'PROPERTY_ACTIVITY_EXECUTION_FOLLOWING' => NS_WFENGINE .'#PropertyActivityExecutionsFollowingActivityExecutions',
//			'PROPERTY_ACTIVITY_EXECUTION_NONCE' => NS_WFENGINE . '#PropertyActivityExecutionsNonce',

			if (common_Utils::isUri($rowId)) {

				$excludedProperties = $this->excludedProperties;
				$activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();
				$activityExecution = new core_kernel_classes_Resource($rowId);
                $status = $activityExecutionService->getStatus($activityExecution);
				
				$this->data[$rowId] = array();

				if (!in_array(PROPERTY_ACTIVITY_EXECUTION_ACTIVITY, $excludedProperties)) {
					$activityExecutionOf = $activityExecutionService->getExecutionOf($activityExecution);
					$this->data[$rowId][PROPERTY_ACTIVITY_EXECUTION_ACTIVITY] = $activityExecutionOf->getLabel();
				}

				if (!in_array(PROPERTY_ACTIVITY_EXECUTION_STATUS, $excludedProperties)) {
					$this->data[$rowId][PROPERTY_ACTIVITY_EXECUTION_STATUS] = is_null($status) ? null : $status->getLabel();
				}
				
				$timeProperties = array(
					PROPERTY_ACTIVITY_EXECUTION_TIME_CREATED,
					PROPERTY_ACTIVITY_EXECUTION_TIME_STARTED,
					PROPERTY_ACTIVITY_EXECUTION_TIME_LASTACCESS
				);
				foreach($timeProperties as $timeProperty){
					if (!in_array($timeProperty, $excludedProperties)) {
						$time = (string) $activityExecution->getOnePropertyValue(new core_kernel_classes_Property($timeProperty));
						$this->data[$rowId][$timeProperty] = !empty($time)?date('d-m-Y G:i:s', $time):'n/a';
					}
				}
				
				if (!in_array(PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER, $excludedProperties)){
					$user = $activityExecutionService->getActivityExecutionUser($activityExecution);
					$this->data[$rowId][PROPERTY_ACTIVITY_EXECUTION_CURRENT_USER] = (is_null($user))?'n/a':$user->getLabel();
				}
				
				if (!in_array(PROPERTY_ACTIVITY_EXECUTION_ACL_MODE, $excludedProperties)){
					$aclMode = $activityExecutionService->getAclMode($activityExecution);
					$this->data[$rowId][PROPERTY_ACTIVITY_EXECUTION_ACL_MODE] = (is_null($aclMode))?'n/a':$aclMode->getLabel();
				}
				
				if (!in_array(PROPERTY_ACTIVITY_EXECUTION_RESTRICTED_USER, $excludedProperties)){
					$restricedRole = $activityExecutionService->getRestrictedRole($activityExecution);
					$restrictedTo = !is_null($restricedRole) ? $restricedRole : $activityExecutionService->getRestrictedUser($activityExecution);
					$this->data[$rowId][PROPERTY_ACTIVITY_EXECUTION_RESTRICTED_USER] = (is_null($restrictedTo))?'n/a':$restrictedTo->getLabel();
				}
				
				if (!in_array(PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION, $excludedProperties)){
					$processExecution = $activityExecutionService->getRelatedProcessExecution($activityExecution);
					$this->data[$rowId][PROPERTY_ACTIVITY_EXECUTION_PROCESSEXECUTION] = (is_null($processExecution))?'n/a':$processExecution->getUri();
				}
				
				if (!in_array('runnable', $excludedProperties)){
                    $runnable = false;
                    $crtUser = wfEngine_models_classes_UserService::singleton()->getCurrentUser();
                    if(!is_null($crtUser)){
                        /**
                         * @todo the null status should not exist
                         * @see Sam when a change will occur
                         */
                        $runnable = is_null($status) 
                            || ($status->getUri() != INSTANCE_PROCESSSTATUS_FINISHED
                            && $status->getUri() != INSTANCE_PROCESSSTATUS_CLOSED
                            && $status->getUri() != INSTANCE_PROCESSSTATUS_STOPPED
                            && $activityExecutionService->checkAcl($activityExecution, $crtUser));
                    }
                    $this->data[$rowId]['runnable'] = $runnable;
                }
                
				if (isset($this->data[$rowId][$columnId])) {
					$returnValue = $this->data[$rowId][$columnId];
				}
			}
		}
        // section 127-0-1-1-6c609706:1337d294662:-8000:000000000000334F end

        return $returnValue;
    }

} /* end of class wfAuthoring_helpers_Monitoring_ActivityPropertiesAdapter */

?>