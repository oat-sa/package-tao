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
?>
<?php
/**
 * This controller provides a service to allow other entities to be authenticated
 * against the platform.
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 
 *
 */
class tao_actions_UserAdminApi extends tao_actions_RemoteServiceModule {
	
	/**
	 * Get detailed information about the current user in JSON.
	 * 
	 * - id
	 * - login
	 * - first_name
	 * - last_name
	 * - email
	 * - lang
	 * - roles
	 * 
	 * @throws common_Exception If the 'userid' parameter is missing.
	 */
	public function getUserInfo() {
		if (!$this->hasRequestParameter('userid')) {
			throw new common_Exception("Missing parameter 'userid'");
		}

		$user = new core_kernel_classes_Resource($this->getRequestParameter('userid'));
		return $this->returnSuccess(array('info' => tao_actions_UserApi::buildInfo($user)));
	}
	
	/**
	 * This action echoes the entire list of users within TAO using a JSON serialization.
	 * 
	 * example:
	 * 
	 * {
	 * 		"list": [
	 * 			{
	 * 				"id": "http://www.tao.lu/Ontologies/TAO.rdf#user1,
	 * 				"login": "user1",
	 * 				"mail": "user1@mail.com",
	 * 				"first": "first name",
	 * 				"last": "last name"
	 * 			},
	 * 			...
	 * 		]
	 * }
	 */
	public function getAllUsers() {
		$service = tao_models_classes_UserService::singleton();
		$users = $service->getAllUsers();
		$list = array();
		foreach ($users as $user) {
			$props = $user->getPropertiesValues(array(
				new core_kernel_classes_Property(PROPERTY_USER_LOGIN),
				new core_kernel_classes_Property(PROPERTY_USER_MAIL),
				new core_kernel_classes_Property(PROPERTY_USER_FIRSTNAME),
				new core_kernel_classes_Property(PROPERTY_USER_LASTNAME),
				));
			$list[] = array(
				'id'	=> $user->getUri(),
				'login'	=> !empty($props[PROPERTY_USER_LOGIN])		? (string)array_pop($props[PROPERTY_USER_LOGIN])	: '',
				'mail'	=> !empty($props[PROPERTY_USER_MAIL])		? (string)array_pop($props[PROPERTY_USER_MAIL])		: '',
				'first'	=> !empty($props[PROPERTY_USER_FIRSTNAME])	? (string)array_pop($props[PROPERTY_USER_FIRSTNAME]): '',
				'last'	=> !empty($props[PROPERTY_USER_LASTNAME])	? (string)array_pop($props[PROPERTY_USER_LASTNAME])	: ''
			);
		}
		return $this->returnSuccess(array('list' => $list));
	}
	
	/**
	 * Get All Roles available on the platform. This action echoes a JSON object with
	 * the 'list' property set with an object where properties are role URIs and property
	 * values are role Labels.
	 * 
	 * For instance:
	 * 
	 * {
	 * 		"list": {
	 * 			"http://www.tao.lu/Ontologies/TAO.rdf#role1": "Role1",
	 * 			"http://www.tao.lu/Ontologies/TAO.rdf#role2": "Role2"
	 * 			...
	 * 		}
	 * }
	 */
	public function getAllRoles() {
		
		$list = array();
		$roleClass = new core_kernel_classes_Class(CLASS_ROLE);
		$roleInstances = $roleClass->getInstances(true);
		
		foreach ($roleInstances->sequence as $role){
			$list[$role->getUri()] = $role->getLabel();
		}
		
		return $this->returnSuccess(array('list' => $list));
	}
	
	/**
	 * This action returns the roles of the user 'userid' in JSON.
	 * 
	 * example:
	 * 
	 * {
	 * 		"roles": [
	 * 			"http://www.tao.lu/Ontologies/TAO.rdf#userRole1",
	 * 			"http://www.tao.lu/Ontologies/TAO.rdf#userRole2"
	 * 		]
	 * }
	 * 
	 * @throws common_Exception If the 'userid' parameter is missing.
	 */
	public function getUserRoles() {
		if (!$this->hasRequestParameter('userid')) {
			throw new common_Exception("Missing parameter 'userid'");
		}
		$user = new core_kernel_classes_Resource($this->getRequestParameter('userid'));
		$uris = array();
		foreach (tao_models_classes_UserService::singleton()->getUserRoles($user) as $role) {
			$uris[] = $role->getUri();
		}
		return $this->returnSuccess(array('roles' => $uris));
	}
	
	/**
	 * Get the users that have the role 'groupid' in JSON.
	 * 
	 * example:
	 * 
	 * {
	 * 		"users": [
	 * 			"http://www.tao.lu/Ontologies/TAO.rdf#userWithRole1",
	 * 			"http://www.tao.lu/Ontologies/TAO.rdf#userWithRole2",
	 * 			...
	 * 		]
	 * }
	 * 
	 * @throws common_Exception If the 'groupid' parameter is missing.
	 */
	public function getRoleUsers() {
		if (!$this->hasRequestParameter('groupid')) {
			throw new common_Exception("Missing parameter 'groupid'");
		}
		$group = new core_kernel_classes_Class($this->getRequestParameter('groupid'));
		$uris = array();
		foreach ($group->getInstances('true') as $user) {
			$uris[] = $user->getUri();
		}
		return $this->returnSuccess(array('users' => $uris));
	}

}
?>