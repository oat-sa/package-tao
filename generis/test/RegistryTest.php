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

namespace oat\generis\test;


use oat\oatbox\BasicRegistry;

class RegistryTest extends GenerisPhpUnitTestRunner 
{
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function setUp()
    {
        GenerisPhpUnitTestRunner::initTest();
    }
    
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testSet(){
        BasicRegistry::getRegistry()->set('key','value');
        $data = BasicRegistry::getRegistry()->getMap();
        $this->assertEquals('value', $data['key']);
    }
    
    /**
     * @depends testSet
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGet(){
        $value = BasicRegistry::getRegistry()->get('key');
        $this->assertEquals('value', $value);
    }
    
    /**
     * @depends testSet
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testRemove(){
        
        $data = BasicRegistry::getRegistry()->getMap();
        $this->assertTrue(isset($data['key']));
        BasicRegistry::getRegistry()->remove('key');
        $data = BasicRegistry::getRegistry()->getMap();
        $this->assertFalse(isset($data['key']));
        
    }
    
}

?>