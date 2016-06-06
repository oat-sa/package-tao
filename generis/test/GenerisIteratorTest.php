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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *               
 * 
 */

use oat\generis\test\GenerisPhpUnitTestRunner;

class GenerisIteratorTest extends GenerisPhpUnitTestRunner {
    
    /**
     * @var core_kernel_classes_Class
     */
    private $topClass;
    
    /**
     * @var core_kernel_classes_Class
     */
    private $emptyClass;
	
    public function setUp()
    {
        GenerisPhpUnitTestRunner::initTest();
        $class = new core_kernel_classes_Class(CLASS_GENERIS_RESOURCE);
        $this->topClass = $class->createSubClass('test class 1');
        $this->emptyClass = $class->createSubClass('test class 2');
        $class1a = $this->topClass->createSubClass('test class 1a');
        $class1b = $this->topClass->createSubClass('test class 1b');
        $class1c = $this->topClass->createSubClass('test class 1c');
        
        $class1c1 = $class1c->createSubClass('test class 1c1');
         
        $instance1_1 = $this->topClass->createInstance('test instance 1.1');
        $instance1_2 = $this->topClass->createInstance('test instance 1.2');
        $instance1_3 = $this->topClass->createInstance('test instance 1.3');
        $instance1b_1 = $class1b->createInstance('test instance 1b.1');
        $instance1b_2 = $class1b->createInstance('test instance 1b.2');
        $instance1c1_1 = $class1c1->createInstance('test instance 1c1.1');
        $instance1c1_2 = $class1c1->createInstance('test instance 1c1.2');
    }
    
    public function tearDown() {
        foreach ($this->topClass->getInstances(true) as $instance) {
            $instance->delete();
        }
        foreach ($this->topClass->getSubClasses(true) as $subClass) {
            $subClass->delete();
        }
        $this->topClass->delete();
        $this->emptyClass->delete();
    }
    
    public function testClassIterator()
    {
        $expected = array($this->topClass->getUri());
        foreach ($this->topClass->getSubClasses(true) as $resource) {
            $expected[] = $resource->getUri();
        }
        sort($expected);
        
        $iterator = new core_kernel_classes_ClassIterator($this->topClass);
         
        $found1 = array();
        foreach ($iterator as $resource) {
            $this->assertIsA($resource, 'core_kernel_classes_Class');
            $found1[] = $resource->getUri();
        }
        sort($found1);
         
        $this->assertEquals($expected, $found1);

        $iterator = new core_kernel_classes_ClassIterator($this->emptyClass);
        
        $found2 = array();
        foreach ($iterator as $instance) {
            $found2[] = $instance->getUri();
        }

        $this->assertEquals(1,$iterator->key());
        $this->assertEquals(array($this->emptyClass->getUri()),$found2);
        
    }
    
	public function testResourceIterator()
	{
	    $expected1 = array();
	    foreach ($this->topClass->getInstances(true) as $resource) {
	        $expected1[] = $resource->getUri();
	    }
	    sort($expected1);
	    
	    $iterator = new core_kernel_classes_ResourceIterator($this->topClass);
	    
	    $found1 = array();
	    foreach ($iterator as $instance) {
	        $this->assertIsA($instance, 'core_kernel_classes_Resource');
	        $found1[] = $instance->getUri();
	    }
	    sort($found1);
	    
	    $this->assertEquals($expected1, $found1);

	    $found2 = array();
	    foreach ($iterator as $instance) {
	        $found2[] = $instance->getUri();
	    }
	    sort($found2);
	    $this->assertEquals($expected1, $found2);
	    
	    $iterator = new core_kernel_classes_ResourceIterator($this->emptyClass);
	    $this->assertFalse($iterator->valid());
	    $this->assertEquals('1#0',$iterator->key());

	   
	}
	
	public function testResourceIteratorRewind()
	{
	    $iterator = new core_kernel_classes_ResourceIterator($this->topClass);
	    
	    $first = $iterator->current();
	    
	    $iterator->next();
	    $second = $iterator->current();
	    $this->assertNotEquals($first->getUri(), $second->getUri());
	    
	    $iterator->rewind();
	    $first2 = $iterator->current();
	    $this->assertEquals($first->getUri(), $first2->getUri());
	     
	}
	
	
}