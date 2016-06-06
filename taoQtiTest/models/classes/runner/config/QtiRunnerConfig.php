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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */

namespace oat\taoQtiTest\models\runner\config;

use oat\taoQtiTest\models\runner\RunnerServiceContext;

/**
 * Class QtiRunnerOptions
 * @package oat\taoQtiTest\models\runner\options
 */
class QtiRunnerConfig implements RunnerConfig
{
    /**
     * The test runner config
     * @var array
     */
    protected $config;

    /**
     * Returns the config of the test runner
     * @return mixed
     */
    public function getConfig()
    {
        if (is_null($this->config)) {
            // get the raw server config, using the old notation
            $rawConfig = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest')->getConfig('testRunner');

            // build the test config using the new notation
            $this->config = [
                'timerWarning' => isset($rawConfig['timerWarning']) ? $rawConfig['timerWarning'] : null,
                'progressIndicator' => [
                    'type' => isset($rawConfig['progress-indicator']) ? $rawConfig['progress-indicator'] : null,
                    'scope' => isset($rawConfig['progress-indicator-scope']) ? $rawConfig['progress-indicator-scope'] : null,
                    'forced' => isset($rawConfig['progress-indicator-forced']) ? $rawConfig['progress-indicator-forced'] : false,
                ],
                'review' => [
                    'enabled' => !empty($rawConfig['test-taker-review']),
                    'scope' => isset($rawConfig['test-taker-review-scope']) ? $rawConfig['test-taker-review-scope'] : null,
                    'forceTitle' => !empty($rawConfig['test-taker-review-force-title']),
                    'itemTitle' => isset($rawConfig['test-taker-review-item-title']) ? $rawConfig['test-taker-review-item-title'] : null,
                    'preventsUnseen' => !empty($rawConfig['test-taker-review-prevents-unseen']),
                    'canCollapse' => !empty($rawConfig['test-taker-review-can-collapse']),
                ],
                'exitButton' => !empty($rawConfig['exitButton']),
                'nextSection' => !empty($rawConfig['next-section']),
                'plugins' => isset($rawConfig['plugins']) ? $rawConfig['plugins'] : null,
                'security' => [ 
                    'csrfToken' => isset($rawConfig['csrf-token']) ? $rawConfig['csrf-token'] : false,
                ],
                'timer' => [
                    'target' => isset($rawConfig['timer']) && isset($rawConfig['timer']['target']) ? $rawConfig['timer']['target'] : null,
                    'resetAfterResume' => !empty($rawConfig['reset-timer-after-resume']),
                ]
            ];
        }
        return $this->config;
    }

    /**
     * Returns the value of a config entry
     * @param string $name
     * @return mixed
     */
    public function getConfigValue($name)
    {
        $config = $this->getConfig();
        if (isset($config[$name])) {
            return $config[$name];
        }
        return null;
    }

    /**
     * Returns the options related to the current test context
     * @param RunnerServiceContext $context The test context
     * @return mixed
     */
    public function getOptions(RunnerServiceContext $context)
    {
        $session = $context->getTestSession();

        // Comment allowed? Skipping allowed? Logout or Exit allowed ?
        $options = [
            'allowComment' => \taoQtiTest_helpers_TestRunnerUtils::doesAllowComment($session),
            'allowSkipping' => \taoQtiTest_helpers_TestRunnerUtils::doesAllowSkipping($session),
            'exitButton' => \taoQtiTest_helpers_TestRunnerUtils::doesAllowExit($session),
            'logoutButton' => \taoQtiTest_helpers_TestRunnerUtils::doesAllowLogout($session),
        ];

        // get the options from the categories owned by the current item
        $categories = \taoQtiTest_helpers_TestRunnerUtils::getCategories($session);
        $prefixCategory = 'x-tao-option-';
        $prefixCategoryLen = strlen($prefixCategory);
        foreach ($categories as $category) {
            if (!strncmp($category, $prefixCategory, $prefixCategoryLen)) {
                // extract the option name from the category, transform to camelCase if needed
                $optionName = lcfirst(str_replace(' ', '', ucwords(strtr(substr($category, $prefixCategoryLen), ['-' => ' ', '_' => ' ']))));

                // the options added by the categories are just flags
                $options[$optionName] = true;
            }
        }

        return $options;
    }
}
