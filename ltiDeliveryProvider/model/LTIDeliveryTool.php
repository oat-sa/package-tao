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

namespace oat\ltiDeliveryProvider\model;

use oat\ltiDeliveryProvider\helper\ResultServer;
use \taoLti_models_classes_LtiTool;
use \taoLti_models_classes_LtiService;
use \core_kernel_classes_Property;
use \core_kernel_classes_Resource;
use \core_kernel_classes_Class;
use \common_session_SessionManager;
use \taoDelivery_models_classes_execution_ServiceProxy;

class LTIDeliveryTool extends taoLti_models_classes_LtiTool {
	
	const TOOL_INSTANCE = 'http://www.tao.lu/Ontologies/TAOLTI.rdf#LTIToolDelivery';
	
    const EXTENSION = 'ltiDeliveryProvider';
	const MODULE = 'DeliveryTool';
	const ACTION = 'launch';
	
	public function getLaunchUrl($parameters = array()) {
		$fullAction = self::ACTION.'/'.base64_encode(json_encode($parameters));
		return _url($fullAction, self::MODULE, self::EXTENSION);
	}
	
	public function getDeliveryFromLink() {
		$remoteLink = taoLti_models_classes_LtiService::singleton()->getLtiSession()->getLtiLinkResource();
		return $remoteLink->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_LINK_DELIVERY));
	}
	
	public function linkDeliveryExecution(core_kernel_classes_Resource $link, $userUri, core_kernel_classes_Resource $deliveryExecution) {
	    
	    $class = new core_kernel_classes_Class(CLASS_LTI_DELIVERYEXECUTION_LINK);
	    $link = $class->createInstanceWithProperties(array(
	        PROPERTY_LTI_DEL_EXEC_LINK_USER => $userUri,
	        PROPERTY_LTI_DEL_EXEC_LINK_LINK => $link,
            PROPERTY_LTI_DEL_EXEC_LINK_EXEC_ID => $deliveryExecution
	    ));
	    return $link instanceof core_kernel_classes_Resource;
	}

	/**
	 * Start a new delivery execution
	 * 
	 * @param core_kernel_classes_Resource $delivery
	 * @param core_kernel_classes_Resource $link
	 * @param string $userUri
	 * @return \taoDelivery_models_classes_execution_DeliveryExecution
	 */
	public function startDelivery(core_kernel_classes_Resource $delivery, core_kernel_classes_Resource $link, $userId) {
	    $deliveryExecution = taoDelivery_models_classes_execution_ServiceProxy::singleton()->initDeliveryExecution(
	        $delivery,
	        $userId
	    );
	    $class = new core_kernel_classes_Class(CLASS_LTI_DELIVERYEXECUTION_LINK);
	    $class->createInstanceWithProperties(array(
	        PROPERTY_LTI_DEL_EXEC_LINK_USER => $userId,
	        PROPERTY_LTI_DEL_EXEC_LINK_LINK => $link,
	        PROPERTY_LTI_DEL_EXEC_LINK_EXEC_ID => $deliveryExecution->getIdentifier()
	    ));
	    return $deliveryExecution;
	}
	
	/**
	 * Returns an array of taoDelivery_models_classes_execution_DeliveryExecution
	 * 
	 * @param core_kernel_classes_Resource $delivery
	 * @param core_kernel_classes_Resource $link
	 * @param string $userId
	 * @return array
	 */
	public function getLinkedDeliveryExecutions(core_kernel_classes_Resource $delivery, core_kernel_classes_Resource $link, $userId) {
	    
	    $class = new core_kernel_classes_Class(CLASS_LTI_DELIVERYEXECUTION_LINK);
	    $links = $class->searchInstances(array(
	    	PROPERTY_LTI_DEL_EXEC_LINK_USER => $userId,
	        PROPERTY_LTI_DEL_EXEC_LINK_LINK => $link,
	    ), array(
	    	'like' => false
	    ));
	    $returnValue = array();
	    foreach ($links as $link) {
	        $execId = $link->getUniquePropertyValue(new \core_kernel_classes_Property(PROPERTY_LTI_DEL_EXEC_LINK_EXEC_ID));
	        $deliveryExecution = \taoDelivery_models_classes_execution_ServiceProxy::singleton()->getDeliveryExecution($execId);
	        if ($delivery->equals($deliveryExecution->getDelivery())) {
	            $returnValue[] = $deliveryExecution;
	        }
	    }
	    return $returnValue;
	}
}