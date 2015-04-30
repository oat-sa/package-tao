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
 * 
 */

/**
 * Abstract service to be implemented by LTI tools
 * 
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoLti
 * @package models_classes
 */
abstract class taoLti_models_classes_LtiTool extends tao_models_classes_Service
{
	/**
	 * Builds a launch url for this tool
	 * 
	 * @param array $parameters additional launch parameters
	 * @return string
	 */
	public abstract function getLaunchUrl($parameters = array());

	public static function getToolService(core_kernel_classes_Resource $tool) {
		$services = $tool->getPropertyValues(new core_kernel_classes_Property(PROPERTY_LTITOOL_SERVICE));
		if (count($services) > 0) {
			if (count($services) > 1) {
				throw new common_exception_Error('Conflicting services for tool '.$tool->getLabel());
			}
			$serviceName = (string)current($services);
			if (class_exists($serviceName) && is_subclass_of($serviceName, __CLASS__)) {
				return call_user_func(array($serviceName, 'singleton'));
			} else {
				throw new common_exception_Error('Tool service '.$serviceName.' not found, or not compatible for tool '.$tool->getLabel());
			}
		} else {
			common_Logger::w('No implementation for '.$tool->getLabel());
		}
	}
	
}