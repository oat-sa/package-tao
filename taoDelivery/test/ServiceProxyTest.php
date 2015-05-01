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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\taoDelivery\test;

use oat\tao\test\TaoPhpUnitTestRunner;
use common_ext_ExtensionsManager;
use taoDelivery_models_classes_execution_ServiceProxy;

class ServiceProxyTest extends TaoPhpUnitTestRunner
{
    private $config;

    /**
     *
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
        $this->config = $ext->getConfig(taoDelivery_models_classes_execution_ServiceProxy::CONFIG_KEY);
    }
    /**
     *
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    protected function tearDown()
    {
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
        $ext->setConfig(taoDelivery_models_classes_execution_ServiceProxy::CONFIG_KEY,$this->config);

    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testSetImplementation()
    {
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
        $serviceProphecy = $this->prophesize('taoDelivery_models_classes_execution_Service');
        $service = $serviceProphecy->reveal();
        taoDelivery_models_classes_execution_ServiceProxy::singleton()->setImplementation($service);

        $new = $ext->getConfig(taoDelivery_models_classes_execution_ServiceProxy::CONFIG_KEY);
        $this->assertEquals($service, $new);

    }
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetUserExecutions()
    {
        $serviceProphecy = $this->prophesize('taoDelivery_models_classes_execution_Service');
        $resource = $this->prophesize('core_kernel_classes_Resource');
        $res = $resource->reveal();
        $serviceProphecy->getUserExecutions($res, '#UserUri')->willReturn(true);

        $service = $serviceProphecy->reveal();
        taoDelivery_models_classes_execution_ServiceProxy::singleton()->setImplementation($service);

        $return = taoDelivery_models_classes_execution_ServiceProxy::singleton()->getUserExecutions($res, '#UserUri');

        $this->assertTrue($return);
    }
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetDeliveryExecutionsByStatus()
    {
        $serviceProphecy = $this->prophesize('taoDelivery_models_classes_execution_Service');
        $serviceProphecy->getDeliveryExecutionsByStatus('#UserUri','status')->willReturn(true);
        $service = $serviceProphecy->reveal();
        taoDelivery_models_classes_execution_ServiceProxy::singleton()->setImplementation($service);

        $return = taoDelivery_models_classes_execution_ServiceProxy::singleton()->getDeliveryExecutionsByStatus('#UserUri','status');
        $this->assertTrue($return);
    }
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetActiveDeliveryExecutions()
    {
        $serviceProphecy = $this->prophesize('taoDelivery_models_classes_execution_Service');
        $serviceProphecy->getDeliveryExecutionsByStatus('#UserUri',INSTANCE_DELIVERYEXEC_ACTIVE)->willReturn(true);
        $service = $serviceProphecy->reveal();
        taoDelivery_models_classes_execution_ServiceProxy::singleton()->setImplementation($service);

        $return = taoDelivery_models_classes_execution_ServiceProxy::singleton()->getActiveDeliveryExecutions('#UserUri');
        $this->assertTrue($return);

    }
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetFinishedDeliveryExecutions()
    {
        $serviceProphecy = $this->prophesize('taoDelivery_models_classes_execution_Service');
        $serviceProphecy->getDeliveryExecutionsByStatus('#UserUri',INSTANCE_DELIVERYEXEC_FINISHED)->willReturn(true);

        $service = $serviceProphecy->reveal();
        taoDelivery_models_classes_execution_ServiceProxy::singleton()->setImplementation($service);

        $return = taoDelivery_models_classes_execution_ServiceProxy::singleton()->getFinishedDeliveryExecutions('#UserUri');
        $this->assertTrue($return);

    }
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testInitDeliveryExecution()
    {
        $serviceProphecy = $this->prophesize('taoDelivery_models_classes_execution_Service');

        $resource = $this->prophesize('core_kernel_classes_Resource');
        $res = $resource->reveal();
        $serviceProphecy->initDeliveryExecution($res,'#UserUri')->willReturn(true);

        $service = $serviceProphecy->reveal();
        taoDelivery_models_classes_execution_ServiceProxy::singleton()->setImplementation($service);

        $return =  taoDelivery_models_classes_execution_ServiceProxy::singleton()->initDeliveryExecution($res,'#UserUri');
        $this->assertTrue($return);
    }
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetExecution()
    {
        $serviceProphecy = $this->prophesize('taoDelivery_models_classes_execution_Service');
        $serviceProphecy->getDeliveryExecution('#id')->willReturn(true);
        $service = $serviceProphecy->reveal();
        taoDelivery_models_classes_execution_ServiceProxy::singleton()->setImplementation($service);

        $return =  taoDelivery_models_classes_execution_ServiceProxy::singleton()->getDeliveryExecution('#id');
        $this->assertTrue($return);
    }
    /**
     * @expectedException common_exception_NoImplementation
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetExecutionsByDeliveryException()
    {
        $serviceProphecy = $this->prophesize('taoDelivery_models_classes_execution_Service');
        $resource = $this->prophesize('core_kernel_classes_Resource');
        $res = $resource->reveal();
        $service = $serviceProphecy->reveal();
        taoDelivery_models_classes_execution_ServiceProxy::singleton()->setImplementation($service);

        taoDelivery_models_classes_execution_ServiceProxy::singleton()->getExecutionsByDelivery($res);
    }
    /**
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetExecutionsByDelivery()
    {
        $serviceProphecy = $this->prophesize('taoDelivery_models_classes_execution_Monitoring');
        $service = $serviceProphecy->reveal();
        taoDelivery_models_classes_execution_ServiceProxy::singleton()->setImplementation($service);

        $resource = $this->prophesize('core_kernel_classes_Resource');
        $res = $resource->reveal();

        taoDelivery_models_classes_execution_ServiceProxy::singleton()->getExecutionsByDelivery($res);
        $serviceProphecy->getExecutionsByDelivery($res)->shouldHaveBeenCalled();

    }


}

?>