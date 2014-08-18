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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 */


use oat\taoOpenWebItem\model\import\ImportService;

require_once dirname(__FILE__) . '/../../tao/test/TaoPhpUnitTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItems
 
 */
class CompilationTest extends TaoPhpUnitTestRunner {
	
	/**
	 * tests initialization
	 */
	public function setUp(){		
		TaoPhpUnitTestRunner::initTest();
	}
	
	public function testCompileComplete() {
		$importService = new ImportService();
		$itemClass = new core_kernel_classes_Class(TAO_ITEM_CLASS);
		
		$owiFolder = dirname(__FILE__).DIRECTORY_SEPARATOR.'samples'.DIRECTORY_SEPARATOR;
		
		$report = $importService->importXhtmlFile($owiFolder.'complete.zip', $itemClass, false);
		$complete = $report->getData();
		$this->assertIsA($complete, 'core_kernel_classes_Resource');
		
		$storage = tao_models_classes_service_FileStorage::singleton();
		$compiler = new taoItems_models_classes_ItemCompiler($complete, $storage);
        $report = $compiler->compile();
        $this->assertEquals($report->getType(), common_report_Report::TYPE_SUCCESS);
        $serviceCall = $report->getData();
        $this->assertNotNull($serviceCall);
        $this->assertInstanceOf('tao_models_classes_service_ServiceCall', $serviceCall);
        
		$itemService = taoItems_models_classes_ItemsService::singleton();
		
		$this->assertTrue($itemService->deleteItem($complete));
	}
	
	// impossible to determin local missing results for now
	/*
	public function testCompileMissingLocal() {
	    $importService = new ImportService();
	    $itemClass = new core_kernel_classes_Class(TAO_ITEM_CLASS);
	
	    $owiFolder = dirname(__FILE__).DIRECTORY_SEPARATOR.'samples'.DIRECTORY_SEPARATOR;
	
	    $report = $importService->importXhtmlFile($owiFolder.'missingLocal.zip', $itemClass, false);
	    $missingLocal = $report->getData();
	    $this->assertIsA($missingLocal, 'core_kernel_classes_Resource');

		$storage = tao_models_classes_service_FileStorage::singleton();
	    $compiler = new taoItems_models_classes_ItemCompiler($missingLocal, $storage);
	    $report = $compiler->compile();
	    $this->assertEquals($report->getType(), common_report_Report::TYPE_ERROR);
	    $serviceCall = $report->getData();
	    $this->assertNull($serviceCall);
	
	    $itemService = taoItems_models_classes_ItemsService::singleton();
	    $this->assertTrue($itemService->deleteItem($missingLocal));
	}
	*/
	
	public function testCompileMissingRemote() {
	    $importService = new ImportService();
	    $itemClass = new core_kernel_classes_Class(TAO_ITEM_CLASS);
	
	    $owiFolder = dirname(__FILE__).DIRECTORY_SEPARATOR.'samples'.DIRECTORY_SEPARATOR;
	
	    $report = $importService->importXhtmlFile($owiFolder.'missingRemote.zip', $itemClass, false);
	    $missingRemote = $report->getData();
	    $this->assertIsA($missingRemote, 'core_kernel_classes_Resource');

	    $storage = tao_models_classes_service_FileStorage::singleton();
	    $compiler = new taoItems_models_classes_ItemCompiler($missingRemote, $storage);
	    $report = $compiler->compile();
	    $this->assertEquals($report->getType(), common_report_Report::TYPE_ERROR);
	    $serviceCall = $report->getData();
	    $this->assertNull($serviceCall);
	
	    $itemService = taoItems_models_classes_ItemsService::singleton();
	    $this->assertTrue($itemService->deleteItem($missingRemote));
	}
}