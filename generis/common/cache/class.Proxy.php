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

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common\cache\class.Proxy.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 21.01.2013, 11:13:18 with ArgoUML PHP module 
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

/* user defined includes */
// section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001EE0-includes begin
// section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001EE0-includes end

/* user defined constants */
// section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001EE0-constants begin
// section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001EE0-constants end

/**
 * Short description of class common_cache_Proxy
 *
 * @abstract
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage cache
 */
abstract class common_cache_Proxy
        implements common_cache_Cache
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute implementation
     *
     * @access private
     * @var Cache
     */
    private $implementation = null;

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
        $this->implementation->put($mixed, $serial);
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
        $returnValue = $this->implementation->get($serial);
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
        $returnValue = $this->implementation->has($serial);
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
        $this->implementation->remove($serial);
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
        $this->implementation->purge();
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F48 end

        return $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F22 begin
        $this->implementation = $this->getImplementation();
        // section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001F22 end
    }

    /**
     * Short description of method getImplementation
     *
     * @abstract
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return common_cache_Cache
     */
    public abstract function getImplementation();

} /* end of abstract class common_cache_Proxy */

?>