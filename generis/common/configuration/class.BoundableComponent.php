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
 * Short description of class common_configuration_BoundableComponent
 *
 * @abstract
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package generis
 
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
        
        parent::__construct($name, $optional);
        $this->setMin($min);
        $this->setMax($max);
        
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
        
        $this->min = $min;
        
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

        
        return $this->min;
        

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
        
    	// Support .x notation.
    	if (!empty($max)){
        	$this->max = preg_replace('/x/u', '99999', $max);
    	}
    	else{
    		$this->max = null;
    	}
        
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

        
        return $this->max;
        

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