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

namespace oat\tao\model\accessControl\func\implementation;

use oat\tao\model\accessControl\func\FuncAccessControl;
use oat\tao\model\accessControl\func\AccessRule;
use common_ext_ExtensionsManager;
use common_session_SessionManager;
use oat\taoDevTools\actions\ControllerMap;
use oat\tao\model\accessControl\func\FuncHelper;
use oat\tao\helpers\ControllerHelper;
use oat\oatbox\user\User;
use Zend\Ldap\Filter\MaskFilter;

/**
 * Simple ACL Implementation deciding whenever or not to allow access
 * strictly by the BASEUSER role and a whitelist
 * 
 * Not to be used in production, since testtakers cann access the backoffice
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 
 */
class SimpleAccess
    implements FuncAccessControl
{
    
    const WHITELIST_KEY = 'SimpleAclWhitelist';
    
    private $controllers = array();
    
    /**
     * 
     */
    public function __construct() {
        $data = common_ext_ExtensionsManager::singleton()->getExtensionById('tao')->getConfig(self::WHITELIST_KEY);
        if (is_array($data)) {
            $this->controllers = $data;
        }
    }

    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\accessControl\func\FuncAccessControl::accessPossible()
     */
    public function accessPossible(User $user, $controller, $action) {
        $isUser = false;
        foreach ($user->getRoles() as $role) {
            if ($role == INSTANCE_ROLE_BASEUSER) {
                $isUser = true;
                break;
            }
        }
        return $isUser || $this->inWhiteList($controller, $action);
    }
    
    public function applyRule(AccessRule $rule) {
        if ($rule->getRole()->getUri() == INSTANCE_ROLE_ANONYMOUS) {
            $mask = $rule->getMask();
            
            if (isset($mask['ext']) && !isset($mask['mod'])) {
                $this->whiteListExtension($mask['ext']);
            } elseif (isset($mask['ext']) && isset($mask['mod']) && !isset($mask['act'])) {
                $this->whiteListController(FuncHelper::getClassName($mask['ext'], $mask['mod']));
            } elseif (isset($mask['ext']) && isset($mask['mod']) && isset($mask['act'])) {
                $this->whiteListAction(FuncHelper::getClassName($mask['ext'], $mask['mod']), $mask['act']);
            } elseif (isset($mask['controller'])) {
                $this->whiteListController($mask['controller']);
            } elseif (isset($mask['act']) && strpos($mask['act'], '@') !== false) {
                list($controller, $action) = explode('@', $mask['act'], 2);
                $this->whiteListAction($controller, $action);
            } else {
                \common_Logger::w('Unregoginised mask keys: '.implode(',', array_keys($mask)));
            }
        }
    }
    
    public function revokeRule(AccessRule $rule) {
        if ($rule->getRole()->getUri() == INSTANCE_ROLE_ANONYMOUS) {
            $mask = $rule->getMask();
            $ruleString = $mask['ext'].'::'.(isset($mask['mod']) ? $mask['mod'] : '*').'::'.(isset($mask['act']) ? $mask['act'] : '*');
            $remaining = array_diff(explode(',', $this->whitelist), array($ruleString));
            $this->whitelist = implode(',', $remaining);
            $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
            common_ext_ExtensionsManager::singleton()->getExtensionById('tao')->setConfig(self::WHITELIST_KEY, $this->whitelist);
        }
    }
    
    private function inWhiteList($controllerName, $action) {
        return isset($this->controllers[$controllerName])
            ? is_array($this->controllers[$controllerName])
                ? isset($this->controllers[$controllerName][$action])
                : true
            : false;
        return false;
    }
    
    private function whiteListExtension($extensionId) {
        foreach (ControllerHelper::getControllers($extensionId) as $controllerClassName) {
            $this->whiteListController($controllerClassName);
        }
        
    }
    
    private function whiteListController($controller) {
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
        // reread controllers to reduce collision risk
        $this->controllers = $ext->hasConfig(self::WHITELIST_KEY) ? $ext->getConfig(self::WHITELIST_KEY) : array();
        $this->controllers[$controller] = '*';
        $ext->setConfig(self::WHITELIST_KEY, $this->controllers);
    }
    
    private function whiteListAction($controller, $action) {
        $ext = common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
        // reread controllers to reduce collision risk
        $this->controllers = $ext->hasConfig(self::WHITELIST_KEY) ? $ext->getConfig(self::WHITELIST_KEY) : array();
        if (!isset($this->controllers[$controller]) || is_array($this->controllers[$controller])) {
            $this->controllers[$controller][$action] = '*';
        }
        $ext->setConfig(self::WHITELIST_KEY, $this->controllers);
        
    }
    
}