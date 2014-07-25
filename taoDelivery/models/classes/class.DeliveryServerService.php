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

/**
 * Service to manage the execution of deliveries
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 
 */
class taoDelivery_models_classes_DeliveryServerService extends tao_models_classes_GenerisService
{
    public function getResumableDeliveries($userUri)
    {
        $started = is_null($userUri)
            ? array()
            : taoDelivery_models_classes_execution_ServiceProxy::singleton()->getActiveDeliveryExecutions($userUri);
        $resumable = array();
        foreach ($started as $deliveryExecution) {
            $delivery = $deliveryExecution->getDelivery();
            if ($delivery->exists()) {
                $resumable[] = $deliveryExecution;
            }
        }
        return $resumable;
    }
    
    /**
     * Return all available (assigned and compiled) deliveries for the userUri.
     * Delivery settings are returned to identify when and how many tokens are left
     * for this delivery
     */
    public function getAvailableDeliveries($userUri)
    {
        $deliveryService = taoDelivery_models_classes_DeliveryAssemblyService::singleton();
        $groups = taoGroups_models_classes_GroupsService::singleton()->getGroups($userUri);

        // check if realy available
        $assemblyData = array();
        foreach ($groups as $group) {
            foreach($this->getAssembliesByGroup($group) as $candidate) {
    
                // status?
                // period?
                // excluded
                // max executions
                
                //check exclusion
                if($this->isUserExcluded($candidate, $userUri)){
                    continue;
                }
    
                $deliverySettings = $this->getDeliverySettings($candidate);
                $deliverySettings["TAO_DELIVERY_USED_TOKENS"] = count(taoDelivery_models_classes_execution_ServiceProxy::singleton()->getUserExecutions($candidate, $userUri));
                $deliverySettings["TAO_DELIVERY_TAKABLE"] = $this->isDeliveryExecutionAllowed($candidate, $userUri);
                $assemblyData[] = array(
                    "compiledDelivery"  =>$candidate,
                    "settingsDelivery"  =>$deliverySettings
                );
            }
        }
       
        return $assemblyData;
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

    /**
     * Moved to taoDelivery_models_classes_execution_ServiceProxy
     * 
     * @deprecated
     * @param core_kernel_classes_Resource $delivery
     * @param string $userUri
     */
    public function getDeliveryUsedTokens(core_kernel_classes_Resource $delivery, $userUri){
        return count(taoDelivery_models_classes_execution_ServiceProxy::singleton()->getUserExecutions($delivery, $userUri));
    }
    
    public function isDeliveryExecutionAllowed(core_kernel_classes_Resource $delivery, $userUri){

        if (is_null($delivery)) {
            common_Logger::w("Attempt to start the compiled delivery ".$delivery->getUri(). " related to no delivery");
            return false;
        }
        
        //first check the user is assigned
        if(!$this->isUserAssigned($delivery, $userUri)){
            common_Logger::w("User ".$userUri." attempts to start the compiled delivery ".$delivery->getUri(). " he was to assigned to.");
            return false;
        }
        
        //check the user is excluded
        if($this->isUserExcluded($delivery, $userUri)){
            common_Logger::d("User ".$userUri." attempts to start the compiled delivery ".$delivery->getUri(). " he was excluded from.");
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
     * Check if a user is excluded from a delivery
     * @param core_kernel_classes_Resource $delivery
     * @param string $userUri the URI of the user to check
     * @return boolean true if excluded
     */
    private function isUserExcluded(core_kernel_classes_Resource $delivery, $userUri){
        
        $excluded = true;
        if(!is_null($delivery)){
            $excludedUsers = $delivery->getPropertyValues(new core_kernel_classes_Property(TAO_DELIVERY_EXCLUDEDSUBJECTS_PROP));
            $excluded = in_array($userUri, $excludedUsers);
        } 
        return $excluded;
    }
    
    /**
     * Check if a user is assigned to a delivery
     * 
     * @param core_kernel_classes_Resource $delivery
     * @param string $userUri the URI of the user to check
     * @return boolean true if assigned
     */
    private function isUserAssigned(core_kernel_classes_Resource $delivery, $userUri){

        $groupClass = new core_kernel_classes_Class(TAO_GROUP_CLASS);
        $groups = $groupClass->searchInstances(array(
            TAO_GROUP_MEMBERS_PROP => $userUri,
            PROPERTY_GROUP_DELVIERY => $delivery
        ), array(
            'like'=>false,
            'recursive' => true
        ));
        return !empty($groups);
    }
    
    /**
     * Check if the date are in range
     * @param type $startDate
     * @param type $endDate
     * @return boolean true if in range
     */
    private function areWeInRange($startDate, $endDate){
        $dateCheck = true;
        if (!empty($startDate)) {
            if (!empty($endDate)) {
                $dateCheck = (date_create() >= $startDate and date_create() <= $endDate);
            } else {
                $dateCheck = (date_create() >= $startDate);
            }
        } else {
            if (!empty($endDate)) {
                $dateCheck = (date_create() <= $endDate);
            }
        }
        return $dateCheck;
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
        taoResultServer_models_classes_ResultServerStateFull::singleton()->storeRelatedTestTaker(wfEngine_models_classes_UserService::singleton()->getCurrentUser()->getUri());

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