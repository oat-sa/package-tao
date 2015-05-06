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
 * Represents tao service parameter
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 
 */
abstract class tao_models_classes_service_Parameter
{
    /**
     * @var core_kernel_classes_Resource
     */
	private $definition;
	
	/**
	 * Base constructor of abstract parameter
	 * 
	 * @param core_kernel_classes_Resource $definition
	 */
	public function __construct(core_kernel_classes_Resource $definition) {
	    $this->definition = $definition;
	}
	
	/**
	 * Returns the formal definition of this parameter
	 * 
	 * @return core_kernel_classes_Resource
	 */
	public function getDefinition() {
	    return $this->definition;
	}
	/**
	 * @return core_kernel_classes_Resource
	 */
	public abstract function toOntology();
	
	/**
	 * Builds a service call parameter from it's serialized form
	 *
	 * @param core_kernel_classes_Resource $resource
	 * @return tao_models_classes_service_Parameter
	 */
	public static function fromResource(core_kernel_classes_Resource $resource) {
	    $values = $resource->getPropertiesValues(array(
	        PROPERTY_ACTUALPARAMETER_FORMALPARAMETER,
	        PROPERTY_ACTUALPARAMETER_CONSTANTVALUE,
	        PROPERTY_ACTUALPARAMETER_PROCESSVARIABLE
	    ));
	    if (count($values[PROPERTY_ACTUALPARAMETER_FORMALPARAMETER]) != 1) {
	        throw new common_exception_InconsistentData('Actual variable '.$resource->getUri().' missing formal parameter');
	    }
	    if (count($values[PROPERTY_ACTUALPARAMETER_CONSTANTVALUE]) + count($values[PROPERTY_ACTUALPARAMETER_PROCESSVARIABLE]) != 1) {
	        throw new common_exception_InconsistentData('Actual variable '.$resource->getUri().' invalid, '
	            .count($values[PROPERTY_ACTUALPARAMETER_CONSTANTVALUE]).' constant values and '
                .count($values[PROPERTY_ACTUALPARAMETER_PROCESSVARIABLE]).' process variables');
	    }
	    if (count($values[PROPERTY_ACTUALPARAMETER_CONSTANTVALUE]) > 0) {
	        $param = new tao_models_classes_service_ConstantParameter(
	            current($values[PROPERTY_ACTUALPARAMETER_FORMALPARAMETER]),
	            current($values[PROPERTY_ACTUALPARAMETER_CONSTANTVALUE])
	       );
	    } else {
	        $param = new tao_models_classes_service_VariableParameter(
	            current($values[PROPERTY_ACTUALPARAMETER_FORMALPARAMETER]),
	            current($values[PROPERTY_ACTUALPARAMETER_PROCESSVARIABLE])
	        );
	    }
	    return $param;
	}
}
