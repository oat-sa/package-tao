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
class taoDelivery_models_classes_execution_ServiceProxy extends tao_models_classes_GenerisService
    implements taoDelivery_models_classes_execution_Service
{
    const CONFIG_KEY = 'delivery_execution_id'; 
    
    /**
     * @var taoDelivery_models_classes_execution_Service
     */
    private $implementation;
    
    /**
     * protected constructor for singleton pattern
     */
    protected function __construct() {
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
        if ($ext->hasConfig(self::CONFIG_KEY)) {
            $className = $ext->getConfig(self::CONFIG_KEY);
            if (class_exists($className)) {
                $this->implementation = forward_static_call(array($className, 'singleton'));
            } else {
                throw new common_exception_Error('Class "'.$className.'" not found');
            }
        } else {
            $this->implementation = taoDelivery_models_classes_execution_OntologyService::singleton();
        }
    }
    
    public function setImplementation($className) {
        if (class_exists($className) && method_exists($className,'singleton')) {
            $implementation = forward_static_call(array($className, 'singleton'));
            if ($implementation instanceof taoDelivery_models_classes_execution_Service) {
                $this->implementation = $implementation;
                $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
                $ext->setConfig(self::CONFIG_KEY, $className);
            } else {
                throw new common_exception_Error('Provided class '.$className.' is not a valid delivery execution service');
            }
        } else {
            throw new common_exception_Error('Provided class '.$className.' not found or is not a singleton');
        }
    }
    
    protected static function getImplementation() {
        return self::singleton()->implementation;
    }
    
    
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::getUserExecutions()
     */
    public function getUserExecutions(core_kernel_classes_Resource $assembly, $userUri) {
        return $this->implementation->getUserExecutions($assembly, $userUri);
    }
    
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::getDeliveryExecutionsByStatus()
     */
    public function getDeliveryExecutionsByStatus($userUri, $status) {
        return $this->implementation->getDeliveryExecutionsByStatus($userUri, $status);
    }

    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::getActiveDeliveryExecutions()
     */
    public function getActiveDeliveryExecutions($userUri)
    {
        return $this->getDeliveryExecutionsByStatus($userUri, INSTANCE_DELIVERYEXEC_ACTIVE);
    }
        
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::getFinishedDeliveryExecutions()
     */
    public function getFinishedDeliveryExecutions($userUri)
    {
        return $this->getDeliveryExecutionsByStatus($userUri, INSTANCE_DELIVERYEXEC_FINISHED);
    }
    
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::initDeliveryExecution()
     */
    public function initDeliveryExecution(core_kernel_classes_Resource $assembly, $userUri)
    {
        return $this->implementation->initDeliveryExecution($assembly, $userUri);
    }
    
   /**
    * (non-PHPdoc)
    * @see taoDelivery_models_classes_execution_Service::finishDeliveryExecution()
    */
    public function finishDeliveryExecution(taoDelivery_models_classes_execution_DeliveryExecution $deliveryExecution)
    {
        return $this->implementation->finishDeliveryExecution($deliveryExecution);
    }

    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::getDeliveryExecution()
     */
    public function getDeliveryExecution($identifier)
    {
        return $this->implementation->getDeliveryExecution($identifier);
    }

    /**
     * Whenever or not the current implementation supports monitoring
     * 
     * @return boolean
     */
    public static function implementsMonitoring() {
        return self::getImplementation() instanceof taoDelivery_models_classes_execution_Monitoring;
    }
    
    /**
     * Implemented in the monitoring interface
     * 
     * @param core_kernel_classes_Resource $compiled
     * @return int the ammount of executions for a single compilation
     */
    public function getExecutionsByDelivery(core_kernel_classes_Resource $compiled)
    {
        if (!self::implementsMonitoring()) {
            throw new common_exception_NoImplementation();
        }
        return $this->implementation->getExecutionsByDelivery($compiled);
    }
}
