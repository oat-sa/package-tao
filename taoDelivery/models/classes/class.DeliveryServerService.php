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
 * @subpackage models_classes
 */
class taoDelivery_models_classes_DeliveryServerService extends tao_models_classes_GenerisService
{

    /**
     * Return all available (assigned and compiled) deliveries for the userUri.
     * Delivery settings are returned to identify when and how many tokens are left
     * for this delivery
     */
    public function getAvailableDeliveries($userUri)
    {
        $deliveryService = taoDelivery_models_classes_DeliveryService::singleton();
        $groups = taoGroups_models_classes_GroupsService::singleton()->getGroups($userUri);

        $deliveryCandidates = array();
        foreach ($groups as $group) {
            foreach($deliveryService->getDeliveriesByGroup($group) as $delivery) {
                $deliveryCandidates[$delivery->getUri()] = $delivery;
            }
        }

        // check if realy available
        $compiledDeliveries = array();
        foreach ($deliveryCandidates as $candidate) {
            $compiled = taoDelivery_models_classes_CompilationService::singleton()->getActiveCompilation($candidate);
            
            // compiled?
            if (empty($compiled)) {
                continue;
            }
            // status?
            // period?
            // excluded
            // max executions
            
            //check exclusion
            if($this->isUserExcluded($candidate, $userUri)){
                continue;
            }

            $deliverySettings = $this->getDeliverySettings($candidate);
            $deliverySettings["TAO_DELIVERY_USED_TOKENS"] = $this->getDeliveryUsedTokens($candidate, $userUri);
            $deliverySettings["TAO_DELIVERY_TAKABLE"] = $this->isDeliveryExecutionAllowed($compiled, $userUri);
            $compiledDeliveries[] = array(
                "compiledDelivery"  =>$compiled,
                "settingsDelivery"  =>$deliverySettings
                );
        }
       
        return $compiledDeliveries;
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

    public function getDeliveryUsedTokens(core_kernel_classes_Resource $delivery, $userUri){
        $returnValue = 0;
        $executionClass = new core_kernel_classes_Class(CLASS_DELVIERYEXECUTION);
        $compilations = taoDelivery_models_classes_CompilationService::singleton()->getAllCompilations($delivery);
        foreach ($compilations as $compilation) {
            $returnValue += count($executionClass->searchInstances(array(
                PROPERTY_DELVIERYEXECUTION_SUBJECT  => $userUri,
                PROPERTY_DELVIERYEXECUTION_DELIVERY => $compilation->getUri()
            ), array(
                'like' => false
            )));
        };

        return $returnValue;
    }
    
    public function isDeliveryExecutionAllowed(core_kernel_classes_Resource $compiled, $userUri){

        $delivery = self::getDeliveryFromCompiledDelivery($compiled);
        if (is_null($delivery)) {
            common_Logger::w("Attempt to start the compiled delivery ".$compiled->getUri(). " related to no delivery");
            return false;
        }
        
        //first check the user is assigned
        if(!$this->isUserAssigned($delivery, $userUri)){
            common_Logger::w("User ".$userUri." attempts to start the compiled delivery ".$compiled->getUri(). " he was to assigned to.");
            return false;
        }
        
        //check the user is excluded
        if($this->isUserExcluded($delivery, $userUri)){
            common_Logger::i("User ".$userUri." attempts to start the compiled delivery ".$compiled->getUri(). " he was excluded from.");
            return false;
        }
        
        $settings = $this->getDeliverySettings($delivery);

        //check Tokens
        $usedTokens = $this->getDeliveryUsedTokens($delivery, $userUri);
        
        if (($settings[TAO_DELIVERY_MAXEXEC_PROP] !=0 ) and ($usedTokens >= $settings[TAO_DELIVERY_MAXEXEC_PROP])) {
            common_Logger::i("Attempt to start the compiled delivery ".$compiled->getUri(). "without tokens");
            return false;
        }

        //check time
        $startDate  =    date_create($settings[TAO_DELIVERY_START_PROP]);
        $endDate    =    date_create($settings[TAO_DELIVERY_END_PROP]);
        if (!$this->areWeInRange($startDate, $endDate)) {
            common_Logger::i("Attempt to start the compiled delivery ".$compiled->getUri(). " at the wrong date");
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
            TAO_GROUP_DELIVERIES_PROP => $delivery
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
                $endDate->add(new DateInterval("P1D"));
                $dateCheck = (date_create() >= $startDate and date_create() <= $endDate);
            } else {
                $dateCheck = (date_create() >= $startDate);
            }
        } else {
            if (!empty($endDate)) {
                $endDate->add(new DateInterval("P1D"));
                $dateCheck = (date_create() <= $endDate);
            }
        }
        return $dateCheck;
    }
    
    public function getAllActiveCompilations()
    {
        $deliveryClass = new core_kernel_classes_Class(TAO_DELIVERY_CLASS);
         
        $compiledDeliveries = array();
        foreach ($deliveryClass->getInstances(true) as $candidate) {
            $compiledDeliveries[] = taoDelivery_models_classes_CompilationService::singleton()->getActiveCompilation($candidate);
        }
        return $compiledDeliveries;
    }
    

    /**
     * initalize the resultserver for a given execution
     * @param core_kernel_classes_resource processExecution
     */
    public function initResultServer($compiledDelivery, $executionIdentifier){

        //starts or resume a taoResultServerStateFull session for results submission

        //retrieve the resultServer definition that is related to this delivery to be used
        $delivery = $this->getDeliveryFromCompiledDelivery($compiledDelivery);
        //retrieve the result server definition
        $resultServer = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP));
        //callOptions are required in the case of a LTI basic storage

        taoResultServer_models_classes_ResultServerStateFull::singleton()->initResultServer($resultServer->getUri());

        //a unique identifier for data collected through this delivery execution
        //in the case of LTI, we should use the sourceId

        //the dependency to taoResultServer should be re-thinked with respect to a delivery level proxy
        taoResultServer_models_classes_ResultServerStateFull::singleton()->spawnResult($executionIdentifier);
         common_Logger::i("Spawning/resuming result identifier related to process execution ".$executionIdentifier);
        //set up the related test taker
        //a unique identifier for the test taker
        taoResultServer_models_classes_ResultServerStateFull::singleton()->storeRelatedTestTaker(wfEngine_models_classes_UserService::singleton()->getCurrentUser()->getUri());

         //a unique identifier for the delivery
        taoResultServer_models_classes_ResultServerStateFull::singleton()->storeRelatedDelivery($delivery->getUri());
    }
    
    public function getDeliveryFromCompiledDelivery(core_kernel_classes_Resource $compiledDelivery) {
        return $compiledDelivery->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_COMPILEDDELIVERY_DELIVERY));
   }
}