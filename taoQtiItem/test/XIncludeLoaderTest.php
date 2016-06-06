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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */
namespace oat\taoQtiItem\test;

use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoItems\model\media\ItemMediaResolver;
use oat\tao\model\media\MediaAsset;
use oat\taoMediaManager\model\MediaSource;
use oat\taoQtiItem\model\qti\Parser;
use oat\taoQtiItem\model\qti\Item;
use oat\taoQtiItem\model\qti\XIncludeLoader;
use oat\taoQtiItem\model\qti\interaction\PortableCustomInteraction;

/**
 *
 * @author Sam, <sam@taotesting.com>
 * @package taoQtiItem
 
 */
class XIncludeLoaderTest extends TaoPhpUnitTestRunner
{
    
    /**
     * tests initialization
     */
    public function setUp(){
        TaoPhpUnitTestRunner::initTest();
    }
    
    /**
     * Test that xinclude are correctly loaded into standard item body elements
     */
    public function testLoadxincludeInBody(){
        
        $file = dirname(__FILE__).'/samples/xml/qtiv2p1/xinclude/associate_include.xml';
        $href1 = 'stimulus.xml';
        $file1 = dirname(__FILE__).'/samples/xml/qtiv2p1/xinclude/stimulus.xml';
        
        $mediaSource1 = $this->prophesize('oat\taoMediaManager\model\MediaSource');
        $mediaSource1->download($href1)->willReturn($file1);
        
        $asset1 = $this->prophesize('oat\tao\model\media\MediaAsset');
        $asset1->getMediaSource()->willReturn($mediaSource1->reveal());
        $asset1->getMediaIdentifier()->willReturn($href1);
        $asset1Revealed = $asset1->reveal();
        
        $resolver = $this->prophesize('oat\taoItems\model\media\ItemMediaResolver');
        $resolver->resolve($href1)->willReturn($asset1Revealed);
        
        $this->assertEquals($file1, $asset1Revealed->getMediaSource()->download($asset1Revealed->getMediaIdentifier()));
        
        //load item model
        $qtiParser = new Parser($file);
        $item = $qtiParser->load();
        $this->assertTrue($item instanceof Item);
        
        $xincludeLoader = new XIncludeLoader($item, $resolver->reveal());
        $xincludes = $xincludeLoader->load();
        
        $this->assertEquals(1, count($xincludes));
    }
    
    /**
     * Test that xincludes are correctly loaded into pci elements
     */
    public function testLoadxincludeInPci(){
        
        $file = dirname(__FILE__).'/samples/xml/qtiv2p1/xinclude/pci_include.xml';
        $href1 = 'stimulus.xml';
        $file1 = dirname(__FILE__).'/samples/xml/qtiv2p1/xinclude/stimulus.xml';
        
        
        $mediaSource1 = $this->prophesize('oat\taoMediaManager\model\MediaSource');
        $mediaSource1->download($href1)->willReturn($file1);
        
        $asset1 = $this->prophesize('oat\tao\model\media\MediaAsset');
        $asset1->getMediaSource()->willReturn($mediaSource1->reveal());
        $asset1->getMediaIdentifier()->willReturn($href1);
        $asset1Revealed = $asset1->reveal();
        
        $resolver = $this->prophesize('oat\taoItems\model\media\ItemMediaResolver');
        $resolver->resolve($href1)->willReturn($asset1Revealed);
        
        $this->assertEquals($file1, $asset1Revealed->getMediaSource()->download($asset1Revealed->getMediaIdentifier()));
        
        //load item model
        $qtiParser = new Parser($file);
        $item = $qtiParser->load();
        $this->assertTrue($item instanceof Item);
        
        //find the unique pci in the sample
        $pci = null;
        foreach($item->getComposingElements() as $element){
            if($element instanceof PortableCustomInteraction){
                $pci = $element;
                break;
            }
        }
        $this->assertNotNull($pci);
        
        //check inital markup
        $markupXml = simplexml_load_string($pci->getMarkup());
        $this->assertEquals(1, count($markupXml->xpath(".//*[name(.)='include']")), 'the pci markup has an include element');
        $this->assertEquals(0, count($markupXml->xpath(".//*[name(.)='img']")));
        $this->assertEquals(0, count($markupXml->xpath(".//*[name(.)='m:math']")));
        
        //load xinclude
        $xincludeLoader = new XIncludeLoader($item, $resolver->reveal());
        $xincludes = $xincludeLoader->load();
        
        //check markup after loading
        $markupXml = simplexml_load_string($pci->getMarkup());
        $this->assertEquals(1, count($xincludes));
        $this->assertEquals(0, count($markupXml->xpath(".//*[name(.)='include']")), 'the include element has been replaced');
        $this->assertEquals(1, count($markupXml->xpath(".//*[name(.)='img']")));
        $this->assertEquals(1, count($markupXml->xpath(".//*[name(.)='m:math']")));
    }
}