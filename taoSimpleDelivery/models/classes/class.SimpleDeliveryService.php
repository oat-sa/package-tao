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
 * Services to manage simple Deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoSimpleDelivery
 
 */
class taoSimpleDelivery_models_classes_SimpleDeliveryService extends tao_models_classes_Service
{
    /**
     * Creates a new simple delivery
     * 
     * @param core_kernel_classes_Class $deliveryClass
     * @param core_kernel_classes_Resource $test
     * @param string $label
     * @return common_report_Report
     */
    public function create(core_kernel_classes_Class $deliveryClass, core_kernel_classes_Resource $test, $label) {
        common_Logger::i('Creating '.$label.' with '.$test->getLabel().' under '.$deliveryClass->getLabel());
        
        $contentClass = new core_kernel_classes_Class(CLASS_SIMPLE_DELIVERYCONTENT);
        $content = $contentClass->createInstanceWithProperties(array(
            PROPERTY_DELIVERYCONTENT_TEST => $test->getUri()
        ));
        $report = taoDelivery_models_classes_DeliveryAssemblyService::singleton()->createAssembly(
        	$deliveryClass, $content, array(RDFS_LABEL => $label)
        );
        $content->delete();
        return $report;
    }
}