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

use oat\oatbox\service\ServiceManager;
use oat\taoDelivery\model\execution\DeliveryExecution;

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
    const CLASS_URI = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryExecution';
    
    const PROPERTY_DELIVERY = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryExecutionDelivery';
    
    const PROPERTY_SUBJECT = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryExecutionSubject';
    
    const PROPERTY_TIME_START = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryExecutionStart';
    
    const PROPERTY_TIME_END = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryExecutionEnd';
    
    const PROPERTY_STATUS = 'http://www.tao.lu/Ontologies/TAODelivery.rdf#StatusOfDeliveryExecution';
    
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
     * @see taoDelivery_models_classes_execution_DeliveryExecution::getFinishTime()
     */
    public function getFinishTime() {
        $finishProperty = new core_kernel_classes_Property(PROPERTY_DELVIERYEXECUTION_END);
        $finishTime = $this->getOnePropertyValue($finishProperty);
        return is_null($finishTime) ? null : (string)$finishTime;
    }
    
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_DeliveryExecution::getState()
     */
    public function getState() {
        $state = $this->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_DELVIERYEXECUTION_STATUS));
        if (!$state instanceof core_kernel_classes_Resource) {
            $state = new core_kernel_classes_Resource((string)$state);
        }
        return $state;
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
        $user = $this->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_DELVIERYEXECUTION_SUBJECT));
        return ($user instanceof core_kernel_classes_Resource) ? $user->getUri() : (string)$user;
    }
    
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_DeliveryExecution::setState()
     */
    public function setState($state) {
        $statusProp = new core_kernel_classes_Property(PROPERTY_DELVIERYEXECUTION_STATUS);
        $currentStatus = $this->getState();
        if ($currentStatus->getUri() == $state) {
            common_Logger::w('Delivery execution '.$this->getIdentifier().' already in state '.$state);
            return false;
        }
        $this->editPropertyValues($statusProp, $state);
        if ($state == DeliveryExecution::STATE_FINISHIED) {
            $this->setPropertyValue(new core_kernel_classes_Property(PROPERTY_DELVIERYEXECUTION_END), microtime());
        }
        return true;
    }
}
