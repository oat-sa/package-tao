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

    private $startTime;
    private $finishTime;
    private $state;
    private $delivery;
    private $userIdentifier;

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
        if (!isset($this->startTime)) {
            $startTime = new core_kernel_classes_Property(PROPERTY_DELVIERYEXECUTION_START);
            $this->startTime = (string)$this->getUniquePropertyValue($startTime);
        }
        return $this->startTime;
    }
    
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_DeliveryExecution::getFinishTime()
     */
    public function getFinishTime() {
        if (!isset($this->finishTime)) {
            $finishProperty = new core_kernel_classes_Property(PROPERTY_DELVIERYEXECUTION_END);
            $finishTime = $this->getOnePropertyValue($finishProperty);
            $this->finishTime = is_null($finishTime) ? null : (string)$finishTime;
        }
        return $this->finishTime;
    }
    
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_DeliveryExecution::getState()
     */
    public function getState() {
        if (!isset($this->state)) {
            $state = $this->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_DELVIERYEXECUTION_STATUS));
            if (!$state instanceof core_kernel_classes_Resource) {
                $state = new core_kernel_classes_Resource((string)$state);
            }
            $this->state = $state;
        }
        return $this->state;
    }
    
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_DeliveryExecution::getDelivery()
     */
    public function getDelivery() {
        if (!isset($this->delivery)) {
            $this->delivery = $this->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_DELVIERYEXECUTION_DELIVERY));
        }
        return $this->delivery;
    }
    
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_DeliveryExecution::getUserIdentifier()
     */
    public function getUserIdentifier() {
        if (!isset($this->userIdentifier)) {
            $user = $this->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_DELVIERYEXECUTION_SUBJECT));
            $this->userIdentifier =  ($user instanceof core_kernel_classes_Resource) ? $user->getUri() : (string)$user;
        }
        return $this->userIdentifier;
    }
    
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_DeliveryExecution::setState()
     */
    public function setState($state) {
        $statusProp = new core_kernel_classes_Property(PROPERTY_DELVIERYEXECUTION_STATUS);
        $state = new core_kernel_classes_Resource($state);
        $currentStatus = $this->getState();
        if ($currentStatus->getUri() == $state->getUri()) {
            common_Logger::w('Delivery execution '.$this->getIdentifier().' already in state '.$state);
            return false;
        }
        $this->editPropertyValues($statusProp, $state);
        if ($state->getUri() == DeliveryExecution::STATE_FINISHIED) {
            $this->setPropertyValue(new core_kernel_classes_Property(PROPERTY_DELVIERYEXECUTION_END), microtime());
        }
        $this->state = $state;
        return true;
    }
}
