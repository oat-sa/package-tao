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
 * Any implementation of the AbstractUriProvider class aims at providing unique
 * to client code. It should take into account the state of the Knowledge Base
 * avoid collisions. The AbstractUriProvider::provide method must be implemented
 * subclasses to return a valid URI.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage uri
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1-85--341437fc:13634d84b3e:-8000:0000000000001978-includes begin
// section 10-13-1-85--341437fc:13634d84b3e:-8000:0000000000001978-includes end

/* user defined constants */
// section 10-13-1-85--341437fc:13634d84b3e:-8000:0000000000001978-constants begin
// section 10-13-1-85--341437fc:13634d84b3e:-8000:0000000000001978-constants end

/**
 * Any implementation of the AbstractUriProvider class aims at providing unique
 * to client code. It should take into account the state of the Knowledge Base
 * avoid collisions. The AbstractUriProvider::provide method must be implemented
 * subclasses to return a valid URI.
 *
 * @abstract
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage uri
 */
abstract class common_uri_AbstractUriProvider
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The database driver the UriProvider is using.
     *
     * @access protected
     * @var string
     */
    protected $driver = '';

    // --- OPERATIONS ---

    /**
     * Returns the database driver that the UriProvider implementation is
     * using.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getDriver()
    {
        $returnValue = (string) '';

        // section 10-13-1-85--341437fc:13634d84b3e:-8000:000000000000197C begin
        $returnValue = $this->driver;
        // section 10-13-1-85--341437fc:13634d84b3e:-8000:000000000000197C end

        return (string) $returnValue;
    }

    /**
     * Sets the database driver that will be used for further URI generations.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string driver A driver name such as 'mysql', 'postgres', ...
     * @return void
     */
    public function setDriver($driver)
    {
        // section 10-13-1-85--341437fc:13634d84b3e:-8000:000000000000197E begin
        $this->driver = strtolower($driver);
        // section 10-13-1-85--341437fc:13634d84b3e:-8000:000000000000197E end
    }

    /**
     * Provides a URI.
     *
     * @abstract
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public abstract function provide();

    /**
     * Instantiates an instance of UriProvider for a given database driver.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string driver A driver name such as 'mysql', 'postgres', ...
     * @return mixed
     */
    public function __construct($driver)
    {
        // section 10-13-1-85--341437fc:13634d84b3e:-8000:0000000000001983 begin
        $this->setDriver($driver);
        // section 10-13-1-85--341437fc:13634d84b3e:-8000:0000000000001983 end
    }

} /* end of abstract class common_uri_AbstractUriProvider */

?>