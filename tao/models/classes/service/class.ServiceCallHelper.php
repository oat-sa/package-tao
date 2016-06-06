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
 * Service to manage services and calls to these services
 * 
 * @author Joel Bout, <joel@taotesting.com>
 */
class tao_models_classes_service_ServiceCallHelper
{
    const CACHE_PREFIX_URL = 'tao_service_url_';
    
    const CACHE_PREFIX_PARAM_NAME = 'tao_service_param_';
    
    public static function getBaseUrl( core_kernel_classes_Resource $serviceDefinition) {
        
        try {
            $url = common_cache_FileCache::singleton()->get(self::CACHE_PREFIX_URL.urlencode($serviceDefinition->getUri()));
        } catch (common_cache_NotFoundException $e) {
            
            $serviceDefinitionUrl = $serviceDefinition->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_SUPPORTSERVICES_URL));
            if($serviceDefinitionUrl instanceof core_kernel_classes_Literal){
                $serviceUrl = $serviceDefinitionUrl->literal;
            }else if($serviceDefinitionUrl instanceof core_kernel_classes_Resource){
                // hack nescessary since fully qualified urls are considered to be resources
                $serviceUrl = $serviceDefinitionUrl->getUri();
            } else {
                throw new common_exception_InconsistentData('Invalid service definition url for '.$serviceDefinition->getUri());
            }
            // Remove the parameters because they are only for show, and they are actualy encoded in the variables
            $urlPart = explode('?',$serviceUrl);
            $url = $urlPart[0];
            if(preg_match('/^\//i', $url)){
                //create absolute url (prevent issue when TAO installed on a subfolder
                $url = ROOT_URL.ltrim($url, '/');
            }
            common_cache_FileCache::singleton()->put($url, self::CACHE_PREFIX_URL.urlencode($serviceDefinition->getUri()));
        }
        
        return $url;
    }
    
    public static function getInputValues(tao_models_classes_service_ServiceCall $serviceCall, $callParameters) {
        $returnValue = array();
        foreach ($serviceCall->getInParameters() as $param) {
            $paramKey = self::getParamName($param->getDefinition());
            switch (get_class($param)) {
            	case 'tao_models_classes_service_ConstantParameter' :
            	    $returnValue[$paramKey] = $param->getValue();
            	    break;
            	case 'tao_models_classes_service_VariableParameter' :
            	    $variableCode = (string)$param->getVariable()->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_PROCESSVARIABLES_CODE));
            	    if (isset($callParameters[$variableCode])) {
            	        $returnValue[$paramKey] = $callParameters[$variableCode];
            	    } else {
            	        common_Logger::w('No variable '.$variableCode.' provided for paramter '.$paramKey);
            	    }
            	    break;
            	default:
            	    throw new common_exception_Error('Unknown class of parameter: '.get_class($param));
            }
        }
        return $returnValue;
    }
    
    protected static function getParamName(core_kernel_classes_Resource $paramDefinition) {
        try {
            $paramKey = common_cache_FileCache::singleton()->get(self::CACHE_PREFIX_PARAM_NAME.urlencode($paramDefinition->getUri()));
        } catch (common_cache_NotFoundException $e) {
            $paramKey = common_Utils::fullTrim($paramDefinition->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_FORMALPARAMETER_NAME)));
            common_cache_FileCache::singleton()->put($paramKey, self::CACHE_PREFIX_PARAM_NAME.urlencode($paramDefinition->getUri()));
        }
        return $paramKey;
        
    }
}