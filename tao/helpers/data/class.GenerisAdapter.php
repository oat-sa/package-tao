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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * This class enables you to manage interfaces with data. 
 * It provides the default prototype to adapt the data import/export from/to any
 * format.
 *
 * @abstract
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 
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
        
        
    	$this->options = $options;
    	
        
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

        
        
        $returnValue = $this->options;
        
        

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
        
        
    	$this->options = $options;
    	
        
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
        
        
    	$this->options[$name] = $value;
    	
        
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