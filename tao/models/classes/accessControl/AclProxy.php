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

namespace oat\tao\model\accessControl;

use oat\tao\model\accessControl\func\AclProxy as FuncProxy;
use oat\tao\model\accessControl\data\DataAccessControl;
use oat\oatbox\user\User;

/**
 * Proxy for the Acl Implementation
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 
 */
class AclProxy
{
    /**
     * @var array
     */
    private static $implementations;

    /**
     * get the current access control implementations
     * 
     * @return array
     */
    protected static function getImplementations() {
        if (is_null(self::$implementations)) {
            self::$implementations = array(
                new FuncProxy(),
                new DataAccessControl()
            );
            
            /*
            $taoExt = \common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
            self::$implementations = array();
            foreach ($taoExt->getConfig('accessControl') as $acClass) {
                if (class_exists($acClass) && in_array('oat\tao\model\accessControl\AccessControl', class_implements($acClass))) {
                    self::$implementations[] = new $acClass();
                } else {
                    throw new \common_exception_Error('Unsupported class '.$acClass);
                }
            }
            */
        }
        return self::$implementations;
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
    public static function hasAccess(User $user, $controller, $action, $parameters) {
        $access = true;
        foreach (self::getImplementations() as $impl) {
            $access = $access && $impl->hasAccess($user, $controller, $action, $parameters);
        }
        return $access;
    }
}
