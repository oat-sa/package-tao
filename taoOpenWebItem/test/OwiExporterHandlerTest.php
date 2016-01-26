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
use oat\taoOpenWebItem\model\export\OwiExportHandler;
use oat\taoOpenWebItem\model\import\ImportService;

class OwiExporterHandlerTest extends TaoPhpUnitTestRunner
{

    /**
     * tests initialization
     */
    public function setUp()
    {
        TaoPhpUnitTestRunner::initTest();
        $this->importService = new ImportService();
        $this->dataFolder = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'samples' . DIRECTORY_SEPARATOR;
    }
    
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetForm()
    {
        $handler = new OwiExportHandler();
        $this->assertInstanceOf('tao_helpers_form_Form', $handler->getExportForm( new \core_kernel_classes_Class(TAO_ITEM_CLASS)));
        $resourceMock = $this->getMockBuilder('core_kernel_classes_Resource')
        ->setMockClassName('FakeResource')
        ->setConstructorArgs(array(
            'emtpy'
        ))
        ->getMock();
        
        $this->assertInstanceOf('tao_helpers_form_Form', $handler->getExportForm($resourceMock));
    }
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testExport()
    {
        $itemClass = new \core_kernel_classes_Class(TAO_ITEM_CLASS);
        
        $report = $this->importService->importXhtmlFile($this->dataFolder . 'complete.zip', $itemClass, false);
        $complete = $report->getData();
        $this->assertInstanceOf('\core_kernel_classes_Resource', $complete);
        
        $destination = sys_get_temp_dir();
        $filename = 'unittest';
        $formValues = array(
            'filename' => $filename,
            'exportInstance' => $complete
        );
        
        $exportHandler = new OwiExportHandler();
        $file = $exportHandler->export($formValues, $destination);
        
        $zipArchive = new ZipArchive();
        $res = $zipArchive->open($file);
        $this->assertTrue($res);
        $this->assertFalse($zipArchive->statName('toto.html'));
        $this->assertNotFalse($zipArchive->statName('complete.js'));
        $this->assertNotFalse($zipArchive->statName('logo.gif'));
        $this->assertNotFalse($zipArchive->statName('index.html'));
        
        $itemService = taoItems_models_classes_ItemsService::singleton();
        unlink($file);
        $this->assertTrue($itemService->deleteItem($complete));
    }
}

?>