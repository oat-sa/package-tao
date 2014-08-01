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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */
class taoResults_models_classes_DbResult 
    extends tao_models_classes_GenerisService 
    implements taoResultServer_models_classes_WritableResultStorage {

    private $taoResultsStorage;


    /**
    * @param string deliveryResultIdentifier if no such deliveryResult with this identifier exists a new one gets created
    */

    public function __construct(){
		parent::__construct();
        common_ext_ExtensionsManager::singleton()->getExtensionById("taoResults");
		$this->taoResultsStorage = new taoResults_models_classes_ResultsService();
       
    }
    /**
     * In the case of a taoResultsDB storage and if the consumer asks for an identifer a uri is returned
     * * //you may also provide your own identifier to the other services like a lis_result_sourcedid:GUID
     */
    public function spawnResult($deliveryResultIdentifier = null){
        
        $spawnedResult =  $this->taoResultsStorage->storeDeliveryResult($deliveryResultIdentifier)->getUri();
        common_Logger::i("taoResults storage spawned result:".$spawnedResult);
        return $spawnedResult;
    }
    /**
    * @param string testTakerIdentifier (uri recommended)
     *
    */
    public function storeRelatedTestTaker($deliveryResultIdentifier, $testTakerIdentifier) {
            // spawns a new delivery result or retrieve an existing one with this identifier
        $deliveryResult = $this->taoResultsStorage->storeDeliveryResult($deliveryResultIdentifier);
        $this->taoResultsStorage->storeTestTaker($deliveryResult, $testTakerIdentifier);
    }
    /**
    * @param string deliveryIdentifier (uri recommended)
    */
    public function storeRelatedDelivery($deliveryResultIdentifier, $deliveryIdentifier) {
         //spawns a new delivery result or retrieve an existing one with this identifier
       $deliveryResult = $this->taoResultsStorage->storeDeliveryResult($deliveryResultIdentifier);
        $this->taoResultsStorage->storeDelivery($deliveryResult, $deliveryIdentifier);
    }
    /**
    * Submit a specific Item Variable, (ResponseVariable and OutcomeVariable shall be used respectively for collected data and score/interpretation computation)
    * @param string test (uri recommended)
    * @param string item (uri recommended)
    * @param taoResultServer_models_classes_ItemVariable itemVariable
    * @param string callId contextual call id for the variable, ex. :  to distinguish the same variable output by the same item but taht is presented several times in the same test 
    */
    public function storeItemVariable($deliveryResultIdentifier, $test, $item, taoResultServer_models_classes_Variable $itemVariable, $callIdItem){
         //spawns a new delivery result or retrieve an existing one with this identifier
       $deliveryResult = $this->taoResultsStorage->storeDeliveryResult($deliveryResultIdentifier);
        $this->taoResultsStorage->storeItemVariable($deliveryResult, $test, $item, $itemVariable, $callIdItem);

    }
    /** Submit a complete Item result
    *
    * @param taoResultServer_models_classes_ItemResult itemResult
    * @param string callId an id for the item instanciation
    */
    public function storeTestVariable($deliveryResultIdentifier, $test, taoResultServer_models_classes_Variable $testVariable, $callIdTest){
        $deliveryResult = $this->taoResultsStorage->storeDeliveryResult($deliveryResultIdentifier);
        $this->taoResultsStorage->storeTestVariable($deliveryResult, $test,  $testVariable, $callIdTest);

    }

    public function configure(core_kernel_classes_Resource $resultServer, $callOptions = array()) {
        //nothing to configure in the case of taoResults storage
    }

}
?>