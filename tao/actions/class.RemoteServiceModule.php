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
 * This controller provides a service to allow other sites to authenticate against
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 
 *
 */
class tao_actions_RemoteServiceModule extends Module {
	
	const SESSION_DURATION = 43200; // 12 horus
	
	private $currentUser = null;

	/**
	 * empty constructor
	 */
	public function __construct()
	{
	}

	/**
	 * Allows a remote system to connect a tao User
	 */
	public function login() {
		
		$user = $this->doLogin();
		
		if ($user == false) {
			echo json_encode(array(
				'success'	=> false,
				'message'	=> __('Login failed')
			));
		} else {
			echo json_encode(array(
				'success'	=> true,
				'user'		=> $user->getUri(),
				'token'		=> $this->buildToken($user)
			));
		}
	}
	
	/**
	 * Allows the remote system to verify if a token is still valid,
	 * and to refresh the Token
	 * 
	 * requires a valid session
	 */
	public function keepAlive() {
		return $this->returnSuccess();
	}
	
	
	/**
	 * Searches for the user with the provided username and verifies his password 
	 * 
	 * @throws common_Exception
	 * @return core_kernel_classes_Resource the logged in user or null
	 */
	protected function doLogin() {
		if (!$this->hasRequestParameter('username') || !$this->hasRequestParameter('password')) {
			throw new common_Exception('Missing paramteres');
		}
		$userService = tao_models_classes_UserService::singleton();
		$user = $userService->getOneUser($this->getRequestParameter('username'));
		if (is_null($user)) {
			return false;
		}
		if ($userService->isPasswordValid($this->getRequestParameter('password'), $user)) {
			$this->currentUser = $user;
			return $user;
		} else {
			common_Logger::w('API login failed for user '.$this->getRequestParameter('username'));
			return false;
		}
	}
	
	protected function returnFailure($errormsg = '') {
		echo json_encode(array(
			'success'	=> false,
			'error'		=> $errormsg
		));
		
	}
	
	protected function returnSuccess($data = array()) {
		$data['success']	= true;
		if (!is_null($this->getCurrentUser())) {
			$data['token']		= $this->buildToken($this->getCurrentUser());
		}
		echo json_encode($data);
	}
	
	/**
	 * This function should build an authentification token for the user
	 * This function is NOT yet secure
	 * 
	 * @param core_kernel_classes_Resource $user
	 * @return returns a token string
	 */
	protected function buildToken(core_kernel_classes_Resource $user, $time = null) {
		$time = is_null($time) ? time() : $time;
		$userPass = (string)$user->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_PASSWORD));
		return $time.'_'.md5($time.$user->getUri().$userPass);
	}
	
	/**
	 * Returns the current user or null
	 * @todo verify first
	 * 
	 * @return core_kernel_classes_Resource
	 */
	protected function getCurrentUser() {
		if ($this->currentUser == null) {
			if ($this->hasRequestParameter('user')) {
				$userUri			= $this->getRequestParameter('user');
				$this->currentUser	= new core_kernel_classes_Resource($userUri);
			}
		}
		return $this->currentUser;
	}
	
	private function getUserFromToken() {
		if (!$this->hasRequestParameter('token') || !$this->hasRequestParameter('user')) {
			return null;
		}
		$token		= $this->getRequestParameter('token');
		$userUri	= $this->getRequestParameter('user');
		$user		= new core_kernel_classes_Resource($userUri);
		
		$time = substr($token, 0, strpos($token, '_'));
		if (!is_numeric($time)) {
			common_Logger::w('invalid token '.$token.' for user '.$userUri);
			return null;
		}
		if (time() - $time > self::SESSION_DURATION) {
			common_Logger::i('Session timed out '.$time.' for user '.$userUri);
			return null;
		}
		
		if ($token != $this->buildToken($user, $time)) {
			common_Logger::w('Invalid token for user '.$userUri);
			return null;
		}
		common_Logger::d('User '.$user->getUri().' authentificated via token.');
		return $user;
	}
}
?>