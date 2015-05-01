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
 * 
 * @author "Patrick Plichart, <patrick@taotesting.com>"
 * @package taoResultServer
 */
class taoResultServer_models_classes_LoggerStorage 
extends tao_models_classes_GenerisService 
implements taoResultServer_models_classes_WritableResultStorage
{
    
    /*
     * (non-PHPdoc) @see taoResultServer_models_classes_WritableResultStorage::storeRelatedTestTaker()
     */
    public function storeRelatedTestTaker($deliveryResultIdentifier, $testTakerIdentifier)
    {
        common_logger::i("LoggerStorage - Test taker storage :" . $testTakerIdentifier . " into " . $deliveryResultIdentifier);
    }
    
    /*
     * (non-PHPdoc) @see taoResultServer_models_classes_WritableResultStorage::storeRelatedDelivery()
     */
    public function storeRelatedDelivery($deliveryResultIdentifier, $deliveryIdentifier)
    {
        common_logger::i("LoggerStorage - Delivery storage:" . $deliveryResultIdentifier . " into " . $deliveryResultIdentifier);
    }
    
    /*
     * (non-PHPdoc) @see taoResultServer_models_classes_WritableResultStorage::storeItemVariable()
     */
    public function storeItemVariable($deliveryResultIdentifier, $test, $item, taoResultServer_models_classes_Variable $itemVariable, $callIdItem)
    {
        common_logger::i("LoggerStorage - StoreItemVariable :" . $test . " item:" . $item . " callid:" . $callIdItem . "variable:" . serialize($itemVariable) . " into " . $deliveryResultIdentifier);
    }
    
    /*
     * (non-PHPdoc) @see taoResultServer_models_classes_WritableResultStorage::storeTestVariable()
     */
    public function storeTestVariable($deliveryResultIdentifier, $test, taoResultServer_models_classes_Variable $testVariable, $callIdTest)
    {
        common_logger::i("LoggerStorage - StoreTestVariable :" . $test . " callid:" . $callIdTest . "variable:" . serialize($testVariable) . " into " . $deliveryResultIdentifier);
    }
    
    /*
     * (non-PHPdoc) @see taoResultServer_models_classes_WritableResultStorage::configure()
     */
    public function configure(core_kernel_classes_Resource $resultServer, $callOptions = array())
    {
        common_logger::i("LoggerStorage - configuration:" . $resultServer . " configuration:" . serialize($callOptions));
    }
    
    /*
     * (non-PHPdoc) @see taoResultServer_models_classes_WritableResultStorage::spawnResult()
     */
    public function spawnResult()
    {
        common_logger::i("LoggerStorage - Spawn request made");
    }
}
?>