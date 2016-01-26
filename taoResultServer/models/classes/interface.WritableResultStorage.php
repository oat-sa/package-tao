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
 * The WritableResultStorage interface.
 * 
 * The WritableResultStorage interface describes all the methods to write results of deliveries
 * taken by test takers into a specific Result Server implementation.
 * 
 * @author Joel Bout <joel@taotesting.com>
 * @author Antoine Robin <antoine.robin@vesperiagroup.com>
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
interface taoResultServer_models_classes_WritableResultStorage {

    /**
     * Initialize a new raw Delivery Result.
     * 
     * After initialization, the Delivery Result will be empty, and will not be linked
     * to a Test Taker or a Delivery. 
     * 
     * Please note that it is the responisibility of the implementer to generate Delivery
     * Result identifiers that are as unique as possible.
     * 
     * @return string The unique identifier of the initialized Delivery Result.
     */
    public function spawnResult();

    /**
     * Attach a given Test Taker to a Delivery Result.
     * 
     * A Delivery Result is always attached to a single Test Taker. This method enables
     * the client code to register a given Test Taker, using its $testTakerIdentifier, to a
     * given Delivery Result, using its $deliveryResultIdentifier.
     * 
     * @param string $deliveryResultIdentifier The identifier of the Delivery Result (usually a Delivery Execution URI).
     * @param string $testTakerIdentifier The identifier of the Test Taker (usually a URI).
     *
     */
    public function storeRelatedTestTaker($deliveryResultIdentifier, $testTakerIdentifier);

    /**
     * Store a delivery related to a specific delivery execution
     * @param string $deliveryResultIdentifier (mostly delivery execution uri)
     * @param string $deliveryIdentifier (uri recommended)
     */
    public function storeRelatedDelivery($deliveryResultIdentifier, $deliveryIdentifier);

    /**
     * Submit a specific Item Variable, (ResponseVariable and OutcomeVariable shall be used respectively for collected data and score/interpretation computation)
     * and store it with all the dependencies
     * @param string $deliveryResultIdentifier
     * @param string $test (uri recommended)
     * @param string $item (uri recommended)
     * @param taoResultServer_models_classes_Variable $itemVariable the variable to store
     * @param string $callIdItem contextual call id for the variable, ex. :  to distinguish the same variable output by the same item and that is presented several times in the same test
     * 
     */
    public function storeItemVariable($deliveryResultIdentifier, $test, $item, taoResultServer_models_classes_Variable $itemVariable, $callIdItem );

    /**
     * Submit a specific test Variable and store it
     * @param string $deliveryResultIdentifier
     * @param string $test
     * @param taoResultServer_models_classes_Variable $testVariable
     * @param $callIdTest
     */
    public function storeTestVariable($deliveryResultIdentifier, $test, taoResultServer_models_classes_Variable $testVariable, $callIdTest);


    /**
     * The storage may configure itself based on the resultServer definition
     * @param core_kernel_classes_Resource $resultServer
     * @param array $callOptions
     */
    public function configure(core_kernel_classes_Resource $resultServer, $callOptions = array());
    
}
?>