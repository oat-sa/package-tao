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
 * @author lionel
 * @license GPLv2
 * @package tao
 *
 */

use oat\tao\test\TaoPhpUnitTestRunner;

include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 *
 * @author Lionel Lecaque, lionel@taotesting.com
 * @package tao

 */
class IconsTest extends TaoPhpUnitTestRunner {
    
    
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     * @dataProvider iconsProvider
     */
    public function testBuildIcons($method,$const){
        
       
        $span = $method->invoke(null);
        $this->assertEquals(preg_match('#<span class="(.*)">#', $span,$res),1);
        
        //$const = $this->getConst();
        $this->assertTrue(in_array($res[1], $const));
        
        $toto = $method->invoke(null,array('element'=> 'toto'));
        $this->assertEquals(preg_match('#<toto class="(.*)">#', $toto,$res),1);
        $this->assertTrue(in_array($res[1], $const));
        
    }
        
    public function iconsProvider(){
        $reflection = new ReflectionClass('tao_helpers_Icon');
        $methods = array();
        foreach ($reflection->getMethods() as $method) {
            if($method->isPublic()){
                $methods[] = array($method,array_values($reflection->getConstants()));
            }
        }
           
        return $methods;
        
        
    }
}