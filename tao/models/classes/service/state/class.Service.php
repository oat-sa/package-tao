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
 */

/**
 * Persistence for the item delivery service
 *
 * @access public
 * @author @author Joel Bout, <joel@taotesting.com>
 * @package taoItemRunner
 * @subpackage models_classes_runner
 */
class tao_models_classes_service_state_Service
    extends tao_models_classes_Service
{
	private $implementation;
	
	protected function __construct() {
		$this->implementation = new tao_models_classes_service_state_FileSystemPersistence();
		parent::__construct();
	}
	
	public function set($user, $serial, $data) {
	    $userId = $user instanceof core_kernel_classes_Resource ? $user->getUri() : (string)$user;
		return $this->getImplementation()->set($userId, $serial, $data);
	}
	
	public function has($user, $serial) {
	    $userId = $user instanceof core_kernel_classes_Resource ? $user->getUri() : (string)$user;
	    return $this->getImplementation()->has($userId, $serial);
	}
	
	public function get($user, $serial) {
	    $userId = $user instanceof core_kernel_classes_Resource ? $user->getUri() : (string)$user;
	    return $this->getImplementation()->get($userId, $serial);
	}
	
	public function del($user, $serial) {
	    $userId = $user instanceof core_kernel_classes_Resource ? $user->getUri() : (string)$user;
	    return $this->getImplementation()->del($userId, $serial);
	}
  
  	private function getImplementation() {
  		return $this->implementation;
  	}
}

?>