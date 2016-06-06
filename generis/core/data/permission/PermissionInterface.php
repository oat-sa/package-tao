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
namespace oat\generis\model\data\permission;

use oat\oatbox\user\User;
/**
 * Rdf interface to access the ontology
 * This is an experimental interface that has not been implemented yet
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package generis
 
 */
interface PermissionInterface
{
    /**
     * All unsupported rigths will be mapped to this right
     * 
     * @var string
     */
    const RIGHT_UNSUPPORTED = 'unsupported';
    
    /**
     * Return the permissions a specified user has on the resources
     * specified by their ids
     * 
     * This function should return an associativ array with the resourceIds
     * as keys an the permission arrays as values
     * 
     * @param User $user
     * @param array $resourceIds
     * @return array
     */
    public function getPermissions(User $user, array $resourceIds);
    
    /**
     * Hook to set initial permissions
     */
    public function onResourceCreated(\core_kernel_classes_Resource $resource);
    
    /**
     * Returns a list of rights ids
     * 
     * @return array
     */    
    public function getSupportedRights();
}