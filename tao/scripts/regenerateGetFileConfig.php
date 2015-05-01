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

//quick TZ fix
if(function_exists("date_default_timezone_set")){
	date_default_timezone_set('UTC');
}

require_once dirname(__FILE__) .'/../includes/raw_start.php';

$taoExtension = common_ext_ExtensionsManager::singleton()->getExtensionById('tao');

$file = $taoExtension->getDir().'includes'.DIRECTORY_SEPARATOR.'configGetFile.php';

//verify write access
if (file_exists($file) && !is_writable($file)) {
    echo 'No write access to "' . $file . '", aborting.' . PHP_EOL;
    exit(1);
}

if ($taoExtension->hasConfig(tao_models_classes_fsAccess_Manager::CONFIG_KEY)) {
    $configData = array();
    foreach ($taoExtension->getConfig(tao_models_classes_fsAccess_Manager::CONFIG_KEY) as $serialized) {
        $provider = tao_models_classes_fsAccess_AccessProvider::restoreFromString($serialized);
        if ($provider instanceof tao_models_classes_fsAccess_TokenAccessProvider) {
            echo 'Restoring provider with Id ' . $provider->getId() . PHP_EOL;
            list($class, $id, $fsUri, $rawConfig) = explode(' ', $serialized, 4);
            $config = json_decode($rawConfig, true);
            if (!is_array($config)) {
                echo 'Unable to read config for access provider ' . $provider->getUri() . ', aborting.' . PHP_EOL;
                exit(1);
            }
        }
        
        $configData[$provider->getId()] = array(
            'secret' => $config['secret'],
            'folder' => $provider->getFileSystem()->getPath()
        );
    }
    $success = file_put_contents($file, "<?php return ".common_Utils::toPHPVariableString($configData).";");
    if ($success !== false) {
        echo 'Successfully saved GetFile config.' . PHP_EOL;
        die(0);
    } else {
        echo 'Unable to write to "' . $file . '", aborting.' . PHP_EOL;
        die(1);
    }
} else {
    echo 'No access providers found, aborting.' . PHP_EOL;
    exit(1);
}