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
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package ltiDeliveryProvider
 * @subpackage actions
 */
class ltiDeliveryProvider_actions_DeliveryRunner extends taoDelivery_actions_DeliveryServer
{
    protected function showControls() {
        return false;
    }
    
    protected function getReturnUrl() {
        return _url('thankYou', 'DeliveryRunner', 'ltiDeliveryProvider');
    }

    public function thankYou() {
        $launchData = taoLti_models_classes_LtiService::singleton()->getLtiSession()->getLaunchData();
        
        if ($launchData->hasVariable(taoLti_models_classes_LtiLaunchData::TOOL_CONSUMER_INSTANCE_NAME)) {
            $this->setData('consumerLabel', $launchData->getVariable(taoLti_models_classes_LtiLaunchData::TOOL_CONSUMER_INSTANCE_NAME));
        }
        
        if ($launchData->hasVariable(taoLti_models_classes_LtiLaunchData::LAUNCH_PRESENTATION_RETURN_URL)) {
            $this->setData('returnUrl', $launchData->getVariable(taoLti_models_classes_LtiLaunchData::LAUNCH_PRESENTATION_RETURN_URL));
        }
        $this->setData('allowRepeat', false);
        $this->setView('thankYou.tpl');
    }
    
}