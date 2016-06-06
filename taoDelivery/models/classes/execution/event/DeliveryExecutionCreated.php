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

namespace oat\taoDelivery\models\classes\execution\event;

use oat\oatbox\event\Event;
use oat\taoDelivery\model\execution\DeliveryExecution;
/**
 * Event triggered whenever a new delivery execution is initialised
 * 
 * @author Joel Bout, <joel@taotesting.com>
 */
class DeliveryExecutionCreated implements Event
{
    const EVENT_NAME = __CLASS__;
    
    /**
     * (non-PHPdoc)
     * @see \oat\oatbox\event\Event::getName()
     */
    public function getName()
    {
        return self::EVENT_NAME;
    }
    
    /**
     * @var DeliveryExecution delivery execution instance
     */
    private $deliveryExecution;

    /**
     * DeliveryExecutionState constructor.
     * @param DeliveryExecution $deliveryExecution
     */
    public function __construct(DeliveryExecution $deliveryExecution)
    {
        $this->deliveryExecution = $deliveryExecution;
    }

    /**
     * Returns newly created delivery execution
     * 
     * @return DeliveryExecution
     */
    public function getDeliveryExecution()
    {
        return $this->deliveryExecution;
    }
}