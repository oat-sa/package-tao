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

use oat\tao\model\accessControl\func\FuncAccessControl;
use oat\tao\model\accessControl\func\AccessRule;
use oat\oatbox\user\User;

/**
 * Proxy for the Acl Implementation
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 */
class funcAcl_models_classes_FuncAcl
    implements FuncAccessControl
{
    /**
     * Ensrue constants are loaded
     */
    public function __construct() {
        common_ext_ExtensionsManager::singleton()->getExtensionById('funcAcl');
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\accessControl\func\FuncAccessControl::accessPossible()
     */
    public function accessPossible(User $user, $controller, $action) {
        $userRoles = $user->getRoles();
        try {
            $controllerAccess = funcAcl_helpers_Cache::getControllerAccess($controller);
            $allowedRoles = isset($controllerAccess['actions'][$action])
                ? array_merge($controllerAccess['module'], $controllerAccess['actions'][$action])
                : $controllerAccess['module'];
            
            $accessAllowed = count(array_intersect($userRoles, $allowedRoles)) > 0;
            if (!$accessAllowed) {
                common_Logger::i('Access denied to '.$controller.'@'.$action.' for user \''.$user->getIdentifier().'\'');
            }
        } catch (ReflectionException $e) {
            common_Logger::i('Unknown controller '.$controller);
            $accessAllowed = false;
        }
        
        return (bool) $accessAllowed;
    }
    
    /**
     * Compatibility class for old implementation
     * 
     * @param string $extension
     * @param string $controller
     * @param string $action
     * @param array $parameters
     * @return boolean
     * @deprecated
     */
    public function hasAccess($action, $controller, $extension, $parameters = array()) {
        $user = common_session_SessionManager::getSession()->getUser();
        $uri = funcAcl_models_classes_ModuleAccessService::singleton()->makeEMAUri($extension, $controller);
        $controllerClassName = funcAcl_helpers_Map::getControllerFromUri($uri);
        return self::accessPossible($user, $controllerClassName, $action);
    }
    
    public function applyRule(AccessRule $rule) {
        $filter = $rule->getMask();
        if ($rule->isGrant()) {
            $accessService = funcAcl_models_classes_AccessService::singleton();
            if (isset($filter['act']) && isset($filter['mod']) && isset($filter['ext'])) {
                $accessService->grantActionAccess($rule->getRole(), $filter['ext'], $filter['mod'], $filter['act']);
            } elseif (isset($filter['mod']) && isset($filter['ext'])) {
                $accessService->grantModuleAccess($rule->getRole(), $filter['ext'], $filter['mod']);
            } elseif (isset($filter['ext'])) {
                $accessService->grantExtensionAccess($rule->getRole(), $filter['ext']);
            } elseif (isset($filter['controller'])) {
                $extension = funcAcl_helpers_Map::getExtensionFromController($filter['controller']);
                $shortName = strpos($filter['controller'], '\\') !== false
                    ? substr($filter['controller'], strrpos($filter['controller'], '\\')+1)
                    : substr($filter['controller'], strrpos($filter['controller'], '_')+1)
                ;
                $accessService->grantModuleAccess($rule->getRole(), $extension, $shortName);
            } elseif (isset($filter['act']) && strpos($filter['act'], '@') !== false) {
                list($controller, $action) = explode('@', $filter['act'], 2);
                $extension = funcAcl_helpers_Map::getExtensionFromController($controller);
                $shortName = strpos($controller, '\\') !== false
                    ? substr($controller, strrpos($controller, '\\')+1)
                    : substr($controller, strrpos($controller, '_')+1)
                ;
                $accessService->grantActionAccess($rule->getRole(), $extension, $shortName, $action);
            } else {
                common_Logger::w('Uninterpretable filter in '.__CLASS__);
            }
        } else {
            common_Logger::w('Only grant rules accepted in '.__CLASS__);
        }
    }
    
    public function revokeRule(AccessRule $rule) {
        if ($rule->isGrant()) {
            $accessService = funcAcl_models_classes_AccessService::singleton();
            $filter = $rule->getMask();
            if (isset($filter['act']) && isset($filter['mod']) && isset($filter['ext'])) {
                $accessService->revokeActionAccess($rule->getRole(), $filter['ext'], $filter['mod'], $filter['act']);
            } elseif (isset($filter['mod']) && isset($filter['ext'])) {
                $accessService->revokeModuleAccess($rule->getRole(), $filter['ext'], $filter['mod']);
            } elseif (isset($filter['ext'])) {
                $accessService->revokeExtensionAccess($rule->getRole(), $filter['ext']);
            } elseif (isset($filter['controller'])) {
                $extension = funcAcl_helpers_Map::getExtensionFromController($filter['controller']);
                $shortName = strpos($filter['controller'], '\\') !== false
                    ? substr($filter['controller'], strrpos($filter['controller'], '\\')+1)
                    : substr($filter['controller'], strrpos($filter['controller'], '_')+1)
                ;
                $accessService->revokeModuleAccess($rule->getRole(), $extension, $shortName);
            } elseif (isset($filter['act']) && strpos($filter['act'], '@') !== false) {
                list($controller, $action) = explode('@', $mask['act'], 2);
                $extension = funcAcl_helpers_Map::getExtensionFromController($controller);
                $shortName = strpos($controller, '\\') !== false
                    ? substr($controller, strrpos($controller, '\\')+1)
                    : substr($controller, strrpos($controller, '_')+1)
                ;
                $accessService->revokeActionAccess($rule->getRole(), $extension, $shortName, $action);
            } else {
                common_Logger::w('Uninterpretable filter in '.__CLASS__);
            }
        } else {
            common_Logger::w('Only grant rules accepted in '.__CLASS__);
        }
    }
    
    private function findExtensionId($controllerClass) {
        if (strpos($controllerClass, '\\') === false) {
            $parts = explode('_', $controllerClass);
            if (count($parts) == 3) {
                return $parts[0];
            } else {
                throw common_exception_Error('Unknown controller '.$controllerClass);
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