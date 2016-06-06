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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */
namespace oat\taoDelivery\model;

use oat\oatbox\user\User;
/**
 * Service to manage the assignment of users to deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 */
interface AssignmentService
{
    const CONFIG_ID = 'taoDelivery/assignment';
     
    /**
     * Returns the deliveries availableto a user
     * 
     * @param User $user
     * @return Assignment[] list of deliveries
     */
    public function getAssignments(User $user);
    
    /**
     * Returns the ids of users assigned to a delivery
     * 
     * @param unknown $deliveryId
     * @return string[] ids of users
     */
    public function getAssignedUsers($deliveryId);
    
    /**
     * Returns whenever or not a user can take a specific delivery
     * 
     * @param string $deliveryIdentifier
     * @param User $user
     * @return boolean
     */
    public function isDeliveryExecutionAllowed($deliveryIdentifier, User $user);

    /**
     * Returns the serviecall to start the delivery
     * 
     * @param string $deliveryId
     * @return \tao_models_classes_service_ServiceCall
     */
    public function getRuntime($deliveryId);
    
    // no longer available: onDelete() please use eventManager
    
}