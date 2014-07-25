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
 * Tao basic user functionalities, includes
 * password and language changes
 * 
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
class taoUserService extends taoLibrary {
	
	/**
	 * module used by this library
	 * @var string
	 */
	const MODULE = 'UserApi';
	
	/**
	 * Returns informations on the current user
	 * 
	 * @return array
	 * @see taoLibrary::startSession()
	 */
	public function getInfo() {
		$data = $this->request(self::MODULE, 'getSelfInfo', array());
		if (is_null($data) || !isset($data['info']) || !isset($data['success']) || !$data['success']) {
			return $this->handleError('getInfo request failed');
		}
		return $data['info'];
	}
	
	/**
	 * Returns the roles of the current user with the roles uris as key
	 * and the roles labels as value
	 * 
	 * @return array
	 */
	public function getRoles() {
		$info = $this->getInfo();
		if (isset($info['roles'])) {
			return $info['roles'];
		} else {
			// error already handled in getInfo
			return $info;
		}
	}
	
	/**
	 * Sets the currents user language
	 * 
	 * @param string $lang language to chnage to
	 * @return boolean whenever or not the changes were successful
	 */
	public function setLanguage($lang) {
		$reply = $this->request(self::MODULE, 'setInterfaceLanguage', array(
			'lang'		=> $lang
		));
		return (!is_null($reply) && isset($reply['success']) && $reply['success']);
	}
	
    /**
	 * Sets the current user's data language
	 * 
	 * @param string $lang language (locale) to chnage to, format: {country_code}-{region}
	 * @return boolean whenever or not the changes were successful
	 */
	public function setDataLanguage($lang) {
		$reply = $this->request(self::MODULE, 'setDataLanguage', array(
			'lang' => $lang
		));
        
		return (!is_null($reply) && isset($reply['success']) && $reply['success']);
	}
    
	/**
	 * Change the current users password
	 * 
	 * @param string $old the old password
	 * @param string $new the new password
	 * @return boolean whenever or not the password was changed
	 */
	public function changePassword($old, $new) {
		$reply = $this->request(self::MODULE, 'changePassword', array(
			'oldpassword'	=> $old,
			'newpassword'	=> $new
		));
		
		return (!is_null($reply) && isset($reply['success']) && $reply['success']);
	}
}
		