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

/**
 * This class provide service on user roles management
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 
 */
class tao_models_classes_RoleService
    extends tao_models_classes_GenerisService
    implements core_kernel_users_RolesManagement
{

    /**
     * the core user service
     *
     * @access public
     * @var core_kernel_users_Service
     */
    protected $generisUserService = null;

    /**
     * the class of the target role
     *
     * @access public
     * @var core_kernel_classes_Class
     */
    private $roleClass = null;


    /**
     * Constructor, calls the initRole method.
     *
     * @access protected
     * @author Joel Bout, <joel@taotesting.com>
     */
    protected function __construct()
    {
    	parent::__construct();
		$this->generisUserService = core_kernel_users_Service::singleton();
		$this->initRole();
    }

    /**
     * Initialize the allowed role.
     * To be overriden.
     *
     * @access protected
     * @author Joel Bout, <joel@taotesting.com>
     * @return mixed
     */
    protected function initRole()
    {
    	$this->roleClass = new core_kernel_classes_Class(CLASS_ROLE);
    }

    /**
     * Get the Role matching the uri
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string uri
     * @return core_kernel_classes_Resource
     */
    public function getRole($uri)
    {
        $returnValue = null;
        
        if(!empty($uri)){
        	$returnValue = new core_kernel_classes_Resource($uri);
        }

        return $returnValue;
    }

    /**
     * get the target role class
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return core_kernel_classes_Class
     */
    public function getRoleClass()
    {
        $returnValue = null;
        
        $returnValue = $this->roleClass;

        return $returnValue;
    }

    /**
     * assign a role to a set of users
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource role
     * @param  array users
     * @return boolean
     */
    public function setRoleToUsers( core_kernel_classes_Resource $role, $users = array())
    {
        $returnValue = (bool) false;
        $userService = tao_models_classes_UserService::singleton();
        
        $rolesProperty = new core_kernel_classes_Property(PROPERTY_USER_ROLES);
    	foreach ($users as $u){
    		$u = ($u instanceof core_kernel_classes_Resource) ? $u : new core_kernel_classes_Resource($u);
    		
    		// just in case of ...
    		$userService->unnatachRole($u, $role);
    		
    		// assign the new role.
    		$u->setPropertyValue($rolesProperty, $role);

    		if (common_session_SessionManager::getSession()->getUserUri() == $u->getUri()) {
    		    common_session_SessionManager::getSession()->refresh();
    		}
    	}
        
    	$returnValue = true;

        return (bool) $returnValue;
    }

    /**
     * get the users who have the role in parameter
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  core_kernel_classes_Resource role
     * @return array
     */
    public function getUsers( core_kernel_classes_Resource $role)
    {
        $returnValue = array();

        $filters = array(PROPERTY_USER_ROLES => $role->getUri());
        $options = array('like' => false, 'recursive' => true);
        
        $userClass = new core_kernel_classes_Class(CLASS_GENERIS_USER);
        $results = $userClass->searchInstances($filters, $options);
        
        $returnValue = array_keys($results);

        return (array) $returnValue;
    }
    
    /**
     * Creates a new Role in persistent memory.
     * 
     * @param string label The label of the new role.
     * @param mixed includedRoles The roles to include to the new role. Can be either a core_kernel_classes_Resource or an array of core_kernel_classes_Resource.
     * @param core_kernel_classes_Class (optional) A specific class for the new role. 
     * @return core_kernel_classes_Resource The newly created role.
     */
    public function addRole($label, $includedRoles = null, core_kernel_classes_Class $class = null)
    {
		return $this->generisUserService->addRole($label, $includedRoles, $class);
	}

	/**
	 * Remove a given Role from persistent memory. References to this role
	 * will also be removed from the persistent memory.
	 * 
	 * @param core_kernel_classes_Resource $role The Role to remove.
	 * @return boolean True if the Role was removed, false otherwise.
	 */
	public function removeRole(core_kernel_classes_Resource $role)
	{
		return $this->generisUserService->removeRole($role);
	}
	
	/**
	 * Returns the Roles included by a given Role.
	 * 
	 * @param core_kernel_classes_Resource $role The Role you want to know what are its included Roles.
	 * @return array An array of core_kernel_classes_Resource corresponding to the included Roles.
	 */
	public function getIncludedRoles(core_kernel_classes_Resource $role)
	{
		return $this->generisUserService->getIncludedRoles($role);
	}
	
	/**
	 * Includes the $roleToInclude Role to the $role Role.
	 * 
	 *  @param core_kernel_classes_Resource role A Role.
	 *  @param core_kernel_classes_Resource roleToInclude A Role to include. 
	 */
	public function includeRole(core_kernel_classes_Resource $role,  core_kernel_classes_Resource $roleToInclude)
	{
		$this->generisUserService->includeRole($role, $roleToInclude);
	}
	
	/**
	 * Uninclude a Role from another Role.
	 * 
	 * @param core_kernel_classes_Resource role The Role from which you want to uninclude another Role.
	 * @param core_kernel_classes_Resource roleToUninclude The Role to uninclude.
	 */
	public function unincludeRole(core_kernel_classes_Resource $role, core_kernel_classes_Resource $roleToUninclude)
	{
		$this->generisUserService->unincludeRole($role, $roleToUninclude);
	}
	
	/**
	 * Returns the whole collection of Roles stored into TAO.
	 * 
	 * @return array An associative array where keys are Role URIs and values are core_kernel_classes_Resource instances.
	 */
	public function getAllRoles()
	{
		return $this->generisUserService->getAllRoles();
	}
}

?>