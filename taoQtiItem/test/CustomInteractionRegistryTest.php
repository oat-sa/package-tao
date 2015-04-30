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
use oat\taoQtiItem\model\CustomInteractionRegistry;

/**
 *
 * @author lionel
 */
class CustomInteractionRegistryTest extends TaoPhpUnitTestRunner
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
    public function testSet()
    {
        $hookMock = $this->getMockBuilder('oat\taoQtiItem\model\qti\interaction\CustomInteraction')
            ->setMockClassName('FakeInteractionMock')
            ->getMock();
        
        CustomInteractionRegistry::getRegistry()->set('fakeInteraction', 'FakeInteractionMock');
        $interactions = CustomInteractionRegistry::getRegistry()->getMap();
        $this->assertEquals('FakeInteractionMock', $interactions['fakeInteraction']);
    }

    /**
     * @depends testSet
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGet()
    {
        $interactions = CustomInteractionRegistry::getRegistry()->getMap();
        $this->assertEquals($interactions['fakeInteraction'], CustomInteractionRegistry::getRegistry()->get('fakeInteraction'));
        $this->assertEquals(CustomInteractionRegistry::getCustomInteractionByName('fakeInteraction'), CustomInteractionRegistry::getRegistry()->get('fakeInteraction'));
        
        
    }

    /**
     * @depends testSet
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testRemove()
    {
        $interactions = CustomInteractionRegistry::getRegistry()->getMap();
        $this->assertTrue(isset($interactions['fakeInteraction']));
        CustomInteractionRegistry::getRegistry()->remove('fakeInteraction');
        $interactions = CustomInteractionRegistry::getRegistry()->getMap();
        
    }
}

?>