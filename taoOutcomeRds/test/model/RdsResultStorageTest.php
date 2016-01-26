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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT); *
 *
 *
 */
namespace oat\taoOutcomeRds\test\model;

use oat\taoOutcomeRds\model\RdsResultStorage;

require_once dirname(__FILE__) . '/../../../tao/includes/class.Bootstrap.php';


/**
 * Test Rds result storage
 *
 * @author Antoine Robin, <antoine.robin@vesperiagroup.com>
 * @package taoOutcomeRds
 *
 */
class RdsResultStorageTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @var RdsResultStorage
     */
    protected $instance;

    public function setUp()
    {
        $this->instance = new RdsResultStorage();
    }

    public function tearDown()
    {
        $this->instance = null;
    }


    public function testStoreRelatedTestTaker()
    {
        $deliveryResultIdentifier = "MyDeliveryResultIdentifier#1";
        $testTakerIdentifier = "mytestTaker#1";
        $this->instance->storeRelatedTestTaker($deliveryResultIdentifier, $testTakerIdentifier);

        $this->assertSame($testTakerIdentifier, $this->instance->getTestTaker($deliveryResultIdentifier));
    }

    public function testStoreRelatedDelivery()
    {
        $deliveryResultIdentifier = "MyDeliveryResultIdentifier#1";
        $deliveryIdentifier = "myDelivery#1";
        $this->instance->storeRelatedDelivery($deliveryResultIdentifier, $deliveryIdentifier);

        $this->assertSame($deliveryIdentifier, $this->instance->getDelivery($deliveryResultIdentifier));
    }

    public function testStoreItemVariable()
    {
        $deliveryResultIdentifier = "MyDeliveryResultIdentifier#1";
        $test = "MyGreatTest#2";
        $item = "MyGreatItem#2";
        $callId = "MyCallId#2";

        $itemVariable = new \taoResultServer_models_classes_OutcomeVariable();
        $itemVariable->setBaseType('float');
        $itemVariable->setCardinality('multiple');
        $itemVariable->setEpoch(microtime());
        $itemVariable->setIdentifier('Identifier');
        $itemVariable->setValue('MyValue');


        $this->instance->storeItemVariable($deliveryResultIdentifier, $test, $item, $itemVariable, $callId);
        $tmp = $this->instance->getVariable($callId, 'Identifier');

        $object = array_shift($tmp);
        $this->assertEquals($test, $object->test);
        $this->assertEquals($item, $object->item);
        $this->assertEquals('float', $object->variable->getBaseType());
        $this->assertEquals('multiple', $object->variable->getCardinality());
        $this->assertEquals('Identifier', $object->variable->getIdentifier());
        $this->assertEquals('MyValue', $object->variable->getValue());
        $this->assertInstanceOf('taoResultServer_models_classes_OutcomeVariable', $object->variable);

        $this->assertEquals('float', $this->instance->getVariableProperty($object->uri, 'baseType'));
        $this->assertEquals('multiple', $this->instance->getVariableProperty($object->uri, 'cardinality'));
        $this->assertEquals('Identifier', $this->instance->getVariableProperty($object->uri, 'identifier'));
        $this->assertEquals('MyValue', $this->instance->getVariableProperty($object->uri, 'value'));
        $this->assertNull($this->instance->getVariableProperty($object->uri, 'unknownProperty'));

    }

    public function testStoreTestVariable()
    {
        $deliveryResultIdentifier = "MyDeliveryResultIdentifier#1";
        $test = "MyGreatTest#3";
        $callId = "MyCallId#3";

        $testVariable = new \taoResultServer_models_classes_OutcomeVariable();
        $testVariable->setBaseType('float');
        $testVariable->setCardinality('multiple');
        $testVariable->setEpoch(microtime());
        $testVariable->setIdentifier('TestIdentifier');
        $testVariable->setValue('MyValue');


        $this->instance->storeTestVariable($deliveryResultIdentifier, $test, $testVariable, $callId);
        $tmp = $this->instance->getVariable($callId, 'TestIdentifier');
        $object = array_shift($tmp);
        $this->assertEquals($test, $object->test);
        $this->assertNull($object->item);
        $this->assertEquals('float', $object->variable->getBaseType());
        $this->assertEquals('multiple', $object->variable->getCardinality());
        $this->assertEquals('TestIdentifier', $object->variable->getIdentifier());
        $this->assertEquals('MyValue', $object->variable->getValue());
        $this->assertInstanceOf('taoResultServer_models_classes_OutcomeVariable', $object->variable);

        $this->assertEquals('float', $this->instance->getVariableProperty($object->uri, 'baseType'));
        $this->assertEquals('multiple', $this->instance->getVariableProperty($object->uri, 'cardinality'));
        $this->assertEquals('TestIdentifier', $this->instance->getVariableProperty($object->uri, 'identifier'));
        $this->assertEquals('MyValue', $this->instance->getVariableProperty($object->uri, 'value'));


    }

}