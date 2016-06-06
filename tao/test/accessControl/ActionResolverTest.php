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

use oat\tao\model\accessControl\ActionResolver;
use oat\tao\model\routing\Resolver;
use oat\tao\test\TaoPhpUnitTestRunner;
include_once dirname(__FILE__) . '/../../includes/raw_start.php';

/**
 * Test {@link oat\tao\model\accessControl\ActionResolver}
 * 
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @package tao
 */
class ActionResolverTest extends TaoPhpUnitTestRunner {
    
   
    /**
     * Provides data for {@link self::testActionResolver} : url, expected controller class name and actioni
     * @todo add a row with a namespaced controller
     * @return array[] the data
     */     
    public function actionResolverProvider(){
        return array(
            array(ROOT_URL . 'tao/Main/index', 'tao_actions_Main', 'index'),
        );
    }   
 
    /**
     * Test {@link ActionResolver} construction from url
     * @dataProvider actionResolverProvider
     * @param string $url
     * @param string $expectedClassName
     * @param string $expectedAction
     */
    public function testActionResolver($url, $expectedClassName, $expectedAction){ 

        $resolver = new ActionResolver($url);

        $this->assertFalse(is_null($resolver));
        $this->assertTrue($resolver instanceof ActionResolver);

        $this->assertEquals($expectedClassName, $resolver->getController());
        $this->assertEquals($expectedAction, $resolver->getAction());
    }

    /**
     * Test the constructor to throw an exception if the url is unknown
     * @expectedException ResolverException
     */
    public function testFailingConstructor(){
        new ActionResolver('/foo/bar/index');
    }

    /**
     * Provides data for {@link self::testByControllerName} : extension, shortname and expacted controller class and action.
     * @todo add a row with a namespaced controller
     * @return array[] the data
     */     
    public function getByControllerNameProvider(){
        return array(
            array('tao', 'Main', 'tao_actions_Main', 'index'),
        );
    }   
 
    /**
     * Test {@link ActionResolver::getByControllerName(} 
     * @dataProvider getByControllerNameProvider 
     * @param string $extension
     * @param string $shortName
     * @param string $expectedClassName
     * @param string $expectedAction
     */
    public function testGetByControllerName($extension, $shortName, $expectedClassName, $expectedAction){    
        $resolver = ActionResolver::getByControllerName($shortName, $extension);

        $this->assertFalse(is_null($resolver));
        $this->assertTrue($resolver instanceof ActionResolver);

        $this->assertEquals($expectedClassName, $resolver->getController());
        $this->assertEquals($expectedAction, $resolver->getAction());
    }
}
