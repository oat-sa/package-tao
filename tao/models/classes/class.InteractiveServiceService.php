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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * This class provide service on interactive services
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 
 */
class tao_models_classes_InteractiveServiceService extends tao_models_classes_ClassService
{
	/**
	 * Get the top class
	 * 
	 * @return core_kernel_classes_Class The user class.
	 */
	public function getRootClass()
	{
		return new core_kernel_classes_Class(CLASS_SERVICESDEFINITION);
	}
	
	/**
     * changes the service definition of a service call
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  core_kernel_classes_Resource serviceCall
     * @param  core_kernel_classes_Resource serviceDefinition
     * @return boolean
     */
    public function setCallOfServiceDefinition( core_kernel_classes_Resource $serviceCall,  core_kernel_classes_Resource $serviceDefinition)
    {
		return $serviceCall->editPropertyValues(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION), $serviceDefinition->getUri());
    }
    
    public function getServiceDefinition( core_kernel_classes_Resource $serviceCall)
    {
		return $serviceCall->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_CALLOFSERVICES_SERVICEDEFINITION));
    }
    
    /**
     * returns an array with the defined default values for the
     * service definition's parameters
     * 
     * @param core_kernel_classes_Resource $serviceDefinition
     */
    public function setDefaultParameters(core_kernel_classes_Resource $serviceCall)
    {
    	$serviceDefinition = $this->getServiceDefinition($serviceCall);
    	$processService = wfAuthoring_models_classes_ProcessService::singleton();
    	
    	$processService->deleteActualParameters($serviceCall);

    	$defaultConstProp = new core_kernel_classes_Property(PROPERTY_FORMALPARAMETER_DEFAULTCONSTANTVALUE);

    	$params = $serviceDefinition->getPropertyValues(new core_kernel_classes_Property(PROPERTY_SERVICESDEFINITION_FORMALPARAMIN));
    	foreach ($params as $paramUri) {
    		$param = new core_kernel_classes_Resource($paramUri);
    		$default = $param->getOnePropertyValue($defaultConstProp);
    		if (!is_null($default)) {
				$processService->setActualParameter($serviceCall, $param, $default, PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN, PROPERTY_ACTUALPARAMETER_CONSTANTVALUE);
    		}
    	}
    	
    	$defaultVariableProp = new core_kernel_classes_Property(PROPERTY_FORMALPARAMETER_DEFAULTPROCESSVARIABLE);
    	
    	$params = $serviceDefinition->getPropertyValues(new core_kernel_classes_Property(PROPERTY_SERVICESDEFINITION_FORMALPARAMIN));
    	foreach ($params as $paramUri) {
    		$param = new core_kernel_classes_Resource($paramUri);
    		$default = $param->getOnePropertyValue($defaultVariableProp);
    		if (!is_null($default)) {
				$processService->setActualParameter($serviceCall, $param, $default, PROPERTY_CALLOFSERVICES_ACTUALPARAMETERIN, PROPERTY_ACTUALPARAMETER_PROCESSVARIABLE);
    		}
    	}
    	
    }
}

?>