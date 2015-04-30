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

interface taoResultServer_models_classes_ReadableResultStorage {
    

    /**
     * get the complete variables list stored for a call id (item or test)
     * @param string $callId an execution identifier
     * @return array that contains the variables related to the call id
     * Array
     *(
     *   [uri] => Array
     *   (
     *       [0] => stdClass Object
     *       (
     *           [uri] => uri
     *           [class] => taoResultServer_models_classes_ResponseVariable
     *           [deliveryResultIdentifier] => http://tao.localdomain:8888/tao.rdf#i14176019092304877
     *           [callIdItem] => http://tao.localdomain:8888/tao.rdf#i14176019092304877.item-1.0
     *           [callIdTest] =>
     *           [test] => http://tao.localdomain:8888/tao.rdf#i14175986702737865-
     *           [item] => http://tao.localdomain:8888/tao.rdf#i141631732273405
     *           [variable] => taoResultServer_models_classes_ResponseVariable Object
     *           (
     *               [correctResponse] =>
     *               [candidateResponse] => MQ==
     *               [identifier] => numAttempts
     *               [cardinality] => single
     *               [baseType] => integer
     *               [epoch] => 0.28031200 1417601924
     *           )
     *
     *       )
     *
     *   )
     *
     *   [uri2] => Array
     *   (
     *       [0] => stdClass Object
     *       (
     *           [uri] => uri2
     *           [class] => taoResultServer_models_classes_OutcomeVariable
     *           [deliveryResultIdentifier] => http://tao.localdomain:8888/tao.rdf#i14176019092304877
     *           [callIdItem] => http://tao.localdomain:8888/tao.rdf#i14176019092304877.item-1.0
     *           [callIdTest] =>
     *           [test] => http://tao.localdomain:8888/tao.rdf#i14175986702737865-
     *           [item] => http://tao.localdomain:8888/tao.rdf#i141631732273405
     *           [variable] => taoResultServer_models_classes_OutcomeVariable Object
     *           (
     *               [normalMaximum] =>
     *               [normalMinimum] =>
     *               [value] => Y29tcGxldGVk
     *               [identifier] => completionStatus
     *               [cardinality] => single
     *               [baseType] => identifier
     *               [epoch] => 0.28939600 1417601924
     *           )
     *
     *       )
     *
     *   )
     *
     *)
     */
    public function getVariables($callId);


    /**
     * Get The variable that match params
     * @param string $callId an execution identifier
     * @param string $variableIdentifier the identifier of the variable
     * @return array variable that match call id and variable identifier
     * Array
     *(
     *   [uri] => Array
     *   (
     *       [0] => stdClass Object
     *       (
     *           [uri] => uri
     *           [class] => taoResultServer_models_classes_OutcomeVariable
     *           [deliveryResultIdentifier] => MyDeliveryResultIdentifier#1
     *           [callIdItem] => MyCallId#2
     *           [callIdTest] =>
     *           [test] => MyGreatTest#2
     *           [item] => MyGreatItem#2
     *           [variable] => taoResultServer_models_classes_OutcomeVariable Object
     *           (
     *               [normalMaximum] =>
     *               [normalMinimum] =>
     *               [value] => TXlWYWx1ZQ==
     *               [identifier] => Identifier
     *               [cardinality] => multiple
     *               [baseType] => float
     *               [epoch] => 0.58277800 1417621663
     *           )
     *
     *       )
     *
     *   )
     *
     *)
     */
    public function getVariable($callId, $variableIdentifier);

    /**
     * Get the test taker id related to one specific delivery execution
     * @param string $deliveryResultIdentifier the identifier of the delivery execution
     * @return string the uri of the test taker related to the delivery execution
     */
    public function getTestTaker($deliveryResultIdentifier);


    /**
     * Get the delivery id related to one specific delivery execution
     * @param string $deliveryResultIdentifier the identifier of the delivery execution
     * @return string the uri of the delivery related to the delivery execution
     */
    public function getDelivery($deliveryResultIdentifier);
    
    /**
     * Get the entire list of call ids that are stored (item or test)
     * @return array the list of executions ids (across all results)
     */
    public function getAllCallIds();

    /**
     * get all the ids of test taker that have attempt a test
     * @return array of all test taker ids array(array('deliveryResultIdentifier' => 123, 'testTakerIdentifier' => 456))
     */
    public function getAllTestTakerIds();

    /**
     * get all the ids of delivery that are stored
     * @return array of all delivery ids array(array('deliveryResultIdentifier' => 123, 'deliveryIdentifier' => 456))
     */
    public function getAllDeliveryIds();
}
?>