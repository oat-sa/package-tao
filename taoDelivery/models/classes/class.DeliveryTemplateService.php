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
class taoDelivery_models_classes_DeliveryTemplateService extends tao_models_classes_ClassService
{

    /**
     * (non-PHPdoc)
     * 
     * @see tao_models_classes_ClassService::getRootClass()
     */
    public function getRootClass()
    {
        return new core_kernel_classes_Class(CLASS_DELIVERY_TEMPLATE);
    }

    /**
     * (non-PHPdoc)
     * 
     * @see tao_models_classes_GenerisService::createInstance()
     */
    public function createInstance(core_kernel_classes_Class $clazz, $label = '')
    {
        $delivery = parent::createInstance($clazz, $label);
        
        // set the default delivery server:
        $defaultServer = taoResultServer_models_classes_ResultServerAuthoringService::singleton()->getDefaultResultServer();
        $delivery->setPropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP), $defaultServer);
        
        // set content model if only 1 available
        $models = $this->getAllContentClasses();
        if (count($models) == 1) {
            $contentClass = new core_kernel_classes_Class(current($models));
            $this->createContent($delivery, $contentClass);
        }
        
        
        return $delivery;
    }

    public function getContent($delivery)
    {
        $content = $delivery->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_DELIVERY_CONTENT));
        return $content;
    }

    /**
     * Returns all the content classes
     * 
     * @return array
     */
    public function getAllContentClasses()
    {
        $deliveryContentSuperClass = new core_kernel_classes_Class(CLASS_ABSTRACT_DELIVERYCONTENT);
        $subclasses = $deliveryContentSuperClass->getSubClasses(true);
        return $subclasses;
    }

    public function onChangeLabel(core_kernel_classes_Resource $delivery)
    {
        $content = $this->getContent($delivery);
        if (! is_null($content)) {
            $impl = $this->getImplementationByContent($content);
            $impl->onChangeDeliveryLabel($delivery);
        }
    }

    public function deleteInstance(core_kernel_classes_Resource $delivery)
    {
        $content = $delivery->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_DELIVERY_CONTENT));
        if (! is_null($content)) {
            $impl = $this->getImplementationByContent($content);
            $impl->delete($content);
        }
        return $delivery->delete();
    }

    /**
     * Delete a class of deliveries
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Class clazz
     * @return boolean
     */
    public function deleteDeliveryClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        if($clazz->isSubClassOf($this->getRootClass()) && !$clazz->equals($this->getRootClass())) {
            $returnValue = $clazz->delete();
        } else {
            common_Logger::w('Cannot '.__FUNCTION__.' of class '.$clazz->getUri());
        }
    
        return (bool) $returnValue;
    }
    
    public function cloneInstance(core_kernel_classes_Resource $delivery, core_kernel_classes_Class $clazz = null)
    {
        $clone = parent::cloneInstance($delivery, $clazz);
            	
        $content = $this->getContent($delivery);
        if (!is_null($content)) {
            $impl = $this->getImplementationByContent($content);
            $cloneContent = $impl->cloneContent($content);
            $clone->editPropertyValues(new core_kernel_classes_Property(PROPERTY_DELIVERY_CONTENT), $cloneContent);
        }
        $this->onChangeLabel($clone);
        return $clone;
    }

    /**
     * Spawns new Content for a delivery
     *
     * @param core_kernel_classes_Resource $delivery            
     * @param core_kernel_classes_Class $contentClass            
     * @return boolean
     */
    public function createContent(core_kernel_classes_Resource $delivery, core_kernel_classes_Class $contentClass)
    {
        $impl = $this->getImplementationByContentClass($contentClass);
        $content = $impl->createContent($delivery);
        return $delivery->editPropertyValues(new core_kernel_classes_Property(PROPERTY_DELIVERY_CONTENT), $content);
    }

    /**
     * Returns the implementation from the content
     *
     * @param core_kernel_classes_Resource $test            
     * @return taoDelivery_models_classes_ContentModel
     */
    public function getImplementationByContent(core_kernel_classes_Resource $content)
    {
        $validContentClasses = $this->getAllContentClasses();
        foreach ($content->getTypes() as $type) {
            foreach ($validContentClasses as $reference) {
                if ($type->equals($reference)) {
                    return $this->getImplementationByContentClass($type);
                }
            }
        }
        throw new common_exception_NoImplementation('No implementation found for DeliveryContent ' . $content->getUri());
    }

    /**
     * Returns the implementation from the content class
     *
     * @param core_kernel_classes_Class $contentClass            
     * @return taoDelivery_models_classes_ContentModel
     */
    public function getImplementationByContentClass(core_kernel_classes_Class $contentClass)
    {
        if (empty($contentClass)) {
            throw new common_exception_NoImplementation(__FUNCTION__ . ' called on a NULL contentClass');
        }
        $classname = (string) $contentClass->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_CONTENTCLASS_IMPLEMENTATION));
        if (empty($classname)) {
            throw new common_exception_NoImplementation('No implementation found for contentClass ' . $contentClass->getUri());
        }
        if (! class_exists($classname) || ! in_array('taoDelivery_models_classes_ContentModel', class_implements($classname))) {
            throw new common_exception_Error('Content implementation '.$classname.' not found, or not compatible for content class '.$contentClass->getUri());
            	
        }
        return new $classname();
    }
}