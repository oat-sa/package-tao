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
 */

/**
 * Service to manage the execution of deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery

 */
class taoDelivery_models_classes_execution_ServiceProxy extends tao_models_classes_Service
    implements taoDelivery_models_classes_execution_Service
{
    const CONFIG_KEY = 'execution_service';

    /**
     * @var taoDelivery_models_classes_execution_Service
     */
    private $implementation;

    public function setImplementation(taoDelivery_models_classes_execution_Service $implementation) {
        $this->implementation = $implementation;
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
        $ext->setConfig(self::CONFIG_KEY, $implementation);
    }

    protected function getImplementation() {
        if (is_null($this->implementation)) {
            $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
            $this->implementation = $ext->getConfig(self::CONFIG_KEY);
            if (!$this->implementation instanceof taoDelivery_models_classes_execution_Service) {
                throw new common_exception_Error('No implementation for '.__CLASS__.' found');
            }
        }
        return $this->implementation;
    }

    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::getUserExecutions()
     */
    public function getUserExecutions(core_kernel_classes_Resource $assembly, $userUri) {
        return $this->getImplementation()->getUserExecutions($assembly, $userUri);
    }

    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::getDeliveryExecutionsByStatus()
     */
    public function getDeliveryExecutionsByStatus($userUri, $status) {
        return $this->getImplementation()->getDeliveryExecutionsByStatus($userUri, $status);
    }

    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::getActiveDeliveryExecutions()
     */
    public function getActiveDeliveryExecutions($userUri)
    {
        return $this->getDeliveryExecutionsByStatus($userUri, INSTANCE_DELIVERYEXEC_ACTIVE);
    }

    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::getFinishedDeliveryExecutions()
     */
    public function getFinishedDeliveryExecutions($userUri)
    {
        return $this->getDeliveryExecutionsByStatus($userUri, INSTANCE_DELIVERYEXEC_FINISHED);
    }

    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::initDeliveryExecution()
     */
    public function initDeliveryExecution(core_kernel_classes_Resource $assembly, $userUri)
    {
        return $this->getImplementation()->initDeliveryExecution($assembly, $userUri);
    }


    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::getDeliveryExecution()
     */
    public function getDeliveryExecution($identifier)
    {
        return $this->getImplementation()->getDeliveryExecution($identifier);
    }

    /**
     * Implemented in the monitoring interface
     *
     * @param core_kernel_classes_Resource $compiled
     * @return int the ammount of executions for a single compilation
     */
    public function getExecutionsByDelivery(core_kernel_classes_Resource $compiled)
    {
        if (!$this->implementsMonitoring()) {
            throw new common_exception_NoImplementation();
        }
        return $this->getImplementation()->getExecutionsByDelivery($compiled);
    }

    /**
     * Whenever or not the current implementation supports monitoring
     *
     * @return boolean
     */
    public function implementsMonitoring() {
        return $this->getImplementation() instanceof taoDelivery_models_classes_execution_Monitoring;
    }
}
