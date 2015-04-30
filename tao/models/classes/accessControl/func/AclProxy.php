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
namespace oat\tao\model\accessControl\func;

use oat\tao\model\accessControl\AccessControl;
use common_ext_ExtensionsManager;
use common_Logger;
use oat\oatbox\user\User;

/**
 * Proxy for the Acl Implementation
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 
 */
class AclProxy implements AccessControl
{
    const CONFIG_KEY_IMPLEMENTATION = 'FuncAccessControl';
    
    const FALLBACK_IMPLEMENTATION_CLASS = 'oat\tao\model\accessControl\func\implementation\NoAccess';
    
    /**
     * @var FuncAccessControl
     */
    private static $implementation;

    /**
     * @return FuncAccessControl
     */
    protected static function getImplementation() {
        if (is_null(self::$implementation)) {
            $implClass = common_ext_ExtensionsManager::singleton()->getExtensionById('tao')->getConfig(self::CONFIG_KEY_IMPLEMENTATION);
            if (empty($implClass) || !class_exists($implClass)) {
                common_Logger::e('No implementation found for Access Control, locking down the server');
                $implClass = self::FALLBACK_IMPLEMENTATION_CLASS;
            }
            self::$implementation = new $implClass();
        }
        return self::$implementation;
    }
    
    /**
     * Change the implementation of the access control permanently
     * 
     * @param FuncAccessControl $implementation
     */
    public static function setImplementation(FuncAccessControl $implementation) {
        self::$implementation = $implementation;
        common_ext_ExtensionsManager::singleton()->getExtensionById('tao')->setConfig(self::CONFIG_KEY_IMPLEMENTATION, get_class($implementation));
    }    
    
    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\accessControl\AccessControl::hasAccess()
     */
    public function hasAccess(User $user, $controller, $action, $parameters) {
        return self::accessPossible($user, $controller, $action);
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\accessControl\func\FuncAccessControl::accessPossible()
     */
    public static function accessPossible(User $user, $controller, $action) {
        return self::getImplementation()->accessPossible($user, $controller, $action);
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\accessControl\func\FuncAccessControl::applyRule()
     */
    public static function applyRule(AccessRule $rule) {
        self::getImplementation()->applyRule($rule);
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\accessControl\func\FuncAccessControl::revokeRule()
     */
    public static function revokeRule(AccessRule $rule) {
        self::getImplementation()->revokeRule($rule);
    }
}