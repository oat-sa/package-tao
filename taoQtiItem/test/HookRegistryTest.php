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
namespace oat\taoQtiItem\test;

use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoQtiItem\model\HookRegistry;

/**
 *
 * @author lionel
 */
class HookRegistryTest extends TaoPhpUnitTestRunner
{
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function setUp()
    {
        TaoPhpUnitTestRunner::initTest();
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetInteractions()
    {
        $interactions = HookRegistry::getRegistry()->getMap();
        $this->assertTrue(is_array( $interactions));
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testSet()
    {
        $prophet = new \Prophecy\Prophet();
        $hookProphecy = $prophet->prophesize('oat\taoQtiItem\model\Hook');
        $hook = $hookProphecy->reveal();

        HookRegistry::getRegistry()->set('fakeInteraction', get_class($hook));
        $interactions = HookRegistry::getRegistry()->getMap();
        $this->assertEquals(get_class($hook), $interactions['fakeInteraction']);
        
        
    }
    
    /**
     * @depends testSet
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGet()
    {
        $interactions = HookRegistry::getRegistry()->getMap();
        $this->assertEquals($interactions['fakeInteraction'],HookRegistry::getRegistry()->get('fakeInteraction'));
    }

    /**
     * @depends testSet
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testRemove()
    {
        $interactions = HookRegistry::getRegistry()->getMap();
        $this->assertTrue(isset($interactions['fakeInteraction']));
        HookRegistry::getRegistry()->remove('fakeInteraction');
        $interactions = HookRegistry::getRegistry()->getMap();
        $this->assertFalse(isset($interactions['fakeInteraction']));
    }
    

}

?>