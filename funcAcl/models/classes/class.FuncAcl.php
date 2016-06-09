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
use oat\oatbox\service\ConfigurableService;

/**
 * Proxy for the Acl Implementation
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 */
class funcAcl_models_classes_FuncAcl extends ConfigurableService
    implements FuncAccessControl
{
    
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
        if ($rule->isGrant()) {
            $accessService = funcAcl_models_classes_AccessService::singleton();
            $elements = $this->evalFilterMask($rule->getMask());
            
            switch (count($elements)) {
                case 1 :
                    $extension = reset($elements);
                    $accessService->grantExtensionAccess($rule->getRole(), $extension);
                    break;
                case 2 :
                    list($extension, $shortName) = $elements;
                    $accessService->grantModuleAccess($rule->getRole(), $extension, $shortName);
                    break;
                case 3 :
                    list($extension, $shortName, $action) = $elements;
                    $accessService->grantActionAccess($rule->getRole(), $extension, $shortName, $action);
                    break;
                default :
                    // fail silently warning should already be send
            }
            
        } else {
            common_Logger::w('Only grant rules accepted in '.__CLASS__);
        }
    }
    
    public function revokeRule(AccessRule $rule) {
        if ($rule->isGrant()) {
            $accessService = funcAcl_models_classes_AccessService::singleton();
            $elements = $this->evalFilterMask($rule->getMask());
            
            switch (count($elements)) {
                case 1 :
                    $extension = reset($elements);
                    $accessService->revokeExtensionAccess($rule->getRole(), $extension);
                    break;
                case 2 :
                    list($extension, $shortName) = $elements;
                    $accessService->revokeModuleAccess($rule->getRole(), $extension, $shortName);
                    break;
                case 3 :
                    list($extension, $shortName, $action) = $elements;
                    $accessService->revokeActionAccess($rule->getRole(), $extension, $shortName, $action);
                    break;
                default :
                    // fail silently warning should already be send
            }
        } else {
            common_Logger::w('Only grant rules accepted in '.__CLASS__);
        }
    }
    
    /**
     * Evaluate the mask to ACL components
     * 
     * @param mixed $mask
     * @return string[] tao ACL components
     */
    public function evalFilterMask($mask) {
        // string masks
        if (is_string($mask)) {
            if (strpos($mask, '@') !== false) {
                list($controller, $action) = explode('@', $mask, 2);
            } else {
                $controller = $mask;
                $action = null;
            }
            if (class_exists($controller)) {
                $extension = funcAcl_helpers_Map::getExtensionFromController($controller);
                $shortName = strpos($controller, '\\') !== false
                    ? substr($controller, strrpos($controller, '\\')+1)
                    : substr($controller, strrpos($controller, '_')+1);
        
                if (is_null($action)) {
                    // grant controller
                    return array($extension, $shortName);
                } else {
                    // grant action
                    return array($extension, $shortName, $action);
                }
            } else {
                common_Logger::w('Unknown controller '.$controller);
            }
        
            /// array masks
        } elseif (is_array($mask)) {
            if (isset($mask['act']) && isset($mask['mod']) && isset($mask['ext'])) {
                return array($mask['ext'], $mask['mod'], $mask['act']);
            } elseif (isset($mask['mod']) && isset($mask['ext'])) {
                return array($mask['ext'], $mask['mod']);
            } elseif (isset($mask['ext'])) {
                return array($mask['ext']);
            } elseif (isset($mask['controller'])) {
                $extension = funcAcl_helpers_Map::getExtensionFromController($mask['controller']);
                $shortName = strpos($mask['controller'], '\\') !== false
                    ? substr($mask['controller'], strrpos($mask['controller'], '\\')+1)
                    : substr($mask['controller'], strrpos($mask['controller'], '_')+1)
                    ;
                return array($extension, $shortName);
            } elseif (isset($mask['act']) && strpos($mask['act'], '@') !== false) {
                list($controller, $action) = explode('@', $mask['act'], 2);
                $extension = funcAcl_helpers_Map::getExtensionFromController($controller);
                $shortName = strpos($controller, '\\') !== false
                    ? substr($controller, strrpos($controller, '\\')+1)
                    : substr($controller, strrpos($controller, '_')+1)
                    ;
                return array($extension, $shortName, $action);
            } else {
                common_Logger::w('Uninterpretable filter in '.__CLASS__);
            }
        } else {
            common_Logger::w('Uninterpretable filtertype '.gettype($mask));
        }
        return array();
    }
    
}