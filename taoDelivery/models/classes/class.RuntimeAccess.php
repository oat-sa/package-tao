<?php
/**
 * 
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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

/**
 * Grants direct Access to compiled data
 * This is the fastest implementation but
 * allows anyone access that guesses the path
 * access to the compiled delivery
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItemRunner
 * @subpackage models_classes_itemAccess
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoDelivery_models_classes_RuntimeAccess
{
    const CONFIG_KEY_CONTROLLER = 'RUNTIME_ACCESS_CONTROLLER';
    const CONFIG_KEY_FILESOURCE = 'RUNTIME_ACCESS_FILESOURCE';
    
    
    /**
     * @var tao_models_classes_fsAccess_RuntimeAccessProvider
     */
    private static $provider = null;
    
    /**
     * @return tao_models_classes_fsAccess_FilesystemAccessProvider
     */
	public static function getAccessProvider() {
	    if (is_null(self::$provider)) {
	        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
	        self::$provider = $ext->getConfig(self::CONFIG_KEY_CONTROLLER);
	    }
	    return self::$provider;
	}
	
	public static function setAccessProvider(tao_models_classes_fsAccess_FilesystemAccessProvider $provider) {
		$old = self::getAccessProvider();
		if (!is_null($old)) {
			$old->cleanupProvider();
		}
		$provider->prepareProvider();
		$ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoDelivery');
		$ext->setConfig(self::CONFIG_KEY_CONTROLLER, $provider);
		//$ext->setConfig(self::CONFIG_KEY_FILESOURCE, $fileSource);
	    self::$provider = $provider;
	}
	
    /**
     * @return core_kernel_fileSystem_FileSystem
     */
	public static function getFileSystem() {
	    return self::getAccessProvider()->getFileSystem();
	}
}