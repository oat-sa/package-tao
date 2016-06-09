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

use core_kernel_classes_Resource;
/**
 * manage lock on a given resource
 *
 * @author plichart
 */
interface LockSystem {
    
    /**
     * set a lock on @resource with owner @user, succeeds also if there is a lock already exists but with the same owner
     *
     * @throws ResourceLockedException if the resource already has a lock with a different owner
     * @param core_kernel_classes_Resource $resource
     * @param core_kernel_classes_Resource $user
     */
    public function setLock(core_kernel_classes_Resource $resource, $ownerId);
    
    /**
     * return true is the resource is locked, else otherwise
     * @return boolean
     */
    public function isLocked(core_kernel_classes_Resource $resource);
    
    /**
     *  release the lock if owned by @user
     * @param core_kernel_classes_Resource $resource
     * @param core_kernel_classes_Resource $user
     * @throw common_Exception no lock to release
     */
    public function releaseLock(core_kernel_classes_Resource $resource, $ownerId);
    
   /**
     *  release the lock 
     * @param core_kernel_classes_Resource $resource
     * @throw common_Exception no lock to release
     */
    public function forceReleaseLock(core_kernel_classes_Resource $resource);
    
    /**
     * Return lock details or null if no lock found
     * 
     * @param core_kernel_classes_Resource $resource
     * @return Lock
     */
    public function getLockData(core_kernel_classes_Resource $resource);

}
