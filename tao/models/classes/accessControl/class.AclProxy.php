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
 * Proxy for the Acl Implementation
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 
 */
class tao_models_classes_accessControl_AclProxy
{
    const CONFIG_KEY_IMPLEMENTATION = 'AclImplementation';
    
    const FALLBACK_IMPLEMENTATION_CLASS = 'tao_models_classes_accessControl_NoAccess';
    
    /**
     * @var tao_models_classes_accessControl_AccessControl
     */
    private static $implementation;

    /**
     * @return tao_models_classes_accessControl_AccessControl
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
     * @param tao_models_classes_accessControl_AccessControl $implementation
     */
    public static function setImplementation(tao_models_classes_accessControl_AccessControl $implementation) {
        self::$implementation = $implementation;
        common_ext_ExtensionsManager::singleton()->getExtensionById('tao')->setConfig(self::CONFIG_KEY_IMPLEMENTATION, get_class($implementation));
    }    
    
    /**
     * Returns whenever or not a user has access to a specified link
     *
     * @param string $action
     * @param string $controller
     * @param string $extension
     * @param array $parameters
     * @return boolean
     */
    public static function hasAccess($action, $controller, $extension, $parameters = array()) {
        return self::getImplementation()->hasAccess($action, $controller, $extension, $parameters);
    }
    
    /**
     * Apply an AccessControl Rule to the current access control implemnentation
     * 
     * @param tao_models_classes_accessControl_AccessRule $rule
     */
    public static function applyRule(tao_models_classes_accessControl_AccessRule $rule) {
        self::getImplementation()->applyRule($rule);
    }
    
    /**
     * Revoke an AccessControl Rule from the current access control implemnentation
     * 
     * @param tao_models_classes_accessControl_AccessRule $rule
     */
    public static function revokeRule(tao_models_classes_accessControl_AccessRule $rule) {
        self::getImplementation()->revokeRule($rule);
    }
}