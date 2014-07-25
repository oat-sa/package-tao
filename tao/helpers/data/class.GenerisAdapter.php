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
 * This class enables you to manage interfaces with data. 
 * It provides the default prototype to adapt the data import/export from/to any
 * format.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage helpers_data
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1--464fd80f:12545a0876a:-8000:0000000000001C8F-includes begin
// section 127-0-1-1--464fd80f:12545a0876a:-8000:0000000000001C8F-includes end

/* user defined constants */
// section 127-0-1-1--464fd80f:12545a0876a:-8000:0000000000001C8F-constants begin
// section 127-0-1-1--464fd80f:12545a0876a:-8000:0000000000001C8F-constants end

/**
 * This class enables you to manage interfaces with data. 
 * It provides the default prototype to adapt the data import/export from/to any
 * format.
 *
 * @abstract
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage helpers_data
 */
abstract class tao_helpers_data_GenerisAdapter
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

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array options
     * @return mixed
     */
    public function __construct($options = array())
    {
        // section 127-0-1-1--464fd80f:12545a0876a:-8000:0000000000001C97 begin
        
    	$this->options = $options;
    	
        // section 127-0-1-1--464fd80f:12545a0876a:-8000:0000000000001C97 end
    }

    /**
     * get the adapter options
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public function getOptions()
    {
        $returnValue = array();

        // section 127-0-1-1-10bf4933:12549adcf20:-8000:0000000000001CC0 begin
        
        $returnValue = $this->options;
        
        // section 127-0-1-1-10bf4933:12549adcf20:-8000:0000000000001CC0 end

        return (array) $returnValue;
    }

    /**
     * set the adapter options
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array options
     * @return mixed
     */
    public function setOptions($options = array())
    {
        // section 127-0-1-1-10bf4933:12549adcf20:-8000:0000000000001CC3 begin
        
    	$this->options = $options;
    	
        // section 127-0-1-1-10bf4933:12549adcf20:-8000:0000000000001CC3 end
    }

    /**
     * add a new option
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string name
     * @param  value
     * @return mixed
     */
    public function addOption($name, $value)
    {
        // section 127-0-1-1--250780b8:12843f3062f:-8000:0000000000002412 begin
        
    	$this->options[$name] = $value;
    	
        // section 127-0-1-1--250780b8:12843f3062f:-8000:0000000000002412 end
    }

    /**
     * import prototype: import the source into the destination class
     *
     * @abstract
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string source
     * @param  Class destination
     * @return boolean
     */
    public abstract function import($source,  core_kernel_classes_Class $destination = null);

    /**
     * export prototype: export the source class
     *
     * @abstract
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Class source
     * @return string
     */
    public abstract function export( core_kernel_classes_Class $source = null);

} /* end of abstract class tao_helpers_data_GenerisAdapter */

?>