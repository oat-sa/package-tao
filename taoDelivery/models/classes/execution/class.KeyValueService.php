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

use oat\oatbox\Configurable;
/**
 * Service to manage the execution of deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 */
class taoDelivery_models_classes_execution_KeyValueService extends Configurable
    implements taoDelivery_models_classes_execution_Service
{
    const OPTION_PERSISTENCE = 'persistence';

    const DELIVERY_EXECUTION_PREFIX = 'kve_de_';

    const USER_EXECUTIONS_PREFIX = 'kve_ue_';

    /**
     * @var common_persistence_KeyValuePersistence
     */
    private $persistence;

    protected function getPersistence()
    {
        if (is_null($this->persistence)) {
            $persistenceOption = $this->getOption(self::OPTION_PERSISTENCE);
            $this->persistence = (is_object($persistenceOption))
                ? $persistenceOption
                : common_persistence_KeyValuePersistence::getPersistence($persistenceOption);
        }
        return $this->persistence;
    }

    public function getUserExecutions(core_kernel_classes_Resource $compiled, $userUri)
    {
        $activ = $this->getDeliveryExecutionsByStatus($userUri, INSTANCE_DELIVERYEXEC_ACTIVE);
        $finished = $this->getDeliveryExecutionsByStatus($userUri, INSTANCE_DELIVERYEXEC_FINISHED);

        $returnValue = array();
        foreach (array_merge($activ, $finished) as $de) {
            if ($compiled->equals($de->getDelivery())) {
                $returnValue[] = $de;
            }
        }
        return $returnValue;
    }

    public function getDeliveryExecutionsByStatus($userUri, $status) {
        $returnValue = array();
        $data = $this->getPersistence()->get(self::USER_EXECUTIONS_PREFIX.$userUri.$status);
        $keys = $data !== false ? json_decode($data) : array();
        if (is_array($keys)) {
            foreach ($keys as $key) {
                $returnValue[$key] = $this->getDeliveryExecution($key);
            }
        } else {
            common_Logger::w('Non array "'.gettype($keys).'" received as active Delivery Keys for user '.$userUri);
        }

        return $returnValue;
    }

    /**
     * Generate a new delivery execution
     *
     * @param core_kernel_classes_Resource $assembly
     * @param string $userUri
     * @return core_kernel_classes_Resource the delivery execution
     */
    public function initDeliveryExecution(core_kernel_classes_Resource $assembly, $userUri)
    {
        $deliveryExecution = taoDelivery_models_classes_execution_KVDeliveryExecution::spawn($this->getPersistence(), $userUri, $assembly);

        $this->updateDeliveryExecutionStatus($deliveryExecution, null, INSTANCE_DELIVERYEXEC_ACTIVE);

        return $deliveryExecution;
    }

    public function getDeliveryExecution($identifier) {
        return new taoDelivery_models_classes_execution_KVDeliveryExecution($this->getPersistence(), $identifier);
    }

    /**
     * Update the collection of deliveries
     *
     * @param taoDelivery_models_classes_execution_KVDeliveryExecution $deliveryExecution
     * @param string $old
     * @param string $new
     */
    public function updateDeliveryExecutionStatus(taoDelivery_models_classes_execution_KVDeliveryExecution $deliveryExecution, $old, $new) {

        $userId = $deliveryExecution->getUserIdentifier();
        if ($old != null) {
            $oldReferences = $this->getDeliveryExecutionsByStatus($userId, $old);
            foreach (array_keys($oldReferences) as $key) {
                if ($oldReferences[$key]->getIdentifier() == $deliveryExecution->getIdentifier()) {
                    unset($oldReferences[$key]);
                }
            }
            $this->setDeliveryExecutions($userId, $old, $oldReferences);
        }

        $newReferences = $this->getDeliveryExecutionsByStatus($userId, $new);
        $newReferences[] = $deliveryExecution;
        $this->setDeliveryExecutions($userId, $new, $newReferences);

    }

    public function getData($deliveryExecutionId) {
        $dataString = $this->getPersistence()->get($deliveryExecutionId);
        $data = json_decode($dataString, true);
        return $data;
    }

    private function setDeliveryExecutions($userUri, $status, $executions)
    {
        $keys = array();
        foreach ($executions as $execution) {
            $keys[] = $execution->getIdentifier();
        }
        return $this->getPersistence()->set(self::USER_EXECUTIONS_PREFIX.$userUri.$status, json_encode($keys));
    }
}
