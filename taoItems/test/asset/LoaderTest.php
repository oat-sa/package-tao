<?php
/*
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
namespace oat\taoItems\test\pack;

use \core_kernel_classes_Resource;
use oat\taoItems\model\asset\Loader;
use oat\tao\test\TaoPhpUnitTestRunner;
use \common_Exception;

/**
 * Test the class {@link Loader}
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 */
class LoaderTest extends TaoPhpUnitTestRunner
{

    public function setUp()
    {
        \common_ext_ExtensionsManager::singleton()->getExtensionById('taoItems');
    }

    /**
     * Test creating an asset Loader
     */
    public function testConstructor(){
        $item = new core_kernel_classes_Resource('toto');
        $loader = new Loader($item);
        $this->assertInstanceOf('oat\taoItems\model\asset\Loader', $loader);
    }

    /**
     * Test creating an asset Loader
     */
    public function testLoadingRemoteAsset(){
        $item = new core_kernel_classes_Resource('toto');
        $loader = new Loader($item);
        $result = $loader->getAssetContent('http://domain.tld/foo.css');
        $this->assertEquals(null, $result);
    }

    /**
     * Test assigning assets to a pack
     */
    public function testLoadingRelAsset(){

        $samplePath = dirname(__FILE__).'/../samples/asset';
        $item = new core_kernel_classes_Resource('foo');

        $this->assertTrue(file_exists($samplePath));

        $serviceMock = $this
                        ->getMockBuilder('\taoItems_models_classes_ItemsService')
                        ->disableOriginalConstructor()
                        ->getMock();

        $serviceMock
            ->method('getItemFolder')
            ->will($this->returnValue($samplePath));

        $serviceMock
            ->method('singleton')
            ->will($this->returnValue($serviceMock));

        $loader = new Loader($item);

        $prop = new \ReflectionProperty('oat\taoItems\model\asset\Loader', 'itemService');
        $prop->setAccessible(true);
        $prop->setValue($loader, $serviceMock);

        $sampleCss = $loader->getAssetContent('sample.css');

        $this->assertTrue(is_string($sampleCss));
        $this->assertTrue(strlen($sampleCss) > 0);
    }

    /**
     * Test assigning assets to a pack
     * @expectedException common_Exception
     */
    public function testLoadingWrongAsset(){

        $samplePath = dirname(__FILE__).'/../samples/asset';
        $item = new core_kernel_classes_Resource('foo');

        $this->assertTrue(file_exists($samplePath));

        $serviceMock = $this
                        ->getMockBuilder('\taoItems_models_classes_ItemsService')
                        ->disableOriginalConstructor()
                        ->getMock();

        $serviceMock
            ->method('getItemFolder')
            ->will($this->returnValue($samplePath));

        $serviceMock
            ->method('singleton')
            ->will($this->returnValue($serviceMock));

        $loader = new Loader($item);

        $prop = new \ReflectionProperty('oat\taoItems\model\asset\Loader', 'itemService');
        $prop->setAccessible(true);
        $prop->setValue($loader, $serviceMock);

        $loader->getAssetContent('zandle.css');
    }
}
