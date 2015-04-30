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
class tao_actions_UserApi extends tao_actions_RemoteServiceModule {

	public function __construct() {
		// make sure the user is autorized
		parent::__construct();
	}
	
	/**
	 * Allows the remote system to change the users password
	 * 
	 * @throws common_Exception
	 */
	public function changePassword() {
		if (!$this->hasRequestParameter('oldpassword')
			|| !$this->hasRequestParameter('newpassword')) {
			throw new common_Exception('Missing paramteres');
		}
		$userService = tao_models_classes_UserService::singleton();
		$user = $this->getCurrentUser();
		if (is_null($user) || !$userService->isPasswordValid($this->getRequestParameter('oldpassword'), $user)) {
			return $this->returnFailure('Invalid password');
		}
		
		$userService->setPassword($user, $this->getRequestParameter('newpassword'));
		$this->returnSuccess();
	}
	
	public function setInterfaceLanguage() {
		$success = false;
		if (!$this->hasRequestParameter('lang')) {
			throw new common_Exception('Missing paramteres');
		}
		
		$user			= $this->getCurrentUser();
		$uiLangResource = tao_helpers_I18n::getLangResourceByCode($this->getRequestParameter('lang'));
		
		if(!is_null($uiLangResource)){
			$success = $user->editPropertyValues(
				new core_kernel_classes_Property(PROPERTY_USER_UILG), $uiLangResource
			);
		} else {
			common_Logger::w('language '.$this->getRequestParameter('lang').' not found');
			return $this->returnFailure(__('Language not supported'));
		}
		
		$this->returnSuccess();
	}
    
	/**
     * Allows the remote system to change the data language
     * 
     * @return string The json response
     * @throws common_Exception
     */
    public function setDataLanguage() {
		$success = false;
		if (!$this->hasRequestParameter('lang')) {
			throw new common_Exception('Missing parameters');
		}
		
		$user			= $this->getCurrentUser();
		$dataLangResource = tao_helpers_I18n::getLangResourceByCode($this->getRequestParameter('lang'));
		
		if(!is_null($dataLangResource)){
			$success = $user->editPropertyValues(
				new core_kernel_classes_Property(PROPERTY_USER_DEFLG), $dataLangResource
			);
            common_Logger::w('Data language changed to "'.$this->getRequestParameter('lang').'"');
		} else {
			common_Logger::w('language '.$this->getRequestParameter('lang').' not found');
			return $this->returnFailure(__('Language not supported'));
		}
		
		$this->returnSuccess();
	}
	
	/**
	 * Get detailed information about the current user
	 * 
	 * @throws common_Exception
	 */
	public function getSelfInfo() {
		return $this->returnSuccess(array('info' => self::buildInfo($this->getCurrentUser())));
	}
	
	/**
	 * Returns an array of the information
	 * a remote system might require 
	 * 
	 * @param core_kernel_classes_Resource $user
	 */
	public static function buildInfo(core_kernel_classes_Resource $user) {
		$props = $user->getPropertiesValues(array(
			new core_kernel_classes_Property(PROPERTY_USER_FIRSTNAME),			
			new core_kernel_classes_Property(PROPERTY_USER_LASTNAME),
			new core_kernel_classes_Property(PROPERTY_USER_LOGIN),
			new core_kernel_classes_Property(PROPERTY_USER_MAIL),			
			new core_kernel_classes_Property(PROPERTY_USER_UILG),			
			));
			
		$roles = array();
		$roleRes = tao_models_classes_UserService::singleton()->getUserRoles($user);
		foreach ($roleRes as $role) {
			$roles[$role->getUri()] = $role->getLabel();
		}	
		if (isset($props[PROPERTY_USER_UILG]) && is_array($props[PROPERTY_USER_UILG])) {
			$langRes = array_pop($props[PROPERTY_USER_UILG]);
			$lang = (string)$langRes->getUniquePropertyValue(new core_kernel_classes_Property(RDF_VALUE));
		} else {
			$lang = DEFAULT_LANG;
		}
		return array(
			'id'			=> $user->getUri(),
			'login'			=> !empty($props[PROPERTY_USER_LOGIN]) ? (string)array_pop($props[PROPERTY_USER_LOGIN]) : '',
			'first_name'	=> !empty($props[PROPERTY_USER_FIRSTNAME]) ? (string)array_pop($props[PROPERTY_USER_FIRSTNAME]) : '',
			'last_name'		=> !empty($props[PROPERTY_USER_LASTNAME]) ? (string)array_pop($props[PROPERTY_USER_LASTNAME]) : '',
			'email'			=> !empty($props[PROPERTY_USER_MAIL]) ? (string)array_pop($props[PROPERTY_USER_MAIL]) : '',
			'lang'			=> $lang,
			'roles'			=> $roles 
		);
	}

}
?>