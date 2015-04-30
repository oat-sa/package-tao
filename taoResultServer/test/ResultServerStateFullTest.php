<?php
namespace oat\taoResultServer\test;

use oat\tao\test\TaoPhpUnitTestRunner;

class ResultServerStateFullTest extends TaoPhpUnitTestRunner
{

    private $service;

    public function setUp()
    {
        TaoPhpUnitTestRunner::initTest();
        $this->service = \taoResultServer_models_classes_ResultServerStateFull::singleton();
    }

    /**
     * @expectedException \common_exception_MissingParameter
     */
    public function testInitResultServerException()
    {
        $this->service->initResultServer(null);
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testInitResultServer()
    {
        $this->service->initResultServer('http://www.tao.lu/Ontologies/TAOResultServer.rdf#void');
        $resutlUri = \PHPSession::singleton()->getAttribute('resultServerUri');
        $this->assertEquals('http://www.tao.lu/Ontologies/TAOResultServer.rdf#void', $resutlUri);
        $resultServerObject = \PHPSession::singleton()->getAttribute("resultServerObject");
        
        $resultServerObject = current($resultServerObject);
        $this->assertInstanceOf('taoResultServer_models_classes_ResultServer', $resultServerObject);
        
        // check if a resultServer has already been intialized for this definition
        $this->service->initResultServer('http://www.tao.lu/Ontologies/TAOResultServer.rdf#void');
        $resultServerObject = \PHPSession::singleton()->getAttribute("resultServerObject");
        
        $resultServerObject = current($resultServerObject);
        $this->assertInstanceOf('taoResultServer_models_classes_ResultServer', $resultServerObject);
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * 
     * @expectedException \common_exception_PreConditionFailure
     * @expectedExceptionMessage The result server hasn't been initalized
     */
    public function testSpawnResultNotInit()
    {
        $this->service->spawnResult(null);
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * 
     */
    public function testSpawnResultDelivery()
    {
        //init the resultserver
        $this->service->initResultServer('http://www.tao.lu/Ontologies/TAOResultServer.rdf#void');
        
        
        //no result id given will take run the spawnResult of storage interface
        $this->service->spawnResult('testcase_deliveryexecId');
        
        $resultServer_deliveryResultIdentifier = \PHPSession::singleton()->getAttribute("resultServer_deliveryResultIdentifier");
        $resultServer_deliveryExecutionIdentifier = \PHPSession::singleton()->getAttribute("resultServer_deliveryExecutionIdentifier");
        
        $this->assertNull($resultServer_deliveryResultIdentifier);
        $this->assertEquals('testcase_deliveryexecId', $resultServer_deliveryExecutionIdentifier);
       
        //provide both identifier
        $this->service->spawnResult('testcase_deliveryexecId','testcase_deliveryresultId');
        
        $resultServer_deliveryResultIdentifier = \PHPSession::singleton()->getAttribute("resultServer_deliveryResultIdentifier");
        $resultServer_deliveryExecutionIdentifier = \PHPSession::singleton()->getAttribute("resultServer_deliveryExecutionIdentifier");
        
        $this->assertEquals('testcase_deliveryresultId', $resultServer_deliveryResultIdentifier);
        $this->assertEquals('testcase_deliveryexecId', $resultServer_deliveryExecutionIdentifier);
        
        //provide no results identifier but will retrieve previous call
        $this->service->spawnResult('testcase_deliveryexecId');
        
        $resultServer_deliveryResultIdentifier = \PHPSession::singleton()->getAttribute("resultServer_deliveryResultIdentifier");
        $resultServer_deliveryExecutionIdentifier = \PHPSession::singleton()->getAttribute("resultServer_deliveryExecutionIdentifier");
        
        $this->assertEquals('testcase_deliveryresultId', $resultServer_deliveryResultIdentifier);
        $this->assertEquals('testcase_deliveryexecId', $resultServer_deliveryExecutionIdentifier);

        
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * 
     * @expectedException \common_exception_MissingParameter
     */
    public function testStoreRelatedTestTakerNull()
    {
        $this->service->storeRelatedTestTaker('');
    }
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testStoreRelatedTestTaker()
    {
        //init the resultserver
        $this->service->initResultServer('http://www.tao.lu/Ontologies/TAOResultServer.rdf#void');
        
        //there is a dependencies between spawnResult and storeRelatedTestTaker that should be remove
        $this->service->spawnResult('testcase_deliveryexecId','testcase_deliveryresultId');
        
        $res = $this->service->storeRelatedTestTaker('testcase_deliveryexecId');
        
        $this->assertEquals('testcase_deliveryresultId', $res);
        
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @expectedException \common_exception_MissingParameter
     */
    public function testStoreRelatedDeliveryNull()
    {
        //init the resultserver
        $this->service->initResultServer('http://www.tao.lu/Ontologies/TAOResultServer.rdf#void');
        $this->service->storeRelatedDelivery('');

    }
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testStoreRelatedDelivery()
    {
        //init the resultserver
        $this->service->initResultServer('http://www.tao.lu/Ontologies/TAOResultServer.rdf#void');
        $this->service->spawnResult('testcase_deliveryexecId','testcase_deliveryresultId');
        
        $res = $this->service->storeRelatedDelivery('test_id');
        $this->assertEquals('testcase_deliveryresultId', $res);
    
    }
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testStoreItemVariable()
    {
        //init the resultserver
        $this->service->initResultServer('http://www.tao.lu/Ontologies/TAOResultServer.rdf#void');
        $this->service->spawnResult('testcase_deliveryexecId','testcase_deliveryresultId');
        
        $prophet = new \Prophecy\Prophet;
        $prophecy = $prophet->prophesize();
        $prophecy->willExtend('taoResultServer_models_classes_Variable');
        $variable = $prophecy->reveal();
        $res = $this->service->storeItemVariable(null,null,$variable,null);
        
        $this->assertEquals('testcase_deliveryresultId', $res);
        
    
    }
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testStoreItemVariableSet()
    {
        //init the resultserver
        $this->service->initResultServer('http://www.tao.lu/Ontologies/TAOResultServer.rdf#void');
        $this->service->spawnResult('testcase_deliveryexecId','testcase_deliveryresultId');
    
        $prophet = new \Prophecy\Prophet;
        $prophecy = $prophet->prophesize();
        $prophecy->willExtend('taoResultServer_models_classes_Variable');
        $variable = $prophecy->reveal();
        
        $res = $this->service->storeItemVariableSet(null,null,array($variable),null);
    
        $this->assertEquals('testcase_deliveryresultId', $res);
    
    
    }
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testStoreTestVariable()
    {
        //init the resultserver
        $this->service->initResultServer('http://www.tao.lu/Ontologies/TAOResultServer.rdf#void');
        $this->service->spawnResult('testcase_deliveryexecId','testcase_deliveryresultId');
    
        $prophet = new \Prophecy\Prophet;
        $prophecy = $prophet->prophesize();
        $prophecy->willExtend('taoResultServer_models_classes_Variable');
        $variable = $prophecy->reveal();
        
        $res = $this->service->storeTestVariable(null,$variable,null);
    
        $this->assertEquals('testcase_deliveryresultId', $res);
    
    
    }

    public function testGetVariables()
    {
        $additionalStorage = array(
            'implementation' => 'taoResultServer_models_classes_ResultStorageContainer',
            'parameters' => null
            
            
        );
        
        $this->service->initResultServer('http://www.tao.lu/Ontologies/TAOResultServer.rdf#void',array($additionalStorage));
        $this->service->spawnResult('testcase_deliveryexecId','testcase_deliveryresultId');
        
        $res = $this->service->getVariables('test');
        $this->assertEmpty($res);
        
        $this->markTestIncomplete('need to test that return value is correct');
        
    
    }
}

?>