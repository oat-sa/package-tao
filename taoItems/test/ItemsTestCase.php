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
require_once dirname(__FILE__) . '/../../tao/test/TaoTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 * @subpackage test
 */
class ItemsTestCase extends UnitTestCase {
	
	/**
	 * 
	 * @var taoItems_models_classes_ItemsService
	 */
	protected $itemsService = null;
	
	/**
	 * tests initialization
	 */
	public function setUp(){		
		TaoTestRunner::initTest();
	}
	
	/**
	 * Test the user service implementation
	 * @see tao_models_classes_ServiceFactory::get
	 * @see taoItems_models_classes_ItemsService::__construct
	 */
	public function testService(){
		
		$itemsService = taoItems_models_classes_ItemsService::singleton();
		$this->assertIsA($itemsService, 'tao_models_classes_Service');
		$this->assertIsA($itemsService, 'taoItems_models_classes_ItemsService');
		
		$this->itemsService = $itemsService;
	}
	
	/**
	 * Usual CRUD (Create Read Update Delete) on the item class  
	 */
	public function testCrud(){
		
		//check parent class
		$this->assertTrue(defined('TAO_ITEM_CLASS'));
		$itemClass = $this->itemsService->getRootClass();
		$this->assertIsA($itemClass, 'core_kernel_classes_Class');
		$this->assertEqual(TAO_ITEM_CLASS, $itemClass->getUri());
		
		//create a subclass
		$subItemClassLabel = 'subItem class';
		$subItemClass = $this->itemsService->createSubClass($itemClass, $subItemClassLabel);
		$this->assertIsA($subItemClass, 'core_kernel_classes_Class');
		$this->assertEqual($subItemClassLabel, $subItemClass->getLabel());
		$this->assertTrue($this->itemsService->isItemClass($subItemClass));
		
		//create an instance of the Item class
		$itemInstanceLabel = 'item instance';
		$itemInstance = $this->itemsService->createInstance($itemClass, $itemInstanceLabel);
		$this->assertIsA($itemInstance, 'core_kernel_classes_Resource');
		$this->assertEqual($itemInstanceLabel, $itemInstance->getLabel());
		
		//create instance of subItem
		$subItemInstanceLabel = 'subItem instance';
		$subItemInstance = $this->itemsService->createInstance($subItemClass, $subItemInstanceLabel);
		$this->assertIsA($subItemInstance, 'core_kernel_classes_Resource');
		$this->assertEqual($subItemInstanceLabel, $subItemInstance->getLabel());
		
		$subItemInstanceLabel2 = 'my sub item instance';
		$subItemInstance->setLabel($subItemInstanceLabel2);
		$this->assertEqual($subItemInstanceLabel2, $subItemInstance->getLabel());
		
		//delete group instance
		$this->assertTrue($itemInstance->delete());
		
		//delete subclass and check if the instance is deleted
		$this->assertTrue($subItemInstance->delete());
		$this->assertFalse($subItemInstance->exists());
		
		$this->assertTrue($subItemClass->delete());
	}
	
	public function testItemContent(){
		//create an instance of the Item class
		$itemClass = $this->itemsService->getRootClass();
		$item = $this->itemsService->createInstance($itemClass, 'test content');
		$this->assertIsA($item, 'core_kernel_classes_Resource');
		$this->assertEqual('test content', $item->getLabel());
		
		$item->setPropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY), TAO_ITEM_MODEL_XHTML);
		
		$this->assertFalse($this->itemsService->hasItemContent($item));
		
		//must use setItemContent and getItemContent
		$this->assertTrue($this->itemsService->setItemContent($item, 'test 2'));
		$this->assertEqual('test 2', $this->itemsService->getItemContent($item));
		
		$this->assertTrue($this->itemsService->setItemContent($item, 'test FR', 'FR'));
		$this->assertEqual('test FR', $this->itemsService->getItemContent($item, 'FR'));
		
		$this->assertTrue($this->itemsService->deleteItem($item));
	}
}
?>