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
 * the wfDelivery service
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoWfTest
 
 */
class taoWfDelivery_models_classes_WfDeliveryService
	extends tao_models_classes_Service
{
    /**
     * The workflow content extension
     * 
     * @var common_ext_Extension
     */
    private $extension;
    
    public function __construct() {
        $this->extension = common_ext_ExtensionsManager::singleton()->getExtensionById('taoWfDelivery');
	}
	
    public function getTestFromService(core_kernel_classes_Resource $service) {
        $returnValue = null;
        $propertyIterator = $service->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN))->getIterator();
        foreach ($propertyIterator as $actualParam) {
            $formalParam = $actualParam->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTUALPARAMETER_FORMALPARAMETER));
            if ($formalParam->getUri() == INSTANCE_FORMALPARAM_TESTURI) {
                $returnValue = $actualParam->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_ACTUALPARAMETER_CONSTANTVALUE));
                break;
            }
        }
        return $returnValue;
    }
}