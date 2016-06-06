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
namespace oat\ltiDeliveryProvider\controller;

use oat\ltiDeliveryProvider\model\LTIDeliveryTool;
use \tao_actions_CommonModule;
use \core_kernel_classes_Resource;
use \tao_helpers_Uri;
use \core_kernel_classes_Property;
use \Exception;
use \core_kernel_classes_Class;

/**
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package filemanager
 
 */
class DeliveryLinks extends tao_actions_CommonModule {
	
    /**
     * Displays the LTI link for the consumer with respect to the currently selected delviery
     * at tdelviery level, checks if the delviery is related to a resultserver cofnigured with the correct outcome service impelmentation
     * @author patrick <patrick@taotesting.com>
     */
	public function index()
	{
        $feedBackMessage = '';
        //checks the constraint for the results handling, depends on taoResultServer, taoLtiBasicOutcome
        $selectedDelivery = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('uri')));
        try {
            $resultServer = $selectedDelivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP));
        } catch (Exception $e) {
            $feedBackMessage = __("The delivery is not associated to a Result server storage policy");
        }
        $this->setData('launchUrl', LTIDeliveryTool::singleton()->getLaunchUrl(array('delivery' => $selectedDelivery->getUri())));

        if (!empty($feedBackMessage)) {
            $this->setData('warning', $feedBackMessage);
        }
        $class = new core_kernel_classes_Class(CLASS_LTI_CONSUMER);
        $this->setData('consumers', $class->getInstances());
        $this->setData('deliveryLabel', $selectedDelivery->getLabel());
        $this->setView('linkManagement.tpl', 'ltiDeliveryProvider');

    }
}