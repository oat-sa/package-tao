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
 * basic interface a cache implementation has to implement
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage cache
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001EE8-includes begin
// section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001EE8-includes end

/* user defined constants */
// section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001EE8-constants begin
// section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001EE8-constants end

/**
 * basic interface a cache implementation has to implement
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage cache
 */
interface common_cache_Cache
{


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
    public function put($mixed, $serial = null);

    /**
     * gets the entry associted to the serial
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string serial
     * @return common_Serializable
     */
    public function get($serial);

    /**
     * test whenever an entry associted to the serial exists
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string serial
     * @return boolean
     */
    public function has($serial);

    /**
     * removes an entry from the cache
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string serial
     * @return mixed
     */
    public function remove($serial);

    /**
     * empties the cache
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    public function purge();

} /* end of interface common_cache_Cache */

?>