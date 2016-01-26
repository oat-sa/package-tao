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
 */
namespace oat\tao\test;

use oat\tao\model\ClientLibConfigRegistry;
use oat\tao\test\TaoPhpUnitTestRunner;

/**
 * 
 * @author Sam, sam@taotesting.com
 */
class ClientLibConfigRegistryTest extends TaoPhpUnitTestRunner
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

        $myUnitTestLibName = 'myUnitTestLib';

        ClientLibConfigRegistry::getRegistry()->register($myUnitTestLibName, array(
            'prop1' => 'value1',
            'prop2' => array(
                'prop2.1' => 'value2.1',
                'prop2.2' => 'value2.2'
            )
        ));

        $myUnitTestLibConfig = ClientLibConfigRegistry::getRegistry()->get($myUnitTestLibName);
        $this->assertNotEmpty($myUnitTestLibConfig);
        $this->assertEquals($myUnitTestLibConfig['prop1'], 'value1');
        $this->assertEquals($myUnitTestLibConfig['prop2']['prop2.1'], 'value2.1');
        $this->assertEquals($myUnitTestLibConfig['prop2']['prop2.2'], 'value2.2');

        //adding new config
        ClientLibConfigRegistry::getRegistry()->register($myUnitTestLibName, array(
            'prop3' => 'value3',
            'prop2' => array(
                'prop2.2' => 'value2.2a',
                'prop2.3' => 'value2.3'
            )
        ));

        $myUnitTestLibConfig = ClientLibConfigRegistry::getRegistry()->get($myUnitTestLibName);
        $this->assertNotEmpty($myUnitTestLibConfig);
        $this->assertEquals($myUnitTestLibConfig['prop1'], 'value1');
        $this->assertEquals($myUnitTestLibConfig['prop2']['prop2.1'], 'value2.1');
        $this->assertEquals($myUnitTestLibConfig['prop2']['prop2.2'], 'value2.2a');
        $this->assertEquals($myUnitTestLibConfig['prop2']['prop2.3'], 'value2.3');
        $this->assertEquals($myUnitTestLibConfig['prop3'], 'value3');

        ClientLibConfigRegistry::getRegistry()->remove($myUnitTestLibName);
        $this->assertEmpty(ClientLibConfigRegistry::getRegistry()->get($myUnitTestLibName));

    }
}