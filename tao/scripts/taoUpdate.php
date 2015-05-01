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

//quick TZ fix
if(function_exists("date_default_timezone_set")){
	date_default_timezone_set('UTC');
}

require_once dirname(__FILE__) .'/../includes/raw_start.php';

echo 'Look for missing required extensions' . PHP_EOL;
$missingId = \helpers_ExtensionHelper::getMissingExtensionIds(common_ext_ExtensionsManager::singleton()->getInstalledExtensions());

$missingExt = array();
foreach ($missingId as $extId) {
    $ext= \common_ext_ExtensionsManager::singleton()->getExtensionById($extId);
    $missingExt[$extId] = $ext;
}

$merged = array_merge(common_ext_ExtensionsManager::singleton()->getInstalledExtensions(),$missingExt);


$sorted = \helpers_ExtensionHelper::sortByDependencies($merged);

foreach ($sorted as $ext) {
    if(!common_ext_ExtensionsManager::singleton()->isInstalled($ext->getId()))
    {
        echo 'Installing ' . $ext->getId() . PHP_EOL;
        $installer = new \tao_install_ExtensionInstaller($ext);
        $installer->install();  
    }
    else {
        $installed = common_ext_ExtensionsManager::singleton()->getInstalledVersion($ext->getId());
        $current = $ext->getVersion();
        if ($installed !== $current) {
            echo $ext->getName().' requires update from '.$installed.' to '.$current.PHP_EOL;
            $updaterClass = $ext->getManifest()->getUpdateHandler();
            if (!is_null($updaterClass)) {
                if (class_exists($updaterClass)) {
                    $updater = new $updaterClass($ext);
                    echo '  Running '.$updaterClass.PHP_EOL;
                    $newVersion = $updater->update($installed);
                    if ($newVersion == $current) {
                        common_ext_ExtensionsManager::singleton()->registerExtension($ext);
                        common_ext_ExtensionsManager::singleton()->setEnabled($ext->getId());
                        echo '  Successfully updated '.$ext->getName().' to '.$newVersion.PHP_EOL;
                    } else {
                        echo '  Update of '.$ext->getName().' exited with version '.$newVersion.''.PHP_EOL;
                    }
                } else {
                    echo '  Updater '.$updaterClass.' not found'.PHP_EOL;
                }
                common_cache_FileCache::singleton()->purge();
            } else {
                echo '  No Updater found for '.$ext->getName().PHP_EOL;
            }
        } else {
            echo $ext->getName().' already up-to-date'.PHP_EOL;
        }
    }

}
//regenerage all client side translation
echo 'Regenerate all client side translations' . PHP_EOL;
tao_models_classes_LanguageService::singleton()->generateClientBundles();
echo 'Update completed' . PHP_EOL;

