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
use oat\tao\model\lock\implementation\SimpleLock;

class SimpleLockTest extends TaoPhpUnitTestRunner
{
    
    /**
     * @expectedException common_exception_Error
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testConstructExecption(){
        $resource = $this->prophesize('core_kernel_classes_Resource');
        $owner = $this->prophesize('core_kernel_classes_Literal');
        $lock = new SimpleLock($resource->reveal(), $owner->reveal(), 'epoch');
    }
    
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     * @return \oat\tao\model\lock\implementation\SimpleLock
     */
    public function testConstruct(){
        $resource = $this->prophesize('core_kernel_classes_Resource');
        $owner = $this->prophesize('core_kernel_classes_Resource');
        $owner->getUri()->willReturn('#ownerUri');
        $lock = new SimpleLock($resource->reveal(), $owner->reveal(), 'epoch');
        
        return $lock;
    }
    
    /**
     * @depends testConstruct
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetOwnerId($lock){
        $this->assertEquals('#ownerUri', $lock->getOwnerId());
    }
    
    /**
     * @depends testConstruct
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetCreationTime($lock){
        $this->assertEquals('epoch', $lock->getCreationTime());
    }
}

?>