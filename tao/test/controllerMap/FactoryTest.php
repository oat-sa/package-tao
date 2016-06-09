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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Konstantin Sasim <sasim@1pt.com>
 * @license GPLv2
 * @package tao
 *
 */

use oat\tao\model\controllerMap\Factory;
use oat\tao\model\controllerMap\ControllerDescription;
use oat\tao\model\controllerMap\ActionDescription;
use oat\tao\test\TaoPhpUnitTestRunner;

use oat\tao\test\controllerMap\stubs\ValidNamespacedController;

//include_once __DIR__ . '/stubs/class.ValidController.php';
include_once dirname(__FILE__) . '/../../includes/raw_start.php';

/* stubs for controller class name validation */
class FakeStandaloneController {

}

abstract class FakeAbstractController extends Module {

}

class FakeValidController extends Module {

}

class FactoryTest extends TaoPhpUnitTestRunner {

    /** @var  Factory */
    protected $factory;

    protected function setUp()
    {
        parent::setUp();

        $this->factory = new Factory();
    }

    /**
     * Provides data for {@link self::testGetControllerDescription}: controller name (non-namespaced, namespaced)
     *
     * @return array[] the data
     */
    public function controllerNameProvider()
    {
        return array(
            array('tao_test_controllerMap_stubs_ValidController'),
            array('oat\\tao\\test\\controllerMap\\stubs\\ValidNamespacedController'),
        );
    }

    /**
     * Provides valid data for {@link self::testGetActionDescription}:
     *  - controller class name (non-namespaced, namespaced)
     *  - action name
     *  - description
     *  - required rights
     *
     * @return array[] the data
     */
    public function controllerActionNameProvider()
    {
        return array(
            array(
                'tao_test_controllerMap_stubs_ValidController',
                'validAction',
                'Valid non-namespaced stub controller action',
                array(
                    'id' => 'READ'
                )
            ),
            array(
                'oat\\tao\\test\\controllerMap\\stubs\\ValidNamespacedController',
                'validAction',
                'Valid namespaced stub controller action',
                array(
                    'id' => 'WRITE'
                )
            ),
        );
    }

    /**
     * Provides invalid data for {@link self::testGetActionDescriptionForMissingAction}:
     *  - controller class name
     *  - action name
     *
     * @return array[] the data
     */
    public function missingControllerActionNameProvider()
    {
        return array(
            array(
                'tao_actions_Main',
                'missingAction'
            ),
            array(
                'missing_Controller',
                'index',
            ),
            array(
                'missing_Controller',
                'missingAction'
            )
        );
    }

    /**
     * Provides data for:
     *  - {@link self::testGetControllers}
     *  - {@link self::testGetControllersClasses}
     * Data: extension name
     *
     * @return array[] the data
     */
    public function extensionNameProvider()
    {
        return array(
            array('tao'),
        );
    }

    /**
     * Test {@link Factory::getControllerDescription}
     *
     * @dataProvider controllerNameProvider
     * @param string $controllerClassName
     */
    public function testGetControllerDescription($controllerClassName)
    {
        $description = $this->factory->getControllerDescription($controllerClassName);

        $this->assertNotNull($description);
        $this->assertTrue($description instanceof ControllerDescription);
        $this->assertEquals($controllerClassName, $description->getClassName());
    }

    /**
     * Negative test {@link Factory::getControllerDescription}
     *
     * @dataProvider missingControllerActionNameProvider
     * @param string $controllerClassName
     * @param string $actionName
     * @expectedException oat\tao\model\controllerMap\ActionNotFoundException
     */
    public function testGetActionDescriptionForMissingAction($controllerClassName, $actionName)
    {
        $this->factory->getActionDescription($controllerClassName, $actionName);
    }

    /**
     * Test {@link Factory::getActionDescription}
     *
     * @dataProvider controllerActionNameProvider
     * @param string $controllerClassName
     * @param string $actionName
     * @param string $descriptionStub
     * @param array  $rightsStub
     */
    public function testGetActionDescription($controllerClassName, $actionName, $descriptionStub, $rightsStub)
    {
        $description = $this->factory->getActionDescription($controllerClassName, $actionName);

        $this->assertTrue($description instanceof ActionDescription);
        $this->assertEquals($actionName, $description->getName());
        $this->assertEquals($descriptionStub, $description->getDescription());
        $this->assertEquals($rightsStub, $description->getRequiredRights());
    }

    /**
     * Test {@link Factory::getControllers}
     *
     * @dataProvider extensionNameProvider
     * @param string $extensionId
     */
    public function testGetControllers($extensionId)
    {
        $controllers = $this->factory->getControllers($extensionId);
        $this->assertNotNull($controllers);
        $this->assertTrue(is_array($controllers));
        $this->assertContainsOnlyInstancesOf('oat\tao\model\controllerMap\ControllerDescription', $controllers);
    }

    /**
     *Test {@link Factory::isControllerClassNameValid}
     */
    public function testIsControllerClassNameValid()
    {
        $methodName = 'isControllerClassNameValid';

        $this->assertFalse( $this->invokeProtectedMethod($this->factory, $methodName, array('FakeStandaloneController') ), 'has valid descendant' );
        $this->assertFalse( $this->invokeProtectedMethod($this->factory, $methodName, array('FakeAbstractController') ), 'is not abstract' );
        $this->assertTrue(  $this->invokeProtectedMethod($this->factory, $methodName, array('FakeValidController') ), 'is valid' );
    }

    /**
     * Test {@link Factory::getControllerClasses}
     *
     * @dependsOn testIsControllerClassNameValid
     * @dataProvider extensionNameProvider
     * @param string $extensionId
     */
    public function testGetControllersClasses($extensionId)
    {
        $extension = new common_ext_Extension( $extensionId );

        $controllers = $this->invokeProtectedMethod($this->factory, 'getControllerClasses', array($extension));
        $this->assertNotNull($controllers);
        $this->assertContainsOnly('string', $controllers, true);

        $validCount = 0;
        foreach( $controllers as $className ){
            if( $this->invokeProtectedMethod($this->factory, 'isControllerClassNameValid', array($className) ) ){
                $validCount++;
            }
        }

        $this->assertEquals(count($controllers), $validCount);
    }

} 