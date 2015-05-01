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

use oat\taoGroups\models\GroupsService;
use oat\oatbox\user\User;

/**
 * Service to manage the assignment of users to deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 */
class taoDelivery_models_classes_AssignmentService extends tao_models_classes_GenerisService
{
    public function getAvailableDeliveries(User $user)
    {
        // check if realy available
        $deliveryUris = array();
        foreach (GroupsService::singleton()->getGroups($user) as $group) {
            foreach ($group->getPropertyValues(new core_kernel_classes_Property(PROPERTY_GROUP_DELVIERY)) as $deliveryUri) {
                $candidate = new core_kernel_classes_Resource($deliveryUri);
                if (!$this->isUserExcluded($candidate, $user) && $candidate->exists()) {
                    $deliveryUris[] = $candidate->getUri();
                }
            }
        }
        return array_unique($deliveryUris);
    }

    /**
     * 
     * @param core_kernel_classes_Resource $delivery
     * @return array identifiers of the users
     */
    public function getAssignedUsers(core_kernel_classes_Resource $delivery)
    {
        $groupClass = GroupsService::singleton()->getRootClass();
        $groups = $groupClass->searchInstances(array(
            PROPERTY_GROUP_DELVIERY => $delivery->getUri()
        ), array('recursive' => true, 'like' => false));
        
        $users = array();
        foreach ($groups as $group) {
            foreach (GroupsService::singleton()->getUsers($group) as $user) {
                $users[] = $user->getUri();
            }
        }
        return array_unique($users);
    }
    
    public function isUserAssigned(core_kernel_classes_Resource $delivery, User $user){
        $userGroups = GroupsService::singleton()->getGroups($user);
        $deliveryGroups = GroupsService::singleton()->getRootClass()->searchInstances(array(
            PROPERTY_GROUP_DELVIERY => $delivery->getUri()
        ), array(
            'like'=>false, 'recursive' => true
        ));
        return count(array_intersect($userGroups, $deliveryGroups)) > 0 && !$this->isUserExcluded($delivery, $user);
    }
    
    public function onDelete(core_kernel_classes_Resource $delivery)
    {
        $groupClass = GroupsService::singleton()->getRootClass();
        $assigned = $groupClass->searchInstances(array(
            PROPERTY_GROUP_DELVIERY => $delivery
        ), array('like' => false, 'recursive' => true));
        
        $assignationProperty = new core_kernel_classes_Property(PROPERTY_GROUP_DELVIERY);
        foreach ($assigned as $groupInstance) {
            $groupInstance->removePropertyValue($assignationProperty, $delivery);
        }
    }
    
    /**
     * Check if a user is excluded from a delivery
     * @param core_kernel_classes_Resource $delivery
     * @param string $userUri the URI of the user to check
     * @return boolean true if excluded
     */
    private function isUserExcluded(core_kernel_classes_Resource $delivery, User $user){
        $excludedUsers = $delivery->getPropertyValues(new core_kernel_classes_Property(TAO_DELIVERY_EXCLUDEDSUBJECTS_PROP));
        return in_array($user->getIdentifier(), $excludedUsers);
    }
}