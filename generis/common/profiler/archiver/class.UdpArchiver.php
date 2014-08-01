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
 * Push and forget profiler data
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package generis
 
 */
class common_profiler_archiver_UdpArchiver implements common_profiler_archiver_Archiver
{
	
	/**
     * Short description of attribute host
     *
     * @access public
     * @var string
     */
    public $host = '127.0.0.1';

    /**
     * Short description of attribute port
     *
     * @access public
     * @var int
     */
    public $port = 27072;
	
    /**
     * Short description of attribute resource
     *
     * @access public
     * @var resource
     */
    public $resource = null;
	
	public function init($configuration){
		
		$returnValue = false;
		
		if (isset($configuration['udp_host'])) {
    		$this->host = (string)$configuration['udp_host'];
    	}
    	
    	if (isset($configuration['udp_port'])) {
    		$this->port = intval($configuration['udp_port']);
    	}
		
		$this->resource = null;
		$returnValue = true;
		
		return $returnValue;
	}
	
	public function store($profileData){
		
		if(is_null($this->resource)){
        	$this->resource  = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        	socket_set_nonblock($this->resource);
        }
        if($this->resource !== false){
        	$message = json_encode($profileData);
        	@socket_sendto($this->resource, $message, strlen($message), 0, $this->host, $this->port);
        }
		
	}

}