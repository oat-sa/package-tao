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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoQtiTest\test;

use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoQtiTest\models\TestRunnerClientConfigRegistry;

include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * Class TestRunnerClientConfigRegistryTest
 * @package oat\taoQtiTest\test
 */
class TestRunnerClientConfigRegistryTest extends TaoPhpUnitTestRunner
{

    protected $currentConfig;

    public function setUp()
    {
        TaoPhpUnitTestRunner::initTest();
    }

    /**
     * @after
     */
    public function returnConfigBack()
    {
        if (is_array($this->currentConfig)) {
            $registry = TestRunnerClientConfigRegistry::getRegistry();
            $registry->set(TestRunnerClientConfigRegistry::RUNNER, $this->currentConfig);
            $registry->set(TestRunnerClientConfigRegistry::RUNNER_PROD, $this->currentConfig);
        }
    }

    /**
     * Test removing plugin from <i>client_lib_config_registry.conf.php</i> config
     * Be aware that in case of fatal error in the config can remain test data.
     */
    public function testRemovePlugin()
    {
        $registry = TestRunnerClientConfigRegistry::getRegistry();

        if ($registry->isRegistered(TestRunnerClientConfigRegistry::RUNNER)) {
            $this->currentConfig = $registry->get(TestRunnerClientConfigRegistry::RUNNER);

            $registry->registerPlugin('must/be/removed', 'categoryName');

            $testConfig = $registry->get(TestRunnerClientConfigRegistry::RUNNER);
            $this->assertNotFalse(array_search([
                'module' => 'must/be/removed',
                'category' => 'categoryName',
                'position' => null,
            ], $testConfig['plugins']));

            $registry->removePlugin('must/be/removed', 'categoryName');

            $testConfig = $registry->get(TestRunnerClientConfigRegistry::RUNNER);
            $this->assertFalse(array_search([
                'module' => 'must/be/removed',
                'category' => 'categoryName',
                'position' => null,
            ], $testConfig['plugins']));

        }
    }
}
