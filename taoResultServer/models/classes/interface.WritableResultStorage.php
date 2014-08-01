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

interface taoResultServer_models_classes_WritableResultStorage {

    /**
     * Optionnally spawn a new result and returns
     * an identifier for it, use of the other services with an unknow identifier
     * will trigger the spawning of a new result
     * you may also provide your own identifier to the other services like a lis_result_sourcedid:GUID
     * @return string deliveryResultIdentifier
     */
    public function spawnResult();

    //public function __construct($callId, $test);
    /**
     * @param deliveryResultIdentifier (example : lis_result_sourcedid)
     * @param string testTakerIdentifier (uri recommended)
     *
     */
    public function storeRelatedTestTaker($deliveryResultIdentifier, $testTakerIdentifier);

    /**
     * @param string deliveryIdentifier (uri recommended)
     */
    public function storeRelatedDelivery($deliveryResultIdentifier, $deliveryIdentifier);

    /**
     * Submit a specific Item Variable, (ResponseVariable and OutcomeVariable shall be used respectively for collected data and score/interpretation computation)
     * @param string test (uri recommended)
     * @param string item (uri recommended)
     * @param taoResultServer_models_classes_ItemVariable itemVariable
     * @param string callId contextual call id for the variable, ex. :  to distinguish the same variable output by the same item and that is presented several times in the same test
     * 
     */
    public function storeItemVariable($deliveryResultIdentifier, $test, $item, taoResultServer_models_classes_Variable $itemVariable, $callIdItem );

    /**
     *  CreateResultValue(sourcedId,ResultValueRecord)
     *  CreateLineItem(sourcedId,lineItemRecord:LineItemRecord)
     */
    public function storeTestVariable($deliveryResultIdentifier, $test, taoResultServer_models_classes_Variable $testVariable, $callIdTest);

    /**
     * The storage may configure itselfs based on the resultServer definition
     */
    public function configure(core_kernel_classes_Resource $resultServer, $callOptions = array());
    
    /** Submit a complete Item result
    *
    * @param taoResultServer_models_classes_ItemResult itemResult
    * @param string callId an id for the item instanciation
    */
    //public function setItemResult($item, taoResultServer_models_classes_ItemResult $itemResult, $callId);
    
    //public function setTestResult($test, taoResultServer_models_classes_TestResult $testResult, $callId);
    
}
?>