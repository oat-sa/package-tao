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
 * Copyright (c) 2015 Open Assessment Technologies S.A.
 * 
 */

namespace  oat\tao\test\lock;

use oat\tao\test\TaoPhpUnitTestRunner;
use oat\tao\model\lock\implementation\OntoLockData;

class OntoLockDataTest extends TaoPhpUnitTestRunner
{

    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetOwner()
    {
        $resource = $this->prophesize('core_kernel_classes_Resource');
        $owner = $this->prophesize('core_kernel_classes_Resource');
        $owner->getUri()->willReturn('#ownerUri');
        $lock = new OntoLockData($resource->reveal(), $owner->reveal(), 'epoch');
        $this->assertInstanceOf('core_kernel_classes_Resource', $lock->getOwner());
        $this->assertEquals('#ownerUri', $lock->getOwner()->getUri());
    }
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testToJson()
    {
        $resource = $this->prophesize('core_kernel_classes_Resource');
        $owner = $this->prophesize('core_kernel_classes_Resource');
        $owner->getUri()->willReturn('#ownerUri');
        $resource->getUri()->willReturn('#resourceUri');
        $lock = new OntoLockData($resource->reveal(), $owner->reveal(), 'epoch');
        
        
        $expected = json_encode(array(
            'resource' => '#resourceUri',
            'owner' =>  '#ownerUri',
            'epoch' => 'epoch'
        ));
        
        $this->assertEquals($expected, $lock->toJson());
    }
    /**
     * @expectedException common_exception_InconsistentData
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetLockDataExeption()
    {
        OntoLockData::getLockData(json_encode(array()));
    }
    
}

?>