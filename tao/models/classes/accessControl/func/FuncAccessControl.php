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
namespace oat\tao\model\accessControl\func;

use oat\oatbox\user\User;
/**
 * Interface for functionality based access control
 */
interface FuncAccessControl
{
    
    /**
     * Returns whenever or not a user has access to a specified call
     *
     * @param User $user
     * @param string $controller
     * @param string $action
     * @return boolean
     */
    public function accessPossible(User $user, $controller, $action);
    
    /**
     * Apply a rule
     * 
     * @param AccessRule $rule
     */
    public function applyRule(AccessRule $rule);
    
    /**
     * Revoke a rule
     * 
     * @param AccessRule $rule
     */
    public function revokeRule(AccessRule $rule);
    
}