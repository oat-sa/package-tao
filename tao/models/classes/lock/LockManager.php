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

namespace oat\tao\model\lock;

use oat\tao\model\lock\implementation\NoLock;
/**
 * 
 */
class LockManager {
    
    const CONFIG_ID = 'lock';
    
    private static $implementation = null;

    /**
     * 
     * @param LockSystem $implementation
     */
    static public function setImplementation(LockSystem $implementation) {
        $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
        $ext->setConfig(self::CONFIG_ID, $implementation);
        self::$implementation = $implementation;
    }

    /**
     * @return LockSystem
     */
    static public function getImplementation() {
        if (is_null(self::$implementation)) {
            $ext = \common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
            self::$implementation = $ext->getConfig(self::CONFIG_ID);
        }
        return self::$implementation;
    }

}