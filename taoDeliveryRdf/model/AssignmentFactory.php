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
use \core_kernel_classes_Property;
use tao_helpers_Date;
/**
 * Service to manage the assignment of users to deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 */
class AssignmentFactory
{
    protected $delivery;
    
    private $user;
    
    private $startable;
    
    public function __construct(\core_kernel_classes_Resource $delivery, User $user, $startable)
    {
        $this->delivery = $delivery;
        $this->user = $user;
        $this->startable = $startable;
    }
    
    public function getDeliveryId()
    {
        return $this->delivery->getUri();
    }
    
    protected function getUserId()
    {
        return $this->user->getIdentifier();
    }
    
    protected function getLabel()
    {
        return $this->delivery->getLabel();    
    }
    
    protected function getDescription()
    {
        $deliveryProps = $this->delivery->getPropertiesValues(array(
            new core_kernel_classes_Property(TAO_DELIVERY_MAXEXEC_PROP),
            new core_kernel_classes_Property(TAO_DELIVERY_START_PROP),
            new core_kernel_classes_Property(TAO_DELIVERY_END_PROP),
        ));
        
        $propMaxExec = current($deliveryProps[TAO_DELIVERY_MAXEXEC_PROP]);
        $propStartExec = current($deliveryProps[TAO_DELIVERY_START_PROP]);
        $propEndExec = current($deliveryProps[TAO_DELIVERY_END_PROP]);
        
        $startTime = (!(is_object($propStartExec)) or ($propStartExec=="")) ? null : $propStartExec->literal;
        $endTime = (!(is_object($propEndExec)) or ($propEndExec=="")) ? null : $propEndExec->literal;
        $maxExecs = (!(is_object($propMaxExec)) or ($propMaxExec=="")) ? 0 : $propMaxExec->literal;
        
        $countExecs = count(\taoDelivery_models_classes_execution_ServiceProxy::singleton()->getUserExecutions($this->delivery, $this->getUserId()));
        
        return $this->buildDescriptionFromData($startTime, $endTime, $countExecs, $maxExecs);
    }
    
    protected function getStartable()
    {
        return $this->startable;
    }
    
    public function getStartTime()
    {
        $prop = $this->delivery->getOnePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_START_PROP));
        return is_null($prop) ? null : (string)$prop;
    }
    
    public function getDeliveryOrder()
    {
        $prop = $this->delivery->getOnePropertyValue(new core_kernel_classes_Property(DELIVERY_DISPLAY_ORDER_PROP));
        return is_null($prop) ? 0 : intval((string)$prop);
    }
    
    protected function buildDescriptionFromData($startTime, $endTime, $countExecs, $maxExecs)
    {
        $descriptions = array();
        if (!empty($startTime) && !empty($endTime)) {
            $descriptions[] = __('Available from %1$s to %2$s',
                tao_helpers_Date::displayeDate($startTime)
                ,tao_helpers_Date::displayeDate($endTime)
            );
        } elseif (!empty($startTime) && empty($endTime)) {
            $descriptions[] = __('Available from %s', tao_helpers_Date::displayeDate($startTime));
        } elseif (!empty($endTime)) {
            $descriptions[] = __('Available until %s', tao_helpers_Date::displayeDate($endTime));
        }
         
        if ($maxExecs !== 0) {
            if ($maxExecs === 1) {
                $descriptions[] = __('Attempt %1$s of %2$s'
                    ,$countExecs
                    ,!empty($maxExecs)
                    ? $maxExecs
                    : __('unlimited'));
            } else {
                $descriptions[] = __('Attempts %1$s of %2$s'
                    ,$countExecs
                    ,!empty($maxExecs)
                    ? $maxExecs
                    : __('unlimited'));
        
            }
        }
        return $descriptions;
    }
    
    public function toAssignment()
    {
        return new Assignment(
            $this->getDeliveryId(),
            $this->getUserId(),
            $this->getLabel(),
            $this->getDescription(),
            $this->getStartable(),
            $this->getDeliveryOrder()
        );
    }
    
    public function __equals(AssignmentFactory $factory)
    {
        return $this->getDeliveryId() == $factory->getDeliveryId();
    }
}
