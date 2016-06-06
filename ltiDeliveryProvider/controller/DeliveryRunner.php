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

namespace oat\ltiDeliveryProvider\controller;

use oat\taoDelivery\controller\DeliveryServer;
use \taoLti_models_classes_LtiService;
use \taoLti_models_classes_LtiLaunchData;
use oat\ltiDeliveryProvider\helper\ResultServer;
use oat\ltiDeliveryProvider\model\LTIDeliveryTool;


/**
 * Called by the DeliveryTool to override DeliveryServer settings
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package ltiDeliveryProvider
 */
class DeliveryRunner extends DeliveryServer
{
    protected function showControls() {
        return false;
    }
    
    protected function getReturnUrl() {
        $launchData = taoLti_models_classes_LtiService::singleton()->getLtiSession()->getLaunchData();
        
        if ($launchData->hasVariable(DeliveryTool::PARAM_SKIP_THANKYOU) && $launchData->getVariable(DeliveryTool::PARAM_SKIP_THANKYOU) == 'true'
            && $launchData->hasVariable(taoLti_models_classes_LtiLaunchData::LAUNCH_PRESENTATION_RETURN_URL)) {
            return $launchData->getVariable(taoLti_models_classes_LtiLaunchData::LAUNCH_PRESENTATION_RETURN_URL);
        }
        return _url('thankYou', 'DeliveryRunner', 'ltiDeliveryProvider');
    }

    /**
     * Shown uppon returning to a finished delivery execution
     */
    public function ltiOverview() {
        $this->setData('delivery', $this->getRequestParameter('delivery'));
        $this->setData('allowRepeat', true);
        $this->setView('learner/overview.tpl');
    }
    
    public function repeat() {
        $delivery = new \core_kernel_classes_Resource($this->getRequestParameter('delivery'));
        
        // is allowed?
        // is active?
        
        $remoteLink = \taoLti_models_classes_LtiService::singleton()->getLtiSession()->getLtiLinkResource();
        $userId = \common_session_SessionManager::getSession()->getUserUri();
         
        $newExecution = LTIDeliveryTool::singleton()->startDelivery($delivery, $remoteLink, $userId);
            
        $this->redirect(_url('runDeliveryExecution', null, null, array('deliveryExecution' => $newExecution->getIdentifier())));
    }
    
    public function thankYou() {
        $launchData = taoLti_models_classes_LtiService::singleton()->getLtiSession()->getLaunchData();
        
        if ($launchData->hasVariable(taoLti_models_classes_LtiLaunchData::TOOL_CONSUMER_INSTANCE_NAME)) {
            $this->setData('consumerLabel', $launchData->getVariable(taoLti_models_classes_LtiLaunchData::TOOL_CONSUMER_INSTANCE_NAME));
        } elseif($launchData->hasVariable(taoLti_models_classes_LtiLaunchData::TOOL_CONSUMER_INSTANCE_DESCRIPTION)) {
            $this->setData('consumerLabel', $launchData->getVariable(taoLti_models_classes_LtiLaunchData::TOOL_CONSUMER_INSTANCE_DESCRIPTION));
        }
        
        if ($launchData->hasVariable(taoLti_models_classes_LtiLaunchData::LAUNCH_PRESENTATION_RETURN_URL)) {
            $this->setData('returnUrl', $launchData->getVariable(taoLti_models_classes_LtiLaunchData::LAUNCH_PRESENTATION_RETURN_URL));
        }
        
        if ($launchData->hasVariable(DeliveryTool::PARAM_THANKYOU_MESSAGE)) {
            $this->setData('message', $launchData->getVariable(DeliveryTool::PARAM_THANKYOU_MESSAGE));
        }
        
        $this->setData('allowRepeat', false);
        $this->setView('learner/thankYou.tpl');
    }

    protected function initResultServer($compiledDelivery, $executionIdentifier) {
        //The result server from LTI context depend on call parameters rather than static result server definition
        // lis_outcome_service_url This value should not change from one launch to the next and in general,
        //  the TP can expect that there is a one-to-one mapping between the lis_outcome_service_url and a particular oauth_consumer_key.  This value might change if there was a significant re-configuration of the TC system or if the TC moved from one domain to another.
        $launchData = taoLti_models_classes_LtiService::singleton()->getLtiSession()->getLaunchData();
        ResultServer::initLtiResultServer($compiledDelivery, $executionIdentifier, $launchData);
    }

}