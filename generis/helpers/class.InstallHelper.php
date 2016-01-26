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

/**
 * Helper class for instalation.
 */
class helpers_InstallHelper
{
    /**
     * 
     * @param array $extensionIDs
     * @param array $installData
     * @throws common_exception_Error
     * @throws common_ext_ExtensionException
     * @return array installed extensions ids
     */
    public static function installRecursively($extensionIDs, $installData=array())
    {
		$toInstall = array();
		$installed = array();
		foreach ($extensionIDs as $id) {
			$ext = common_ext_ExtensionsManager::singleton()->getExtensionById($id);
			
			if (!common_ext_ExtensionsManager::singleton()->isInstalled($ext->getId())) {
			    common_Logger::d('Extension ' . $id . ' needs to be installed');
				$toInstall[$id] = $ext;
			}
		}
        
        while (!empty($toInstall)) {
        	$modified = false;
        	foreach ($toInstall as $key => $extension) {
        		// if all dependencies are installed
        	    common_Logger::d('Considering extension ' . $key);
        		$allInstalled	= array_keys(common_ext_ExtensionsManager::singleton()->getinstalledextensions());
        		$missing	= array_diff(array_keys($extension->getDependencies()), $allInstalled);
        		if (count($missing) == 0) {
    			    static::install($extension, $installData);
					$installed[] = $extension->getId();
                    common_Logger::i('Extension '.$extension->getId().' installed');
        			unset($toInstall[$key]);
        			$modified = true;
        		} else {
        			$missing = array_diff($missing, array_keys($toInstall));
        			foreach ($missing as $extID) {
        			    common_Logger::d('Extension ' . $extID . ' is required but missing, added to install list');
        				$toInstall[$extID] = common_ext_ExtensionsManager::singleton()->getExtensionById($extID);
        				$modified = true;
        			}
        		}
        	}
        	// no extension could be installed, and no new requirements was added
        	if (!$modified) {
        		throw new \common_exception_Error('Unfulfilable/Cyclic reference found in extensions');
        	}
        }
        return $installed;
    }
    
    protected static function install($extension, $installData) {
        $importLocalData = (isset($installData['import_local']) && $installData['import_local'] == true);
        $extinstaller = static::getInstaller($extension, $importLocalData);
        
        helpers_TimeOutHelper::setTimeOutLimit(helpers_TimeOutHelper::LONG);
        $extinstaller->install();
        helpers_TimeOutHelper::reset();;
    }
    
    protected static function getInstaller($extension, $importLocalData) {
        return new \common_ext_ExtensionInstaller($extension, $importLocalData);
    }

}
