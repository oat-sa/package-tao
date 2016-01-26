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
use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoItems\model\media\LocalItemSource;
use oat\tao\model\media\MediaAsset;

/**
 * This class aims at testing LocalItemSource.
 *
 * @package taoItems
 */
class LocalItemSourceTest extends TaoPhpUnitTestRunner {
    
    public function setUp()
    {
        parent::setUp();
        common_ext_ExtensionsManager::singleton()->getExtensionById('taoItems');
    }


    public function testAdd()
    {
        $itemService = \taoItems_models_classes_ItemsService::singleton();
        $item = $itemService->createInstance($itemService->getRootClass(), 'testItem');
        $source = new LocalItemSource(array(
	    	'item' => $item,
	        'lang' => DEFAULT_LANG
	    ));
        $sampleFile = dirname(__DIR__).DIRECTORY_SEPARATOR.'samples'.DIRECTORY_SEPARATOR.'asset'.DIRECTORY_SEPARATOR.'sample.css';
	    $info = $source->add($sampleFile, 'example.txt', '/');

	    $this->assertEquals('example.txt', $info['name']);
	    $this->assertEquals('/example.txt', $info['uri']);
	    $this->assertEquals('/example.txt', $info['filePath']);
	     
	    // this is only true for local item source 
	    $link = $info['uri'];
	    $asset = new MediaAsset($source, $link);
	    
	    return $asset;
    }
        
    /**
     * @depends testAdd
     */
    public function testGetFileInfo($asset)
	{
	    $source = $asset->getMediaSource(); 
	    $info = $source->getFileInfo($asset->getMediaIdentifier());
	    $this->assertEquals('/example.txt', $info['uri']);
	    return $asset;
	}
	
	/**
	 * @depends testGetFileInfo
	 */
	public function testDelete($asset)
	{
	    $source = $asset->getMediaSource();
	    $success = $source->delete($asset->getMediaIdentifier());
	    $this->assertTrue($success);
	    try {
	        $info = $source->getFileInfo($asset->getMediaIdentifier());
	        $this->fail('GetFileInfo on a deleted file should throw error');
	    } catch (tao_models_classes_FileNotFoundException $e) {
	        // should not be found
	    }
	    return $source;
	}
	
	
	/**
	 * @depends testDelete
	 */
	public function testItemTearDown($source)
	{
	    $this->assertInstanceOf('oat\taoItems\model\media\LocalItemSource', $source);
	    if ($source instanceof LocalItemSource) {
	        $item = $source->getItem();
	        $item->delete();
	    }
	}
	
}
