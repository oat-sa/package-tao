<?php
use oat\oatbox\user\LoginService;
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
 *               2013-2014 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

/**
 * This class provide service on user management
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 
 */
class tao_models_classes_UserService
    extends tao_models_classes_ClassService
    implements core_kernel_users_UsersManagement
{

    /**
     * the core user service
     *
     * @access protected
     * @var core_kernel_users_Service
     */
    protected $generisUserService = null;


    /**
     * constructor
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    protected function __construct()
    {
		$this->generisUserService = core_kernel_users_Service::singleton();
    }

    /**
     * authenticate a user
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string login
     * @param  string password
     * @return boolean
     * @deprecated
     */
    public function loginUser($login, $password)
    {
        
        $returnValue = (bool) false;
        try{
            $returnValue = LoginService::login($login, $password);
        }
        catch(core_kernel_users_Exception $ue){
        	common_Logger::e("A fatal error occured at user login time: " . $ue->getMessage());
        }
        return (bool) $returnValue;
    }

    /**
     * retrieve the logged in user
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return core_kernel_classes_Resource
     */
    public function getCurrentUser()
    {
        $returnValue = null;

    	if(!common_session_SessionManager::isAnonymous()){
        	$userUri = \common_session_SessionManager::getSession()->getUser()->getIdentifier();
			if(!empty($userUri)){
        		$returnValue = new core_kernel_classes_Resource($userUri);
			} else {
				common_Logger::d('no userUri');
			}
    	}

        return $returnValue;
    }

    /**
     * Check if the login is already used
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param string login
     * @param 
     * @return boolean
     */
    public function loginExists($login, core_kernel_classes_Class $class = null)
    {
        $returnValue = (bool) false;

        $returnValue = $this->generisUserService->loginExists($login, $class);

        return (bool) $returnValue;
    }

    /**
     * Check if the login is available (because it's unique)
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string login
     * @return boolean
     */
    public function loginAvailable($login)
    {
        $returnValue = (bool) false;

		if(!empty($login)){
			$returnValue = !$this->loginExists($login);
		}

        return (bool) $returnValue;
    }

    /**
     * Get a user that has a given login.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param string login the user login is the unique identifier to retrieve him.
     * @param core_kernel_classes_Class A specific class to search the user.
     * @return core_kernel_classes_Resource
     */
    public function getOneUser($login, core_kernel_classes_Class $class = null)
    {
        $returnValue = null;

		if (empty($login)){
			throw new common_exception_InvalidArgumentType('Missing login for '.__FUNCTION__);
		}
			
		$class = (!empty($class)) ? $class : $this->getRootClass();
		
		$user = $this->generisUserService->getOneUser($login, $class);
		
		if (!empty($user)){
			
			$userRolesProperty = new core_kernel_classes_Property(PROPERTY_USER_ROLES);
			$userRoles = $user->getPropertyValuesCollection($userRolesProperty);
			$allowedRoles = $this->getAllowedRoles();
			
			if($this->generisUserService->userHasRoles($user, $allowedRoles)){
				$returnValue = $user;
			} else {
			    common_Logger::i('User found for login \''.$login.'\' but does not have matchign roles');
			}
		} else {
			common_Logger::i('No user found for login \''.$login.'\'');
		}

        return $returnValue;
    }

    /**
     * Get the list of users by role(s)
     * options are: order, orderDir, start, end, search
     * with search consisting of: field, op, string
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array roles
     * @param  array options the user list options to order the list and paginate the results
     * @return array
     */
    public function getUsersByRoles($roles, $options = array())
    {
        $returnValue = array();

        //the users we want are instances of the role
		$fields = array('login' => PROPERTY_USER_LOGIN,
						'password' => PROPERTY_USER_PASSWORD,
						'uilg' => PROPERTY_USER_UILG,
						'deflg' => PROPERTY_USER_DEFLG,
						'mail' => PROPERTY_USER_MAIL,
		    			'email' => PROPERTY_USER_MAIL,
						'role' => RDF_TYPE,
						'roles' => PROPERTY_USER_ROLES,
						'firstname' => PROPERTY_USER_FIRSTNAME,
						'lastname' => PROPERTY_USER_LASTNAME,
						'name' => PROPERTY_USER_FIRSTNAME);
		
		$ops = array('eq' => "%s",
					 'bw' => "%s*",
					 'ew' => "*%s",
					 'cn' => "*%s*");
		
		$opts = array('recursive' => true, 'like' => false);
		if (isset($options['start'])) {
			$opts['offset'] = $options['start'];
		}
		if (isset($options['limit'])) {
			$opts['limit'] = $options['limit'];
		}
		
		$crits = array(PROPERTY_USER_LOGIN => '*');
		if (isset($options['search']) && !is_null($options['search']) && isset($options['search']['string']) && isset($ops[$options['search']['op']])) {
			$crits[$fields[$options['search']['field']]] = sprintf($ops[$options['search']['op']], $options['search']['string']);
		}
		// restrict roles
		$crits[PROPERTY_USER_ROLES] = $roles;
		
		if (isset($options['order'])) {
			$opts['order'] = $fields[$options['order']]; 
			if (isset($options['orderDir'])) {
				$opts['orderdir'] = $options['orderDir']; 
			}
		}
		
		$userClass = new core_kernel_classes_Class(CLASS_GENERIS_USER);
		
		$returnValue = $userClass->searchInstances($crits, $opts);

        return (array) $returnValue;
    }

    /**
     * Remove a user
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource user
     * @return boolean
     */
    public function removeUser( core_kernel_classes_Resource $user)
    {
        $returnValue = (bool) false;

        if(!is_null($user)){
			$returnValue = $this->generisUserService->removeUser($user);
		}

        return (bool) $returnValue;
    }

    /**
     * returns a list of all concrete roles(instances of CLASS_ROLE)
     * which are allowed to login
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getAllowedRoles()
    {
        $returnValue = array();

        $returnValue = array(INSTANCE_ROLE_BACKOFFICE => new core_kernel_classes_Resource(INSTANCE_ROLE_BACKOFFICE));

        return (array) $returnValue;
    }
    
    public function getDefaultRole()
    {
    	return new core_kernel_classes_Resource(INSTANCE_ROLE_BACKOFFICE);
    }

    /**
     * Short description of method logout
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return boolean
     */
    public function logout()
    {
        $returnValue = (bool) false;

        $returnValue = $this->generisUserService->logout();

        return (bool) $returnValue;
    }

    /**
     * Short description of method getAllUsers
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array options
     * @return array
     */
    public function getAllUsers($options = array())
    {
        $returnValue = array();

        $userClass = new core_kernel_classes_Class(CLASS_TAO_USER);
		$options = array_merge(array('recursive' => true, 'like' => true), $options);
		$filters = array(PROPERTY_USER_LOGIN => '*');
		$returnValue = $userClass->searchInstances($filters, $options);

        return (array) $returnValue;
    }

    /**
     * returns the nr of users fullfilling the criterias,
     * uses the same syntax as getUsersByRole
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array roles
     * @param  array options
     * @return int
     */
    public function getUserCount($roles, $options = array())
    {
        $returnValue = (int) 0;

        $opts = array(
        	'recursive' => true,
        	'like' => false
        );

		$crits = array(PROPERTY_USER_LOGIN => '*');
		if (isset($options['search']['string']) && isset($options['search']['op'])
			&& !empty($options['search']['string']) && !empty($options['search']['op'])) {
			$fields = array('login' => PROPERTY_USER_LOGIN,
						'password' => PROPERTY_USER_PASSWORD,
						'uilg' => PROPERTY_USER_UILG,
						'deflg' => PROPERTY_USER_DEFLG,
						'mail' => PROPERTY_USER_MAIL,
		    			'email' => PROPERTY_USER_MAIL,
						'role' => RDF_TYPE,
						'roles' => PROPERTY_USER_ROLES,
						'firstname' => PROPERTY_USER_FIRSTNAME,
						'lastname' => PROPERTY_USER_LASTNAME,
						'name' => PROPERTY_USER_FIRSTNAME);
			$ops = array('eq' => "%s",
					 'bw' => "%s*",
					 'ew' => "*%s",
					 'cn' => "*%s*");
			$crits[$fields[$options['search']['field']]] = sprintf($ops[$options['search']['op']], $options['search']['string']);
		}
		
		$crits[PROPERTY_USER_ROLES] = $roles;
		
		$userClass = new core_kernel_classes_Class(CLASS_GENERIS_USER);
		$returnValue = $userClass->countInstances($crits, $opts);

        return (int) $returnValue;
    }

    /**
     * Short description of method toTree
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Class clazz
     * @param  array options
     * @return array
     */
    public function toTree( core_kernel_classes_Class $clazz, $options)
    {
        $returnValue = array();

    	$users = $this->getAllUsers(array('order' => PROPERTY_USER_LOGIN));
		foreach($users as $user){
			$login = (string) $user->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_LOGIN));
			$returnValue[] = array(
					'data' 	=> tao_helpers_Display::textCutter($login, 16),
					'attributes' => array(
						'id' => tao_helpers_Uri::encode($user->getUri()),
						'class' => 'node-instance'
					)
				);

		}

        return (array) $returnValue;
    }
    
    /**
     * Add a new user.
     * 
     * @param string login The login to give the user.
     * @param string password the password in clear.
     * @param core_kernel_classes_Resource role A role to grant to the user.
     * @param core_kernel_classes_Class A specific class to use to instantiate the new user. If not specified, the class returned by the getUserClass method is used.
     * @return core_kernel_classes_Resource the new user
     * @throws core_kernel_users_Exception If an error occurs.
     */
    public function addUser($login, $password, core_kernel_classes_Resource $role = null, core_kernel_classes_Class $class = null){
		
    	if (empty($class)){
    		$class = $this->getRootClass();
    	}
    	
        $user = $this->generisUserService->addUser($login, $password, $role, $class);
        
        //set up default properties
        if(!is_null($user)){
            $user->setPropertyValue(new core_kernel_classes_Property(PROPERTY_USER_FIRSTTIME), GENERIS_TRUE);
        }
    	
        return $user;
    }
	
	/**
	 * Indicates if a user session is currently opened or not.
	 * 
	 * @return boolean True if a session is opened, false otherwise.
	 */
	public function isASessionOpened() {
	    return common_user_auth_Service::singleton()->isASessionOpened();
	}
	
	/**
	 * Indicates if a given user has a given password.
	 * 
	 * @param string password The password to check.
	 * @param core_kernel_classes_Resource user The user you want to check the password.
	 * @return boolean
	 */
	public function isPasswordValid($password,  core_kernel_classes_Resource $user){
		return $this->generisUserService->isPasswordValid($password, $user);
	}
	
	/**
	 * Change the password of a given user.
	 * 
	 * @param core_kernel_classes_Resource user The user you want to change the password.
	 * @param string password The md5 hash of the new password.
	 */
	public function setPassword(core_kernel_classes_Resource $user, $password){
		return $this->generisUserService->setPassword($user, $password);
	}
	
	/**
	 * Get the roles of a given user.
	 * 
	 * @param core_kernel_classes_Resource $user The user you want to retrieve the roles.
	 * @return array An array of core_kernel_classes_Resource.
	 */
	public function getUserRoles(core_kernel_classes_Resource $user){
		return $this->generisUserService->getUserRoles($user);
	}
	
	/**
	 * Indicates if a user is granted with a set of Roles.
	 *
	 * @access public
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @param  Resource user The User instance you want to check Roles.
	 * @param  roles Can be either a single Resource or an array of Resource that are instances of Role.
	 * @return boolean
	 */
	public function userHasRoles(core_kernel_classes_Resource $user, $roles){
		return $this->generisUserService->userHasRoles($user, $roles);
	}
	
	/**
	 * Attach a Generis Role to a given TAO User. A UserException will be
	 * if an error occurs. If the User already has the role, nothing happens.
	 *
	 * @access public
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @param  Resource user The User you want to attach a Role.
	 * @param  Resource role A Role to attach to a User.
	 * @throws core_kernel_users_Exception If an error occurs.
	 */
	public function attachRole(core_kernel_classes_Resource $user, core_kernel_classes_Resource $role)
	{
		$this->generisUserService->attachRole($user, $role);
	}
	
	/**
	 * Unnatach a Role from a given TAO User.
	 *
	 * @access public
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @param  Resource user A TAO user from which you want to unnattach the Role.
	 * @param  Resource role The Role you want to Unnatach from the TAO User.
	 * @throws core_kernel_users_Exception If an error occurs.
	 */
	public function unnatachRole(core_kernel_classes_Resource $user, core_kernel_classes_Resource $role)
	{
		$this->generisUserService->unnatachRole($user, $role);
	}
        
	/**
	 * Get the class to use to instantiate users.
	 * 
	 * @return core_kernel_classes_Class The user class.
	 */
	public function getRootClass()
	{
		return new core_kernel_classes_Class(CLASS_TAO_USER);
	}
}
