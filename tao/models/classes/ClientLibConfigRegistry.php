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
 */

namespace oat\tao\model;

use oat\oatbox\AbstractRegistry;
use \common_ext_ExtensionsManager;

/**
 * 
 * Registry to store client library config that will be provide to requireJs
 *
 * @author Sam, sam@taotesting.com
 */
class ClientLibConfigRegistry extends AbstractRegistry
{

    /**
     * @see \oat\oatbox\AbstractRegistry::getConfigId()
     */
    protected function getConfigId()
    {
        return 'client_lib_config_registry';
    }

    /**
     * @see \oat\oatbox\AbstractRegistry::getExtension()
     */
    protected function getExtension()
    {
        return common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
    }

    /**
     * Register a new path for given alias, trigger a warning if path already register
     *
     * @author Sam, sam@taotesting.com
     * @param string $id
     * @param string $newLibConfig
     */
    public function register($id, $newLibConfig)
    {
        $registry = self::getRegistry();
        $libConfig = array();
        if ($registry->isRegistered($id)) {
            $libConfig = $registry->get($id);
        }

        $libConfig = array_replace_recursive($libConfig, $newLibConfig);
        $registry->set($id, $libConfig);
    }
}