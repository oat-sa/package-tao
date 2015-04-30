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
namespace oat\tao\test;

use oat\tao\model\ClientLibRegistry;
use oat\tao\test\TaoPhpUnitTestRunner;

/**
 * 
 * @author Lionel Lecaque, lionel@taotesting.com
 */
class ClientLibRegistryTest extends TaoPhpUnitTestRunner
{

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function setUp()
    {
        TaoPhpUnitTestRunner::initTest();
    }


    public function testRegister()
    {
        $map = ClientLibRegistry::getRegistry()->getMap();
        $this->assertFalse(empty($map));
        
        ClientLibRegistry::getRegistry()->register('OAT/test', dirname(__FILE__) . '/samples/fakeSourceCode/views/js/');
        $map = ClientLibRegistry::getRegistry()->getMap();
        $this->assertInternalType('array', $map);
        $this->assertTrue(isset($map['OAT/test']));
        $this->assertEquals(dirname(__FILE__) . '/samples/fakeSourceCode/views/js/', $map['OAT/test']);
        
        ClientLibRegistry::getRegistry()->remove('OAT/test');
    }
}

?>