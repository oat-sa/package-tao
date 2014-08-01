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
 * Short description of class core_kernel_users_RolesManagement
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package generis
 
 */
interface core_kernel_users_RolesManagement
{

    /**
     * Add a role in Generis.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string label The label to apply to the newly created Generis Role.
     * @param  includedRoles The Role(s) to be included in the newly created Generis Role. Can be either a Resource or an array of Resources.
     * @return core_kernel_classes_Resource
     */
    public function addRole($label, $includedRoles = null, core_kernel_classes_Class $class = null);

    /**
     * Remove a Generis role from the persistent memory. User References to this
     * will be removed.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource role The Role to remove.
     * @return boolean
     */
    public function removeRole(core_kernel_classes_Resource $role);

    /**
     * Get an array of the Roles included by a Generis Role.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource role A Generis Role.
     * @return array
     */
    public function getIncludedRoles(core_kernel_classes_Resource $role);

    /**
     * Make a Role include another Role.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  core_kernel_classes_Resource role The role that needs to include another role.
     * @param  core_kernel_classes_Resource roleToInclude The role to be included.
     */
    public function includeRole(core_kernel_classes_Resource $role,  core_kernel_classes_Resource $roleToInclude);
    
    /**
     * Uninclude a Role from another Role.
     * 
     * @author Jerome Bogaerts <jerome.taotesting.com>
     * @param core_kernel_classes_Resource role The role from which you want to uninclude a Role.
     * @param core_kernel_classes_Resource roleToUninclude The Role to uninclude.
     */
    public function unincludeRole(core_kernel_classes_Resource $role, core_kernel_classes_Resource $roleToUninclude);
    
    /**
     * Return all instances of Roles from the persistent memory of Generis.
     * 
     * @access public
     * @author Jerome Bogaerts
     * @return array An associative array where keys are Role URIs and values are instances of the core_kernel_classes_Resource class.
     */
    public function getAllRoles();

}

?>