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
use oat\taoTestLinear\model\TestCompiler;
use oat\taoTestLinear\model\TestModel;


/**
 * Test the compiler of a linear test
 *
 * @access public
 * @author Antoine Robin, <antoine.robin@vesperiagroup.com>
 * @package taoTestLinear
 */
class TestCompilerTest extends TaoPhpUnitTestRunner {

    /**
     * @var \tao_models_classes_service_FileStorage
     */
    private $storage = null;

    /**
     * @var \core_kernel_classes_Resource
     */
    private $test = null;

    /**
     * @var \core_kernel_classes_Resource
     */
    private $item = null;

    /**
     * @var TestModel
     */
    private $testModel = null;

    public function setUp(){
        TaoPhpUnitTestRunner::initTest();
        $this->test = new \core_kernel_classes_Resource('http://myFancyDomain.com/myGreatResourceUriForTest');
        $this->item = new \core_kernel_classes_Resource('http://myFancyDomain.com/myGreatResourceUriForItem');
        $this->item->setPropertyValue(new \core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModel'), 'http://www.tao.lu/Ontologies/TAOItem.rdf#QTI');
        $this->testModel = new TestModel();
        $this->storage = \tao_models_classes_service_FileStorage::singleton();

        $this->testModel->save($this->test, array());
    }

    public function testCompileEmpty() {

        //test without items
        $testCompiler = new TestCompiler($this->test, $this->storage);
        $waitingReport = new \common_report_Report(\common_report_Report::TYPE_ERROR, __("A Test must contain at least one item to be compiled."));
        $report = $testCompiler->compile();

        $this->assertEquals($waitingReport, $report, 'No items in sample test');
    }

    public function testCompile(){
        //test with items
        $config = array('previous' => true);
        $items = array($this->item->getUri());
        $this->testModel->save($this->test, array('itemUris' => $items, 'config' => $config));
        $waitingReport = new \common_report_Report(\common_report_Report::TYPE_SUCCESS);

        $serviceCall = $this->getMockBuilder('tao_models_classes_service_ServiceCall')
            ->disableOriginalConstructor()
            ->setMethods(array('serializeToString'))
            ->getMock();
        $serviceCall->expects($this->once())
            ->method('serializeToString')
            ->willReturn('greatString');

        $waitingReport->setData($serviceCall);


        $testCompiler = $this->getMockBuilder('oat\taoTestLinear\model\TestCompiler')
            ->setConstructorArgs(array($this->test, $this->storage))
            ->setMethods(array('subCompile', 'spawnPrivateDirectory'))
            ->getMock();

        $testCompiler->expects($this->once())
            ->method('subCompile')
            ->willReturn($waitingReport);


        //will spawn a new directory and store the content file
        $directoryMock = $this->getMockBuilder('tao_models_classes_service_StorageDirectory')
            ->disableOriginalConstructor()
            ->setMethods(array('getPath'))
            ->getMock();

        $tmpDir = \tao_helpers_File::createTempDir();
        $this->assertFileExists($tmpDir);
        
        $directoryMock->expects($this->once())
            ->method('getPath')
            ->willReturn($tmpDir);


        $testCompiler->expects($this->once())
            ->method('spawnPrivateDirectory')
            ->willReturn($directoryMock);



        $report = $testCompiler->compile();

        $this->assertEquals(__('Test Compilation'), $report->getMessage(),__('Compilation should work'));
        $this->assertFileExists($tmpDir. 'data.json', __('Compilation file not created'));
        $compile = '{"items":{"http:\/\/myFancyDomain.com\/myGreatResourceUriForItem":"greatString"},"previous":true}';
        $this->assertEquals($compile, file_get_contents(($tmpDir.'data.json'), __('File content error')));
        
        \tao_helpers_File::delTree($tmpDir);
        $this->assertFileNotExists($tmpDir);
    }

}
 