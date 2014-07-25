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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

require_once(dirname(__FILE__)."/lib/taoLibrary.php");

/**
 * 
 * Administrativ Services, might not be available to all users
 * 
 * @author Joel Bout, <joel.bout@tudor.lu>
 *
 */
class taoAdminService extends taoLibrary {
	
	/**
	 * module used by this library
	 * @var string
	 */
	const MODULE = 'UserAdminApi';
	
	/**
	 * Returns a list of all users 
	 * 
	 * @return array
	 */
	public function getAllUsers() {
		$data = $this->request(self::MODULE, 'getAllUsers', array());
		return $data['list'];
	}
	
	/**
	 * Returns an associativ array with the role uris as keys
	 * an the roles labels as values
	 * 
	 * @return array 
	 */
	public function getAllRoles() {
		$data = $this->request(self::MODULE, 'getAllRoles', array());
		return $data['list'];
	}
	
	/**
	 * Return informations on a specific user
	 * 
	 * @param string $userUri
	 * @see taoLibrary::startSession()
	 */
	public function getUserInfo($userUri) {
		$data = $this->request(self::MODULE, 'getUserInfo', array(
			'userid'	=> $userUri
		));
		if (is_null($data) || !isset($data['info']) || !isset($data['success']) || !$data['success']) {
			$this->handleError('User info cannot be retrieved');
			return false;
		}
		return $data['info'];
	}
	
	/**
	 * NOT IMPLEMENTED, since no information besides label
	 * exists for the role at the moment
	 * 
	 * @throws Exception
	 */
	public function getRoleInfo() {
		throw new Exception('not implemented');
	}
	
	/**
	 * Returns a list of the specified user's roles 
	 * 
	 * @param string $userUri
	 * @return array
	 * @see taoAdminService::getAllRoles()
	 */
	public function getUserRoles($userUri) {
		$data = $this->request(self::MODULE, 'getUserRoles', array('userid' => $userUri));
		return $data['roles'];
	}

	/**
	 * Returns a list of the users that have the specified role
	 * 
	 * @param string $groupUri
	 * @return array
	 */
	public function getRoleUsers($groupUri) {
		$data = $this->request(self::MODULE, 'getRoleUsers', array('groupid' => $groupUri));
		return $data['users'];
	}
}
		