<?php

/**
 * todo move it in the taoResults
 * Implements the Services for the storage of item and test variables,
 * This implementations depends on results for the the physical storage
 * TODO : move the impl to results services
 * @author plichart
 */
class taoResults_models_classes_DbResult
    extends tao_models_classes_GenerisService
    implements taoResultServer_models_classes_ResultStorage {

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
    public function spawnResult(){
        
        $spawnedResult =  $this->taoResultsStorage->storeDeliveryResult()->getUri();
        common_Logger::i("taoResults storage spawned result:".$spawnedResult);
        return $spawnedResult;
    }
    /**
    * @param string testTakerIdentifier (uri recommended)
     *
    */
    public function storeRelatedTestTaker($deliveryResultIdentifier, $testTakerIdentifier) {
         //spawns a new delivery result or retrieve an existing one with this identifier
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
            /*
     *  CreateResultValue(sourcedId,ResultValueRecord)
     *  CreateLineItem(sourcedId,lineItemRecord:LineItemRecord)
     */
    }
    /** Submit a complete Item result
    *
    * @param taoResultServer_models_classes_ItemResult itemResult
    * @param string callId an id for the item instanciation
    */
//    public function setItemResult($item, taoResultServer_models_classes_ItemResult $itemResult, $callId ) {}
//    public function setTestResult($test, taoResultServer_models_classes_TestResult $testResult, $callId){}

    public function storeTestVariable($deliveryResultIdentifier, $test, taoResultServer_models_classes_Variable $testVariable, $callIdTest){
        $deliveryResult = $this->taoResultsStorage->storeDeliveryResult($deliveryResultIdentifier);
        $this->taoResultsStorage->storeTestVariable($deliveryResult, $test,  $testVariable, $callIdTest);

    }

    public function configure(core_kernel_classes_Resource $resultServer, $callOptions = array()) {
        //nothing to configure in the case of taoResults storage
    }

}
?>