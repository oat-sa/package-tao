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
 */               
namespace oat\taoDelivery\helper;

use oat\oatbox\user\User;
/**
 * Helper to render the delivery form on the group page
 * 
 * @author joel bout, <joel@taotesting.com>
 * @package taoDelivery
 
 */
class Delivery
{
    const ID = 'id';
    
    const LABEL = 'label';
    
    const AUTHORIZED = 'TAO_DELIVERY_TAKABLE';
    
    const DESCRIPTION = 'description';
    
    const LAUNCH_URL = 'launchUrl';
    
    static function buildFromAssembly($assignment, User $user)
    {
        $data = array(
            self::ID => $assignment->getDeliveryId(),
            self::LABEL => $assignment->getLabel(),
            self::LAUNCH_URL => _url('initDeliveryExecution','DeliveryServer',null, array('uri' => $assignment->getDeliveryId())),
            self::DESCRIPTION => $assignment->getDescriptionStrings(),
            self::AUTHORIZED => $assignment->isStartable()
        );
        return $data;
    }
    
    static function buildFromDeliveryExecution(\taoDelivery_models_classes_execution_DeliveryExecution $deliveryExecution)
    {
        $data = array();
        $data[self::ID] = $deliveryExecution->getIdentifier();
        $data[self::LABEL] = $deliveryExecution->getLabel();
        $data[self::LAUNCH_URL] = _url('runDeliveryExecution', 'DeliveryServer', null, array('deliveryExecution' => $deliveryExecution->getIdentifier()));
        $data[self::DESCRIPTION] = array(__("Started at %s", \tao_helpers_Date::displayeDate($deliveryExecution->getStartTime())));
        $data[self::AUTHORIZED] = true;
        return $data;
    }
}
