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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 *
 */

use oat\tao\model\requiredAction\implementation\RequiredActionRedirect;
use oat\tao\model\requiredAction\implementation\TimeRule;
use oat\tao\test\TaoPhpUnitTestRunner;
use Prophecy\Argument;

include_once dirname(__FILE__) . '/../../includes/raw_start.php';

/**
 * class RequiredActionTest
 *
 * @author Aleh Hutnilau <hutnikau@1pt.com>
 * @package tao
 */
class RequiredActionRedirectTest extends TaoPhpUnitTestRunner
{
    /**
     * tests initialization
     */
    public function setUp()
    {
        TaoPhpUnitTestRunner::initTest();
    }

    /**
     * tests clean up
     */
    public function tearDown()
    {

    }

    /**
     * @dataProvider optionsProvider
     */
    public function testMustBeExecuted($result, $rules)
    {
        $action = new RequiredActionRedirect('testAction', $rules, 'url');
        $this->assertEquals($action->mustBeExecuted(), $result);
    }

    /**
     * @return array
     */
    public function optionsProvider()
    {
        return [
            [//no rules
                'result' => false,
                'rules' => [],
            ],
            [//one rule which returns false
                'result' => false,
                'rules' => [
                    $this->getNegativeRule()
                ],
            ],
            [//one rule which returns true
                'result' => true,
                'rules' => [
                    $this->getPositiveRule(),
                ],
            ],
            [//two rules and one of them returns true
                'result' => true,
                'rules' => [
                    $this->getPositiveRule(),
                    $this->getNegativeRule()
                ],
            ],
        ];
    }

    private function getPositiveRule()
    {
        $ruleMock = $this->prophesize('oat\tao\model\requiredAction\implementation\TimeRule');
        $ruleMock->setRequiredAction(Argument::type('oat\tao\model\requiredAction\RequiredActionInterface'))->willReturn(null);
        $ruleMock->check()->willReturn(true);

        return $ruleMock->reveal();
    }

    private function getNegativeRule()
    {
        $ruleMock = $this->prophesize('oat\tao\model\requiredAction\implementation\TimeRule');
        $ruleMock->setRequiredAction(Argument::type('oat\tao\model\requiredAction\RequiredActionInterface'))->willReturn(null);
        $ruleMock->check()->willReturn(false);

        return $ruleMock->reveal();
    }

}