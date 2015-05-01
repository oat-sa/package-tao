<?php
/*  
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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

use oat\oatbox\user\LoginService;

/**
 * The UserService aims at providing an API to manage Users and Roles within Generis.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package generis
 
 */
class core_kernel_users_Service
        implements core_kernel_users_UsersManagement,
                   core_kernel_users_RolesManagement
{
    
    CONST LEGACY_ALGORITHM = 'md5';
    CONST LEGACY_SALT_LENGTH = 0;
    
    /**
     *
     * @access private
     * @var core_kernel_users_Service
     */
    private static $instance = null;


    /**
     * Returns the hashing algorithm defined in generis configuration
     *
     * @return helpers_PasswordHash
     */
    public static function getPasswordHash() {
        return new helpers_PasswordHash(
            defined('PASSWORD_HASH_ALGORITHM') ? PASSWORD_HASH_ALGORITHM : self::LEGACY_ALGORITHM,
            defined('PASSWORD_HASH_SALT_LENGTH') ? PASSWORD_HASH_SALT_LENGTH : self::LEGACY_SALT_LENGTH
        );
    }

    /**
     * Returns true if a user with login = $login is currently in the
     * persistent memory of Generis.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string login A string that is used as a Generis user login in the persistent memory.
     * @param  Class class A specific sub class of User where the login must be searched into.
     * @return boolean
     */
    public function loginExists($login,  core_kernel_classes_Class $class = null)
    {
        $returnValue = (bool) false;

    	if(is_null($class)){
        	$class = new core_kernel_classes_Class(CLASS_GENERIS_USER);
        }
        $users = $class->searchInstances(
        	array(PROPERTY_USER_LOGIN => $login), 
        	array('like' => false, 'recursive' => true)
        );
        
        if(count($users) > 0){
        	$returnValue = true;
        }

        return (bool) $returnValue;
    }

    /**
     * Create a new Generis User with a specific Role. If the $role is not
     * the user will be given the Generis Role.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string login A specific login for the User to create.
     * @param  string password A password for the User to create (raw).
     * @param  Resource role A Role to grant to the new User.
     * @return core_kernel_classes_Resource
     */
    public function addUser($login, $password,  core_kernel_classes_Resource $role = null, core_kernel_classes_Class $class = null)
    {
        $returnValue = null;

    	if($this->loginExists($login)){
        	throw new core_kernel_users_Exception("Login '${login}' already in use.", core_kernel_users_Exception::LOGIN_EXITS);
        }
        else{
        	$role = (empty($role)) ? new core_kernel_classes_Resource(INSTANCE_ROLE_GENERIS) : $role;
        	
        	$userClass = (!empty($class)) ? $class : new core_kernel_classes_Class(CLASS_GENERIS_USER);
        	$returnValue = $userClass->createInstanceWithProperties(array(
        	    RDFS_LABEL => "User ${login}",
        	    RDFS_COMMENT => 'User Created on ' . date(DATE_ISO8601),
        	    PROPERTY_USER_LOGIN => $login,
        	    PROPERTY_USER_PASSWORD => core_kernel_users_Service::getPasswordHash()->encrypt($password),
        	    PROPERTY_USER_ROLES => $role
        	));
        	
        	if (empty($returnValue)){
        		throw new core_kernel_users_Exception("Unable to create user with login = '${login}'.");
        	}
        }

        return $returnValue;
    }

    /**
     * Remove a Generis User from persistent memory. Bound roles will remain
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource user A reference to the User to be removed from the persistent memory of Generis.
     * @return boolean
     */
    public function removeUser( core_kernel_classes_Resource $user)
    {
        $returnValue = (bool) false;

        $returnValue = $user->delete();

        return (bool) $returnValue;
    }

    /**
     * Get a specific Generis User from the persistent memory of Generis that
     * a specific login. If multiple users have the same login, a UserException
     * be thrown.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string login A Generis User login.
     * @param  Class class A specific sub Class of User where in the User has to be searched in.
     * @return core_kernel_classes_Resource
     */
    public function getOneUser($login,  core_kernel_classes_Class $class = null)
    {
        $returnValue = null;

    	if(empty($class)){
        	$class = new core_kernel_classes_Class(CLASS_GENERIS_USER);
    	}
        
    	$users = $class->searchInstances(
    		array(PROPERTY_USER_LOGIN => $login), 
    		array('like' => false, 'recursive' => true)
    	);
    	
    	if (count($users) == 1){
    		$returnValue = current($users);	
    	}
    	else if (count($users) > 1){
    		$msg = "More than one user have the same login '${login}'.";
    	}

        return $returnValue;
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
        return !\common_session_SessionManager::isAnonymous();
    }

    /**
     * used in conjunction with the callback validator
     * to test the pasword entered
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string password used in conjunction with the callback validator to test the pasword entered
     * @param  Resource user used in conjunction with the callback validator to test the pasword entered
     * @return boolean
     */
    public function isPasswordValid($password,  core_kernel_classes_Resource $user)
    {
        $returnValue = (bool) false;

        if(!is_string($password)){
			throw new core_kernel_users_Exception('The password must be of "string" type, got '.gettype($password));
		}
		
		$hash = $user->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_PASSWORD));
		$returnValue = core_kernel_users_Service::getPasswordHash()->verify($password, $hash);

        return (bool) $returnValue;
    }

    /**
     * Set the password of a specifc user.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource user The user you want to set the password.
     * @param  string password The md5 hash of the password you want to set to the user.
     */
    public function setPassword( core_kernel_classes_Resource $user, $password)
    {
        if(!is_string($password)){
			throw new core_kernel_users_Exception('The password must be of "string" type, got '.gettype($password));
		}
		
		$user->editPropertyValues(new core_kernel_classes_Property(PROPERTY_USER_PASSWORD),core_kernel_users_Service::getPasswordHash()->encrypt($password));
    }

    /**
     * Get the roles that a given user has.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource user A Generis User.
     * @return array
     */
    public function getUserRoles( core_kernel_classes_Resource $user)
    {
        $returnValue = array();
        // We use a Depth First Search approach to flatten the Roles Graph.
        $rolesProperty = new core_kernel_classes_Property(PROPERTY_USER_ROLES);
        $rootRoles = $user->getPropertyValuesCollection($rolesProperty);
        
        foreach ($rootRoles->getIterator() as $r){
        	$returnValue[$r->getUri()] = $r;
        	$returnValue = array_merge($returnValue, $this->getIncludedRoles($r));
        }
        
        $returnValue = array_unique($returnValue);
        
        return (array) $returnValue;
    }

    /**
     * Indicates if a user is granted with a set of Roles.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource user The User instance you want to check Roles.
     * @param  roles Can be either a single Resource or an array of Resource depicting Role(s).
     * @return boolean
     */
    public function userHasRoles( core_kernel_classes_Resource $user, $roles)
    {
        $returnValue = (bool) false;

    	if (empty($roles)){
        	throw new InvalidArgumentException('The $roles parameter must not be empty.');	
        }
        
    	$roles = (is_array($roles)) ? $roles : array($roles);
    	$searchRoles = array();
    	foreach ($roles as $r){
    		$searchRoles[] = ($r instanceof core_kernel_classes_Resource) ? $r->getUri() : $r;
    	}
    	unset($roles);
    	
        if (common_session_SessionManager::getSession()->getUserUri() == $user->getUri()){
            foreach (common_session_SessionManager::getSession()->getUserRoles() as $role) {
                if (in_array($role, $searchRoles)) {
                    $returnValue = true;
                    break;
                }
            }
        } else {
    	    // After introducing remote users, we can no longer guarantee that any user and his roles are available
        	common_Logger::w('Roles of non current user ('.$user->getUri().') checked, trying fallback to local ontology');	
        	$userRoles = array_keys($this->getUserRoles($user));
        	$identicalRoles = array_intersect($searchRoles, $userRoles);
        	
        	$returnValue = (count($identicalRoles) === count($searchRoles));
        }
    	
        return (bool) $returnValue;
    }

    /**
     * Attach a Generis Role to a given Generis User. A UserException will be
     * if an error occurs. If the User already has the role, nothing happens.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource user The User you want to attach a Role.
     * @param  Resource role A Role to attach to a User.
     * @return void
     */
    public function attachRole( core_kernel_classes_Resource $user,  core_kernel_classes_Resource $role)
    {
    	try{
	        if (false === $this->userHasRoles($user, $role)){
	        	$rolesProperty = new core_kernel_classes_Property(PROPERTY_USER_ROLES);
	        	$user->setPropertyValue($rolesProperty, $role);	
	        }
        }
        catch (common_Exception $e){
        	$roleUri = $role->getUri;
        	$userUri = $user->getUri();
        	$msg = "An error occured while attaching role '${roleUri}' to user '${userUri}': " . $e->getMessage();
        	throw new core_kernel_users_Exception($msg);
        }
    }

    /**
     * Short description of method unnatachRole
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource user A Generis user from which you want to unnattach the Generis Role.
     * @param  Resource role The Generis Role you want to Unnatach from the Generis User.
     */
    public function unnatachRole( core_kernel_classes_Resource $user,  core_kernel_classes_Resource $role)
    {
    	try{
        	if (true === $this->userHasRoles($user, $role)){
        		$rolesProperty = new core_kernel_classes_Property(PROPERTY_USER_ROLES);
        		$options = array('like' => false, 'pattern' => $role->getUri());
        		$user->removePropertyValues($rolesProperty, $options);
        	}
        }
        catch (common_Exception $e){
        	$roleUri = $role->getUri();
        	$userUri = $user->getUri();
        	$msg = "An error occured while unnataching role '${roleUri}' from user '${userUri}': " . $e->getMessage();	
        }
    }

    /**
     * Add a role in Generis.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string label The label to apply to the newly created Generis Role.
     * @param  includedRoles The Role(s) to be included in the newly created Generis Role. Can be either a Resource or an array of Resources.
     * @return core_kernel_classes_Resource
     */
    public function addRole($label, $includedRoles = null, core_kernel_classes_Class $class = null)
    {
        $returnValue = null;

        $includedRoles = is_array($includedRoles) ? $includedRoles : array($includedRoles);
		$includedRoles = empty($includedRoles[0]) ? array() : $includedRoles;
		
		$classRole =  (empty($class)) ? new core_kernel_classes_Class(CLASS_ROLE) : $class;
		$includesRoleProperty = new core_kernel_classes_Property(PROPERTY_ROLE_INCLUDESROLE);
        $role = $classRole->createInstance($label, "${label} Role");
        
        foreach ($includedRoles as $ir){
        	$role->setPropertyValue($includesRoleProperty, $ir);	
        }
        
        $returnValue = $role;

        return $returnValue;
    }

    /**
     * Remove a Generis Role from the persistent memory. Any reference to the Role
     * will be removed.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource role The Role to remove.
     * @return boolean
     */
    public function removeRole( core_kernel_classes_Resource $role)
    {
        $returnValue = (bool) false;
        
    	if (GENERIS_CACHE_USERS_ROLES == true && core_kernel_users_Cache::areIncludedRolesInCache($role)){	
    		
    		if ($role->delete(true) == true){ // delete references.
        		$returnValue = core_kernel_users_Cache::removeIncludedRoles($role);
        		
        		// We also need to remove all included roles cache that contain
        		// the role we just deleted.
        		foreach ($this->getAllRoles() as $r){
        			if (array_key_exists($role->getUri(), $this->getIncludedRoles($r))){
        				core_kernel_users_Cache::removeIncludedRoles($r);
        			}
        		}
        	}
        	else{
        		$roleUri = $role->getUri();
        		$msg = "An error occured while removing role '${roleUri}'. It could not be deleted from the cache.";
        		throw new core_kernel_users_Exception($msg);
        	}
        }
        else{
        	$returnValue = $role->delete(true);	// delete references to this role!
        }

        return (bool) $returnValue;
    }

    /**
     * Get an array of the Roles included by a Generis Role.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource role A Generis Role.
     * @return array An associative array where keys are Role URIs and values are instances of core_kernel_classes_Resource.
     */
    public function getIncludedRoles( core_kernel_classes_Resource $role)
    {
        $returnValue = array();

    	if (GENERIS_CACHE_USERS_ROLES == true && core_kernel_users_Cache::areIncludedRolesInCache($role) == true){
    		$returnValue = core_kernel_users_Cache::retrieveIncludedRoles($role);
        }
        else{
	        // We use a Depth First Search approach to flatten the Roles Graph.
	        $includesRoleProperty = new core_kernel_classes_Property(PROPERTY_ROLE_INCLUDESROLE);
	        $visitedRoles = array();
	        $s = array(); // vertex stack.
	        array_push($s, $role); // begin with $role as the first vertex.
	        
	        while (!empty($s)){
	        	$u = array_pop($s);
	
	        	if (false === in_array($u->getUri(), $visitedRoles)){
	        		$visitedRoles[] = $u->getUri();
	        		$returnValue[$u->getUri()] = $u;
	        		
	        		$ar = $u->getPropertyValuesCollection($includesRoleProperty);
	        		foreach ($ar->getIterator() as $w){
	        			if (false === in_array($w->getUri(), $visitedRoles)){ // not visited
	        				array_push($s, $w);
	        			}
	        		}
	        	}
	        }
	        
	        // remove the root vertex which is actually the role we are testing.
	        unset($returnValue[$role->getUri()]);
	        
	        if (GENERIS_CACHE_USERS_ROLES == true){
	        	try{
					core_kernel_users_Cache::cacheIncludedRoles($role, $returnValue);
	        	}
	        	catch(core_kernel_users_CacheException $e){
	        		$roleUri = $role->getUri();
	        		$msg = "Unable to retrieve included roles from cache memory for role '${roleUri}': ";
	        		$msg.= $e->getMessage();
	        		throw new core_kernel_users_Exception($msg);	
	        	}
	        }
        }

        return (array) $returnValue;
    }

    /**
     * Returns an array of Roles (as Resources) where keys are their URIs. The
     * roles represent which kind of Roles are accepted to be identified against
     * system.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return array
     */
    public function getAllowedRoles()
    {
        $returnValue = array();

        $role = new core_kernel_classes_Resource(INSTANCE_ROLE_GENERIS);
        $returnValue = array($role->getUri() => $role);

        return (array) $returnValue;
    }

    /**
     * Returns a Role (as a Resource) which represents the default role of the
     * If a user has to be created but no Role is given to him, it will receive
     * role.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return core_kernel_classes_Resource
     */
    public function getDefaultRole()
    {
        $returnValue = null;

        $returnValue = new core_kernel_classes_Resource(INSTANCE_ROLE_GENERIS);

        return $returnValue;
    }

    /**
     * Make a Role include another Role.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  core_kernel_classes_Resource role The role that needs to include another role.
     * @param  core_kernel_classes_Resource Resource roleToInclude The role to be included.
     */
    public function includeRole( core_kernel_classes_Resource $role,  core_kernel_classes_Resource $roleToInclude)
    {
        $includesRoleProperty = new core_kernel_classes_Property(PROPERTY_ROLE_INCLUDESROLE);
        
        // Clean to avoid double entries...
        $role->removePropertyValues($includesRoleProperty, array('like' => false, 'pattern' => $roleToInclude->getUri()));
        
        // Include the Role.
        $role->setPropertyValue($includesRoleProperty, $roleToInclude->getUri());
        
        // Reset cache.
        core_kernel_users_Cache::removeIncludedRoles($role);
    }
    
    /**
     * Uninclude a Role from antother Role.
     * 
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param core_kernel_classes_Resource role The Role from which you want to uninclude a Role.
     * @param core_kernel_classes_Resource roleToUninclude The Role to uninclude. 
     */
    public function unincludeRole(core_kernel_classes_Resource $role, core_kernel_classes_Resource $roleToUninclude)
    {
    	$includesRoleProperty = new core_kernel_classes_Property(PROPERTY_ROLE_INCLUDESROLE);
    	$role->removePropertyValues($includesRoleProperty, array('like' => false, 'pattern' => $roleToUninclude->getUri()));

    	// invalidate cache for the role.
    	if (GENERIS_CACHE_USERS_ROLES == true){
    		core_kernel_users_Cache::removeIncludedRoles($role);
    		
    		// For each roles that have $role for included role,
    		// remove the cache entry.
    		foreach ($this->getAllRoles() as $r){
    			$includedRoles = $this->getIncludedRoles($r);
    			
    			if (array_key_exists($role->getUri(), $includedRoles)){
    				core_kernel_users_Cache::removeIncludedRoles($r);
    			}
    		}
    	}
    	
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
    public function login($login, $password, $allowedRoles)
    {
        return LoginService::login($login, $password);
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
     * Get a unique instance of the UserService.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return core_kernel_users_Service
     */
    public static function singleton()
    {
        $returnValue = null;

        if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c();
		}
		$returnValue = self::$instance;

        return $returnValue;
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
     * Returns the whole collection of Roles in Generis.
     * 
     * @return array An associative array where keys are Role URIs and values are instances of the core_kernel_classes_Resource PHP class.
     */
    public function getAllRoles()
    {
    	$roleClass = new core_kernel_classes_Class(CLASS_ROLE);
    	return $roleClass->getInstances(true);
    }
}
