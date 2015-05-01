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

use oat\taoGroups\models\GroupsService;
use oat\oatbox\user\User;

/**
 * Service to manage the execution of deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 
 */
class taoDelivery_models_classes_DeliveryServerService extends tao_models_classes_GenerisService
{
    public function getResumableDeliveries(User $user)
    {
        $started = is_null($user)
            ? array()
            : taoDelivery_models_classes_execution_ServiceProxy::singleton()->getActiveDeliveryExecutions($user->getIdentifier());
        $resumable = array();
        foreach ($started as $deliveryExecution) {
            $delivery = $deliveryExecution->getDelivery();
            if ($delivery->exists()) {
                $resumable[] = $deliveryExecution;
            }
        }
        return $resumable;
    }
    
    public function getDeliverySettings(core_kernel_classes_Resource $delivery){
        $deliveryProps = $delivery->getPropertiesValues(array(
            new core_kernel_classes_Property(TAO_DELIVERY_MAXEXEC_PROP),
            new core_kernel_classes_Property(TAO_DELIVERY_START_PROP),
            new core_kernel_classes_Property(TAO_DELIVERY_END_PROP),
        ));
        
        $propMaxExec = current($deliveryProps[TAO_DELIVERY_MAXEXEC_PROP]);
        $propStartExec = current($deliveryProps[TAO_DELIVERY_START_PROP]);
        $propEndExec = current($deliveryProps[TAO_DELIVERY_END_PROP]);

        $settings[TAO_DELIVERY_MAXEXEC_PROP] = (!(is_object($propMaxExec)) or ($propMaxExec=="")) ? 0 : $propMaxExec->literal;
        $settings[TAO_DELIVERY_START_PROP] = (!(is_object($propStartExec)) or ($propStartExec=="")) ? null : $propStartExec->literal;
        $settings[TAO_DELIVERY_END_PROP] = (!(is_object($propEndExec)) or ($propEndExec=="")) ? null : $propEndExec->literal;

        return $settings;
    }

    public function isDeliveryExecutionAllowed(core_kernel_classes_Resource $delivery, User $user){

        $userUri = $user->getIdentifier();
        if (is_null($delivery)) {
            common_Logger::w("Attempt to start the compiled delivery ".$delivery->getUri(). " related to no delivery");
            return false;
        }
        
        //first check the user is assigned
        if(!taoDelivery_models_classes_AssignmentService::singleton()->isUserAssigned($delivery, $user)){
            common_Logger::w("User ".$userUri." attempts to start the compiled delivery ".$delivery->getUri(). " he was not assigned to.");
            return false;
        }
        
        $settings = $this->getDeliverySettings($delivery);

        //check Tokens
        $usedTokens = count(taoDelivery_models_classes_execution_ServiceProxy::singleton()->getUserExecutions($delivery, $userUri));
        
        if (($settings[TAO_DELIVERY_MAXEXEC_PROP] !=0 ) and ($usedTokens >= $settings[TAO_DELIVERY_MAXEXEC_PROP])) {
            common_Logger::d("Attempt to start the compiled delivery ".$delivery->getUri(). "without tokens");
            return false;
        }

        //check time
        $startDate  =    date_create('@'.$settings[TAO_DELIVERY_START_PROP]);
        $endDate    =    date_create('@'.$settings[TAO_DELIVERY_END_PROP]);
        if (!$this->areWeInRange($startDate, $endDate)) {
            common_Logger::d("Attempt to start the compiled delivery ".$delivery->getUri(). " at the wrong date");
            return false;
        }
        
        return true;
    }
    
    /**
     * Check if the date are in range
     * @param type $startDate
     * @param type $endDate
     * @return boolean true if in range
     */
    private function areWeInRange($startDate, $endDate){
        return (empty($startDate) || date_create() >= $startDate)
            && (empty($endDate) || date_create() <= $endDate);
    }
    
    /**
     * initalize the resultserver for a given execution
     * @param core_kernel_classes_resource processExecution
     */
    public function initResultServer($compiledDelivery, $executionIdentifier){

        //starts or resume a taoResultServerStateFull session for results submission

        //retrieve the result server definition
        $resultServer = $compiledDelivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP));
        //callOptions are required in the case of a LTI basic storage

        taoResultServer_models_classes_ResultServerStateFull::singleton()->initResultServer($resultServer->getUri());

        //a unique identifier for data collected through this delivery execution
        //in the case of LTI, we should use the sourceId


        taoResultServer_models_classes_ResultServerStateFull::singleton()->spawnResult($executionIdentifier, $executionIdentifier);
         common_Logger::i("Spawning/resuming result identifier related to process execution ".$executionIdentifier);
        //set up the related test taker
        //a unique identifier for the test taker
        taoResultServer_models_classes_ResultServerStateFull::singleton()->storeRelatedTestTaker(common_session_SessionManager::getSession()->getUserUri());

         //a unique identifier for the delivery
        taoResultServer_models_classes_ResultServerStateFull::singleton()->storeRelatedDelivery($compiledDelivery->getUri());
    }
    
    public function getDeliveryFromCompiledDelivery(core_kernel_classes_Resource $compiledDelivery) {
        return $compiledDelivery->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_COMPILEDDELIVERY_DELIVERY));
   }

    public function getAssembliesByGroup(core_kernel_classes_Resource $group) {
        $returnValue = array();
        foreach ($group->getPropertyValues(new core_kernel_classes_Property(PROPERTY_GROUP_DELVIERY)) as $groupUri) {
            $returnValue[] = new core_kernel_classes_Resource($groupUri);
        }
        return $returnValue;
    }
   
}