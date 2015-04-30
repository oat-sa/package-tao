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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\taoOutcomeUi\test\model;

use oat\tao\test\TaoPhpUnitTestRunner;
use \common_ext_ExtensionsManager;
use common_cache_FileCache;
use oat\taoOutcomeUi\model\ResultsService;
use Prophecy\Prophet;

/**
 * This test case focuses on testing ResultsService.
 *
 * @author Lionel Lecaque <lionel@taotesting.com>
 *        
 */
class ResultsServiceTest extends TaoPhpUnitTestRunner
{

    /**
     * 
     * @var ResultsService
     */
    protected $service;

    /**
     * 
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    public function setUp()
    {
        TaoPhpUnitTestRunner::initTest();
        common_ext_ExtensionsManager::singleton()->getExtensionById('taoOutcomeUi');
        $this->service = ResultsService::singleton();
    }

    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetRootClass()
    {
        $this->assertInstanceOf('core_kernel_classes_Class', $this->service->getRootClass());
        $this->assertEquals('http://www.tao.lu/Ontologies/TAOResult.rdf#DeliveryResult', $this->service->getRootClass()
            ->getUri());
    }

    /**
     * @expectedException \common_exception_Error
     * 
     * @author Lionel Lecaque, lionel@taotesting.com     
     */
    public function testGetImplementation()
    {
        $this->service->getImplementation();
    }
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testSetImplementation()
    {
        $prophet = new Prophet();
        $impProphecy = $prophet->prophesize('oat\taoOutcomeRds\model\RdsResultStorage');
        
        $imp = $impProphecy->reveal();
        
        $this->service->setImplementation($imp);
        $this->assertEquals($imp, $this->service->getImplementation());
    }
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetItemResultsFromDeliveryResult()
    {
        $prophet = new Prophet();
        
        $deliveryResultProphecy = $prophet->prophesize('core_kernel_classes_Resource');
        $deliveryResultProphecy->getUri()->willReturn('#fakeUri');
        
        $deliveryResult = $deliveryResultProphecy->reveal();
        
        $impProphecy = $prophet->prophesize('oat\taoOutcomeRds\model\RdsResultStorage');
        
        $impProphecy->getRelatedItemCallIds('#fakeUri')->willReturn('#fakeDelivery');
        $imp = $impProphecy->reveal();
        $this->service->setImplementation($imp);
        
        $this->assertEquals('#fakeDelivery', $this->service->getItemResultsFromDeliveryResult($deliveryResult));
    }
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetDelivery()
    {
        $prophet = new Prophet();
        
        $deliveryResultProphecy = $prophet->prophesize('core_kernel_classes_Resource');
        $deliveryResultProphecy->getUri()->willReturn('#fakeUri');
        $deliveryResult = $deliveryResultProphecy->reveal();
        
        $impProphecy = $prophet->prophesize('oat\taoOutcomeRds\model\RdsResultStorage');
        
        $impProphecy->getDelivery('#fakeUri')->willReturn('#fakeDelivery');
        $imp = $impProphecy->reveal();
        
        $this->service->setImplementation($imp);
        
        $delivery = $this->service->getDelivery($deliveryResult);
        $this->assertInstanceOf('core_kernel_classes_Resource', $delivery);
        $this->assertEquals('#fakeDelivery', $delivery->getUri());
    }
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetVariablesFromObjectResult()
    {
        $prophet = new Prophet();
        $impProphecy = $prophet->prophesize('oat\taoOutcomeRds\model\RdsResultStorage');
        
        $impProphecy->getVariables('#foo')->willReturn(true);
        $imp = $impProphecy->reveal();
        
        $this->service->setImplementation($imp);
        $this->assertTrue($this->service->getVariablesFromObjectResult('#foo'));
    }
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetVariableCandidateResponse()
    {
        $prophet = new Prophet();
        $impProphecy = $prophet->prophesize('oat\taoOutcomeRds\model\RdsResultStorage');
        
        $impProphecy->getVariableProperty('#foo', 'candidateResponse')->willReturn(true);
        $imp = $impProphecy->reveal();
        
        $this->service->setImplementation($imp);
        $this->assertTrue($this->service->getVariableCandidateResponse('#foo'));
    }
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetVariableBaseType()
    {
        $prophet = new Prophet();
        $impProphecy = $prophet->prophesize('oat\taoOutcomeRds\model\RdsResultStorage');
        
        $impProphecy->getVariableProperty('#foo', 'baseType')->willReturn(true);
        $imp = $impProphecy->reveal();
        
        $this->service->setImplementation($imp);
        $this->assertTrue($this->service->getVariableBaseType('#foo'));
    }
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetItemFromItemResult()
    {
        $prophet = new Prophet();
        $impProphecy = $prophet->prophesize('oat\taoOutcomeRds\model\RdsResultStorage');
        
        $item = new \stdClass();
        $item->item = '#item';
        $impProphecy->getVariables('#foo')->willReturn(array(
            array(
                $item
            )
        ));
        $imp = $impProphecy->reveal();
        
        $this->service->setImplementation($imp);
        
        $item = $this->service->getItemFromItemResult('#foo');
        $this->assertInstanceOf('core_kernel_classes_Resource', $item);
        $this->assertEquals('#item', $item->getUri());
    }
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetVariableDataFromDeliveryResult()
    {
        $prophet = new Prophet();
        $impProphecy = $prophet->prophesize('oat\taoOutcomeRds\model\RdsResultStorage');
        
        $var = new \stdClass();
        $var->callIdTest = 'callId';
        $var->variable = 'variable';
        $impProphecy->getVariables('#fakeUri')->willReturn(array(
            array(
                $var
            )
        ));
        $imp = $impProphecy->reveal();
        
        $this->service->setImplementation($imp);
        
        $deliveryResultProphecy = $prophet->prophesize('core_kernel_classes_Resource');
        $deliveryResultProphecy->getUri()->willReturn('#fakeUri');
        $deliveryResult = $deliveryResultProphecy->reveal();
        
        $varData = $this->service->getVariableDataFromDeliveryResult($deliveryResult);
        $this->assertArraySubset(array(
            'variable'
        ), $varData);
    }
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetTestTaker()
    {
        $prophet = new Prophet();
        $impProphecy = $prophet->prophesize('oat\taoOutcomeRds\model\RdsResultStorage');
        
        $impProphecy->getTestTaker('#fakeUri')->willReturn('#testTaker');
        $imp = $impProphecy->reveal();
        
        $this->service->setImplementation($imp);
        
        $deliveryResultProphecy = $prophet->prophesize('core_kernel_classes_Resource');
        $deliveryResultProphecy->getUri()->willReturn('#fakeUri');
        $deliveryResult = $deliveryResultProphecy->reveal();
        
        $item = $this->service->getTestTaker($deliveryResult);
        $this->assertInstanceOf('core_kernel_classes_Resource', $item);
        $this->assertEquals('#testTaker', $item->getUri());
    }
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testDeleteResult()
    {
        $prophet = new Prophet();
        $impProphecy = $prophet->prophesize('oat\taoOutcomeRds\model\RdsResultStorage');
        
        $impProphecy->deleteResult('#foo')->willReturn(true);
        $imp = $impProphecy->reveal();
        $this->service->setImplementation($imp);
        
        $this->assertTrue($this->service->deleteResult('#foo'));
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     *         @expectedException \common_exception_Error
     *         @expectedExceptionMessage This delivery has no Result Server
     */
    public function testGetReadableImplementationNoResultStorage()
    {
        $prophet = new Prophet();
        
        $deliveryProphecy = $prophet->prophesize('core_kernel_classes_Resource');
        $delivery = $deliveryProphecy->reveal();
        
        $this->service->getReadableImplementation($delivery);
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     *         @expectedException \common_exception_Error
     *         @expectedExceptionMessage This delivery has no readable Result Server
     */
    public function testGetReadableImplementationNoResultServer()
    {
        $prophet = new Prophet();
        
        $resultProphecy = $prophet->prophesize('core_kernel_classes_Resource');
        
        $resultServer = $resultProphecy->reveal();
        
        $deliveryProphecy = $prophet->prophesize('core_kernel_classes_Resource');
        
        $deliveryProphecy->getOnePropertyValue(new \core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP))->willReturn($resultServer);
        
        $delivery = $deliveryProphecy->reveal();
        $this->service->getReadableImplementation($delivery);
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     *         @expectedException \common_exception_Error
     *         @expectedExceptionMessage This delivery has no readable Result Server
     */
    public function testGetReadableImplementationNoResultServerModel()
    {
        $prophet = new Prophet();
        
        $resultProphecy = $prophet->prophesize('core_kernel_classes_Resource');
        $resultProphecy->getPropertyValues(new \core_kernel_classes_Property(TAO_RESULTSERVER_MODEL_PROP))->willReturn(array(
            '#fakeUri'
        ));
        $resultServer = $resultProphecy->reveal();
        
        $deliveryProphecy = $prophet->prophesize('core_kernel_classes_Resource');
        
        $deliveryProphecy->getOnePropertyValue(new \core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP))->willReturn($resultServer);
        
        $delivery = $deliveryProphecy->reveal();
        $this->service->getReadableImplementation($delivery);
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetReadableImplementation()
    {
        $prophet = new Prophet();
        
        $resultProphecy = $prophet->prophesize('core_kernel_classes_Resource');
        $resultProphecy->getPropertyValues(new \core_kernel_classes_Property(TAO_RESULTSERVER_MODEL_PROP))->willReturn(array(
            'http://www.tao.lu/Ontologies/taoOutcomeRds.rdf#RdsResultStorageModel'
        ));
        $resultServer = $resultProphecy->reveal();
        
        $deliveryProphecy = $prophet->prophesize('core_kernel_classes_Resource');
        
        $deliveryProphecy->getOnePropertyValue(new \core_kernel_classes_Property(TAO_DELIVERY_RESULTSERVER_PROP))->willReturn($resultServer);
        
        $delivery = $deliveryProphecy->reveal();
        
        $this->assertInstanceOf('oat\taoOutcomeRds\model\RdsResultStorage', $this->service->getReadableImplementation($delivery));
    }

    public function testGetVariables()
    {
        common_cache_FileCache::singleton()->purge();
        $prophet = new Prophet();
        
        $impProphecy = $prophet->prophesize('oat\taoOutcomeRds\model\RdsResultStorage');
        $impProphecy->getRelatedItemCallIds('#fakeUri')->willReturn(array(
            '#itemsResults1' => '#itemResultVariable'
        ));

        $impProphecy->getRelatedTestCallIds('#fakeUri')->willReturn(array(
                '#testResults1' => '#testResultVariable'
            ));
        
        $impProphecy->getVariables('#itemResultVariable')->willReturn(array(
            'itemResultVariable1' => 'foo'
        ));

        $impProphecy->getVariables('#testResultVariable')->willReturn(array(
                'testResultVariable1' => 'bar'
            ));

        $imp = $impProphecy->reveal();
        
        $this->service->setImplementation($imp);
        $deliveryResultProphecy = $prophet->prophesize('core_kernel_classes_Resource');
        $deliveryResultProphecy->getUri()->willReturn('#fakeUri');
        $deliveryResultProphecy->getUniquePropertyValue(new \core_kernel_classes_Property(PROPERTY_DELVIERYEXECUTION_STATUS))->willReturn(new \core_kernel_classes_Resource(INSTANCE_DELIVERYEXEC_FINISHED));
        
        $deliveryResult = $deliveryResultProphecy->reveal();
        
        $this->assertFalse(common_cache_FileCache::singleton()->has('deliveryResultVariables:#fakeUri'));
        
        $var = ($this->service->getVariables($deliveryResult));
        $this->assertArrayHasKey('#itemResultVariable_itemResultVariable1', $var);
        $this->assertEquals('foo', $var['#itemResultVariable_itemResultVariable1']);
        
        // use cache
        $this->assertTrue(common_cache_FileCache::singleton()->has('deliveryResultVariables:#fakeUri'));
        
        $var = ($this->service->getVariables($deliveryResult));
        $this->assertArrayHasKey('#itemResultVariable_itemResultVariable1', $var);
        $this->assertEquals('foo', $var['#itemResultVariable_itemResultVariable1']);
        
        $this->assertTrue(common_cache_FileCache::singleton()->has('deliveryResultVariables:#fakeUri'));
        // purge cache
        common_cache_FileCache::singleton()->purge();
        
        $var = $this->service->getVariables($deliveryResult, false);
        $this->assertArrayHasKey('#itemResultVariable', $var);
        $this->assertArrayHasKey('itemResultVariable1', $var['#itemResultVariable']);
        $this->assertEquals('foo', $var['#itemResultVariable']['itemResultVariable1']);
        
        common_cache_FileCache::singleton()->purge();
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetItemVariableDataFromDeliveryResult()
    {
        $prophet = new Prophet();
        
        $impProphecy = $prophet->prophesize('oat\taoOutcomeRds\model\RdsResultStorage');
        $impProphecy->getRelatedItemCallIds('#fakeUri')->willReturn(array(
            '#itemsResults1' => '#itemResultVariable',
            '#itemsResults2' => '#itemResultVariable2',
            '#itemsResults3' => '#itemResultVariable3'
        ));
        
        $item = new \stdClass();
        $item->item = '#item';
        $item->uri = '#uri';
        
        $var = new \taoResultServer_models_classes_TraceVariable();
        $var->setEpoch(microtime());
        $var->setIdentifier('varIdentifier');
        $item->variable = $var;
        
        $item2 = new \stdClass();
        $item2->item = '#item2';
        $item2->uri = '#uri2';
        
        $var2 = new \taoResultServer_models_classes_ResponseVariable();
        $var2->setEpoch(microtime());
        $var2->setIdentifier('varIdentifier2');
        // response correct
        $var2->setCorrectResponse(1);
        $item2->variable = $var2;
        
        $item3 = new \stdClass();
        $item3->item = '#item3';
        $item3->uri = '#uri3';
        
        $var3 = new \taoResultServer_models_classes_ResponseVariable();
        $var3->setEpoch(microtime());
        $var3->setIdentifier('varIdentifier3');
        // response incorrect
        $var3->setCorrectResponse(0);
        $item3->variable = $var3;
        
        $impProphecy->getVariables('#itemResultVariable')->willReturn(array(
            array(
                $item
            )
        ));
        $impProphecy->getVariables('#itemResultVariable2')->willReturn(array(
            array(
                $item2
            )
        ));
        $impProphecy->getVariables('#itemResultVariable3')->willReturn(array(
            array(
                $item3
            )
        )
        );
        
        $imp = $impProphecy->reveal();
        
        $this->service->setImplementation($imp);
        $deliveryResultProphecy = $prophet->prophesize('core_kernel_classes_Resource');
        $deliveryResultProphecy->getUri()->willReturn('#fakeUri');
        $deliveryResult = $deliveryResultProphecy->reveal();
        
        $itemVar = $this->service->getItemVariableDataFromDeliveryResult($deliveryResult, 'lastSubmitted');
        
        $this->assertArrayHasKey('#item', $itemVar);
        $this->assertArrayHasKey('itemModel', $itemVar['#item']);
        $this->assertEquals('unknown', $itemVar['#item']['itemModel']);
        
        $this->assertArrayHasKey('sortedVars', $itemVar['#item']);
        $this->assertArrayHasKey('taoResultServer_models_classes_TraceVariable', $itemVar['#item']['sortedVars']);
        $this->assertArrayHasKey('varIdentifier', $itemVar['#item']['sortedVars']['taoResultServer_models_classes_TraceVariable']);
        
        $this->assertArrayHasKey('uri', $itemVar['#item']['sortedVars']['taoResultServer_models_classes_TraceVariable']['varIdentifier'][0]);
        $this->assertEquals('#uri', $itemVar['#item']['sortedVars']['taoResultServer_models_classes_TraceVariable']['varIdentifier'][0]['uri']);
        
        $this->assertArrayHasKey('isCorrect', $itemVar['#item']['sortedVars']['taoResultServer_models_classes_TraceVariable']['varIdentifier'][0]);
        $this->assertEquals('unscored', $itemVar['#item']['sortedVars']['taoResultServer_models_classes_TraceVariable']['varIdentifier'][0]['isCorrect']);
        
        $this->assertArrayHasKey('var', $itemVar['#item']['sortedVars']['taoResultServer_models_classes_TraceVariable']['varIdentifier'][0]);
        $this->assertInstanceOf('taoResultServer_models_classes_TraceVariable', $itemVar['#item']['sortedVars']['taoResultServer_models_classes_TraceVariable']['varIdentifier'][0]['var']);
        $this->assertEquals($var, $itemVar['#item']['sortedVars']['taoResultServer_models_classes_TraceVariable']['varIdentifier'][0]['var']);
        
        $this->assertArrayHasKey('label', $itemVar['#item']);
        
        // item2
        $this->assertArrayHasKey('#item2', $itemVar);
        $this->assertArrayHasKey('itemModel', $itemVar['#item2']);
        $this->assertEquals('unknown', $itemVar['#item2']['itemModel']);
        
        $this->assertArrayHasKey('sortedVars', $itemVar['#item2']);
        $this->assertArrayHasKey('taoResultServer_models_classes_ResponseVariable', $itemVar['#item2']['sortedVars']);
        $this->assertArrayHasKey('varIdentifier2', $itemVar['#item2']['sortedVars']['taoResultServer_models_classes_ResponseVariable']);
        
        $this->assertArrayHasKey('uri', $itemVar['#item2']['sortedVars']['taoResultServer_models_classes_ResponseVariable']['varIdentifier2'][0]);
        $this->assertEquals('#uri2', $itemVar['#item2']['sortedVars']['taoResultServer_models_classes_ResponseVariable']['varIdentifier2'][0]['uri']);
        
        $this->assertArrayHasKey('isCorrect', $itemVar['#item2']['sortedVars']['taoResultServer_models_classes_ResponseVariable']['varIdentifier2'][0]);
        $this->assertEquals('correct', $itemVar['#item2']['sortedVars']['taoResultServer_models_classes_ResponseVariable']['varIdentifier2'][0]['isCorrect']);
        
        // item3
        $this->assertArrayHasKey('#item3', $itemVar);
        $this->assertArrayHasKey('itemModel', $itemVar['#item3']);
        $this->assertEquals('unknown', $itemVar['#item3']['itemModel']);
        
        $this->assertArrayHasKey('sortedVars', $itemVar['#item3']);
        $this->assertArrayHasKey('taoResultServer_models_classes_ResponseVariable', $itemVar['#item3']['sortedVars']);
        $this->assertArrayHasKey('varIdentifier3', $itemVar['#item3']['sortedVars']['taoResultServer_models_classes_ResponseVariable']);
        
        $this->assertArrayHasKey('uri', $itemVar['#item3']['sortedVars']['taoResultServer_models_classes_ResponseVariable']['varIdentifier3'][0]);
        $this->assertEquals('#uri3', $itemVar['#item3']['sortedVars']['taoResultServer_models_classes_ResponseVariable']['varIdentifier3'][0]['uri']);
        
        $this->assertArrayHasKey('isCorrect', $itemVar['#item3']['sortedVars']['taoResultServer_models_classes_ResponseVariable']['varIdentifier3'][0]);
        $this->assertEquals('incorrect', $itemVar['#item3']['sortedVars']['taoResultServer_models_classes_ResponseVariable']['varIdentifier3'][0]['isCorrect']);
    }

    /**
     * @depends testGetItemVariableDataFromDeliveryResult
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetItemVariableDataStatsFromDeliveryResult()
    {
        $prophet = new Prophet();
        
        $deliveryResultProphecy = $prophet->prophesize('core_kernel_classes_Resource');
        $deliveryResultProphecy->getUri()->willReturn('#fakeUri');
        $deliveryResult = $deliveryResultProphecy->reveal();
        
        $itemVar = $this->service->getItemVariableDataStatsFromDeliveryResult($deliveryResult, 'lastSubmitted');
        
        $this->assertArrayHasKey('nbResponses', $itemVar);
        $this->assertEquals(2, $itemVar['nbResponses']);
        $this->assertArrayHasKey('nbCorrectResponses', $itemVar);
        $this->assertEquals(1, $itemVar['nbCorrectResponses']);
        $this->assertArrayHasKey('nbIncorrectResponses', $itemVar);
        $this->assertEquals(1, $itemVar['nbIncorrectResponses']);
        $this->assertArrayHasKey('nbUnscoredResponses', $itemVar);
        $this->assertEquals(0,$itemVar['nbUnscoredResponses']);
        
        $this->assertArrayHasKey('data',$itemVar);        
    }
    
}