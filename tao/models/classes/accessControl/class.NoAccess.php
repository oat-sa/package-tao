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
class tao_models_classes_accessControl_NoAccess
    implements tao_models_classes_accessControl_AccessControl
{
    /**
     * 
     */
    public function __construct() {
    }
    
    /**
     * (non-PHPdoc)
     * @see tao_models_classes_accessControl_AccessControl::hasAccess()
     */
    public function hasAccess($action, $controller, $extension, $parameters) {
        return false;
    }
    
    public function applyRule(tao_models_classes_accessControl_AccessRule $rule) {
        // nothing can be done
    }
    
    public function revokeRule(tao_models_classes_accessControl_AccessRule $rule) {
        // nothing can be done
    }
}