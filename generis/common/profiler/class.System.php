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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 *	Represent the system being profiled
 * 
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package generis
 
 */
class common_profiler_System
{
	public function __construct(){
		$this->computerId = $this->getComputerId();
		$this->taoId = $this->getTaoInstanceId();
		$this->uname = php_uname();
		$this->hostname = gethostname();
		$this->php = phpversion();
	}
	
	public function getTaoInstanceId(){
		$key = LOCAL_NAMESPACE.GENERIS_INSTANCE_NAME.GENERIS_SESSION_NAME;
		return md5($key);
	}
	
	public function getComputerId(){
		$key = $_SERVER['SERVER_SIGNATURE'].$_SERVER['SERVER_ADMIN'].$_SERVER['DOCUMENT_ROOT'];
		return md5($key);
	}
	
	public function toArray(){
		return get_object_vars($this);
	}
}
