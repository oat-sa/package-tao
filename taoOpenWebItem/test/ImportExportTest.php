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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

use oat\taoOpenWebItem\model\import\ImportService;

?>
<?php
require_once dirname(__FILE__) . '/../../tao/test/TaoPhpUnitTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItems
 
 */
class ImportExportTest extends TaoPhpUnitTestRunner {
	
	/**
	 * tests initialization
	 */
	public function setUp(){		
		TaoPhpUnitTestRunner::initTest();
	}
	
	public function testImportOwi() {
		$importService = new ImportService();
		$itemClass = new core_kernel_classes_Class(TAO_ITEM_CLASS);
		
		$owiFolder = dirname(__FILE__).DIRECTORY_SEPARATOR.'samples'.DIRECTORY_SEPARATOR;
		
		//validate malformed html
		$report = $importService->importXhtmlFile($owiFolder.'badItem.zip', $itemClass, true);
		$this->assertFalse($report->containsSuccess());
		$this->assertTrue($report->containsError());
		
		$count = 0;
		foreach ($report as $element) {
		    $this->assertEquals(common_report_Report::TYPE_ERROR, $element->getType());
		    $count++;
		}
		$this->assertEquals (2, $count);
		
		$report = $importService->importXhtmlFile($owiFolder.'complete.zip', $itemClass, false);
		$this->assertEquals(common_report_Report::TYPE_SUCCESS, $report->getType());
		$owiItem = $report->getData();
		
		$this->assertIsA($owiItem, 'core_kernel_classes_Resource');
		
		$itemService = taoItems_models_classes_ItemsService::singleton();
		$content = $itemService->getItemContent($owiItem);
		$this->assertFalse(empty($content));
		
		$folder = $itemService->getItemFolder($owiItem);
		$this->assertTrue(file_exists($folder.'index.html'));
		$this->assertTrue(file_exists($folder.'logo.gif'));
		
		$this->assertTrue($itemService->deleteItem($owiItem));
	}
	
}
?>