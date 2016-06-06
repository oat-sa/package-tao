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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\taoItems\test\pack;

use \core_kernel_classes_Resource;
use oat\taoQtiItem\model\pack\QtiItemPacker;
use oat\taoItems\model\pack\Packable;
use oat\taoItems\model\pack\ItemPack;
use oat\tao\test\TaoPhpUnitTestRunner;


/**
 * Test the class {@link ItemPack}
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 */
class QtiItemPackerTest extends TaoPhpUnitTestRunner
{

    public function setUp()
    {
        \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem');
    }

    /**
     * Test creating a QtiItemPacker
     */
    public function testConstructor(){
        $itemPacker = new QtiItemPacker();
        $this->assertInstanceOf('oat\taoItems\model\pack\ItemPacker', $itemPacker);
    }

    /**
     * Test the exception when a wrong content type is given
     *
     * @expectedException \InvalidArgumentException
     */
    //public function testWrongContentTypeToPack(){

        //$itemPacker = new QtiItemPacker();
        //$itemPacker->packItem(new core_kernel_classes_Resource('foo'), null);
    //}

    /**
     * Test the exception when a wrong content is given to the parser
     *
     * @expectedException \common_Exception
     */
    public function testWrongContentToPack(){

        $itemPacker = new QtiItemPacker();
        $itemPacker->packItem(new core_kernel_classes_Resource('foo'), 'en-US');
    }



    /**
     * Test packing an item where QTI content isn't valid
     * @expectedException \common_Exception
     */
    public function testPackingInvalidQtiItem(){

        $samplePath = dirname(__FILE__).'/../samples/wrong/';
        $sample = 'notvalid_associate.xml';

        $this->assertTrue(file_exists($samplePath . $sample));

        $itemPackerMock = $this
                    ->getMockBuilder('oat\taoQtiItem\model\pack\QtiItemPacker')
                    ->setMethods(array('getItemContent', 'getPath'))
                    ->getMock();

        $itemPackerMock
            ->method('getItemContent')
            ->will($this->returnValue(file_get_contents($samplePath . $sample)));

        $itemPackerMock
            ->method('getPath')
            ->will($this->returnValue($samplePath));


        $itemPackerMock->packItem(new core_kernel_classes_Resource('foo'), 'en-US');
    }

    /**
     * Test packing a simple item that has no assets.
     */
    public function testPackingSimpleItem(){

        $samplePath = dirname(__FILE__).'/../samples/xml/qtiv2p1/';
        $sample = 'inline_choice.xml';

        $this->assertTrue(file_exists($samplePath . $sample));

        $itemPackerMock = $this
                    ->getMockBuilder('oat\taoQtiItem\model\pack\QtiItemPacker')
                    ->setMethods(array('getItemContent', 'getPath'))
                    ->getMock();

        $itemPackerMock
            ->method('getItemContent')
            ->will($this->returnValue(file_get_contents($samplePath . $sample)));

        $itemPackerMock
            ->method('getPath')
            ->will($this->returnValue($samplePath));

        $itemPack = $itemPackerMock->packItem(new core_kernel_classes_Resource('foo'), 'en-US');

        $this->assertInstanceOf('oat\taoItems\model\pack\ItemPack', $itemPack);
        $this->assertEquals('qti', $itemPack->getType());

        $data = $itemPack->getData();

        $this->assertEquals('assessmentItem', $data['qtiClass']);
        $this->assertEquals('inlineChoice', $data['identifier']);

        $this->assertEquals(array(), $itemPack->getAssets('js'));
        $this->assertEquals(array(), $itemPack->getAssets('css'));
        $this->assertEquals(array(), $itemPack->getAssets('img'));
        $this->assertEquals(array(), $itemPack->getAssets('font'));
    }

    /**
     * Assert that response processing is part of the pack
     */
    public function testPackingItemResponseProcessing(){

        $samplePath = dirname(__FILE__).'/../samples/xml/qtiv2p1/';
        $sample = 'inline_choice.xml';

        $this->assertTrue(file_exists($samplePath . $sample));

        $itemPackerMock = $this
                    ->getMockBuilder('oat\taoQtiItem\model\pack\QtiItemPacker')
                    ->setMethods(array('getItemContent', 'getPath'))
                    ->getMock();

        $itemPackerMock
            ->method('getItemContent')
            ->will($this->returnValue(file_get_contents($samplePath . $sample)));

        $itemPackerMock
            ->method('getPath')
            ->will($this->returnValue($samplePath));

        $itemPack = $itemPackerMock->packItem(new core_kernel_classes_Resource('foo'), 'en-US');

        $this->assertInstanceOf('oat\taoItems\model\pack\ItemPack', $itemPack);
        $this->assertEquals('qti', $itemPack->getType());

        $data = $itemPack->getData();

        $this->assertEquals('assessmentItem', $data['qtiClass']);
        $this->assertTrue(is_array($data['responseProcessing']['responseRules']));
        $this->assertTrue(count($data['responseProcessing']['responseRules']) > 0);
    }

    /**
     * Test packing an item  that contain images.
     */
    public function testPackingItemWithImages(){

        $samplePath = dirname(__FILE__).'/../samples/xml/qtiv2p1/';
        $sample = 'sample-astronomy.xml';

        $this->assertTrue(file_exists($samplePath . $sample));

        $itemPackerMock = $this
            ->getMockBuilder('oat\taoQtiItem\model\pack\QtiItemPacker')
            ->setMethods(array('getItemContent', 'getPath'))
            ->getMock();

        $itemPackerMock
            ->method('getItemContent')
            ->will($this->returnValue(file_get_contents($samplePath . $sample)));

        $itemPackerMock
            ->method('getPath')
            ->will($this->returnValue($samplePath));

        $itemPack = $itemPackerMock->packItem(new core_kernel_classes_Resource('foo'), 'en-US');

        $this->assertInstanceOf('oat\taoItems\model\pack\ItemPack', $itemPack);
        $this->assertEquals('qti', $itemPack->getType());

        $data = $itemPack->getData();

        $this->assertEquals('assessmentItem', $data['qtiClass']);
        $this->assertEquals('astronomy', $data['identifier']);

        $this->assertEquals(6, count($itemPack->getAssets('img')));
        $this->assertTrue(in_array('samples/test_base_www/img/earth.png', $itemPack->getAssets('img')));

        $this->assertEquals(array(), $itemPack->getAssets('js'));
        $this->assertEquals(array(), $itemPack->getAssets('css'));
        $this->assertEquals(array(), $itemPack->getAssets('font'));
    }

    /**
     * Test packing an item  that contain a graphic interaction.
     */
    public function testPackingGraphicItem(){

        $samplePath = dirname(__FILE__).'/../samples/xml/qtiv2p1/';
        $sample = 'hotspot.xml';

        $this->assertTrue(file_exists($samplePath . $sample));

        $itemPackerMock = $this
            ->getMockBuilder('oat\taoQtiItem\model\pack\QtiItemPacker')
            ->setMethods(array('getItemContent', 'getPath'))
            ->getMock();

        $itemPackerMock
            ->method('getItemContent')
            ->will($this->returnValue(file_get_contents($samplePath . $sample)));

        $itemPackerMock
            ->method('getPath')
            ->will($this->returnValue($samplePath));

        $itemPack = $itemPackerMock->packItem(new core_kernel_classes_Resource('foo'), 'en-US');

        $this->assertInstanceOf('oat\taoItems\model\pack\ItemPack', $itemPack);
        $this->assertEquals('qti', $itemPack->getType());

        $data = $itemPack->getData();

        $this->assertEquals('assessmentItem', $data['qtiClass']);
        $this->assertEquals('hotspot', $data['identifier']);

        $this->assertEquals(1, count($itemPack->getAssets('img')));
    }

    /**
     * Test packing an item  that contain an object element in a container.
     */
    public function testPackingItemObjectInBody(){

        $samplePath = dirname(__FILE__).'/../samples/xml/qtiv2p1/';
        $sample = 'svg.xml';

        $this->assertTrue(file_exists($samplePath . $sample));

        $itemPackerMock = $this
            ->getMockBuilder('oat\taoQtiItem\model\pack\QtiItemPacker')
            ->setMethods(array('getItemContent', 'getPath'))
            ->getMock();

        $itemPackerMock
            ->method('getItemContent')
            ->will($this->returnValue(file_get_contents($samplePath . $sample)));

        $itemPackerMock
            ->method('getPath')
            ->will($this->returnValue($samplePath));

        $itemPack = $itemPackerMock->packItem(new core_kernel_classes_Resource('foo'), 'en-US');

        $this->assertInstanceOf('oat\taoItems\model\pack\ItemPack', $itemPack);
        $this->assertEquals('qti', $itemPack->getType());

        $data = $itemPack->getData();

        $this->assertEquals('assessmentItem', $data['qtiClass']);
        $this->assertEquals('SVG', $data['identifier']);

        $this->assertEquals(1, count($itemPack->getAssets('img')));
    }

    /**
     * Test packing an item that contains audio and video
     */
    public function testPackingMultiMediaItem(){

        $samplePath = dirname(__FILE__).'/../samples/xml/qtiv2p1/';
        $sample = 'audio-video.xml';

        $this->assertTrue(file_exists($samplePath . $sample));

        $itemPackerMock = $this
            ->getMockBuilder('oat\taoQtiItem\model\pack\QtiItemPacker')
            ->setMethods(array('getItemContent', 'getPath'))
            ->getMock();

        $itemPackerMock
            ->method('getItemContent')
            ->will($this->returnValue(file_get_contents($samplePath . $sample)));

        $itemPackerMock
            ->method('getPath')
            ->will($this->returnValue($samplePath));

        $itemPack = $itemPackerMock->packItem(new core_kernel_classes_Resource('foo'), 'en-US');

        $this->assertInstanceOf('oat\taoItems\model\pack\ItemPack', $itemPack);
        $this->assertEquals('qti', $itemPack->getType());

        $data = $itemPack->getData();

        $this->assertEquals('assessmentItem', $data['qtiClass']);
        $this->assertEquals('mediaInteraction', $data['identifier']);

        $this->assertEquals(1, count($itemPack->getAssets('audio')));
        $this->assertEquals(1, count($itemPack->getAssets('video')));
    }

    /**
     * Test packing an item that contain a stylesheet.
     */
    public function testPackingItemWithCss(){

        $sample = dirname(__FILE__).'/../samples/xml/qtiv2p1/sample-elections.xml';
        $path   = dirname(__FILE__).'/../samples/css';

        $this->assertTrue(file_exists($sample));

        $itemPackerMock = $this
                    ->getMockBuilder('oat\taoQtiItem\model\pack\QtiItemPacker')
                    ->setMethods(array('getItemContent', 'getPath'))
                    ->getMock();

        $itemPackerMock
            ->method('getItemContent')
            ->will($this->returnValue(file_get_contents($sample)));

        $itemPackerMock
            ->method('getPath')
            ->will($this->returnValue($path));

        $itemPack = $itemPackerMock->packItem(new core_kernel_classes_Resource('foo'), 'en-US');

        $this->assertInstanceOf('oat\taoItems\model\pack\ItemPack', $itemPack);
        $this->assertEquals('qti', $itemPack->getType());

        $data = $itemPack->getData();

        $this->assertEquals('assessmentItem', $data['qtiClass']);
        $this->assertEquals('elections-in-the-united-states-2004', $data['identifier']);

        $this->assertEquals(3, count($itemPack->getAssets('img')));
        $this->assertEquals(3, count($itemPack->getAssets('css')));
        $this->assertEquals(11, count($itemPack->getAssets('font')));
    }

    /**
     * @expectedException \common_Exception
     */
    public function testPackingItemWithCssBase64(){
        $sample = __DIR__ . '/../samples/xml/qtiv2p1/sample-elections.xml';
        $path   = __DIR__ . '/../samples/css';


        $this->assertTrue(file_exists($sample));

        /**
         * @var QtiItemPacker $itemPackerMock
         */
        $itemPackerMock = $this
            ->getMockBuilder('oat\taoQtiItem\model\pack\QtiItemPacker')
            ->setMethods(array('getItemContent', 'getPath'))
            ->getMock();

        $itemPackerMock
            ->method('getItemContent')
            ->will($this->returnValue(file_get_contents($sample)));

        $itemPackerMock
            ->method('getPath')
            ->will($this->returnValue($path));

        $itemPackerMock->setAssetEncoders(array('js'    => 'base64file',
                                          'css'   => 'base64file',
                                          'font'  => 'base64file',
                                          'img'   => 'none',
                                          'audio' => 'none',
                                          'video' => 'none'));

        $itemPackerMock->setNestedResourcesInclusion(false);

        $itemPack = $itemPackerMock->packItem(new core_kernel_classes_Resource('foo'), 'en-US');


        $this->assertInstanceOf('oat\taoItems\model\pack\ItemPack', $itemPack);
        $this->assertEquals('qti', $itemPack->getType());

        $data = $itemPack->getData();

        $this->assertEquals('assessmentItem', $data['qtiClass']);
        $this->assertEquals('elections-in-the-united-states-2004', $data['identifier']);

        $this->assertEquals(1, count($itemPack->getAssets('img')));
        $this->assertEquals(1, count($itemPack->getAssets('css')));
        $this->assertEquals(0, count($itemPack->getAssets('font')));

        $css = $itemPack->getAssets( 'css' );
        
        $this->assertStringStartsWith( 'data:text/css;', current($css), 'Have appropriate prefix' );
        $this->assertRegExp( '/icon-checkbox/', base64_decode( str_replace('data:text/css;base64,', '', current($css)) ), 'Correctly decoded back' );

        $itemPackerMock->setNestedResourcesInclusion(true);
        $itemPack = $itemPackerMock->packItem(new core_kernel_classes_Resource('foo'), 'en-US');

    }

    public function testPackingItemWithVideoBase64(){
        $sample = __DIR__ . '/../samples/xml/packer/qti.xml';
        $path   = __DIR__ . '/../samples/xml/packer';

        $this->assertTrue(file_exists($sample));

        /**
         * @var QtiItemPacker $itemPackerMock
         */
        $itemPackerMock = $this
            ->getMockBuilder('oat\taoQtiItem\model\pack\QtiItemPacker')
            ->setMethods(array('getItemContent', 'getPath'))
            ->getMock();

        $itemPackerMock
            ->method('getItemContent')
            ->will($this->returnValue(file_get_contents($sample)));

        $itemPackerMock
            ->method('getPath')
            ->will($this->returnValue($path));

        $itemPackerMock->setAssetEncoders(array('js'    => 'none',
                                                'css'   => 'none',
                                                'font'  => 'none',
                                                'img'   => 'base64file',
                                                'audio' => 'base64file',
                                                'video' => 'base64file'));


        $itemPack = $itemPackerMock->packItem(new core_kernel_classes_Resource('foo'), 'en-US');


        $this->assertInstanceOf('oat\taoItems\model\pack\ItemPack', $itemPack);
        $this->assertEquals('qti', $itemPack->getType());

        $this->assertEquals(1, count($itemPack->getAssets('img')));
        $this->assertEquals(2, count($itemPack->getAssets('video')));

        $video = $itemPack->getAssets( 'video' );
        $this->assertStringStartsWith( 'data:video/mp4;', $video['sample.mp4'], 'Encoded as it is local resource' );
        $this->assertStringStartsWith( 'https://', $video['https://www.youtube.com/watch?v=J1c2KzJbcGA'], 'Is external resource' );

    }

    /**
     * Test packing a PCI item
     */
    public function testPackingPciItem(){

        $samplePath = dirname(__FILE__).'/../samples/xml/qtiv2p1/';
        $sample = 'likert.xml';

        $this->assertTrue(file_exists($samplePath . $sample));

        $itemPackerMock = $this
            ->getMockBuilder('oat\taoQtiItem\model\pack\QtiItemPacker')
            ->setMethods(array('getItemContent', 'getPath'))
            ->getMock();

        $itemPackerMock
            ->method('getItemContent')
            ->will($this->returnValue(file_get_contents($samplePath . $sample)));

        $itemPackerMock
            ->method('getPath')
            ->will($this->returnValue($samplePath));

        $itemPack = $itemPackerMock->packItem(new core_kernel_classes_Resource('foo'), 'en-US');

        $this->assertInstanceOf('oat\taoItems\model\pack\ItemPack', $itemPack);
        $this->assertEquals('qti', $itemPack->getType());

        $data = $itemPack->getData();

        $this->assertEquals('assessmentItem', $data['qtiClass']);
        $this->assertEquals('pci002', $data['identifier']);

        $this->assertEquals(3, count($itemPack->getAssets('img')));
        $this->assertEquals(2, count($itemPack->getAssets('css')));
        $this->assertEquals(3, count($itemPack->getAssets('js')));
    }
}
