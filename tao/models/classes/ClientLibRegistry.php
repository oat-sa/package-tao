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
namespace oat\tao\model;

use oat\oatbox\AbstractRegistry;
use \common_ext_ExtensionsManager;
use \common_Logger;

/**
 * 
 * Registry to store client library paths that will be provide to requireJs
 *
 * @author Lionel Lecaque, lionel@taotesting.com
 */
class ClientLibRegistry extends AbstractRegistry
{

    /**
     * @see \oat\oatbox\AbstractRegistry::getConfigId()
     */
    protected function getConfigId()
    {
        return 'client_lib_registry';
    }

    /**
     * @see \oat\oatbox\AbstractRegistry::getExtension()
     */
    protected function getExtension()
    {
        return common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
    }

    /**
     * 
     * Return all lib alias with relative path
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     * @return array
     */
    public function getLibAliasMap()
    {
        $extensionsAliases = array();
        foreach (ClientLibRegistry::getRegistry()->getMap() as $alias => $lib ){
            $extensionsAliases[$alias] = str_replace(ROOT_URL, '../../../', $lib);
        }
        return $extensionsAliases;
    }
    
    
    /**
     * Register a new path for given alias, trigger a warning if path already register
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param string $id
     * @param string $fullPath
     */
    public function register($id, $fullPath)
    {
        if (self::getRegistry()->isRegistered($id)) {
            common_Logger::w('Lib already registered');
        }
        self::getRegistry()->set($id, $fullPath);
    }
}

?>