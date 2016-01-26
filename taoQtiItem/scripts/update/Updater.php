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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoQtiItem\scripts\update;

use oat\taoQtiItem\model\SharedLibrariesRegistry;
use oat\tao\model\ThemeRegistry;
use oat\tao\model\websource\TokenWebSource;
use oat\tao\model\ClientLibRegistry;


/**
 * 
 * @author Sam <sam@taotesting.com>
 */
class Updater extends \common_ext_ExtensionUpdater
{

    /**
     * 
     * @param string $initialVersion
     * @return string
     */
    public function update($initialVersion){

        $currentVersion = $initialVersion;

        //add portable shared libraries:
        $libBasePath = ROOT_PATH.'taoQtiItem/views/js/portableSharedLibraries';
        $libRootUrl = ROOT_URL.'taoQtiItem/views/js/portableSharedLibraries';
        $installBasePath = ROOT_PATH.'taoQtiItem/install/scripts/portableSharedLibraries';
        $registry = new SharedLibrariesRegistry($libBasePath, $libRootUrl);

        //migrate from 2.6 to 2.7.0
        if($currentVersion == '2.6'){

            $registry->registerFromFile('IMSGlobal/jquery_2_1_1', $installBasePath.'/IMSGlobal/jquery_2_1_1.js');
            $registry->registerFromFile('OAT/lodash', $installBasePath.'/OAT/lodash.js');
            $registry->registerFromFile('OAT/async', $installBasePath.'/OAT/async.js');
            $registry->registerFromFile('OAT/raphael', $installBasePath.'/OAT/raphael.js');
            $registry->registerFromFile('OAT/scale.raphael', $installBasePath.'/OAT/scale.raphael.js');
            $registry->registerFromFile('OAT/util/xml', $installBasePath.'/OAT/util/xml.js');
            $registry->registerFromFile('OAT/util/math', $installBasePath.'/OAT/util/math.js');
            $registry->registerFromFile('OAT/util/html', $installBasePath.'/OAT/util/html.js');
            $registry->registerFromFile('OAT/util/EventMgr', $installBasePath.'/OAT/util/EventMgr.js');
            $registry->registerFromFile('OAT/util/event', $installBasePath.'/OAT/util/event.js');

            $currentVersion = '2.7.0';
        }

        //migrate from 2.7.0 to 2.7.1
        if($currentVersion == '2.7.0'){

            $registry->registerFromFile('OAT/sts/common', $installBasePath . '/OAT/sts/common.js');
            $registry->registerFromFile('OAT/interact', $installBasePath . '/OAT/interact.js');
            $registry->registerFromFile('OAT/interact-rotate', $installBasePath . '/OAT/interact-rotate.js');

            $currentVersion = '2.7.1';
        }

        //migrate from 2.7.0 to 2.7.1
        if($currentVersion == '2.7.1'){

            $registry->registerFromFile('OAT/sts/transform-helper', $installBasePath . '/OAT/sts/transform-helper.js');

            $currentVersion = '2.7.2';
        }

        //migrate from 2.7.2 to 2.7.3
        if($currentVersion == '2.7.2'){

            $registry->registerFromFile('IMSGlobal/jquery_2_1_1', $installBasePath.'/IMSGlobal/jquery_2_1_1.js');
            $registry->registerFromFile('OAT/lodash', $installBasePath.'/OAT/lodash.js');
            $registry->registerFromFile('OAT/async', $installBasePath.'/OAT/async.js');
            $registry->registerFromFile('OAT/raphael', $installBasePath.'/OAT/raphael.js');
            $registry->registerFromFile('OAT/scale.raphael', $installBasePath.'/OAT/scale.raphael.js');
            $registry->registerFromFile('OAT/util/xml', $installBasePath.'/OAT/util/xml.js');
            $registry->registerFromFile('OAT/util/math', $installBasePath.'/OAT/util/math.js');
            $registry->registerFromFile('OAT/util/html', $installBasePath.'/OAT/util/html.js');
            $registry->registerFromFile('OAT/util/EventMgr', $installBasePath.'/OAT/util/EventMgr.js');
            $registry->registerFromFile('OAT/util/event', $installBasePath.'/OAT/util/event.js');
            $registry->registerFromFile('OAT/sts/common', $installBasePath . '/OAT/sts/common.js');
            $registry->registerFromFile('OAT/interact', $installBasePath . '/OAT/interact.js');
            $registry->registerFromFile('OAT/interact-rotate', $installBasePath . '/OAT/interact-rotate.js');
            $registry->registerFromFile('OAT/sts/transform-helper', $installBasePath . '/OAT/sts/transform-helper.js');

            $currentVersion = '2.7.3';
        }

        //migrate from 2.7.3 to 2.7.4
        if($currentVersion == '2.7.3'){
            $registry->registerFromFile('OAT/handlebars', $installBasePath.'/OAT/handlebars.js');
            $currentVersion = '2.7.4';
        }

        if($currentVersion == '2.7.4'){
            $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem');
            $ext->setConfig('qtiCreator', array('multi-column' => false));
            $currentVersion = '2.7.5';
        }
        
        if($currentVersion == '2.7.5'){

            $registry->registerFromFile('OAT/sts/stsEventManager', $installBasePath . '/OAT/sts/stsEventManager.js');

            $currentVersion = '2.7.6';
        }
        
        if($currentVersion == '2.7.6'){

            $registry->registerFromFile('OAT/sts/common', $installBasePath . '/OAT/sts/common.js');

            $currentVersion = '2.7.7';
        }
        
        if($currentVersion == '2.7.7'){

            $itemThemesDataPath = FILES_PATH.'tao'.DIRECTORY_SEPARATOR.'themes'.DIRECTORY_SEPARATOR;
            $itemThemesDataPathFs = \tao_models_classes_FileSourceService::singleton()->addLocalSource('Theme FileSource', $itemThemesDataPath);

            $websource = TokenWebSource::spawnWebsource($itemThemesDataPathFs);
            ThemeRegistry::getRegistry()->setWebSource($websource->getId());

            ThemeRegistry::getRegistry()->createTarget('items', 'taoQtiItem/views/css/qti-runner.css');
            ThemeRegistry::getRegistry()->registerTheme('tao', 'TAO', 'taoQtiItem/views/css/themes/default.css', array('items'));
            ThemeRegistry::getRegistry()->setDefaultTheme('items', 'tao');

        	$currentVersion = '2.7.8';
        }

		if($currentVersion == '2.7.8'){

            $clientLibRegistry = ClientLibRegistry::getRegistry();
            $clientLibRegistry->register('qtiCustomInteractionContext', '../../../taoQtiItem/views/js/runtime/qtiCustomInteractionContext');
            $clientLibRegistry->register('qtiInfoControlContext', '../../../taoQtiItem/views/js/runtime/qtiInfoControlContext');

            $currentVersion = '2.7.9';
        }
        if($currentVersion == '2.7.9'){
             $currentVersion = '2.8.0';
        }
        
        if($currentVersion == '2.8.0'){
            $currentVersion = '2.8.1';
            $registry->registerFromFile('OAT/sts/common', $installBasePath . '/OAT/sts/common.js');
        }
        
        if ($currentVersion == '2.8.1') {
            $qtiItem = \common_ext_ExtensionsManager::singleton()->getExtensionById('taoQtiItem');
            $qtiItem->setConfig('userScripts', array());
            $currentVersion = '2.9.0';
        }

        if ($currentVersion === '2.9.0') {
            $registry->registerFromFile('OAT/waitForMedia', $installBasePath . '/OAT/waitForMedia.js');
            $currentVersion = '2.9.1';
        }
        if ($currentVersion === '2.9.1') {
            $currentVersion = '2.10.0';
        }
        if($currentVersion === '2.10.0') {
            $currentVersion = '2.11.0';
        }
        if($currentVersion === '2.11.0') {
            $registry->registerFromFile('OAT/util/asset', $installBasePath . '/OAT/util/asset.js');
            $registry->registerFromFile('OAT/util/tpl', $installBasePath . '/OAT/util/tpl.js');
            $currentVersion = '2.12.0';
        }
        if($currentVersion === '2.12.0'){
            $currentVersion = '2.12.1';
        }
        return $currentVersion;
    }

}