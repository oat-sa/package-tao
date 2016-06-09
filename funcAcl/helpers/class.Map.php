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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

use oat\tao\model\accessControl\func\FuncHelper;

/**
 * Helper to map URIs to controllers
 *
 * @access public
 * @author Joel Bout <joel@taotesting.com>
 * @package tao
 */
class funcAcl_helpers_Map
{
    
    public static function getUriForExtension($extId) {
        return funcAcl_models_classes_AccessService::singleton()->makeEMAUri($extId);
    }
    
    public static function getUriForController($controllerClassName) {
        $extension = self::getExtensionFromController($controllerClassName);
        $shortName = strpos($controllerClassName, '\\') !== false
            ? substr($controllerClassName, strrpos($controllerClassName, '\\')+1)
            : substr($controllerClassName, strrpos($controllerClassName, '_')+1)
        ;
        return funcAcl_models_classes_AccessService::singleton()->makeEMAUri($extension, $shortName);
    }

    public static function getUriForAction($controllerClassName, $actionName) {
        $extension = self::getExtensionFromController($controllerClassName);
        $shortName = strpos($controllerClassName, '\\') !== false
            ? substr($controllerClassName, strrpos($controllerClassName, '\\')+1)
            : substr($controllerClassName, strrpos($controllerClassName, '_')+1)
        ;
        return funcAcl_models_classes_AccessService::singleton()->makeEMAUri($extension, $shortName, $actionName);
    }

    public static function getControllerFromUri($uri) {
        list($type, $extension, $controller) = explode('_', substr($uri, strpos($uri,'#')+1));
        return FuncHelper::getClassName($extension, $controller);
    }
    
    public static function getActionFromUri($uri) {
        list($type, $extension, $controller, $action) = explode('_', substr($uri, strpos($uri,'#')+1));
        return $action;
    }
    
    public static function getExtensionFromController($controllerClass) {
        if (strpos($controllerClass, '\\') === false) {
            $parts = explode('_', $controllerClass);
            if (count($parts) == 3) {
                return $parts[0];
            } else {
                throw new common_exception_Error('Unknown controller '.$controllerClass);
            }
        } else {
            foreach (common_ext_ExtensionsManager::singleton()->getEnabledExtensions() as $ext) {
                foreach ($ext->getManifest()->getRoutes() as $routePrefix => $namespace) {
                    if (is_string($namespace) && substr($controllerClass, 0, strlen($namespace)) == $namespace) {
                        return $ext->getId();
                    }
                }
            }
            throw new common_exception_Error('Unknown controller '.$controllerClass);
        }
    }
}