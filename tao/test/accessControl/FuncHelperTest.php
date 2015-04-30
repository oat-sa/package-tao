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
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @license GPLv2
 * @package tao
 *
 */

use oat\tao\model\accessControl\func\FuncHelper;
use oat\tao\test\TaoPhpUnitTestRunner;

include_once dirname(__FILE__) . '/../../includes/raw_start.php';

/**
 * Unit test the  oat\tao\model\menu\Section class 
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @package tao
 */
class ActionTest extends TaoPhpUnitTestRunner {
    
   
    /**
     * Provides data for testGetClassNameByUrl : url and expected controller class name
     * @return array() the data
     */     
    public function getClassNameByUrlProvider(){
        return array(
            array(ROOT_URL. 'tao/Main/index', 'tao_actions_Main')
        );
    }   
 
    /**
     * Test {@link FuncHelper::getClassNameByUrl}}
     * @dataProvider getClassNameByUrlProvider 
     * @param string $url
     * @param string $expectedClassName
     */
    public function testGetClassNameByUrl($url, $expectedClassName){    
        $className = FuncHelper::getClassNameByUrl($url);

        $this->assertTrue(is_string($className));
        $this->assertEquals($expectedClassName, $className);
    }

    /**
     * Test {@link FuncHelper::getClassNameByUrl}} with wrong parameters
     * @expectedException common_exception_Error
     */
    public function testFailingGetClassNameByUrl(){
       FuncHelper::getClassNameByUrl(''); 
    }

    /**
     * Provides data for testGetClassName : extension name, controller shortname and expected controller class name
     * @return array() the data
     */     
    public function getClassNameProvider(){
        return array(
            array('tao', 'Main', 'tao_actions_Main')
        );
    }   
 
    /**
     * Test {@link FuncHelper::getClassName}}
     * @dataProvider getClassNameProvider 
     * @param string $extension
     * @param string $shortName
     * @param string $expectedClassName
     */
    public function testGetClassName($extension, $shortName, $expectedClassName){    
        $className = FuncHelper::getClassName($extension, $shortName);

        $this->assertTrue(is_string($className));
        $this->assertEquals($expectedClassName, $className);
    }
}
