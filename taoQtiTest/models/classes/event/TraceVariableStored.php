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

namespace oat\taoQtiTest\models\event;

use oat\oatbox\event\Event;
use oat\taoDelivery\model\execution\DeliveryExecution;
/**
* Event should be triggered after storing test trace variable
*
*/
class TraceVariableStored implements Event
{

    private $deliveryExecutionId;

    private $deliveryExecution;

    /**
     * DeliveryExecutionState constructor.
     * @param $deliveryExecutionId
     */
    public function __construct($deliveryExecutionId)
    {
        $this->deliveryExecutionId = $deliveryExecutionId;
    }

    /**
     * @return DeliveryExecution
     */
    public function getDeliveryExecution()
    {
        if(is_null($this->deliveryExecution)){
            $this->deliveryExecution = \taoDelivery_models_classes_execution_ServiceProxy::singleton()->getDeliveryExecution($this->deliveryExecutionId);
        }
        return $this->deliveryExecution;
    }

    /**
     * @return string
     */
    public function getState()
    {
        $deliveryExecution = $this->getDeliveryExecution();
        return $deliveryExecution->getState()->getUri();
    }


    /**
     * @return string
     */
    public function getName()
    {
        return __CLASS__;
    }

}