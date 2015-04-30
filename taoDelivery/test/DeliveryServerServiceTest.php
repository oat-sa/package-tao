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
use \common_ext_ExtensionsManager;
use \taoDelivery_models_classes_DeliveryServerService;
use \core_kernel_classes_Literal;

class DeliveryServerServiceTest extends TaoPhpUnitTestRunner
{

    private $service;

    /**
     * tests initialization
     */
    public function setUp()
    {
        common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
        TaoPhpUnitTestRunner::initTest();
        $this->service = taoDelivery_models_classes_DeliveryServerService::singleton();
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param string $uri            
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function getResourceMock($uri)
    {
        $resourceMock = $this->getMockBuilder('core_kernel_classes_Resource')
            ->setMockClassName('FakeResource')
            ->setConstructorArgs(array(
            $uri
        ))
            ->getMock();
        
        return $resourceMock;
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function getSettingsProvider()
    {
        return array(
            array(
                '12',
                time(0, 0, 0, date('m'), date('d') - 1, date('Y')),
                time()
            ),
            array(
                '2',
               time(),
                time(0, 0, 0, date('m'), date('d') + 2, date('Y'))
            )
        );
    }

    /**
     * @dataProvider getSettingsProvider
     */
    public function testGetDeliverySettings($maxEx, $start, $end)
    {
        $resourceMock = $this->getResourceMock('fakeDelivery');
        
        $resourceMock->expects($this->once())
            ->method('getPropertiesValues')
            ->with($this->callback(function ($array)
        {
            $valid = false;
            foreach ($array as $prop) {
                $valid = true;
                if ($prop instanceof \core_kernel_classes_Property) {
                    $valid &= in_array($prop->getUri(), array(
                        TAO_DELIVERY_MAXEXEC_PROP,
                        TAO_DELIVERY_START_PROP,
                        TAO_DELIVERY_END_PROP
                    ));
                }
            }
            return $valid;
        }))
            ->will($this->returnValue(array(
            TAO_DELIVERY_MAXEXEC_PROP => array(
                new core_kernel_classes_Literal($maxEx)
            ),
            TAO_DELIVERY_START_PROP => array(
                new core_kernel_classes_Literal($start)
            ),
            TAO_DELIVERY_END_PROP => array(
                new core_kernel_classes_Literal($end)
            )
        )));
        
        $result = $this->service->getDeliverySettings($resourceMock);
        $this->assertTrue(is_array($result));
        $this->assertEquals($maxEx, $result[TAO_DELIVERY_MAXEXEC_PROP]);
        $this->assertEquals($start, $result[TAO_DELIVERY_START_PROP]);
        $this->assertEquals($end, $result[TAO_DELIVERY_END_PROP]);
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @return boolean
     */
    public function testGetDeliveryFromCompiledDelivery()
    {
        $resourceMock = $this->getResourceMock('fakerDeliveryAssembly');
        
        $resourceMock->expects($this->once())
            ->method('getUniquePropertyValue')
            ->with($this->callback(function ($prop)
        {
            return $prop instanceof \core_kernel_classes_Property && $prop->getUri() == PROPERTY_COMPILEDDELIVERY_DELIVERY;
        }))
            ->will($this->returnValue(true));
        
        $this->assertTrue($this->service->getDeliveryFromCompiledDelivery($resourceMock));
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @return boolean
     */
    public function testGetAssembliesByGroup()
    {
        $resourceMock = $this->getResourceMock('fakerDeliveryAssembly');
        
        $resourceMock->expects($this->any())
            ->method('getPropertyValues')
            ->with($this->callback(function ($prop)
        {
            return $prop instanceof \core_kernel_classes_Property && $prop->getUri() == PROPERTY_GROUP_DELVIERY;
        }))
            ->will($this->returnValue(array(
            GENERIS_TRUE
        )));
        $result = current($this->service->getAssembliesByGroup($resourceMock));
        $this->assertInstanceOf('core_kernel_classes_Resource', $result);
        $this->assertEquals(GENERIS_TRUE, $result->getUri());
    }
    
    
}

?>