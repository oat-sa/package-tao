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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               
 * 
 */

/**
 * Func ACL roles services
 *
 * @access public
 * @author Jehan Bihin
 * @package tao
 * @since 2.2
 
 */
class funcAcl_models_classes_RoleService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method add
     *
     * @access public
     * @author Jehan Bihin, <jehan.bihin@tudor.lu>
     * @param  string name
     * @return string
     */
    public function add($name)
    {
        $returnValue = (string) '';

        
		$roleService = tao_models_classes_RoleService::singleton();
		$role = $roleService->addRole($name);
		
		$returnValue = $role->getUri();
        

        return (string) $returnValue;
    }

    /**
     * Short description of method edit
     *
     * @access public
     * @author Jehan Bihin, <jehan.bihin@tudor.lu>
     * @param  string uri
     * @param  string name
     * @return mixed
     */
    public function edit($uri, $name)
    {
        
		$instance = new core_kernel_classes_Resource($uri);
		$instance->setLabel($name);
        
    }

    /**
     * Short description of method remove
     *
     * @access public
     * @author Jehan Bihin, <jehan.bihin@tudor.lu>
     * @param  string uri
     * @return mixed
     */
    public function remove($uri)
    {
        
		$instance = new core_kernel_classes_Resource($uri);
		$instance->delete();
        
    }

    /**
     * Short description of method attachUser
     *
     * @access public
     * @author Jehan Bihin, <jehan.bihin@tudor.lu>
     * @param  string userUri
     * @param  string roleUri
     * @return mixed
     */
    public function attachUser($userUri, $roleUri)
    {
        
		$userRes = new core_kernel_classes_Resource($userUri);
		$userRes->setPropertyValue(new core_kernel_classes_Property(PROPERTY_USER_ROLES), $roleUri);
        
    }

    /**
     * Short description of method unattachUser
     *
     * @access public
     * @author Jehan Bihin, <jehan.bihin@tudor.lu>
     * @param  string userUri
     * @param  string roleUri
     * @throws core_kernel_users_Exception
     */
    public function unattachUser($userUri, $roleUri)
    {
        
		$userService = tao_models_classes_UserService::singleton();
		$user = new core_kernel_classes_Resource($userUri);
		$role = new core_kernel_classes_Resource($roleUri);
		
		$userService->unnatachRole($user, $role);
        
    }

    /**
     * Short description of method getRoles
     *
     * @access public
     * @author Jehan Bihin, <jehan.bihin@tudor.lu>
     * @param  string userUri
     * @return array
     */
    public function getRoles($userUri)
    {
        $returnValue = array();

        
		$userRes = new core_kernel_classes_Resource($userUri);

		$rolesc = new core_kernel_classes_Class(CLASS_ROLE);
		$userRoles = $userRes->getTypes();
		foreach ($rolesc->getInstances(true) as $id => $r) {
			if ($id != INSTANCE_ROLE_ANONYMOUS) {
				$nrole = array('id' => tao_helpers_Uri::encode($id), 'label' => $r->getLabel(), 'selected' => false);
				//Selected
				foreach ($userRoles as $uri => $t) {
					if ($uri == $id) {
					    $nrole['selected'] = true;
					}
				}
				$returnValue[] = $nrole;
			}
		}
        

        return (array) $returnValue;
    }

} /* end of class funcAcl_models_classes_RoleService */

?>