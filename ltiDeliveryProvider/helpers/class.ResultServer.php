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
 * Copyright (c) 2013 Open Assessment Technologies S.A.
 
 * 
 */

class ltiDeliveryProvider_helpers_ResultServer
{

    public static function initLtiResultServer(core_kernel_classes_Resource $delivery, core_kernel_classes_Resource $deliveryExecution, $launchData) {
	      $storageImplFromLaunch = array(
            array(
	        "implementation" =>"taoLtiBasicOutcome_models_classes_LtiBasicOutcome",
	        "parameters" => array(
                "result_identifier" => $launchData->getVariable("lis_result_sourcedid"),
                "consumer_key" => $launchData->getOauthKey(),
                "service_url" => $launchData->getVariable("lis_outcome_service_url"),
                "user_identifier" => common_session_SessionManager::getSession()->getUserUri(),
	            "user_fullName" => ($launchData->hasVariable(taoLti_models_classes_LtiLaunchData::LIS_PERSON_NAME_FULL)
	                ? $launchData->getVariable(taoLti_models_classes_LtiLaunchData::LIS_PERSON_NAME_FULL)
	                : '')
                )
            )
	    );

	    try {
        $resultServer = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP));
        } catch (Exception $e) {
            //No static result server was associated with the delivery
            $resultServer = new core_kernel_classes_Resource(TAO_VOID_RESULT_SERVER);
        }

        /*todo manage result dientifier per storage */
        $storage = current($storageImplFromLaunch);
        $storageParameters = $storage["parameters"];
        $launchResultIdentifier = $storageParameters["result_identifier"];
        $launchUserIdentifier = ($storageParameters["user_identifier"]=="") ? $storageParameters["user_identifier"] : $storageParameters["user_fullName"];
       

	    taoResultServer_models_classes_ResultServerStateFull::singleton()->initResultServer($resultServer->getUri(), $storageImplFromLaunch);
	    $resultIdentifier = (isset($launchResultIdentifier)) ? $launchResultIdentifier :$deliveryExecution->getUri();
	    taoResultServer_models_classes_ResultServerStateFull::singleton()->spawnResult($deliveryExecution->getUri(), $resultIdentifier);
	    common_Logger::i("Spawning".$resultIdentifier ."related to process execution ".$deliveryExecution->getUri());
	    $userIdentifier = (isset($launchUserIdentifier)) ? $launchUserIdentifier :wfEngine_models_classes_UserService::singleton()->getCurrentUser()->getUri();
	  
        taoResultServer_models_classes_ResultServerStateFull::singleton()->storeRelatedTestTaker( $userIdentifier);
	    taoResultServer_models_classes_ResultServerStateFull::singleton()->storeRelatedDelivery($delivery->getUri());
	}


   
}

?>
