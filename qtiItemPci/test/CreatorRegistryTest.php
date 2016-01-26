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
 * Copyright (c) 2013-2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */
namespace oat\qtiItemPci\test;

use oat\tao\test\TaoPhpUnitTestRunner;
use oat\qtiItemPci\model\CreatorRegistry;


class CreatorRegistryTest extends TaoPhpUnitTestRunner
{

    protected $qtiService;
    protected $registry;

    /**
     * tests initialization
     * load registry service
     */
    public function setUp(){
        TaoPhpUnitTestRunner::initTest();
        $this->registry = new CreatorRegistry();
        $this->registry->removeAll();
    }
    
    /**
     * remove all created instances
     */
    public function tearDown(){
        if($this->registry != null){
            $this->registry->removeAll();
        }
        else {
            $this->fail('registry should not be null' );
        }
    }
    
    public function testAddGetRemove(){

        $packageValid = dirname(__FILE__).'/samples/valid.zip';
        $typeIdentifier = 'likertScaleInteraction';
        
        //test add
        $hook = $this->registry->add($packageValid);
        $this->assertEquals($hook['typeIdentifier'], $typeIdentifier);
        
        //test get:
        $hook = $this->registry->get($typeIdentifier);
        $this->assertEquals($hook['typeIdentifier'], $typeIdentifier);
        
        //test remove
        $this->registry->remove($typeIdentifier);
        $this->assertNull($this->registry->get($typeIdentifier));
        
    }
    
    public function testMultipleAdd(){
        
        $packageValid = dirname(__FILE__).'/samples/valid.zip';
        $typeIdentifier = 'likertScaleInteraction';
        
        //test add
        $hook = $this->registry->add($packageValid);
        $this->assertEquals($hook['typeIdentifier'], $typeIdentifier);
        
        $this->setExpectedException('common_Exception');
        $hook = $this->registry->add($packageValid);
        
    }
    
    public function testRemoveAll(){
        
        $packageValid = dirname(__FILE__).'/samples/valid.zip';
        $typeIdentifier = 'likertScaleInteraction';
        
        $hook = $this->registry->add($packageValid);
        $this->assertEquals($hook['typeIdentifier'], $typeIdentifier);
        
        $this->registry->removeAll();
        $this->assertNull($this->registry->get($typeIdentifier));
    }
    
    public function testGetDevInteractions(){
        
        $existingId = 'likertScaleInteraction';
        $noExistingId = 'likertscaleinteraction';
        
        $devInteractions = $this->registry->getDevImplementations();
        $this->assertEquals(2, count($devInteractions));
        
        $this->assertNotNull($this->registry->getDevImplementation($existingId));
        
        $this->assertNull($this->registry->getDevImplementation($noExistingId));
        
        $this->assertNotNull($this->registry->getDevImplementationDirectory($existingId));
        
        $this->setExpectedException('common_Exception');
        $this->assertNull($this->registry->getDevImplementationDirectory($noExistingId));
    }
}