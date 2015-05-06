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
 * 
 */

/**
 * Abstract User
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package generis
 
 */
abstract class common_user_User
{
	abstract public function getIdentifier();
	
	abstract public function getPropertyValues($property);

	abstract public function refresh();
	
	/**
	 * Extends the users explizit roles with the implizit rules
	 * of the local system
	 * 
	 * @return array the identifiers of the roles:
	 */
	public function getRoles() {
	    $returnValue = array();
	    // We use a Depth First Search approach to flatten the Roles Graph.
	    foreach ($this->getPropertyValues(PROPERTY_USER_ROLES) as $roleUri){
	        $returnValue[] = $roleUri;
	        foreach (core_kernel_users_Service::singleton()->getIncludedRoles(new core_kernel_classes_Resource($roleUri)) as $role) {
	            $returnValue[] = $role->getUri();
	        }
	    }
	    return array_unique($returnValue);
	}
	
}