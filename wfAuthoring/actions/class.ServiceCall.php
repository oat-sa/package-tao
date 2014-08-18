<?php

/*
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
 *  Controller to manage service calls (ak. callOfService)
 * 
 * @author Joel Bout, <joel@taotesting.com>
 * @package wfEngine
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class wfAuthoring_actions_ServiceCall extends tao_actions_CommonModule {
	
	public function setServiceDefinition() {
		$serviceCallUri = tao_helpers_Uri::decode($this->getRequestParameter('callOfServiceUri'));
		$serviceDefinitionUri = tao_helpers_Uri::decode($this->getRequestParameter(tao_helpers_Uri::encode(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION)));
		
		if (empty($serviceCallUri)) {
			throw new tao_models_classes_MissingRequestParameterException('callOfServiceUri');
		}
		if (empty($serviceDefinitionUri)) {
			throw new tao_models_classes_MissingRequestParameterException(tao_helpers_Uri::encode(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION));
		}
		$serviceCall		= new core_kernel_classes_Resource($serviceCallUri);
		$serviceDefinition	= new core_kernel_classes_Resource($serviceDefinitionUri);
		$service = tao_models_classes_InteractiveServiceService::singleton();
	    $service->setCallOfServiceDefinition($serviceCall, $serviceDefinition);
    	$service->setDefaultParameters($serviceCall);
    	echo json_encode(array('saved' => 'true'));
	}
	
}
?>