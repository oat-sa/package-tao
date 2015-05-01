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
 * Service to manage the authoring of deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 */
class taoDelivery_models_classes_DeliveryAssemblyService extends tao_models_classes_ClassService
{

    /**
     * (non-PHPdoc)
     * 
     * @see tao_models_classes_ClassService::getRootClass()
     */
    public function getRootClass()
    {
        return new core_kernel_classes_Class(CLASS_COMPILEDDELIVERY);
    }
    
    public function createAssemblyFromServiceCall(core_kernel_classes_Class $deliveryClass, tao_models_classes_service_ServiceCall $serviceCall, $properties = array()) {

        $properties[PROPERTY_COMPILEDDELIVERY_TIME]      = time();
        $properties[PROPERTY_COMPILEDDELIVERY_RUNTIME]   = $serviceCall->toOntology();
        
        if (!isset($properties[TAO_DELIVERY_RESULTSERVER_PROP])) {
            $properties[TAO_DELIVERY_RESULTSERVER_PROP] = taoResultServer_models_classes_ResultServerAuthoringService::singleton()->getDefaultResultServer();
        }
        
        $compilationInstance = $deliveryClass->createInstanceWithProperties($properties);
        
        return $compilationInstance;
    }
    
    /**
     * Returns all assemblies marked as active
     * 
     * @return array
     */
    public function getAllAssemblies() {
        return $this->getRootClass()->getInstances(true);
    }
    
    public function deleteInstance(core_kernel_classes_Resource $assembly)
    {
        taoDelivery_models_classes_AssignmentService::singleton()->onDelete($assembly);
        $runtimeResource = $assembly->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_COMPILEDDELIVERY_RUNTIME));
        $runtimeResource->delete();
        // cleanup data
        return $assembly->delete();
    }
    
    /**
     * Gets the service call to run this assembly
     *
     * @param core_kernel_classes_Resource $assembly
     * @return tao_models_classes_service_ServiceCall
     */
    public function getRuntime( core_kernel_classes_Resource $assembly) {
        $runtimeResource = $assembly->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_COMPILEDDELIVERY_RUNTIME));
        return tao_models_classes_service_ServiceCall::fromResource($runtimeResource);
    }
    
    /**
     * Returns the date of the compilation of an assembly as a timestamp
     *
     * @param core_kernel_classes_Resource $assembly
     * @return string
     */
    public function getCompilationDate( core_kernel_classes_Resource $assembly) {
        return (string)$assembly->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_COMPILEDDELIVERY_TIME));
    }

}