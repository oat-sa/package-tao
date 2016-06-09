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

use oat\tao\test\TaoPhpUnitTestRunner;
use  oat\tao\model\requiredAction\implementation\TimeRule;

include_once dirname(__FILE__) . '/../../includes/raw_start.php';

class TimeRuleTest extends TaoPhpUnitTestRunner
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
    public function testCheck($result, $options)
    {
        $executionTime = isset($options['executionTime']) ? $options['executionTime'] : null;
        $interval = isset($options['interval']) ? $options['interval'] : null;

        $rule = new TimeRule($interval, $executionTime);
        $rule->setRequiredAction($this->getRequiredAction());
        $ruleResult = $rule->check();

        $this->assertEquals($ruleResult, $result);
    }

    /**
     * @return array
     */
    public function optionsProvider()
    {
        return [
            [//action has never been performed
                'result' => true,
                'options' => []
            ],
            [//action should be performed again
                'result' => true,
                'options' => [
                    'interval' => new DateInterval('PT1H'),
                    'executionTime' => (new DateTime())->setTimestamp((time() - 3601)),
                ],
            ],
            [//action has been performed
                'result' => false,
                'options' => [
                    'executionTime' =>  (new DateTime())->setTimestamp((time() - 1)),
                ],
            ],
            [//action has been performed and should not be performed yet
                'result' => false,
                'options' => [
                    'interval' => new DateInterval('PT1H'),
                    'executionTime' => (new DateTime())->setTimestamp((time() - 1)),
                ],
            ],
        ];
    }

    private function getRequiredAction()
    {
        $actionMock = $this->prophesize('oat\tao\model\requiredAction\implementation\RequiredActionRedirect');
        $actionMock->getName()->willReturn('RequiredActionRedirect');

        return $actionMock->reveal();
    }
}