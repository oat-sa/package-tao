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
namespace oat\tao\helpers;

use common_Logger;
/**
 * Utility class for instalation.
 *
 * @package tao
 */
class InstallHelper extends \helpers_InstallHelper
{
    /**
     * Override of original install helper to throw install exception
     * on errors
     * 
     * @param array $extensionIDs
     * @param array $installData
     * @throws \tao_install_utils_Exception
     * @return multitype:string
     */
    public static function installRecursively($extensionIDs, $installData=array())
    {
        try {
            return parent::installRecursively($extensionIDs, $installData);
        } catch (\common_ext_ExtensionException $e) {
            common_Logger::w('Exception('.$e->getMessage().') during install for extension "'.$e->getExtensionId().'"');
            throw new \tao_install_utils_Exception("An error occured during the installation of extension '" . $e->getExtensionId() . "'.");
        }
    }
    
    protected static function getInstaller($extension, $importLocalData) {
        return new \tao_install_ExtensionInstaller($extension, $importLocalData);
    }

}
