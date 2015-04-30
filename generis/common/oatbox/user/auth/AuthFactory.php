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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

namespace oat\oatbox\user\auth;

use common_ext_ExtensionsManager;

/**
 * Create the configured auth adapters
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package generis
 */
class AuthFactory
{
    const CONFIG_KEY = 'auth';
    
    public static function createAdapters() {
        $adapters = array();
        $config = common_ext_ExtensionsManager::singleton()->getExtensionById('generis')->getConfig('auth');
        if (is_array($config)) {
            foreach ($config as $key => $adapterConf) {
                if (isset($adapterConf['driver'])) {
                    $className = $adapterConf['driver'];
                    unset($adapterConf['driver']);
                    if (class_exists($className) && in_array(__NAMESPACE__.'\LoginAdapter', class_implements($className))) {
                        $adapter = new $className();
                        $adapter->setOptions($adapterConf);
                        $adapters[] = $adapter;
                    } else {
                        \common_Logger::e($className.' is not a valid LoginAdapter');
                    }
                } else {
                    \common_Logger::e('No driver for auth adapter '.$key);
                }
            }
        }
        return $adapters;
    }

}