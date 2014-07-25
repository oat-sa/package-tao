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
 * @package taoResultServer
 
 * @license GPLv2  
 * 
 * A session for a particular delivery execution/session on the corresponding result server
 * Statefull api for results submission
 * 
 */
class taoResultServer_models_classes_ResultServerStateFull extends tao_models_classes_GenerisService
{

    /**
     * constructor: initialize the service and the default data
     * 
     * @return Delivery
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     * @author "Patrick Plichart, <patrick@taotesting.com>"
     * @param string $resultServerUri            
     * @param string $callOptions            
     * @throws common_exception_MissingParameter
     */
    public function initResultServer($resultServerUri, $callOptions = null)
    {
        if (common_Utils::isUri($resultServerUri)) {
            PHPSession::singleton()->setAttribute("resultServerUri", $resultServerUri);
            
            // check if a resultServer has already been intialized for this definition
            $initializedResultServer = null;
            $listResultServers = array();
            if (PHPSession::singleton()->hasAttribute("resultServerObject")) {
                $listResultServers = PHPSession::singleton()->getAttribute("resultServerObject");
                if (isset($listResultServers[$resultServerUri])) {
                    $initializedResultServer = $listResultServers[$resultServerUri];
                }
            }
            
            if (is_null($callOptions) and (! (is_null($initializedResultServer)))) {
                // the policy is that if the result server has already been intialized and configured further calls without callOptions will reuse the same calloptions
            } else {
                $listResultServers[$resultServerUri] = new taoResultServer_models_classes_ResultServer($resultServerUri, $callOptions);
                PHPSession::singleton()->setAttribute("resultServerObject", $listResultServers);
            }
        } else {
            throw new common_exception_MissingParameter("resultServerUri");
        }
    }

    /**
     *
     * @author "Patrick Plichart, <patrick@taotesting.com>"
     * @throws common_exception_PreConditionFailure
     * @return Ambiguous
     */
    private function restoreResultServer()
    {
        if (PHPSession::singleton()->hasAttribute("resultServerUri")) {
            $resultServerUri = PHPSession::singleton()->getAttribute("resultServerUri");
            $callOptions = array();
            if (PHPSession::singleton()->hasAttribute("resultServerObject")) {
                $callOptionsList = PHPSession::singleton()->getAttribute("resultServerObject");
                if (isset($callOptionsList[$resultServerUri])) {
                    return $callOptionsList[$resultServerUri];
                }
            }
        } else {
            throw new common_exception_PreConditionFailure("The result server hasn't been initalized");
        }
    }

    /**
     *
     * @example http://tao-dev/taoResultServer/ResultServerStateFull/spawnResult
     * @return type
     */
    public function spawnResult($deliveryExecutionIdentifier, $deliveryResultIdentifier = null)
    {
        if ($deliveryResultIdentifier == null) {
            $resultServer = $this->restoreResultServer();
            if ((PHPSession::singleton()->hasAttribute("resultServer_deliveryExecutionIdentifier")) and ((PHPSession::singleton()->getAttribute("resultServer_deliveryExecutionIdentifier")) == $deliveryExecutionIdentifier)) {
                $resultServer_deliveryResultIdentifier = PHPSession::singleton()->getAttribute("resultServer_deliveryResultIdentifier");
            } else {
                $resultServer_deliveryResultIdentifier = $resultServer->getStorageInterface()->spawnResult();
            }
        } else {
            $resultServer_deliveryResultIdentifier = $deliveryResultIdentifier;
        }
        PHPSession::singleton()->setAttribute("resultServer_deliveryResultIdentifier", $resultServer_deliveryResultIdentifier);
        PHPSession::singleton()->setAttribute("resultServer_deliveryExecutionIdentifier", $deliveryExecutionIdentifier);
        return $resultServer_deliveryResultIdentifier;
    }

    /**
     * http://tao-dev/taoResultServer/ResultServerStateFull/storeRelatedTestTaker?testTakerIdentifier=15
     * 
     * @param string $testTakerIdentifier may be different from a uri
     * @return type
     */
    public function storeRelatedTestTaker($testTakerIdentifier)
    {
        if ($testTakerIdentifier != "") {
            $resultServer = $this->restoreResultServer();
            $resultServer->getStorageInterface()->storeRelatedTestTaker(PHPSession::singleton()->getAttribute("resultServer_deliveryResultIdentifier"), $testTakerIdentifier);
            return PHPSession::singleton()->getAttribute("resultServer_deliveryResultIdentifier");
        } else {
            throw new common_exception_MissingParameter("testTakerIdentifier");
        }
    }

    /**
     *
     * @example http://tao-dev/taoResultServer/ResultServerStateFull/storeRelatedDelivery?deliveryIdentifier=12
     * @param type $deliveryResultIdentifier            
     * @param type $deliveryIdentifier            
     * @return type
     */
    public function storeRelatedDelivery($deliveryIdentifier)
    {
        $resultServer = $this->restoreResultServer();
        if ($deliveryIdentifier != "") {
            $resultServer->getStorageInterface()->storeRelatedDelivery(PHPSession::singleton()->getAttribute("resultServer_deliveryResultIdentifier"), $deliveryIdentifier);
            return PHPSession::singleton()->getAttribute("resultServer_deliveryResultIdentifier");
        } else {
            throw new common_exception_MissingParameter("deliveryIdentifier");
        }
    }

    public function storeItemVariable($test, $item, taoResultServer_models_classes_Variable $itemVariable, $callIdItem)
    {
        $resultServer = $this->restoreResultServer();
        $resultServer->getStorageInterface()->storeItemVariable(PHPSession::singleton()->getAttribute("resultServer_deliveryResultIdentifier"), $test, $item, $itemVariable, $callIdItem);
        return PHPSession::singleton()->getAttribute("resultServer_deliveryResultIdentifier");
    }

    /**
     *
     * @param string $test Ideally the URI of the test.
     * @param string $item Ideally the URI of the item.
     * @param array $itemVariableSet An array of taoResultServer_models_classes_Variable objects.
     * @param string $callIdItem An identifier that identifies uniquely an item delivery.
     * @return string The identifier of the delivery result.
     * @throws Exception
     */
    public function storeItemVariableSet($test, $item, $itemVariableSet, $callIdItem)
    {
        $resultServer = $this->restoreResultServer();
        $storageInterface = $resultServer->getStorageInterface();
        foreach ($itemVariableSet as $itemVariable) {
            $storageInterface->storeItemVariable(PHPSession::singleton()->getAttribute("resultServer_deliveryResultIdentifier"), $test, $item, $itemVariable, $callIdItem);
        }
        return PHPSession::singleton()->getAttribute("resultServer_deliveryResultIdentifier");
    }

    /**
     *
     * @param type $test an identifier for the test uri rpeferred
     * @param taoResultServer_models_classes_Variable $testVariable            
     * @param type $callIdTest a call test reference (distinguish test being embdded twice)
     * @return type
     */
    public function storeTestVariable($test, taoResultServer_models_classes_Variable $testVariable, $callIdTest)
    {
        $resultServer = $this->restoreResultServer();
        $resultServer->getStorageInterface()->storeTestVariable(PHPSession::singleton()->getAttribute("resultServer_deliveryResultIdentifier"), $test, $testVariable, $callIdTest);
        return PHPSession::singleton()->getAttribute("resultServer_deliveryResultIdentifier");
    }
    
    public function getVariables($callId){
        $resultServer = $this->restoreResultServer();
        return $resultServer->getStorageInterface()->getVariables($callId) ;
        
    }    
    public function getVariable($callId, $variableIdentifier){
        $resultServer = $this->restoreResultServer();
        return $resultServer->getStorageInterface()->getVariable($callId, $variableIdentifier) ;
    }
    public function getTestTaker($deliveryResultIdentifier){
        $resultServer = $this->restoreResultServer();
        return $resultServer->getStorageInterface()->getTestTaker($deliveryResultIdentifier) ;
    }
    public function getDelivery($deliveryResultIdentifier){
        $resultServer = $this->restoreResultServer();
        return $resultServer->getStorageInterface()->getDelivery($deliveryResultIdentifier) ;
    }
}
?>