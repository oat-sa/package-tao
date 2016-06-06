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
 *
 */

namespace oat\taoMediaManager\model\fileManagement;

use oat\oatbox\service\ServiceManager;
class FileManager
{

    const CONFIG_KEY = 'fileManager';

    /**
     * @var FileManagement
     */
    private static $fileManager = null;


    /**
     * @return mixed|FileManagement|SimpleFileManagement
     * @throws \common_exception_Error
     */
    public static function getFileManagementModel()
    {
        if (is_null(self::$fileManager)) {
            $data = ServiceManager::getServiceManager()->get(FileManagement::SERVICE_ID);
            if (is_string($data)) {
                // legacy
                if (class_exists($data)) {
                    self::$fileManager = new $data();
                } else {
                    \common_Logger::w('No file manager implementation found');
                    self::$fileManager = new SimpleFileManagement();
                }
            } elseif (is_object($data)) {
                self::$fileManager = $data;
            } else {
                throw new \common_exception_Error('Unsupported configuration for ' . __CLASS__);
            }
        }
        return self::$fileManager;
    }

    /**
     * @param FileManagement $model
     */
    public static function setFileManagementModel(FileManagement $model)
    {
        \common_ext_ExtensionsManager::singleton()->getExtensionById('taoMediaManager')->setConfig(
            self::CONFIG_KEY,
            $model
        );
    }

} 