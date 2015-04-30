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

use oat\taoItems\model\pack\ItemPack;
use oat\tao\test\TaoPhpUnitTestRunner;
 
/**
 * Test the class {@link ItemPack}
 *  
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 */
class ItemPackTest extends TaoPhpUnitTestRunner
{

    /**
     * Test creating an ItemPack
     */
    public function testConstructor(){
        $type = 'qti';
        $data = array('foo' => 'bar');

        $pack = new ItemPack($type, $data);
        $this->assertInstanceOf('oat\taoItems\model\pack\ItemPack', $pack);
        $this->assertEquals($type, $pack->getType());
        $this->assertEquals($data, $pack->getData());
    }

    /**
     * Test assigning assets to a pack
     */
    public function testSetAssets(){

        $pack = new ItemPack('qti', array('foo' => 'bar'));
        $jsAssets = array(
            'lodash.js',
            'jquery.js'
        );
        $cssAssets = array('style.css');

        $pack->setAssets('js', $jsAssets);

        $this->assertEquals($jsAssets, $pack->getAssets('js'));
        $this->assertEquals(array(), $pack->getAssets('css'));

        
        $pack->setAssets('css', $cssAssets);

        $this->assertEquals($cssAssets, $pack->getAssets('css'));
    }

    /**
     * Test the constructor with an empty type
     * @expectedException InvalidArgumentException
     */
    public function testWrongTypeConstructor(){
        new ItemPack(null, array());
    }

    /**
     * Test the constructor with invalid data
     * @expectedException InvalidArgumentException
     */
    public function testWrongDataConstructor(){
        new ItemPack('qti', '{"foo":"bar"}');
    }

    /**
     * Test assigning unallowed assets
     * @expectedException InvalidArgumentException
     */
    public function testWrongAssetType(){
        $pack = new ItemPack('qti', array('foo' => 'bar'));
        $pack->setAssets('coffescript', array('jquery.coffee'));
    }

    /**
     * Test set wrong assets type
     * @expectedException InvalidArgumentException
     */
    public function testWrongAssets(){
        $pack = new ItemPack('qti', array('foo' => 'bar'));
        $pack->setAssets('js', 'jquery.js');
    }

    /**
     * Provides data to test the bundle
     * @return array() the data
     */     
    public function jsonSerializableProvider(){
        
        $data = array();

        $pack1 = new ItemPack('qti', array('foo' => 'bar'));
        $json1 = '{"type":"qti","data":{"foo":"bar"},"assets":[]}';
        $data[0] = array($pack1, $json1);
   
        
        $pack2 = new ItemPack('owi', array('foo' => 'bar'));
        $pack2->setAssets('js', array(
            'lodash.js',
            'jquery.js'
        ));
        $json2 = '{"type":"owi","data":{"foo":"bar"},"assets":{"js":["lodash.js","jquery.js"]}}';
        $data[1] = array($pack2, $json2);
 
        return $data;
    }   
 
    /**
     * Test the itemPack serializaion
     * @param ItemPack $itemPack
     * @param string $expectedJson
     * @dataProvider jsonSerializableProvider
     */
    public function testSerialization($itemPack, $expectedJson){
       
       $this->assertInstanceOf('oat\taoItems\model\pack\ItemPack', $itemPack);
       $this->assertTrue(is_string($expectedJson));
       $this->assertEquals($expectedJson, json_encode($itemPack));
    }

}
