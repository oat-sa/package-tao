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

use oat\oatbox\Configurable;
use oat\taoDelivery\models\classes\execution\DeliveryExecution;
use oat\taoDelivery\model\execution\DeliveryExecution as InterfaceDeliveryExecution;
/**
 * Service to manage the execution of deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 */
class taoDelivery_models_classes_execution_OntologyService extends Configurable
    implements taoDelivery_models_classes_execution_Service,
        taoDelivery_models_classes_execution_Monitoring
{

    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::getExecutionsByDelivery()
     */
    public function getExecutionsByDelivery(core_kernel_classes_Resource $compiled)
    {
        $executionClass = new core_kernel_classes_Class(taoDelivery_models_classes_execution_OntologyDeliveryExecution::CLASS_URI);
        $resources = $executionClass->searchInstances(array(
            PROPERTY_DELVIERYEXECUTION_DELIVERY => $compiled->getUri()
        ), array(
            'like' => false
        ));
        $returnValue = array();
        foreach ($resources as $resource) {
            $returnValue[] = $this->getDeliveryExecution($resource);
        }
        return $returnValue;
    }
    
    public function getDeliveryExecutionsByStatus($userUri, $status) {
        $executionClass = new core_kernel_classes_Class(taoDelivery_models_classes_execution_OntologyDeliveryExecution::CLASS_URI);
        $started = $executionClass->searchInstances(array(
            PROPERTY_DELVIERYEXECUTION_SUBJECT => $userUri,
            PROPERTY_DELVIERYEXECUTION_STATUS => $status
        ), array(
            'like' => false
        ));
        $returnValue = array();
        foreach ($started as $resource) {
            $returnValue[] = $this->getDeliveryExecution($resource);
        }
        return $returnValue;
    }
    
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::getUserExecutions()
     */
    public function getUserExecutions(core_kernel_classes_Resource $compiled, $userUri)
    {
        $executionClass = new core_kernel_classes_Class(taoDelivery_models_classes_execution_OntologyDeliveryExecution::CLASS_URI);
        $instances = $executionClass->searchInstances(array(
            PROPERTY_DELVIERYEXECUTION_SUBJECT  => $userUri,
            PROPERTY_DELVIERYEXECUTION_DELIVERY => $compiled->getUri()
        ), array(
            'like' => false
        ));
        $deliveryExecutions = array();
        foreach ($instances as $resource) {
            $deliveryExecutions[] = new DeliveryExecution(
                new \taoDelivery_models_classes_execution_OntologyDeliveryExecution($resource->getUri())
            );
        }
        return $deliveryExecutions;
    }

    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::initDeliveryExecution()
     */
    public function initDeliveryExecution(core_kernel_classes_Resource $assembly, $userUri)
    {
        $executionClass = new core_kernel_classes_Class(taoDelivery_models_classes_execution_OntologyDeliveryExecution::CLASS_URI);
        $execution = $executionClass->createInstanceWithProperties(array(
            RDFS_LABEL                            => $assembly->getLabel(),
            PROPERTY_DELVIERYEXECUTION_DELIVERY   => $assembly,
            PROPERTY_DELVIERYEXECUTION_SUBJECT    => $userUri,
            PROPERTY_DELVIERYEXECUTION_START      => microtime(),
            PROPERTY_DELVIERYEXECUTION_STATUS     => InterfaceDeliveryExecution::STATE_ACTIVE        	
        ));
        return $this->getDeliveryExecution($execution);
    }
    
    /**
     * (non-PHPdoc)
     * @see taoDelivery_models_classes_execution_Service::getDeliveryExecution()
     */
    public function getDeliveryExecution($identifier) {
        return new DeliveryExecution(
            new \taoDelivery_models_classes_execution_OntologyDeliveryExecution($identifier)
        );
    }
}
