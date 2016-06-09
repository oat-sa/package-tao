<?php
/*
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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA ;
 *
 */

namespace oat\taoQtiTest\models;

use oat\tao\model\ClientLibConfigRegistry;
/**
 * Description of TestRunnerConfigRegistry
 *
 * @author sam
 */
class TestRunnerClientConfigRegistry extends ClientLibConfigRegistry
{

    const AMD = 'taoQtiTest/controller/runtime/testRunner';

    /**
     * Path to the runner controller module
     */
    const RUNNER = 'taoQtiTest/controller/runner/runner';

    /**
     * Path to the runner controller module in production mode
     */
    const RUNNER_PROD = 'taoQtiTest/qtiTestRunner.min';

    /**
     * Register a qti tools in the client lib config registry
     *
     * @param string $name
     * @param array $toolConfig
     */
    public function registerQtiTools($name, $toolConfig){
        $newConfig = array('qtiTools' => array());
        //@todo validate tool config structure before registration
        $newConfig['qtiTools'][$name] = $toolConfig;
        $this->register(self::AMD, $newConfig);
    }

    /**
     * Register a runner plugin
     * @param string $module
     * @param string $category
     * @param string|int $position
     * @throws \common_exception_InvalidArgumentType
     */
    public function registerPlugin($module, $category, $position = null){
        if (!is_string($module)) {
            throw new \common_exception_InvalidArgumentType('The module path must be a string!');
        }

        if (!is_string($category)) {
            throw new \common_exception_InvalidArgumentType('The category name must be a string!');
        }

        if (!is_null($position) && !is_string($position) && !is_numeric($position)) {
            throw new \common_exception_InvalidArgumentType('The position must be a string or a number!');
        }

        $config = [];
        $registry = self::getRegistry();
        if ($registry->isRegistered(self::RUNNER)) {
            $config = $registry->get(self::RUNNER);
        }

        $plugins = [];
        if (isset($config['plugins'])) {
            foreach($config['plugins'] as $plugin) {
                if ($plugin['module'] != $module) {
                    $plugins[] = $plugin;
                }
            }
        }

        $plugins[] = [
            'module' => $module,
            'category' => $category,
            'position' => $position,
        ];

        $config['plugins'] = $plugins;
        $registry->set(self::RUNNER, $config);
        $registry->set(self::RUNNER_PROD, $config);

        // TODO: store the list of plugins into json file to compile the controller with dependencies
        // example: file_put_contents($jsonPath, json_encode($plugins, JSON_PRETTY_PRINT));
    }

    /**
     * @param $module
     * @param $category
     * @param null|int $position
     */
    public function removePlugin($module, $category, $position = null)
    {
        $config = [];
        $registry = self::getRegistry();
        
        if ($registry->isRegistered(self::RUNNER)) {
            $config = $registry->get(self::RUNNER);
        }

        if (!isset($config['plugins'])) {
            return;
        }

        $plugins = $config['plugins'];

        $plugin = [
            'module' => $module,
            'category' => $category,
            'position' => $position,
        ];

        $key = array_search($plugin, $plugins);
        if (is_numeric($key)) {
            unset($plugins[$key]);
        }

        $config['plugins'] = $plugins;
        $registry->set(self::RUNNER, $config);
        $registry->set(self::RUNNER_PROD, $config);
    }
}
