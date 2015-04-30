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

namespace oat\taoDacSimple\scripts\update;

use oat\taoDacSimple\model\PermissionProvider;
use oat\taoDacSimple\model\AdminService;

/**
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
class Updater extends \common_ext_ExtensionUpdater {
    
    /**
     * 
     * @param string $currentVersion
     * @return string $versionUpdatedTo
     */
    public function update($initialVersion) {
        
        $currentVersion = $initialVersion;
        
        if ($currentVersion == '1.0') {
            $impl = new PermissionProvider();
            
            // add read access to Items
            $class = new \core_kernel_classes_Class(TAO_ITEM_CLASS);
            AdminService::addPermissionToClass($class, INSTANCE_ROLE_BACKOFFICE, array('READ'));
            
            // add backoffice user rights to Tests
            $class = new \core_kernel_classes_Class(TAO_TEST_CLASS);
            AdminService::addPermissionToClass($class, INSTANCE_ROLE_BACKOFFICE, $impl->getSupportedRights());
            $currentVersion = '1.0.1';
        }
        
        return $currentVersion;
    }
}