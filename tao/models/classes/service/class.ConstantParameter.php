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
class tao_models_classes_service_ConstantParameter
extends tao_models_classes_service_Parameter
{
    /**
     * @var string
     */
	private $value;
	
	/**
	 * Instantiates a parameter that takes
	 * a constant value
	 * 
	 * @param core_kernel_classes_Resource $definition
	 * @param string $value
	 */
	public function __construct(core_kernel_classes_Resource $definition, $value) {
	    parent::__construct($definition);
	    $this->value = is_object($value) && $value instanceof core_kernel_classes_Resource ? $value->getUri() : (string)$value;
	}
	
	/**
	 * Returns the actual value associated to this parameter
	 * 
	 * @return string
	 */
	public function getValue() {
	    return $this->value;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see tao_models_classes_service_Parameter::serialize()
	 */
	public function toOntology() {
	    $serviceCallClass = new core_kernel_classes_Class(CLASS_ACTUALPARAMETER);
	    $resource = $serviceCallClass->createInstanceWithProperties(array(
	        PROPERTY_ACTUALPARAMETER_FORMALPARAMETER    => $this->getDefinition(),
	        PROPERTY_ACTUALPARAMETER_CONSTANTVALUE      => $this->value
	    ));
	    return $resource;
	}
}
