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
 * @author "Lionel Lecaque, <lionel@taotesting.com>"
 * @package taoResults
 
 */
require_once dirname(__FILE__) . '/../../tao/test/TaoPhpUnitTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

class ResultsServiceTestCase extends TaoPhpUnitTestRunner {
    
    protected $service =null;
    /**
     * @access public
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     */
    public function setUp(){
        TaoPhpUnitTestRunner::initTest();
        $this->service = taoResults_models_classes_ResultsService::singleton();
    }
    
    /**
     * 
     * @access 
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     */
    public function testService(){
    
        $service = taoResults_models_classes_ResultsService::singleton();
        $this->assertIsA($service, 'tao_models_classes_Service');
        $this->assertIsA($service, 'taoResults_models_classes_ResultsService');
    }
    /**
     * 
     * @access 
     * @author "Patrick Plichart <patrick@taotesting.com>"
     */
    public function testStoreDeliveryResult(){
        $deliveryResult = $this->service->storeDeliveryResult();
        $this->assertIsA($deliveryResult, 'core_kernel_classes_Resource');
        $deliveryResult->delete();
        
        $deliveryResult = $this->service->storeDeliveryResult("1");
        $this->assertIsA($deliveryResult, 'core_kernel_classes_Resource');
        
        
        $deliveryResult->delete();
    }
    /**
     * 
     * @access 
     * @author "Patrick Plichart <patrick@taotesting.com>"
     */
    public function testStoreItemVariable(){
        //storeItemVariable(core_kernel_classes_Resource $deliveryResult, $test, $item, taoResultServer_models_classes_Variable $itemVariable, $callId)
        $deliveryResult = $this->service->storeDeliveryResult();
        $this->service->storeTestTaker($deliveryResult, "unittest_testtaker");
        $this->service->storeDelivery($deliveryResult, "unittest_delivery");
        
        $itemVariable = new taoResultServer_models_classes_ResponseVariable();
        $itemVariable->setCandidateResponse("unittest_candidateResponse");
        $itemVariable->setCardinality("single");
        $itemVariable->setIdentifier("unittest_identifier");
        $itemVariable->setBaseType("file");
        
        $storedVariable = $this->service->storeItemVariable($deliveryResult, "myTest", "myItem", $itemVariable, "callid_31");    
        
    }
    
    
}