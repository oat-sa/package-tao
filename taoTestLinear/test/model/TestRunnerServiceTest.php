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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoTestLinear\test\model;


use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoTestLinear\model\TestRunnerService;


/**
 * Test the test runner service to verify that it get the right data
 *
 * @access public
 * @author Antoine Robin, <antoine.robin@vesperiagroup.com>
 * @package taoTestLinear
 */
class TestRunnerServiceTest extends TaoPhpUnitTestRunner {

    /**
     * @var TestRunnerService
     */
    private $service = null;

    private $storageMock = null;

    private $directoryMock = null;


    public function setUp(){
        TaoPhpUnitTestRunner::initTest();
        $this->service = TestRunnerService::singleton();

        $this->storageMock = $this->getMockBuilder('tao_models_classes_service_FileStorage')
            ->disableOriginalConstructor()
            ->setMethods(array('getDirectoryById'))
            ->getMock();

        $ref = new \ReflectionProperty('tao_models_classes_service_FileStorage', 'instance');
        $ref->setAccessible(true);
        $ref->setValue(null, $this->storageMock);

        $this->directoryMock = $this->getMockBuilder('tao_models_classes_service_StorageDirectory')
            ->disableOriginalConstructor()
            ->setMethods(array('getPath'))
            ->getMock();

    }

    public function tearDown() {
        $ref = new \ReflectionProperty('tao_models_classes_service_FileStorage', 'instance');
        $ref->setAccessible(true);
        $ref->setValue(null, null);

        $this->storageMock = null;
        $this->directoryMock = null;
        $this->service = null;
    }

    public function testGetItemDataWithoutConfig() {

        $compilationId = "MyFirstCompilationID";
        $this->directoryMock->expects($this->once())
            ->method('getPath')
            ->willReturn(dirname(__FILE__). '/../sample/withoutConfig/');



        $this->storageMock->expects($this->once())
            ->method('getDirectoryById')
            ->with($compilationId)
            ->willReturn($this->directoryMock);

        $itemData = $this->service->getItemData($compilationId);

        $arrayKeys = array(
            "http://tao.localdomain:8888/tao.rdf#i142142605577127",
            "http://tao.localdomain:8888/tao.rdf#i142142605349615",
            "http://tao.localdomain:8888/tao.rdf#i142142605618879",
            "http://tao.localdomain:8888/tao.rdf#i1421426057643811"
        );

        $this->assertInternalType('array', $itemData, __('Get item Data should return an array'));
        $this->assertEquals($arrayKeys, array_keys($itemData), __('Keys of return value are wrong'));

    }

    public function testGetItemDataWithConfig() {


        $compilationId = "MySecondCompilationID";
        $this->directoryMock->expects($this->once())
            ->method('getPath')
            ->willReturn(dirname(__FILE__). '/../sample/withoutConfig/');



        $this->storageMock->expects($this->once())
            ->method('getDirectoryById')
            ->with($compilationId)
            ->willReturn($this->directoryMock);

        $itemData = $this->service->getItemData($compilationId);

        $arrayKeys = array(
            "http://tao.localdomain:8888/tao.rdf#i142142605577127",
            "http://tao.localdomain:8888/tao.rdf#i142142605349615",
            "http://tao.localdomain:8888/tao.rdf#i142142605618879",
            "http://tao.localdomain:8888/tao.rdf#i1421426057643811"
        );

        $this->assertInternalType('array', $itemData, __('Get item Data should return an array'));
        $this->assertEquals($arrayKeys, array_keys($itemData), __('Keys of return value are wrong'));

    }

    public function testGetPreviousWithoutConfig() {

        $compilationId = "MyCompilationID#3";

        $this->directoryMock->expects($this->once())
            ->method('getPath')
            ->willReturn(dirname(__FILE__). '/../sample/withoutConfig/');



        $this->storageMock->expects($this->once())
            ->method('getDirectoryById')
            ->with($compilationId)
            ->willReturn($this->directoryMock);

        $previous = $this->service->getPrevious($compilationId);


        $this->assertInternalType('boolean', $previous, __('Get previous should return a boolean'));
        $this->assertFalse($previous, __('Previous should be false'));

    }

    public function testGetPreviousWithConfig() {

        $compilationId = "MyCompilationID#4";
        $this->directoryMock->expects($this->once())
            ->method('getPath')
            ->willReturn(dirname(__FILE__). '/../sample/withConfig/');



        $this->storageMock->expects($this->once())
            ->method('getDirectoryById')
            ->with($compilationId)
            ->willReturn($this->directoryMock);

        $previous = $this->service->getPrevious($compilationId);


        $this->assertInternalType('boolean', $previous, __('Get previous should return a boolean'));
        $this->assertTrue($previous, __('Previous should be true'));

    }


}
 