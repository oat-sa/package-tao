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
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 06.07.2010, 17:45:45 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Represents a FormElement entity
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/helpers/form/class.FormElement.php');

/* user defined includes */
// section 127-0-1-1-3ed01c83:12409dc285c:-8000:00000000000019FE-includes begin
// section 127-0-1-1-3ed01c83:12409dc285c:-8000:00000000000019FE-includes end

/* user defined constants */
// section 127-0-1-1-3ed01c83:12409dc285c:-8000:00000000000019FE-constants begin
// section 127-0-1-1-3ed01c83:12409dc285c:-8000:00000000000019FE-constants end

/**
 * Short description of class tao_helpers_form_elements_MultipleElement
 *
 * @abstract
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements
 */
abstract class tao_helpers_form_elements_MultipleElement
    extends tao_helpers_form_FormElement
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute options
     *
     * @access protected
     * @var array
     */
    protected $options = array();

    /**
     * Short description of attribute values
     *
     * @access protected
     * @var array
     */
    protected $values = array();

    // --- OPERATIONS ---

    /**
     * Short description of method setOptions
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array options
     * @return mixed
     */
    public function setOptions($options)
    {
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001A07 begin
		$this->options = $options;
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001A07 end
    }

    /**
     * Short description of method getOptions
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public function getOptions()
    {
        $returnValue = array();

        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001A37 begin
		$returnValue = $this->options;
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001A37 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setValue
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string value
     * @return mixed
     */
    public function setValue($value)
    {
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001A2C begin
		$this->value = tao_helpers_Uri::encode($value);
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001A2C end
    }

    /**
     * Short description of method addValue
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string value
     * @return mixed
     */
    public function addValue($value)
    {
        // section 127-0-1-1-bed3971:124720c750d:-8000:0000000000001A97 begin
		$this->values[] = tao_helpers_Uri::encode($value);
        // section 127-0-1-1-bed3971:124720c750d:-8000:0000000000001A97 end
    }

    /**
     * Short description of method getValues
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public function getValues()
    {
        $returnValue = array();

        // section 127-0-1-1-bed3971:124720c750d:-8000:0000000000001A9D begin
		$returnValue = $this->values;
        // section 127-0-1-1-bed3971:124720c750d:-8000:0000000000001A9D end

        return (array) $returnValue;
    }

    /**
     * Short description of method setValues
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array values
     * @return mixed
     */
    public function setValues($values)
    {
        // section 127-0-1-1-c213658:12568a3be0b:-8000:0000000000001CED begin
		
		$this->values = $values;
		
        // section 127-0-1-1-c213658:12568a3be0b:-8000:0000000000001CED end
    }

} /* end of abstract class tao_helpers_form_elements_MultipleElement */

?>