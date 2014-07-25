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
class tao_models_classes_service_state_FileSystemPersistence
	implements tao_models_classes_service_state_StatePersistence
{
	public function __construct() {
		
	}
	
	public function set($user, $serial, $data) {
		$string = "<? return ".common_Utils::toPHPVariableString($data).";?>";
		$filePath = $this->getPath($user, $serial);
		$folderPath = dirname($filePath);
		if (!file_exists($folderPath)) {
			mkdir($folderPath, 0740, true);
		}
    	
		$success = false;
    	if (false !== ($fp = @fopen($filePath, 'c')) && true === flock($fp, LOCK_EX)){
    	
    	    // We first need to truncate.
    	    ftruncate($fp, 0);
    	
    	    $success = fwrite($fp, $string);
    	    @flock($fp, LOCK_UN);
    	    @fclose($fp);
    	    if ($success) {
    	        // OPcache cleanup
    	        if (function_exists('opcache_invalidate')) {
    	            opcache_invalidate($filePath, true);
    	        }
    	    } else {
    	        common_Logger::w('Could not write '.$filePath);
    	    }
    	}
        return $success !== false;
	}
	
	public function has($user, $serial) {
		return file_exists($this->getPath($user, $serial));
	}
	
	public function get($user, $serial) {
		if ($this->has($user, $serial)) {
			return include @$this->getPath($user, $serial);
		} else {
			return null;
		}
	}
	
	public function del($user, $serial) {
		if ($this->has($user, $serial)) {
			return unlink(@$this->getPath($user, $serial));
		} else {
			return false;
		}
	}
  
  	private function getPath($user, $serial) {
  		return SERVICE_STORAGE_DIRECTORY . md5($user) . DIRECTORY_SEPARATOR . md5($serial);
  	}
}