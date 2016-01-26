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
 */

namespace oat\taoQtiTest\scripts\update;

use oat\tao\model\accessControl\func\AccessRule;
use oat\tao\model\accessControl\func\AclProxy;
use oat\taoQtiTest\models\TestRunnerClientConfigRegistry;
use oat\oatbox\service\ServiceNotFoundException;
use oat\taoQtiTest\models\SessionStateService;

/**
 *
 * @author Jean-S�bastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */
class Updater extends \common_ext_ExtensionUpdater {
    
    /**
     * 
     * @param string $initialVersion
     * @return string $versionUpdatedTo
     */
    public function update($initialVersion) {

        $currentVersion = $initialVersion;
        
        // add testrunner config
        if ($currentVersion == '2.6') {

            \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest')->setConfig('testRunner', array(
                'progress-indicator' => 'percentage',
                'timerWarning' => array(
                    'assessmentItemRef' => null,
                    'assessmentSection' => 300,
                    'testPart' => null
                )
            ));

            $currentVersion = '2.6.1';
        }
   
        if ($currentVersion == '2.6.1') {
            $config = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest')->getConfig('testRunner');
            $config['exitButton'] = false;
            \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest')->setConfig('testRunner', $config);

            $currentVersion = '2.6.2';
        }
        
        // add testrunner review screen config
        if ($currentVersion == '2.6.2') {
            $extension = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest');
            $config = $extension->getConfig('testRunner');
            $extension->setConfig('testRunner', array_merge($config, array(
                'test-taker-review' => false,
                'test-taker-review-region' => 'left',
                'test-taker-review-section-only' => false,
                'test-taker-review-prevents-unseen' => true,
            )));

            $currentVersion = '2.6.3';
        }
        
        // adjust testrunner config
        if ($currentVersion == '2.6.3') {
            $defaultConfig = array(
                'timerWarning' => array(
                    'assessmentItemRef' => null,
                    'assessmentSection' => null,
                    'testPart'          => null
                ),
                'progress-indicator' => 'percentage',
                'progress-indicator-scope' => 'testSection',
                'test-taker-review' => false,
                'test-taker-review-region' => 'left',
                'test-taker-review-section-only' => false,
                'test-taker-review-prevents-unseen' => true,
                'exitButton' => false
            );

            $extension = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest');
            $config = $extension->getConfig('testRunner');
            foreach($defaultConfig as $key => $value) {
                if (!isset($config[$key])) {
                    $config[$key] = $value;
                }
            }
            $extension->setConfig('testRunner', $config);

            $currentVersion = '2.6.4';
        }

        if ($currentVersion == '2.6.4') {
            $currentVersion = '2.7.0';
        }

        // add markForReview button
        if ($currentVersion === '2.7.0') {
            $registry = TestRunnerClientConfigRegistry::getRegistry();
            
            $registry->registerQtiTools('markForReview', array(
                'label' => 'Mark for review',
                'icon' => 'anchor',
                'hook' => 'taoQtiTest/testRunner/actionBar/markForReview'
            ));
            
            $currentVersion = '2.8.0';
         }

        // adjust testrunner config: set the review scope
        if ($currentVersion == '2.8.0') {
            $extension = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest');
            $config = $extension->getConfig('testRunner');
            $config['test-taker-review-scope'] = 'test';
            unset($config['test-taker-review-section-only']);
            $extension->setConfig('testRunner', $config);

            $currentVersion = '2.9.0';
        }

       // add show/hide button
        // adjust testrunner config: set the "can collapse" option
        if ($currentVersion == '2.9.0') {
            $registry = TestRunnerClientConfigRegistry::getRegistry();
            
            $registry->registerQtiTools('collapseReview', array(
                'title' => 'Show/Hide the review screen',
                'label' => 'Review',
                'icon' => 'mobile-menu',
                'hook' => 'taoQtiTest/testRunner/actionBar/collapseReview',
                'order' => -1
            ));

            $extension = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest');
            $config = $extension->getConfig('testRunner');
            $config['test-taker-review-can-collapse'] = false;
            $extension->setConfig('testRunner', $config);

            $currentVersion = '2.10.0';
        }

        // adjust testrunner config: set the item sequence number options
        if ($currentVersion == '2.10.0') {
            $extension = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest');
            $config = $extension->getConfig('testRunner');
            $config['test-taker-review-force-title'] = false;
            $config['test-taker-review-item-title'] = 'Item %d';
            $extension->setConfig('testRunner', $config);

            $currentVersion = '2.11.0';
        }

        if ($currentVersion == '2.11.0') {
            $currentVersion = '2.11.1';
        }

        // adjust testrunner config: set the force progress indicator display
        if ($currentVersion == '2.11.1') {
            $extension = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest');
            $config = $extension->getConfig('testRunner');
            $config['progress-indicator-forced'] = false;
            $extension->setConfig('testRunner', $config);

            $currentVersion = '2.12.0';
        }
        
        // update the test taker review action buttons
        if ($currentVersion == '2.12.0') {
            $registry = TestRunnerClientConfigRegistry::getRegistry();

            $registry->registerQtiTools('collapseReview', array(
                'hook' => 'taoQtiTest/testRunner/actionBar/collapseReview',
                'order' => 'first',
                'title' => null,
                'label' => null,
                'icon' => null,
            ));

            $registry->registerQtiTools('markForReview', array(
                'hook' => 'taoQtiTest/testRunner/actionBar/markForReview',
                'order' => 'last',
                'title' => null,
                'label' => null,
                'icon' => null,
            ));

            $currentVersion = '2.13.0';
        }

        // adjust testrunner config: set the next section button display
        if ($currentVersion == '2.13.0') {
            $extension = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiTest');
            $config = $extension->getConfig('testRunner');
            $config['next-section'] = false;
            $extension->setConfig('testRunner', $config);

            $currentVersion = '2.14.0';
        }
        
        if ($currentVersion === '2.14.0') {
            try {
                $this->getServiceManager()->get('taoQtiTest/SessionStateService');
            } catch (ServiceNotFoundException $e) {
                $sessionStateService = new SessionStateService();
                $sessionStateService->setServiceManager($this->getServiceManager());

                $this->getServiceManager()->register('taoQtiTest/SessionStateService', $sessionStateService);
            }

            $currentVersion = '2.15.0';
        }

        if ($currentVersion === '2.15.0') {
            $registry = TestRunnerClientConfigRegistry::getRegistry();
            $registry->registerQtiTools('comment', array(
                'hook' => 'taoQtiTest/testRunner/actionBar/comment'
            ));

            $currentVersion = '2.16.0';
        }

        return $currentVersion;
    }
}
