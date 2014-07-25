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
?>
<?php
require_once dirname(__FILE__) . '/../../tao/test/TaoPhpUnitTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItems
 
 */
class setContentTestCase extends TaoPhpUnitTestRunner {

	/**
	 * tests initialization
	 */
	public function setUp(){		
		TaoPhpUnitTestRunner::initTest();
	}
	
	public function testItemContent(){
        
	    $itemsService = taoItems_models_classes_ItemsService::singleton();

		//create an instance of the Item class
		$itemClass = $itemsService->getRootClass();
		$item = $itemsService->createInstance($itemClass, 'test content');
		$this->assertIsA($item, 'core_kernel_classes_Resource');
		$this->assertEquals('test content', $item->getLabel());
		
		$item->setPropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY), TAO_ITEM_MODEL_XHTML);
		
		$this->assertFalse($itemsService->hasItemContent($item));
		
		//must use setItemContent and getItemContent
		$this->assertTrue($itemsService->setItemContent($item, 'test 2'));
		$this->assertEquals('test 2', $itemsService->getItemContent($item));
		
		$this->assertTrue($itemsService->setItemContent($item, 'test FR', 'FR'));
		$this->assertEquals('test FR', $itemsService->getItemContent($item, 'FR'));
		
		$this->assertTrue($itemsService->deleteItem($item));
	}
	
}
?>