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

namespace oat\tao\helpers;

use oat\tao\model\controllerMap\Factory;
use oat\generis\model\data\permission\PermissionManager;
use oat\generis\model\data\permission\PermissionInterface;
/**
 * Utility class that focuses on he controllers.
 *
 * @author Joel Bout <joel@taotesting.com>
 * @package tao
 */
class ControllerHelper
{
    const EXTENSION_PREFIX = 'controllerMap_e_';
    const CONTROLLER_PREFIX = 'controllerMap_c_';
    const ACTION_PREFIX = 'controllerMap_a_';
    
    /**
     * Returns al lthe controllers of an extension
     * 
     * @param string $extensionId
     * @return array
     */
    public static function getControllers($extensionId) {
        try {
            $controllerClasses = \common_cache_FileCache::singleton()->get(self::EXTENSION_PREFIX.$extensionId);
        } catch (\common_cache_NotFoundException $e) {
            $factory = new Factory();
            $controllerClasses = array();
            foreach ($factory->getControllers($extensionId) as $controller) {
                $controllerClasses[] = $controller->getClassName();
            }
            \common_cache_FileCache::singleton()->put($controllerClasses, self::EXTENSION_PREFIX.$extensionId);
        }
        return $controllerClasses;
    }
    
    /**
     * Get the list of actions for a controller
     * 
     * @param unknown $controllerClassName
     * @return array
     */
    public static function getActions($controllerClassName) {
        try {
            $actions = \common_cache_FileCache::singleton()->get(self::CONTROLLER_PREFIX.$controllerClassName);
        } catch (\common_cache_NotFoundException $e) {
            $factory = new Factory();
            $desc =  $factory->getControllerDescription($controllerClassName);
            
            $actions = array();
            foreach ($desc->getActions() as $action) {
                $actions[] = $action->getName();
            }
            \common_cache_FileCache::singleton()->put($actions, self::CONTROLLER_PREFIX.$controllerClassName);
        }
        return $actions;
    }
    
    /**
     * Get the required rights for the execution of an action
     *
     * Returns an associative array with the parameter as key
     * and the rights as values
     *
     * @param string $controllerClassName
     * @param string $actionName
     * @return array
     */
    public static function getRequiredRights($controllerClassName, $actionName) {
        try {
            $rights = \common_cache_FileCache::singleton()->get(self::ACTION_PREFIX.$controllerClassName.'@'.$actionName);
        } catch (\common_cache_NotFoundException $e) {
            $factory = new Factory();
            $controller = $factory->getActionDescription($controllerClassName, $actionName);
            $rights = $controller->getRequiredRights();
            \common_cache_FileCache::singleton()->put($rights, self::ACTION_PREFIX.$controllerClassName.'@'.$actionName);
        }
        return $rights;
    }
}