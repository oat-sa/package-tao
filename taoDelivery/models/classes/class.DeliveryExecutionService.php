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
 * @subpackage models_classes
 */
class taoDelivery_models_classes_DeliveryExecutionService extends tao_models_classes_GenerisService
{
    /**
     * Returns the number of delivery executions for a compiled directory
     * 
     * @param core_kernel_classes_Resource $compiled
     * @return number
     */
    public function getDeliveryExecutionCount(core_kernel_classes_Resource $compiled)
    {
        $executionClass = new core_kernel_classes_Class(CLASS_DELVIERYEXECUTION);
        $count = count($executionClass->searchInstances(array(
            PROPERTY_DELVIERYEXECUTION_DELIVERY => $compiled->getUri()
        ), array(
            'like' => false
        )));
        return $count;
    }

    /**
     * Returns all activ Delivery Executions of a User
     *
     * @param unknown $userUri            
     * @return Ambigous <multitype:, array>
     */
    public function getActiveDeliveryExecutions($userUri)
    {
        $executionClass = new core_kernel_classes_Class(CLASS_DELVIERYEXECUTION);
        $started = $executionClass->searchInstances(array(
            PROPERTY_DELVIERYEXECUTION_SUBJECT => $userUri,
            PROPERTY_DELVIERYEXECUTION_STATUS => INSTANCE_DELIVERYEXEC_ACTIVE
        ), array(
            'like' => false
        ));
        return $started;
    }
        
    public function getDeliveryExecutionStartTime($deliveryExecution){
        $startTime = new core_kernel_classes_Property(PROPERTY_DELVIERYEXECUTION_START);
        return $deliveryExecution->getUniquePropertyValue($startTime);
    }
    /**
     * Returns all finished Delivery Executions of a User
     *
     * @param unknown $userUri            
     * @return Ambigous <multitype:, array>
     */
    public function getFinishedDeliveryExecutions($userUri)
    {
        $executionClass = new core_kernel_classes_Class(CLASS_DELVIERYEXECUTION);
        $started = $executionClass->searchInstances(array(
            PROPERTY_DELVIERYEXECUTION_SUBJECT => $userUri,
            PROPERTY_DELVIERYEXECUTION_STATUS => INSTANCE_DELIVERYEXEC_FINISHED
        ), array(
            'like' => false
        ));
        return $started;
    }
    
    /**
     * Generate a new delivery execution
     * 
     * @param core_kernel_classes_Resource $compiled
     * @param string $userUri
     * @return core_kernel_classes_Resource the delivery execution
     */
    public function initDeliveryExecution(core_kernel_classes_Resource $compiled, $userUri)
    {
        $executionClass = new core_kernel_classes_Class(CLASS_DELVIERYEXECUTION);
        $execution = $executionClass->createInstanceWithProperties(array(
            RDFS_LABEL                            => $compiled->getLabel(),
            PROPERTY_DELVIERYEXECUTION_DELIVERY   => $compiled,
            PROPERTY_DELVIERYEXECUTION_SUBJECT    => $userUri,
            PROPERTY_DELVIERYEXECUTION_START      => time(),
            PROPERTY_DELVIERYEXECUTION_STATUS     => INSTANCE_DELIVERYEXEC_ACTIVE        	
        ));
        return $execution;
    }
    
   /**
    * Finishes a delivery execution
    *
    * @param core_kernel_classes_Resource $deliveryExecution
    * @return boolean success
    */
    public function finishDeliveryExecution(core_kernel_classes_Resource $deliveryExecution)
    {
        $statusProp = new core_kernel_classes_Property(PROPERTY_DELVIERYEXECUTION_STATUS);
        $currentStatus = $deliveryExecution->getUniquePropertyValue($statusProp);
        if ($currentStatus->getUri() == INSTANCE_DELIVERYEXEC_FINISHED) {
            throw new common_Exception('Delivery execution '.$deliveryExecution->getUri().' has laready been finished');
        }
        $deliveryExecution->editPropertyValues($statusProp, INSTANCE_DELIVERYEXEC_FINISHED);
        $deliveryExecution->setPropertyValue(new core_kernel_classes_Property(PROPERTY_DELVIERYEXECUTION_END), time());
        return true;
    }    

}
