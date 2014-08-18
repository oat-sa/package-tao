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
 * Compiles a simple Delivery
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoSimpleDelivery
 
 */
class taoSimpleDelivery_models_classes_DeliveryCompiler extends taoDelivery_models_classes_DeliveryCompiler
{
    /**
     * Compiles a simple delivery
     * 
     * @param core_kernel_classes_Resource $deliveryContent
     * @param core_kernel_file_File $directory
     * @param core_kernel_classes_Resource $resultServer
     * @return tao_models_classes_service_ServiceCall
     */
    public function compile() {
        
        $test = $this->getResource()->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_DELIVERYCONTENT_TEST));
        if (is_null($test)) {
            throw new taoDelivery_models_classes_EmptyDeliveryException($this->getResource());
        }
        return $this->subCompile($test);
    }
}