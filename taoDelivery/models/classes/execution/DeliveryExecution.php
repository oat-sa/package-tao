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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoDelivery\models\classes\execution;

use oat\oatbox\service\ServiceManager;
use oat\oatbox\event\EventManager;
use oat\oatbox\event\Event;
use oat\taoDelivery\models\classes\execution\event\DeliveryExecutionState;
use \common_Logger;
use oat\taoDelivery\model\execution\DeliveryExecution as DeliveryExecutionInterface;

class DeliveryExecution implements DeliveryExecutionInterface
{

    /**
     * @var \taoDelivery_models_classes_execution_DeliveryExecution
     */
    private $implementation;

    public function __construct(\taoDelivery_models_classes_execution_DeliveryExecution $implementation)
    {
        $this->setImplementation($implementation);
    }

    public function setImplementation(\taoDelivery_models_classes_execution_DeliveryExecution $implementation) {
        $this->implementation = $implementation;
    }

    /**
     * @return \taoDelivery_models_classes_execution_DeliveryExecution
     */
    public function getImplementation() {
        return $this->implementation;
    }

    /**
     * Returns the identifier of the delivery execution
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->getImplementation()->getIdentifier();
    }

    /**
     * Returns a human readable test representation of the delivery execution
     * Should respect the current user's language
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->getImplementation()->getLabel();
    }

    /**
     * Returns when the delivery execution was started
     */
    public function getStartTime()
    {
        return $this->getImplementation()->getStartTime();
    }

    /**
     * Returns when the delivery execution was finished
     * or null if not yet finished
     */
    public function getFinishTime()
    {
        return $this->getImplementation()->getFinishTime();
    }

    /**
     * Returns the delivery execution state as resource
     */
    public function getState()
    {
        return $this->getImplementation()->getState();
    }

    /**
     *
     * @param string $state
     * @return boolean success
     */
    public function setState($state)
    {
        $result = $this->getImplementation()->setState($state);
        $prevState = $this->getState();

        $event = new DeliveryExecutionState($this, $state, $prevState);
        $this->triggerEvent($event);
        common_Logger::i("DeliveryExecutionState Event triggered.");

        return $result;
    }

    /**
     * Returns the delivery execution delivery as resource
     *
     * @return core_kernel_classes_Resource
     */
    public function getDelivery()
    {
        return $this->getImplementation()->getDelivery();
    }

    /**
     * Returns the delivery executions user identifier
     *
     * @return string
     */
    public function getUserIdentifier()
    {
        return $this->getImplementation()->getUserIdentifier();
    }

    /**
     * @param Event $event event to be triggered.
     */
    protected function triggerEvent(Event $event)
    {
        $eventManager = ServiceManager::getServiceManager()->get(EventManager::CONFIG_ID);
        $eventManager->trigger($event);
    }

    /**
     * Calls the named method which is not a class method.
     * Do not call this method.
     * @param string $name the method name
     * @param array $parameters method parameters
     * @return mixed the method return value
     */
    public function __call($name, $parameters)
    {
        return call_user_func_array(array($this->getImplementation(), $name), $parameters);
    }
}
