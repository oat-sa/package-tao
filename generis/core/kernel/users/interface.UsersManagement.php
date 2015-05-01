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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Short description of class core_kernel_users_UsersManagement
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package generis
 
 */
interface core_kernel_users_UsersManagement
{


    // --- OPERATIONS ---

    /**
     * Returns true if the a user with login = $login is currently in the
     * memory of Generis.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string login A string that is used as a Generis user login in the persistent memory.
     * @param  Class class A specific sub class of User where the login must be searched into.
     * @return boolean
     */
    public function loginExists($login,  core_kernel_classes_Class $class = null);

    /**
     * Create a new Generis User with a specific Role. If the $role is not
     * the user will be given the Generis Role.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string login A specific login for the User to create.
     * @param  string password A password for the User to create (md5 hash).
     * @param  Resource role A Role to grant to the new User.
     * @return core_kernel_classes_Resource
     */
    public function addUser($login, $password,  core_kernel_classes_Resource $role = null);

    /**
     * Remove a Generis User from persistent memory. Bound roles will remain
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource user A reference to the User to be removed from the persistent memory of Generis.
     * @return boolean
     */
    public function removeUser( core_kernel_classes_Resource $user);

    /**
     * Get a specific Generis User from the persistent memory of Generis that
     * a specific login. If multiple users have the same login, a UserException
     * be thrown.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string login A Generis User login.
     * @param  Class class A specific sub Class of User where in the User has to be searched in.
     * @return core_kernel_classes_Resource
     */
    public function getOneUser($login,  core_kernel_classes_Class $class = null);

    /**
     * Indicates if an Authenticated Session is open.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return boolean
     */
    public function isASessionOpened();

    /**
     * used in conjunction with the callback validator
     * to test the pasword entered
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string password used in conjunction with the callback validator
to test the pasword entered
     * @param  Resource user used in conjunction with the callback validator
to test the pasword entered
     * @return boolean
     */
    public function isPasswordValid($password,  core_kernel_classes_Resource $user);

    /**
     * Set the password of a specifc user.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource user The user you want to set the password.
     * @param  string password The md5 hash of the password you want to set to the user.
     * @return void
     */
    public function setPassword( core_kernel_classes_Resource $user, $password);

    /**
     * Get the roles that a given user has.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource user A Generis User.
     * @return array
     */
    public function getUserRoles( core_kernel_classes_Resource $user);

    /**
     * Indicates if a user is granted with a set of Roles.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource user The User instance you want to check Roles.
     * @param  roles Can be either a single Resource or an array of Resource depicting Role(s).
     * @return boolean
     */
    public function userHasRoles( core_kernel_classes_Resource $user, $roles);

    /**
     * Attach a Generis Role to a given Generis User. A UserException will be
     * if an error occurs. If the User already has the role, nothing happens.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource user The User you want to attach a Role.
     * @param  Resource role A Role to attach to a User.
     * @return void
     */
    public function attachRole( core_kernel_classes_Resource $user,  core_kernel_classes_Resource $role);

    /**
     * Short description of method unnatachRole
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource user A Generis user from which you want to unnattach the Generis Role.
     * @param  Resource role The Generis Role you want to Unnatach from the Generis User.
     * @return void
     */
    public function unnatachRole( core_kernel_classes_Resource $user,  core_kernel_classes_Resource $role);
    
    /**
     * Returns an array of Roles (as Resources) where keys are their URIs. The
     * roles represent which kind of Roles are accepted to be identified against
     * system.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return array
     */
    public function getAllowedRoles();
    
    /**
     * Returns a Role (as a Resource) which represents the default role of the
     * If a user has to be created but no Role is given to him, it will receive
     * role.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return core_kernel_classes_Resource
    */
    public function getDefaultRole();

} /* end of interface core_kernel_users_UsersManagement */

?>