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
use oat\oatbox\service\ServiceManager;

/**
 * @access public
 * @package tao
 
 */
class tao_helpers_ServiceJavascripts
{
    public static function getServiceStorage($serviceCallId) {
        $serviceService = ServiceManager::getServiceManager()->get('tao/stateStorage');
        
        $state = $serviceService->get(
            common_session_SessionManager::getSession()->getUserUri(),
            $serviceCallId
        );
        $submitUrl = _url('submitState', 'ServiceModule', 'tao', array('serviceCallId' => $serviceCallId));
        return 'new StateStorage('.tao_helpers_Javascript::buildObject($state).', '.tao_helpers_Javascript::buildObject($submitUrl).')';
    }
    
    public static function getUserInfoService() {
        $preloaded = array();
        $requestUrl = _url('getUserPropertyValues', 'ServiceModule', 'tao');
        return 'new UserInfoService('.tao_helpers_Javascript::buildObject($requestUrl).','.tao_helpers_Javascript::buildObject($preloaded).')';
    }    
    
    public static function getServiceApi(tao_models_classes_service_ServiceCall $serviceCall, $serviceCallId, $customParams = array()) {
        $inputParameters = tao_models_classes_service_ServiceCallHelper::getInputValues($serviceCall, $customParams);
        $inputParameters['standalone'] = true;
        return 'new ServiceApi('.
            tao_helpers_Javascript::buildObject(tao_models_classes_service_ServiceCallHelper::getBaseUrl($serviceCall->getServiceDefinition())).','.
            tao_helpers_Javascript::buildObject($inputParameters).','.
            tao_helpers_Javascript::buildObject($serviceCallId).','.
            self::getServiceStorage($serviceCallId).','.
            self::getUserInfoService().
        ')';
    }
    
    public static function getFinishedSniplet() {
        $taoExt = common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
        $tpl = $taoExt->getConstant('DIR_VIEWS').'templates'.DIRECTORY_SEPARATOR.'snippet'.DIRECTORY_SEPARATOR.'finishService.tpl';
        $renderer = new Renderer($tpl); 
        return $renderer->render();
    }
}