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
 * Copyright (c) 2013-2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */

namespace oat\qtiItemPci\test;

use oat\tao\test\TaoPhpUnitTestRunner;
use oat\qtiItemPci\model\CreatorHook;
use oat\taoQtiItem\model\CreatorConfig;
use oat\qtiItemPci\model\CreatorRegistry;



class CreatorHookTest extends TaoPhpUnitTestRunner
{

    protected $registry;

    /**
     * tests initialization
     * load registry service
     */
    public function setUp(){
        
        TaoPhpUnitTestRunner::initTest();
        
        $this->registry = new CreatorRegistry();
        $packageValid = dirname(__FILE__).'/samples/valid.zip';
        $this->registry->add($packageValid);
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
    
    public function testInit(){
        
        $config = new CreatorConfig();
        $hook = new CreatorHook();
        
        $hook->init($config);
        
        $configData = $config->toArray();
        
        $this->assertEquals(count($configData['interactions']), 3);
    }
    
}