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

/**
 * Service to manage the execution of deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 
 */
class taoDelivery_models_classes_execution_OntologyDeliveryExecution extends core_kernel_classes_Resource 
    implements taoDelivery_models_classes_execution_DeliveryExecution
{
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_DeliveryExecution::getIdentifier()
     */
    public function getIdentifier() {
        return $this->getUri();
    }
    
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_DeliveryExecution::getStartTime()
     */
    public function getStartTime() {
        $startTime = new core_kernel_classes_Property(PROPERTY_DELVIERYEXECUTION_START);
        return (string)$this->getUniquePropertyValue($startTime);
    }
    
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_DeliveryExecution::getState()
     */
    public function getState() {
        return $this->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_DELVIERYEXECUTION_STATUS));
    }
    
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_DeliveryExecution::getDelivery()
     */
    public function getDelivery() {
        return $this->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_DELVIERYEXECUTION_DELIVERY));
    }
    
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_DeliveryExecution::getUserIdentifier()
     */
    public function getUserIdentifier() {
        return $this->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_DELVIERYEXECUTION_SUBJECT))->getUri();
    }
    
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_DeliveryExecution::setState()
     */
    public function setState($state) {
        $statusProp = new core_kernel_classes_Property(PROPERTY_DELVIERYEXECUTION_STATUS);
        $currentStatus = $this->getState();
        if ($currentStatus->getUri() == $state) {
            common_Logger::w('Delivery execution '.$deliveryExecution->getUri().' already in state '.$state);
            return false;
        }
        $this->editPropertyValues($statusProp, $state);
        if ($state == INSTANCE_DELIVERYEXEC_FINISHED) {
            $this->setPropertyValue(new core_kernel_classes_Property(PROPERTY_DELVIERYEXECUTION_END), time());
        }
        return true;
    }
}