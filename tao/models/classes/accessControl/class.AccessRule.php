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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * Sample ACL Implementation allowing access to everything
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 
 */
class tao_models_classes_accessControl_AccessRule
{
    const GRANT = 'grant';
    const DENY = 'deny';
    
    private $grantDeny;
    
    private $role;
    
    private $mask;
    
    public function __construct($mode, $roleUri, $mask) {
        $this->grantDeny = $mode;
        $this->role = new core_kernel_classes_Resource($roleUri);
        $this->mask = $mask;
    }
    
    /**
     * Those the role grant you access?
     * @return bool
     */
    public function isGrant() {
        return $this->grantDeny == self::GRANT;
    }
    
    /**
     * Gets the role this rule applies to
     * @return core_kernel_classes_Resource
     */
    public function getRole() {
        return $this->role;
    }
    
    /**
     * Returns the filter of the rule
     * @return array
     */
    public function getMask() {
        return $this->mask;
    }
        
}