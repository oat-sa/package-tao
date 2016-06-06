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
namespace oat\taoDelivery\model;

use oat\oatbox\user\User;
/**
 * Basic Assignment object that represents the assignment
 * of a test-taker to a delivery. It is used by the assignment service
 * to determine which deliveries have been assigned to a test-taker.
 *
 * @author Open Assessment Technologies SA
 * @license GPL-2.0
 *
 */
class Assignment {
    
    private $deliveryId;
    
    private $label;
    
    private $desc;
    
    private $startable;
    
    /**
     * Simple constructor to create a new assigment object
     * 
     * @param string $deliveryId
     * @param string $userId
     * @param string $label
     * @param string[] $desc
     * @param boolean $startable
     * @param array $launchParams
     */
    public function __construct($deliveryId, $userId, $label, $desc, $startable)
    {
        $this->deliveryId = $deliveryId;
        $this->label = $label;
        $this->desc = $desc;
        $this->startable = $startable;
    }
    
    /**
     * Returns the id of the delivery to run
     * @return string
     */
    public function getDeliveryId()
    {
        return $this->deliveryId;
    }
    
    /**
     * Returns the label of the asignment, which will often correspond
     * to the label of the delivery
     * 
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }
    
    /**
     * An array of description strings to give
     * enhanced informations about the assignment
     * and its restrictions
     * 
     * @return string[]
     */
    public function getDescriptionStrings()
    {
        return $this->desc;
    }
    
    /**
     * Whenever or not the assigment is statable
     * 
     * @return boolean
     */
    public function isStartable()
    {
        return $this->startable;
    }
}
