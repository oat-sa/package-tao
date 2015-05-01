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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
namespace oat\oatbox\user;

use oat\oatbox\user\auth\AuthFactory;
use common_user_auth_AuthFailedException;
use common_user_User;
use common_session_DefaultSession;
use common_session_SessionManager;

/**
 * Login service
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package generis
 */
class LoginService
{
    /**
     * Login a user using login, password
     * 
     * @param string $userLogin
     * @param string $userPassword
     * @return boolean
     */
    public static function login($userLogin, $userPassword)
    {
        try {
            $user = self::authenticate($userLogin, $userPassword);
            $loggedIn = self::startSession($user);
        } catch (common_user_auth_AuthFailedException $e) {
            $loggedIn = false;
        }
        return $loggedIn;
    }
    
    /**
     * 
     * @param string $userLogin
     * @param string $userPassword
     * @throws LoginFailedException
     * @return common_user_User
     */
    public static function authenticate($userLogin, $userPassword)
    {
        $user = null;
        
        $adapters = AuthFactory::createAdapters();
        $exceptions = array();
        while (!empty($adapters) && is_null($user)) {
            $adapter = array_shift($adapters);
            $adapter->setCredentials($userLogin, $userPassword);
            try {
                $user = $adapter->authenticate();
            } catch (common_user_auth_AuthFailedException $exception) {
                // try next adapter or login failed
                $exceptions[] = $exception;
            }
        }
        if (!is_null($user)) {
            return $user;
        } else {
            throw new LoginFailedException($exceptions);
        }
    }
    
    
    /**
     * Start a session for a provided user
     * 
     * @param common_user_User $user
     * @return boolean
     */
    public static function startSession(common_user_User $user)
    {
        $session = new common_session_DefaultSession($user);
        common_session_SessionManager::startSession($session);
        return true;
    }
}