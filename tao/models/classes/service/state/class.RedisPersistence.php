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
 * requires the phpredis library
 *
 * @access public
 * @author @author Joel Bout, <joel@taotesting.com>
 * @package taoItemRunner
 * @subpackage models_classes_runner
 */
class tao_models_classes_service_state_RedisPersistence
	implements tao_models_classes_service_state_StatePersistence
{
	private $server = null;
	
	public function __construct() {
		$this->server = new Redis();
		if ($this->server == false) {
			throw new common_Exception("Redis php module not found");
		} 
		if (!$this->server->connect($GLOBALS['REDIS_SERVER']['host'])) {
			throw new common_Exception("Unable to connect to redis server");
		};
	}
	
	public function set($userId, $serial, $data) {
		$redisSerial = $this->getSerial($userId, $serial);
		$dataString = json_encode($data, true);
		return $this->server->set($redisSerial, $dataString);
	}
	
	public function get($userId, $serial) {
		$redisSerial = $this->getSerial($userId, $serial);
	    $returnValue = $this->server->get($redisSerial);
		if ($returnValue === false && !$this->has($user, $serial)) {
			$returnValue = null; 
		} else {
			$returnValue = json_decode($returnValue, true);
		}
		return $returnValue;
	}
	
	public function has($userId, $serial) {
		$redisSerial = $this->getSerial($userId, $serial);
		return $this->server->exists($redisSerial);
	}
	
	public function del($userId, $serial) {
		$redisSerial = $this->getSerial($userId, $serial);
		return $this->server->del($redisSerial);
	}
  
  	private function getSerial($userId, $serial) {
  		return $userId.'_'.$serial;;
  	}
}