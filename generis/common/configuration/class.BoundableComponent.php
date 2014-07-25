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
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 19.07.2012, 16:31:45 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage configuration
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include common_configuration_Component
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('common/configuration/class.Component.php');

/* user defined includes */
// section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AE5-includes begin
// section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AE5-includes end

/* user defined constants */
// section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AE5-constants begin
// section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001AE5-constants end

/**
 * Short description of class common_configuration_BoundableComponent
 *
 * @abstract
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage configuration
 */
abstract class common_configuration_BoundableComponent
    extends common_configuration_Component
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute min
     *
     * @access private
     * @var string
     */
    private $min = '';

    /**
     * Short description of attribute max
     *
     * @access private
     * @var string
     */
    private $max = '';

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string min
     * @param  string max
     * @param  string name
     * @param  boolean optional
     * @return mixed
     */
    public function __construct($min, $max, $name = 'unknown', $optional = false)
    {
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B55 begin
        parent::__construct($name, $optional);
        $this->setMin($min);
        $this->setMax($max);
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B55 end
    }

    /**
     * Short description of method setMin
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string min
     * @return void
     */
    public function setMin($min)
    {
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B04 begin
        $this->min = $min;
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B04 end
    }

    /**
     * Short description of method getMin
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getMin()
    {
        $returnValue = (string) '';

        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B07 begin
        return $this->min;
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B07 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setMax
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string max
     * @return void
     */
    public function setMax($max)
    {
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B09 begin
    	// Support .x notation.
    	if (!empty($max)){
        	$this->max = preg_replace('/x/u', '99999', $max);
    	}
    	else{
    		$this->max = null;
    	}
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B09 end
    }

    /**
     * Short description of method getMax
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getMax()
    {
        $returnValue = (string) '';

        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B0C begin
        return $this->max;
        // section -64--88-56-1--548fa03:1387a8a40e2:-8000:0000000000001B0C end

        return (string) $returnValue;
    }

    /**
     * Short description of method getValue
     *
     * @abstract
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public abstract function getValue();

} /* end of abstract class common_configuration_BoundableComponent */

?>