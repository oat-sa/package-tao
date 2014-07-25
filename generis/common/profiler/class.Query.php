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
 *	Represent a query to be profiled
 * 
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package generis
 
 */
class common_profiler_Query
{
	protected $statement = '';
	protected $key = '';
	protected $params = array();
	protected $time = 0;
	
	public function __construct($statement, $params, $time){
		$this->statement = $statement;
		$this->key = hash('crc32b', $this->statement);
		$this->params = $params;
		$this->time = $time;//µs
	}
	
	public function getStatementKey(){
		return $this->key;
	}
	
	public function toArray(){
		return get_object_vars($this);
	}
	
	public function getTime($unit = 'µs'){
		$returnValue = 0;
		switch(strtolower($unit)){
			case 'ms':
				$returnValue = round($this->time * 1000, 3);
				break;
			case 'µs':
			default:
				$returnValue = $this->time;
				break;	
		}
		return $returnValue;
	}
	
	public function getStatement(){
		return $this->statement;
	}
}
