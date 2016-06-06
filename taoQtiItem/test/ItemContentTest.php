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
 * 
 */



use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoQtiItem\model\qti\ImportService;
use oat\taoItems\model\media\LocalItemSource;

include_once dirname(__FILE__).'/../includes/raw_start.php';

/**
 * test the item content access
 *
 */
class ItemContentTest extends TaoPhpUnitTestRunner
{

    /**
     * tests initialization
     * load qti service
     */
    public function setUp(){
        TaoPhpUnitTestRunner::initTest();
    }


    public function testResourceManager() {
        $itemClass = taoItems_models_classes_ItemsService::singleton()->getRootClass();
        $report = ImportService::singleton()->importQTIPACKFile(dirname(__FILE__).'/samples/package/QTI/package.zip', $itemClass);

        $items = array();
        foreach ($report as $itemReport) {
            $data = $itemReport->getData();
            if (!is_null($data)) {
                $items[] = $data;
            }
        }
        $this->assertEquals(1, count($items));
        
        $item = current($items);
        $this->assertIsA($item, 'core_kernel_classes_Resource');
        $this->assertTrue($item->exists());
        
        $rm = new LocalItemSource(array('item'=> $item , 'lang' => DEFAULT_LANG));

        $data = $rm->getDirectory();
        $this->assertTrue(is_array($data));
        $this->assertTrue(isset($data['path']));
        $this->assertEquals('/', $data['path']);
        
        $this->assertTrue(isset($data['children']));
        $children = $data['children'];
        $this->assertEquals(2, count($children));
        
        $file = null;
        $dir = null;
        foreach ($children as $child) {
            if (isset($child['path'])) {
                $dir = $child;
            }
            if (isset($child['name'])) {
                $file = $child;
            }
        }
        
        $this->assertEquals("qti.xml", $file['name']);
        $this->assertTrue(strpos($file['mime'], '/xml') !== false); //can be 'application/xml' or 'text/xml'
        $this->assertTrue($file['size'] > 0);
        
        $this->assertEquals("/images/", $dir['path']);
        
        taoItems_models_classes_ItemsService::singleton()->deleteItem($item);
        $this->assertFalse($item->exists());
    }
}