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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *
 *
 */

namespace oat\taoDacSimple\model;

use oat\taoDacSimple\model\DataBaseAccess;
use oat\tao\model\menu\Action;

/**
 * Service to administer the privileges
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
class AdminService
{
    /**
     * Set a new Owener, removing the old owner(s)
     * 
     * @param string $resourceUri
     * @param string $userUri
     * @param string $userType
     * @return boolean
     */
    public static function setOwner($resourceUri, $userUri) {
        
        $db = new DataBaseAccess();
        
        // Needs better abstraction
        $dbRow = $db->getUsersWithPermissions(array($resourceUri));
        foreach ($dbRow as $row) {
            if ($row['resource_id'] == $resourceUri && $row['privilege'] == 'OWNER') {
                $db->removePermissions($row['user_id'], $resourceUri, array('OWNER'));
            }
        }
        
        return $db->addPermissions($userUri, $resourceUri, array('OWNER'));
    }
    
    /**
     * Get a list of users with permissions for a given resource
     * 
     * Returns an associativ array  with userid as key and an array of rights as value
     * 
     * @param string $resourceIds
     * @return array
     */
    public static function getUsersPermissions($resourceUri)
    {
        $db = new DataBaseAccess();
        $results = $db->getUsersWithPermissions(array($resourceUri));
    
        $permissions = array();
        foreach ($results as $result) {
            $user = $result['user_id'];
            
            if (!isset($permissions[$user])) {
                $permissions[$user] = array();
            }
            $permissions[$user][] = $result['privilege'];
        }
        
        return $permissions;
    }
    
    /**
     * recursivly add permissions to a class and all instances
     */
    public static function addPermissionToClass(\core_kernel_classes_Class $class, $userUri, $rights) {
        $dbAccess = new DataBaseAccess();
        $dbAccess->addPermissions($userUri, $class->getUri(), $rights);
        foreach ($class->getInstances(false) as $instance) {
            $dbAccess->addPermissions($userUri, $instance->getUri(), $rights);
        }
        foreach ($class->getSubClasses(false) as $subclass) {
            self::addPermissionToClass($subclass, $userUri, $rights);
        }
    }
}