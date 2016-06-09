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
namespace oat\taoDeliveryRdf\model;

use oat\taoGroups\models\GroupsService;
use oat\oatbox\user\User;
use oat\oatbox\service\ServiceManager;
use oat\oatbox\service\ConfigurableService;
use oat\taoDelivery\model\SimpleDelivery;
use core_kernel_classes_Resource;
use core_kernel_classes_Class;
use \core_kernel_classes_Property;
use oat\taoDelivery\model\AssignmentService;
use oat\taoDeliveryRdf\model\guest\GuestTestUser;
/**
 * Service to manage the assignment of users to deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 */
class GroupAssignment extends ConfigurableService implements AssignmentService
{
    /**
     * Interface part
     */
    
    /**
     * (non-PHPdoc)
     * @see \oat\taoDelivery\model\AssignmentService::getAssignments()
     */
    public function getAssignments(User $user)
    {
        $assignments = array();
        foreach ($this->getAssignmentFactories($user) as $factory) {
            $assignments[] = $factory->toAssignment();
        }
        
        return $this->orderAssignments($assignments);
    }
    
    public function getAssignmentFactories(User $user)
    {
        $assignments = array();
        
        //$assignmentFactory = new AssignmentFactory();
        if ($this->isDeliveryGuestUser($user)) {
            foreach ($this->getGuestAccessDeliveries() as $id) {
                $delivery = new \core_kernel_classes_Resource($id);
                $startable = $this->verifyTime($delivery);
                $assignments[] = new AssignmentFactory($delivery, $user, $startable);
            }
        } else {
            foreach ($this->getDeliveryIdsByUser($user) as $id) {
                $delivery = new \core_kernel_classes_Resource($id);
                $startable = $this->verifyTime($delivery) && $this->verifyToken($delivery, $user);
                $assignments[] = new AssignmentFactory($delivery, $user, $startable);
            }
        }
        return $assignments;
    }

    /**
     * (non-PHPdoc)
     * @see \oat\taoDelivery\model\AssignmentService::getRuntime()
     */
    public function getRuntime($deliveryId)
    {
        $delivery = new \core_kernel_classes_Resource($deliveryId);
        return DeliveryAssemblyService::singleton()->getRuntime($delivery);
    }
    
    
    /**
     * 
     * @param string $deliveryId
     * @return array identifiers of the users
     */
    public function getAssignedUsers($deliveryId)
    {
        $groupClass = GroupsService::singleton()->getRootClass();
        $groups = $groupClass->searchInstances(array(
            PROPERTY_GROUP_DELVIERY => $deliveryId
        ), array('recursive' => true, 'like' => false));
        
        $users = array();
        foreach ($groups as $group) {
            foreach (GroupsService::singleton()->getUsers($group) as $user) {
                $users[] = $user->getUri();
            }
        }
        return array_unique($users);
    }
    
    /**
     * Helpers
     */
    
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
    
    public function getDeliveryIdsByUser(User $user)
    {
        $deliveryUris = array();
        // check if really available
        foreach (GroupsService::singleton()->getGroups($user) as $group) {
            foreach ($group->getPropertyValues(new \core_kernel_classes_Property(PROPERTY_GROUP_DELVIERY)) as $deliveryUri) {
                $candidate = new core_kernel_classes_Resource($deliveryUri);
                if (!$this->isUserExcluded($candidate, $user) && $candidate->exists()) {
                    $deliveryUris[] = $candidate->getUri();
                }
            }
        }
        return array_unique($deliveryUris);
    }
    
    /**
     * Check if a user is excluded from a delivery
     * @param core_kernel_classes_Resource $delivery
     * @param string $userUri the URI of the user to check
     * @return boolean true if excluded
     */
    protected function isUserExcluded(\core_kernel_classes_Resource $delivery, User $user){
        $excludedUsers = $delivery->getPropertyValues(new \core_kernel_classes_Property(TAO_DELIVERY_EXCLUDEDSUBJECTS_PROP));
        return in_array($user->getIdentifier(), $excludedUsers);
    }

    /**
     * Search for deliveries configured for guest access
     *
     * @return array
     */
    public function getGuestAccessDeliveries()
    {
        $class = new core_kernel_classes_Class(CLASS_COMPILEDDELIVERY);

        return $class->searchInstances(
            array(
                TAO_DELIVERY_ACCESS_SETTINGS_PROP => DELIVERY_GUEST_ACCESS
            ),
            array('recursive' => true)
        );
    }

    /**
     * Check if current user is guest
     *
     * @param User $user
     * @return bool
     */
    public function isDeliveryGuestUser(User $user)
    {
        return ($user instanceof GuestTestUser);
    }

    public function isDeliveryExecutionAllowed($deliveryIdentifier, User $user)
    {
        $delivery = new \core_kernel_classes_Resource($deliveryIdentifier);
        return $this->verifyUserAssigned($delivery, $user)
            && $this->verifyTime($delivery)
            && $this->verifyToken($delivery, $user);
    }
    
    protected function verifyUserAssigned(core_kernel_classes_Resource $delivery, User $user){
        $returnValue = false;
    
        //check for guest access mode
        if($this->isDeliveryGuestUser($user) && $this->hasDeliveryGuestAccess($delivery)){
            $returnValue = true;
        } else {
            $userGroups = GroupsService::singleton()->getGroups($user);
            $deliveryGroups = GroupsService::singleton()->getRootClass()->searchInstances(array(
                PROPERTY_GROUP_DELVIERY => $delivery->getUri()
            ), array(
                'like'=>false, 'recursive' => true
            ));
            $returnValue = count(array_intersect($userGroups, $deliveryGroups)) > 0 && !$this->isUserExcluded($delivery, $user);
        }
    
        return $returnValue;
    }
    
    /**
     * Check if delivery configured for guest access
     *
     * @param core_kernel_classes_Resource $delivery
     * @return bool
     * @throws common_exception_InvalidArgumentType
     */
    protected function hasDeliveryGuestAccess(core_kernel_classes_Resource $delivery )
    {
        $returnValue = false;
    
        $properties = $delivery->getPropertiesValues(array(
            new core_kernel_classes_Property(TAO_DELIVERY_ACCESS_SETTINGS_PROP),
        ));
        $propAccessSettings = current($properties[TAO_DELIVERY_ACCESS_SETTINGS_PROP]);
        $accessSetting = (!(is_object($propAccessSettings)) or ($propAccessSettings=="")) ? null : $propAccessSettings->getUri();
    
        if( !is_null($accessSetting) ){
            $returnValue = ($accessSetting === DELIVERY_GUEST_ACCESS);
        }
    
        return $returnValue;
    }
    
    protected function verifyToken(core_kernel_classes_Resource $delivery, User $user)
    {
        $propMaxExec = $delivery->getOnePropertyValue(new \core_kernel_classes_Property(TAO_DELIVERY_MAXEXEC_PROP));
        $maxExec = is_null($propMaxExec) ? 0 : $propMaxExec->literal;
        
        //check Tokens
        $usedTokens = count(\taoDelivery_models_classes_execution_ServiceProxy::singleton()->getUserExecutions($delivery, $user->getIdentifier()));
    
        if (($maxExec != 0) && ($usedTokens >= $maxExec)) {
            \common_Logger::d("Attempt to start the compiled delivery ".$delivery->getUri(). "without tokens");
            return false;
        }
        return true;
    }
    
    protected function verifyTime(core_kernel_classes_Resource $delivery)
    {
        $deliveryProps = $delivery->getPropertiesValues(array(
            TAO_DELIVERY_START_PROP,
            TAO_DELIVERY_END_PROP,
        ));
        
        $startExec = empty($deliveryProps[TAO_DELIVERY_START_PROP])
            ? null
            : (string)current($deliveryProps[TAO_DELIVERY_START_PROP]);
        $stopExec = empty($deliveryProps[TAO_DELIVERY_END_PROP])
            ? null
            : (string)current($deliveryProps[TAO_DELIVERY_END_PROP]);
        
        $startDate  =    date_create('@'.$startExec);
        $endDate    =    date_create('@'.$stopExec);
        if (!$this->areWeInRange($startDate, $endDate)) {
            \common_Logger::d("Attempt to start the compiled delivery ".$delivery->getUri(). " at the wrong date");
            return false;
        }
        return true;
    }
    
    /**
     * Check if the date are in range
     * @param type $startDate
     * @param type $endDate
     * @return boolean true if in range
     */
    protected function areWeInRange($startDate, $endDate){
        return (empty($startDate) || date_create() >= $startDate)
        && (empty($endDate) || date_create() <= $endDate);
    }
    
    /**
     * Order Assignments of a given user.
     * 
     * By default, this method relies on the taoDelivery:DisplayOrder property
     * to order the assignments (Ascending order). However, implementers extending
     * the GroupAssignment class are encouraged to override this method if they need
     * another behaviour.
     * 
     * @param array $assignments An array of assignments.
     * @return array The $assignments array ordered.
     */
    protected function orderAssignments(array $assignments) {
        usort($assignments, function ($a, $b) {
            return $a->getDisplayOrder() - $b->getDisplayOrder();
        });
        
        return $assignments;
    }
}
