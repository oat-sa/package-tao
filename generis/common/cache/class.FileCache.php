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
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);              2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common\cache\class.FileCache.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 18.01.2013, 15:31:57 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage cache
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * basic interface a cache implementation has to implement
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('common/cache/interface.Cache.php');

/**
 * Classes that implement this class claims their instances are serializable and
 * be identified by a unique serial string.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('common/interface.Serializable.php');

/* user defined includes */
// section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001ED0-includes begin
// section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001ED0-includes end

/* user defined constants */
// section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001ED0-constants begin
// section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001ED0-constants end

/**
 * Short description of class common_cache_FileCache
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage cache
 */
class common_cache_FileCache
        implements common_cache_Cache
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instance
     *
     * @access private
     * @var FileCache
     */
    private static $instance = null;

    // --- OPERATIONS ---

    /**
     * puts "something" into the cache,
     *      * If this is an object and implements Serializable,
     *      * we use the serial provided by the object
     *      * else a serial must be provided
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  mixed
     * @param  string serial
     * @return mixed
     */
    public function put($mixed, $serial = null)
    {
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F34 begin
        if ($mixed instanceof common_Serializable) {
        	if (!is_null($serial) && $serial != $mixed->getSerial()) {
        		throw new common_exception_Error('Serial mismatch for Serializable '.$mixed->getSerial());
        	}
        	$serial = $mixed->getSerial();
        }
        
        $data = "<? return ".common_Utils::toPHPVariableString($mixed).";?>";
       	
        try{
        	// Acquire the lock and open with mode 'c'. Indeed, we do not use mode 'w' because
        	// it could truncate the file before it gets the lock!
        	$filePath = $this->getFilePath($serial);
        	if (false !== ($fp = @fopen($filePath, 'c')) && true === flock($fp, LOCK_EX)){
        		
        		// We first need to truncate.
        		ftruncate($fp, 0);
        		
        		fwrite($fp, $data);
        		@flock($fp, LOCK_UN);
        		@fclose($fp);
        	}
        	else{
        		$msg = "Unable to write cache file '${filePath}'.";
        		throw new common_exception_FileSystemError($msg);
        	}
        	
        }
        catch (common_exception_FileSystemError $e){
        	$msg  = "An unexpected error occured while creating a temporary ";
        	$msg .= "file to cache data with serial '${serial}': " . $e->getMessage();
        	
        	throw new common_cache_Exception($msg);
        }
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F34 end
    }

    /**
     * gets the entry associted to the serial
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string serial
     * @return common_Serializable
     */
    public function get($serial)
    {
        $returnValue = null;

        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F3C begin
        
        // Acquire a shared lock for reading on the main lock file.
        // We acquire the lock here because we have a critical section below:
        // 1. Check if we have something for this serial.
        // 2. Include the file corresponding to the serial. 
        $filePath = $this->getFilePath($serial);
        if (false !== ($fp = @fopen($filePath, 'r')) && true === flock($fp, LOCK_SH)){
        	$returnValue = include $this->getFilePath($serial);
        	
        	@flock($fp, LOCK_UN);
        	@fclose($fp);
        }
        else{
        	$msg = "Unable to read cache file '${filePath}'.";
        	throw new common_cache_NotFoundException($msg);
        }
    	
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F3C end

        return $returnValue;
    }

    /**
     * test whenever an entry associted to the serial exists
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string serial
     * @return boolean
     */
    public function has($serial)
    {
        $returnValue = (bool) false;

        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F40 begin
        $returnValue = file_exists($this->getFilePath($serial));
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F40 end

        return (bool) $returnValue;
    }

    /**
     * removes an entry from the cache
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string serial
     * @return mixed
     */
    public function remove($serial)
    {
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F44 begin
        @unlink($this->getFilePath($serial));
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F44 end
    }

    /**
     * empties the cache
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    public function purge()
    {
        $returnValue = null;

        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F48 begin
    	$cachepath =  GENERIS_CACHE_PATH;
        if (false !== ($files = scandir($cachepath))){
            foreach ($files as $f) {
                $filePath = $cachepath . $f;
                if (substr($f, 0, 1) != '.' && file_exists($filePath)){
                    @unlink($filePath);
                }
            }
        }
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F48 end

        return $returnValue;
    }

    /**
     * Short description of method getFilePath
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string serial
     * @return string
     */
    private function getFilePath($serial)
    {
        $returnValue = (string) '';

        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001EFA begin
        $returnValue = GENERIS_CACHE_PATH . $serial;
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001EFA end

        return (string) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return common_cache_FileCache
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 10-13-1-85-4b3a31bc:13c4dd4402a:-8000:0000000000001F30 begin
        if (!isset(self::$instance)){
        	self::$instance = new self();
        }
        
        return self::$instance;
        // section 10-13-1-85-4b3a31bc:13c4dd4402a:-8000:0000000000001F30 end

        return $returnValue;
    }

} /* end of class common_cache_FileCache */

?>