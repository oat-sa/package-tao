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

namespace oat\generis\model\data\permission\implementation;

use oat\generis\model\data\permission\PermissionInterface;
use oat\oatbox\user\User;
use oat\oatbox\Configurable;

/**
 * Simple permissible Permission model
 * 
 * does not require privileges
 * does not grant privileges
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 */
class FreeAccess extends Configurable
    implements PermissionInterface
{
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\PermissionInterface::getPermissions()
     */
    public function getPermissions(User $user, array $resourceIds) {
        return array_fill_keys($resourceIds, array(PermissionInterface::RIGHT_UNSUPPORTED));
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\PermissionInterface::onResourceCreated()
     */
    public function onResourceCreated(\core_kernel_classes_Resource $resource) {
        // do nothing
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\PermissionInterface::getSupportedPermissions()
     */
    public function getSupportedRights() {
        return array();
    }
}