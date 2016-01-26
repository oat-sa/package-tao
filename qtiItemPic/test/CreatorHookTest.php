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

namespace oat\qtiItemPic\test;

use oat\tao\test\TaoPhpUnitTestRunner;
use oat\qtiItemPic\model\CreatorHook;
use oat\taoQtiItem\model\Config;
use oat\qtiItemPic\model\CreatorRegistry;



class CreatorHookTest extends TaoPhpUnitTestRunner
{

    protected $qtiService;

    /**
     * tests initialization
     * load registry service
     */
    public function setUp(){
        TaoPhpUnitTestRunner::initTest();
        $this->registry = new CreatorRegistry();
    }
    
    public function testInit(){
        
        $config = new Config();
        $hook = new CreatorHook();
        
        $hook->init($config);
        
        $configData = $config->toArray();
        
        $this->assertEquals(count($configData['infoControls']), 2);
    }
    
}