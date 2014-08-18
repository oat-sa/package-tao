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
class taoDelivery_models_classes_execution_KVDeliveryExecution 
    implements taoDelivery_models_classes_execution_DeliveryExecution
{
    const DELIVERY_EXECUTION_PREFIX = 'kve_de_';
    
    private $id;
    
    private $data;
    
    /**
     * 
     * @param unknown $userId
     * @param core_kernel_classes_Resource $assembly
     * @return taoDelivery_models_classes_execution_KVDeliveryExecution
     */
    public static function spawn($userId, core_kernel_classes_Resource $assembly) {
        $identifier = self::DELIVERY_EXECUTION_PREFIX.common_Utils::getNewUri();
        $de = new self($identifier, array(
            RDFS_LABEL                            => $assembly->getLabel(),
            PROPERTY_DELVIERYEXECUTION_DELIVERY   => $assembly->getUri(),
            PROPERTY_DELVIERYEXECUTION_SUBJECT    => $userId,
            PROPERTY_DELVIERYEXECUTION_START      => time(),
            PROPERTY_DELVIERYEXECUTION_STATUS     => INSTANCE_DELIVERYEXEC_ACTIVE
        ));
        $de->save();
        return $de;
    }
    
    public function __construct($identifier, $data = null) {
        $this->id = $identifier;
        $this->data = $data;
    }
    
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_DeliveryExecution::getIdentifier()
     */
    public function getIdentifier() {
        return $this->id;
    }

    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_DeliveryExecution::getStartTime()
     */
    public function getStartTime() {
        return $this->getData(PROPERTY_DELVIERYEXECUTION_START);
    }

    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_DeliveryExecution::getLabel()
     */
    public function getLabel() {
        return $this->getData(RDFS_LABEL);
    }
    
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_DeliveryExecution::getState()
     */
    public function getState() {
        return new core_kernel_classes_Resource($this->getData(PROPERTY_DELVIERYEXECUTION_STATUS));
    }
    
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_DeliveryExecution::getDelivery()
     */
    public function getDelivery() {
        return new core_kernel_classes_Resource($this->getData(PROPERTY_DELVIERYEXECUTION_DELIVERY));
    }
    
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_DeliveryExecution::getUserIdentifier()
     */
    public function getUserIdentifier() {
        return $this->getData(PROPERTY_DELVIERYEXECUTION_SUBJECT);
    }
    
    /**
     * Updates the state to finished
     */
    public function setFinished() {
        $this->setData(PROPERTY_DELVIERYEXECUTION_STATUS, INSTANCE_DELIVERYEXEC_FINISHED);
        $this->setData(PROPERTY_DELVIERYEXECUTION_END, time());
        $this->save();
    }
    
    public function setState($state) {
        $oldState = $this->getState()->getUri();
        if ($oldState == $state) {
            common_Logger::w('Delivery execution '.$this->getUri().' already in state '.$state);
            return false;
        }
        $this->setData(PROPERTY_DELVIERYEXECUTION_STATUS, $state);
        if ($state == INSTANCE_DELIVERYEXEC_FINISHED) {
            $this->setData(PROPERTY_DELVIERYEXECUTION_END, time());
        }
        $this->save();
        taoDelivery_models_classes_execution_KeyValueService::singleton()->updateDeliveryExecutionStatus($this, $oldState, $state);
        return true;
    }

    private function getPersistence() {
        return common_persistence_KeyValuePersistence::getPersistence('deliveryExecution');
    }
    
    private function getData($dataKey) {
        if (is_null($this->data)) {
            $dataString = $this->getPersistence()->get($this->id);
            $this->data = json_decode($dataString, true);
        }
        if (!isset($this->data[$dataKey])) {
            throw new common_Exception('Information '.$dataKey.' not found for entry '.$this->id);
        }
        return $this->data[$dataKey];
    }
    
    private function setData($dataKey, $value) {
        if (is_null($this->data)) {
            $dataString = $this->getPersistence()->get($deliveryExecutionId);
            $this->data = json_decode($dataString, true);
        }
        $this->data[$dataKey] = $value;
    }
        
    /**
     * Stored the current data
     */
    private function save() {
        if (!is_null($this->data)) {
            $this->getPersistence()->set($this->getIdentifier(), json_encode($this->data));
        } else {
            common_Logger::w('Tried to save a delivery that was not loaded');
        }
    }
}
