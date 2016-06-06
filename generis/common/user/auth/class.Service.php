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
 * The UserService aims at providing an API to manage Users and Roles within Generis.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package generis
 
 */
class common_user_auth_Service
{

    /**
     *
     * @access private
     * @var common_user_auth_Service
     */
    private static $instance = null;

    /**
     * Get a unique instance of the UserService.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return common_user_auth_Service
     */
    public static function singleton()
    {
    	if (!isset(self::$instance)) {
    		self::$instance = new static();
    	}
    	return self::$instance;
    }
    
    /**
     * The constructor is private to implement the Singleton Design Pattern.
     *
     * @access private
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     */
    private function __construct()
    {
		// Only to restrict instances of this class to a single instance.
    }

    /**
     * Log in a user into Generis that has one of the provided $allowedRoles.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string login The login of the user.
     * @param  string password the md5 hash of the password.
     * @param  allowedRoles A Role or an array of Roles that are allowed to be logged in. If the user has a Role that matches one or more Roles in this array, the login request will be accepted.
     * @return boolean
     */
    public function login(common_user_auth_Adapter $adapter, $allowedRoles = array())
    {
        $returnValue = (bool) false;

		try {
			$user = $adapter->authenticate();
			if (!empty($allowedRoles)) {
		        // Role can be either a scalar value or a collection.
				$allowedRoles = is_array($allowedRoles) ? $allowedRoles : array($allowedRoles);
				$roles = array();
				foreach ($allowedRoles as $r){
					$roles[] = (($r instanceof core_kernel_classes_Resource) ? $r->getUri() : $r);
				}
				unset($allowedRoles);
				$intersect = array_intersect($roles, $user->getRoles());
				if (empty($intersect)) {
				    common_Logger::w('User '.$user->getIdentifier().' does not have the nescessary role');
					return false;
				}
			}
			$returnValue = $this->startSession($user);
		} catch (common_user_auth_AuthFailedException $exception) {
			// failed return false;
			
		}

        return (bool) $returnValue;
    }

    /**
     * Indicates if an Authenticated Session is open.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return boolean
     */
    public function isASessionOpened()
    {
        return !common_session_SessionManager::isAnonymous();
    }
    
    /**
     * Logout the current user. The session will be entirely reset.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return boolean
     */
    public function logout()
    {
        return \common_session_SessionManager::endSession();
    }
    
    /**
     * Short description of method startSession
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource user
     * @return boolean
     */
    public function startSession(common_user_User $user)
    {
        $session = new common_session_DefaultSession($user);
        return \common_session_SessionManager::startSession($session);
    }

}