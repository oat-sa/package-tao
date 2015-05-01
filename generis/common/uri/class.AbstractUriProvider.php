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
 * Any implementation of the AbstractUriProvider class aims at providing unique
 * to client code. It should take into account the state of the Knowledge Base
 * avoid collisions. The AbstractUriProvider::provide method must be implemented
 * subclasses to return a valid URI.
 *
 * @abstract
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package generis
 
 */
abstract class common_uri_AbstractUriProvider implements common_uri_UriProvider
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

        
        $returnValue = $this->driver;
        

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
        
        $this->driver = strtolower($driver);
        
    }

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
        
        $this->setDriver($driver);
        
    }

} /* end of abstract class common_uri_AbstractUriProvider */

?>