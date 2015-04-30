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
 * Copyright (c) 2013 Open Assessment Technologies S.A.
 */

namespace  oat\tao\test\lock;

use core_kernel_classes_Resource;
use core_kernel_classes_Property;
use core_kernel_classes_Class;
use oat\tao\test\TaoPhpUnitTestRunner;
use oat\tao\model\lock\implementation\OntoLock;

/**
 * Test the lock implemented as a property in the ontology
 *
 * @author plichart
 * @package tao
 *         
 */
class OntoLockTest extends TaoPhpUnitTestRunner
{

    protected $tempResource = null;

    protected $ontoLock = null;

    /**
     * 
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    public function setUp()
    {
        $resourceClass = new core_kernel_classes_Class(RDFS_RESOURCE);
        $this->tempResource = $resourceClass->createInstance('MyTest');
        $this->ontoLock = new OntoLock();
        
        $this->owner = new core_kernel_classes_Resource('#virtualOwner');
    }

    /**
     * 
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    public function tearDown()
    {
        $this->tempResource->delete();
    }
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testSetLock()
    {
        $this->assertFalse($this->ontoLock->isLocked($this->tempResource));
        $this->ontoLock->setLock($this->tempResource, $this->owner);
        $this->assertTrue($this->ontoLock->isLocked($this->tempResource));
        $other = new core_kernel_classes_Resource('#other');
        // setting a lock while it is locked should return an exception
        try {
            $this->ontoLock->setLock($this->tempResource, $other->getUri());
            $this->fail('should have throw a exception');
        } catch (\common_Exception $e) {
            $this->assertInstanceOf('oat\tao\model\lock\ResourceLockedException', $e);
        }
    }

    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testReleaseLock()
    {
        $this->assertFalse($this->ontoLock->isLocked($this->tempResource));
        $this->assertFalse($this->ontoLock->releaseLock($this->tempResource, $this->owner->getUri()));
        
        $this->ontoLock->setLock($this->tempResource, $this->owner->getUri());
        $this->assertTrue($this->ontoLock->isLocked($this->tempResource));
        $this->ontoLock->releaseLock($this->tempResource, $this->owner->getUri());
        $this->assertFalse($this->ontoLock->isLocked($this->tempResource));
        
        $this->ontoLock->setLock($this->tempResource, $this->owner->getUri());
        $other = new core_kernel_classes_Resource('#other');
        try {
            $this->ontoLock->releaseLock($this->tempResource, $other->getUri());
            $this->fail('should have throw a exception');
        } catch (\common_Exception $e) {
            $this->assertInstanceOf('\common_exception_Unauthorized', $e);
        }
    }
    
    /**
     * @expectedException common_exception_InconsistentData
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testReleaseLockException()
    {
        $resource = $this->prophesize('core_kernel_classes_Resource');
        
        $lockProp = new core_kernel_classes_Property(PROPERTY_LOCK);
        $resource->getPropertyValues($lockProp)->willReturn(array('foo','bar'));
        $this->ontoLock->releaseLock($resource->reveal(), $this->owner->getUri());
    }
    
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testForceReleaseLock()
    {
        $resource = $this->prophesize('core_kernel_classes_Resource');
        $lockProp = new core_kernel_classes_Property(PROPERTY_LOCK);
        
        $this->ontoLock->forceReleaseLock($resource->reveal());
        
        $resource->removePropertyValues($lockProp)->shouldHaveBeenCalled();
        
    }
}
