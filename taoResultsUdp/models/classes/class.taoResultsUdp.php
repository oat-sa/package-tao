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
 * Implements tao results storage storing the data in simple ascii files
 *
 */

class taoResultsUdp_models_classes_taoResultsUdp
    extends tao_models_classes_GenerisService
    implements taoResultServer_models_classes_ResultStorage {



      //basic socket resource handling specific to that example
    private $cfgHost = "127.0.0.1";
    private $cfgPort = 65538;
    private $socketRes;
    private function initSocket(){
        if (is_null($this->socketRes)) {
            $this->socketRes = fsockopen($this->cfgHost, $this->cfgPort);
        }
    }
    private function send($message){
       fwrite($this->socketRes, $message);
    }
    public function __destruct() {
        fclose($this->socketRes);
    }


    //implementation of the called services for the data storage
    public function storeRelatedTestTaker($deliveryResultIdentifier, $testTakerIdentifier) {
        $this->send("Test Taker identification ".$deliveryResultIdentifier." ".$testTakerIdentifier);
    }

    public function storeRelatedDelivery($deliveryResultIdentifier, $deliveryIdentifier) {
         $this->send("Delivery identification ".$deliveryResultIdentifier." ".$deliveryIdentifier);
    }

    public function storeItemVariable($deliveryResultIdentifier, $test, $item, taoResultServer_models_classes_Variable $itemVariable, $callIdItem){
         $this->send("Item Variable ".$deliveryResultIdentifier." ".$itemVariable->getIdentifier()." ".$itemVariable->getValue());
    }

    public function __construct(){
		parent::__construct();
        common_ext_ExtensionsManager::singleton()->getExtensionById("taoResultsUdp");
        $this->initSocket();
    }
    /**
     * @param type $deliveryResultIdentifier lis_result_sourcedid
     * @param type $test ignored
     * @param taoResultServer_models_classes_Variable $testVariable
     * @param type $callIdTest ignored
     */
    public function storeTestVariable($deliveryResultIdentifier, $test, taoResultServer_models_classes_Variable $testVariable, $callIdTest){
        $this->send($deliveryResultIdentifier." Test Variable ".$testVariable->getIdentifier()." ".$itemVariable->getValue());
    }
    /*
    * retrieve specific parameters from the resultserver to configure the storage
    */
    /*sic*/
    public function configure(core_kernel_classes_Resource $resultserver, $callOptions = array()) {
       common_Logger::i("Some logged information");
       //no configuration adaptation based on call parameters of he driver.
    }
     /**
     * In the case of An LtiBasic OutcomeSubmission, spawnResult has no effect
     */
    public function spawnResult(){
       return uniqid();
    }


}
?>