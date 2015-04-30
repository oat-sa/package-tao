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

use oat\ltiDeliveryProvider\model\LTIDeliveryTool;
use \taoLti_actions_ToolModule;
use \tao_models_classes_accessControl_AclProxy;
use \tao_helpers_Uri;
use \common_session_SessionManager;
use \common_Logger;
use \core_kernel_classes_Resource;
use \taoLti_models_classes_LtiService;

/**
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package ltiDeliveryProvider
 
 */
class DeliveryTool extends taoLti_actions_ToolModule
{
    /**
     * (non-PHPdoc)
     * @see taoLti_actions_ToolModule::run()
     */
    public function run()
    {
        $compiledDelivery = $this->getDelivery();
        if (is_null($compiledDelivery) || !$compiledDelivery->exists()) {
            if (tao_models_classes_accessControl_AclProxy::hasAccess('configureDelivery', 'LinkConfiguration','ltiDeliveryProvider')) {
                // user authorised to select the Delivery
                $this->redirect(tao_helpers_Uri::url('configureDelivery', 'LinkConfiguration', null));
            } else {
                // user NOT authorised to select the Delivery
                $this->returnError(__('This tool has not yet been configured, please contact your instructor'), false);
            }
        } else {
            $user = common_session_SessionManager::getSession()->getUser();
            $isLearner = !is_null($user) && in_array(INSTANCE_ROLE_CONTEXT_LEARNER, $user->getRoles());
            if ($isLearner) {
                if (tao_models_classes_accessControl_AclProxy::hasAccess('runDeliveryExecution', 'DeliveryRunner', 'ltiDeliveryProvider')) {
                    $this->redirect($this->getLearnerUrl($compiledDelivery));
                } else {
                    common_Logger::e('Lti learner has no access to delivery runner');
                    $this->returnError(__('Access to this functionality is restricted'), false);
                }   
            } elseif (tao_models_classes_accessControl_AclProxy::hasAccess('configureDelivery', 'LinkConfiguration', 'ltiDeliveryProvider')) {
                $this->redirect(_url('showDelivery', 'LinkConfiguration', null, array('uri' => $compiledDelivery->getUri())));
            } else {
                $this->returnError(__('Access to this functionality is restricted to students'), false);
            }
        }
    }
    
    protected function getLearnerUrl(\core_kernel_classes_Resource $delivery) {
        $remoteLink = \taoLti_models_classes_LtiService::singleton()->getLtiSession()->getLtiLinkResource();
        $userId = \common_session_SessionManager::getSession()->getUserUri();
         
        $active = null;
        $executions = $this->getTool()->getLinkedDeliveryExecutions($delivery, $remoteLink, $userId);
        if (empty($executions)) {
            $active = $this->getTool()->startDelivery($delivery, $remoteLink, $userId);
            return _url('runDeliveryExecution', 'DeliveryRunner', null, array('deliveryExecution' => $active->getIdentifier()));
        } else {
            foreach ($executions as $deliveryExecution) {
                if ($deliveryExecution->getState()->getUri() == INSTANCE_DELIVERYEXEC_ACTIVE) {
                    $active = $deliveryExecution;
                    break;
                }
            }
            if (is_null($active)) {
                return _url('ltiOverview', 'DeliveryRunner', null, array('delivery' => $delivery->getUri()));
            } else {
                return _url('runDeliveryExecution', 'DeliveryRunner', null, array('deliveryExecution' => $active->getIdentifier()));
            }
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see taoLti_actions_ToolModule::getTool()
     */
    protected function getTool()
    {
        return LTIDeliveryTool::singleton();
    }
    
    /**
     * Returns the delivery associated with the current link
     * either from url or from the remote_link if configured
     * returns null if none found
     *
     * @return core_kernel_classes_Resource
     */
    private function getDelivery()
    {
        $returnValue = null;
        //passed as aprameter
        if ($this->hasRequestParameter('delivery')) {
            $returnValue = new core_kernel_classes_Resource($this->getRequestParameter('delivery'));
        } else {
            
            $launchData = taoLti_models_classes_LtiService::singleton()->getLtiSession()->getLaunchData();
            $deliveryUri = $launchData->getCustomParameter('delivery');
            
            if (!is_null($deliveryUri)) {
                $returnValue = new core_kernel_classes_Resource($deliveryUri);
            } else {
                // stored in link
                $returnValue = LTIDeliveryTool::singleton()->getDeliveryFromLink();
            }
        }
        return $returnValue;
    }
    
}