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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - tao/helpers/grid/Cell/class.Adapter.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 09.11.2011, 16:42:16 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package tao
 * @subpackage helpers_grid_Cell
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032AE-includes begin
// section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032AE-includes end

/* user defined constants */
// section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032AE-constants begin
// section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032AE-constants end

/**
 * Short description of class tao_helpers_grid_Cell_Adapter
 *
 * @abstract
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package tao
 * @subpackage helpers_grid_Cell
 */
abstract class tao_helpers_grid_Cell_Adapter
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute data
     *
     * @access protected
     * @var array
     */
    protected $data = array();

    /**
     * Short description of attribute options
     *
     * @access protected
     * @var array
     */
    protected $options = array();

    /**
     * Short description of attribute excludedProperties
     *
     * @access public
     * @var array
     */
    public $excludedProperties = array();

    // --- OPERATIONS ---

    /**
     * Short description of method getValue
     *
     * @abstract
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string rowId
     * @param  string columnId
     * @param  string data
     * @return mixed
     */
    public abstract function getValue($rowId, $columnId, $data = null);

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  array options
     * @return mixed
     */
    public function __construct($options = array())
    {
        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032EA begin
		$this->options = $options;
		$this->excludedProperties = (is_array($this->options) && isset($this->options['excludedProperties'])) ? $this->options['excludedProperties'] : array();
        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032EA end
    }

    /**
     * Short description of method getData
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return array
     */
    public function getData()
    {
        $returnValue = array();

        // section 127-0-1-1-3d16f06:13388f94a40:-8000:0000000000003368 begin
		$returnValue = $this->data;
        // section 127-0-1-1-3d16f06:13388f94a40:-8000:0000000000003368 end

        return (array) $returnValue;
    }

} /* end of abstract class tao_helpers_grid_Cell_Adapter */

?>