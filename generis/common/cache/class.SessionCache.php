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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Short description of class common_cache_SessionCache
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package generis
 
 */
class common_cache_SessionCache
        implements common_cache_Cache
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute items
     *
     * @access public
     * @var array
     */
    public $items = array();

    /**
     * Short description of attribute SESSION_KEY
     *
     * @access public
     * @var string
     */
    const SESSION_KEY = 'cache';

    /**
     * Short description of attribute instance
     *
     * @access private
     * @var SessionCache
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
        
        if ($mixed instanceof common_Serializable) {
        	if (!is_null($serial) && $serial != $mixed->getSerial()) {
        		throw new common_exception_Error('Serial mismatch for Serializable '.$mixed->getSerial());
        	}
        	$serial = $mixed->getSerial();
        }
        $this->items[$serial] = $mixed;
        
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

        
        if (!isset($this->items[$serial])) {
        	if ($this->has($serial)) {
        		$storage = PHPSession::singleton()->getAttribute(static::SESSION_KEY);
	        	$data = @unserialize($storage[$serial]);
		        
	        	// check if serialize successfull, see http://lu.php.net/manual/en/function.unserialize.php
	        	if ($data === false && $storage[$serial] !== serialize(false)){
	        		throw new common_exception_Error("Unable to unserialize session entry identified by \"".$serial.'"');
	        	}
	        	$this->items[$serial] = $data;
	        } else {
        		throw new common_cache_NotFoundException('Failed to get ('.$serial.')');
        	}
        }
        $returnValue = $this->items[$serial];
        

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

        
        $session = PHPSession::singleton();
        
    	if (isset($this->items[$serial])) {
			$returnValue = true;
		} else {
			if ($session->hasAttribute(static::SESSION_KEY)) {
				
				$storage = $session->getAttribute(static::SESSION_KEY);
				$returnValue = isset($storage[$serial]);
			}
		}
        

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
        
    	if (isset($this->items[$serial])) {
	        unset($this->items[$serial]);
	        unset($_SESSION[SESSION_NAMESPACE][static::SESSION_KEY][$serial]);
        }
        
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

        
        PHPSession::singleton()->removeAttribute(static::SESSION_KEY);
        $this->items = array();
        

        return $returnValue;
    }

    /**
     * Short description of method __destruct
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    public function __destruct()
    {
        
    	foreach ($this->items as $key => $value) {
			// not clean put reading the session and then adding data to the session causses concurrency problems
			// therefore this DOES NOT WORK: session::setAttribute(static::SESSION_KEY, $storage)
        	$_SESSION[SESSION_NAMESPACE][static::SESSION_KEY][$key] = serialize($value);
    	}
        
    }

    /**
     * Short description of method getAll
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getAll()
    {
        $returnValue = array();

        
        $session = PHPSession::singleton();
        
        if ($session->hasAttribute(static::SESSION_KEY)) {
    		foreach ($session->getAttribute(static::SESSION_KEY) as $serial => $raw) {
    			if (!isset($this->items[$serial])) {
    				// loads the serial to the item
    				$this->get($serial);
    			}
	        }
    	}
    	$returnValue = $this->items;
        

        return (array) $returnValue;
    }

    /**
     * Short description of method contains
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string serial
     * @return boolean
     */
    public function contains($serial)
    {
        $returnValue = (bool) false;

        
        $session = PHPSession::singleton();
        
    	if (isset($this->items[$serial])) {
        	$returnValue = true;
        } elseif (!empty($serial) && $session->hasAttribute(static::SESSION_KEY)){
        	
        	$storage = $session->getAttribute(static::SESSION_KEY);
        	$returnValue = isset($storage[$serial]);
        }
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return common_cache_SessionCache
     */
    public static function singleton()
    {
        $returnValue = null;

        
        if (!isset(self::$instance)){
        	self::$instance = new self();
        }
        
        return self::$instance;
        

        return $returnValue;
    }

} /* end of class common_cache_SessionCache */

?>