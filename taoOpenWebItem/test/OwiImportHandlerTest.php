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
namespace oat\taoOpenWebItem\test;

use oat\tao\test\TaoPhpUnitTestRunner;
use \ZipArchive;
use \taoItems_models_classes_ItemsService;
use oat\taoOpenWebItem\model\import\ImportService;
use oat\taoOpenWebItem\model\import\OwiImportHandler;

class OwiImportHandlerTest extends TaoPhpUnitTestRunner
{

    /**
     * tests initialization
     */
    public function setUp()
    {
        TaoPhpUnitTestRunner::initTest();
        $this->dataFolder = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'samples' . DIRECTORY_SEPARATOR;
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetForm()
    {
        $handler = new OwiImportHandler();
        $this->assertInstanceOf('tao_helpers_form_Form', $handler->getForm());
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testImport()
    {
        $itemClass = new \core_kernel_classes_Class(TAO_ITEM_CLASS);
        
        $cp = copy($this->dataFolder . 'complete.zip', sys_get_temp_dir() . '/complete.zip');
        $fileinfo = array(
            'uploaded_file' => sys_get_temp_dir() . '/complete.zip'
        );
        $form = $this->getMockBuilder('oat\taoOpenWebItem\model\import\OwiImportForm')
            ->setMockClassName('FakeOwiImportForm')
            ->setMethods(array(
                'getValue'
            ))
            ->getMock();
        
        $form->expects($this->exactly(2))
            ->method('getValue')
            ->withConsecutive($this->equalTo('source'), $this->equalTo('disable_validation'))
            ->will($this->onConsecutiveCalls($fileinfo, array()));
        
        $handler = new OwiImportHandler();
        $report = $handler->import($itemClass, $form);
        $this->assertInstanceOf('common_report_Report', $report);
        
        $this->assertEquals($report->getType(), \common_report_Report::TYPE_SUCCESS);
        $this->assertInstanceOf('\core_kernel_classes_Resource', $report->getData());
        
        $itemService = taoItems_models_classes_ItemsService::singleton();
        $this->assertTrue($itemService->deleteItem($report->getData()));
    }
}

?>