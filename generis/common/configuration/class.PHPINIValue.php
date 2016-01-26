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
 * Short description of class common_configuration_PHPINIValue
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package generis
 
 */
class common_configuration_PHPINIValue
    extends common_configuration_Component
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute expectedValue
     *
     * @access private
     * @var string
     */
    private $expectedValue = '';

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string expectedValue
     * @param  string name
     * @param  boolean optional
     * @return mixed
     */
    public function __construct($expectedValue, $name = 'unknown', $optional = false)
    {
        
        parent::__construct($name, $optional);
        $this->setExpectedValue($expectedValue);
        
    }

    /**
     * Short description of method check
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return common_configuration_Report
     */
    public function check()
    {
        $returnValue = null;

        
        $validity = null;
        $name = $this->getName();
        if (($value = ini_get($name)) !== false){
            // The ini value exists for this name.
            if ((($value == '') ? '0' : $value) == $this->getExpectedValue()){
                $validity = common_configuration_Report::VALID;
                $message = "PHP Configuration Option '${name}' = '${value}' has an expected value.";
            }
            else {
                $validity = common_configuration_Report::INVALID;
                $message = "PHP Configuration Option '${name}' = '${value}' has an unexpected value.";
            }
        }
        else {
            // Unknown ini value name.
            $validity = common_configuration_Report::UNKNOWN;
            $message = "PHP Configuration Option '${name}' is unknown.";
        }

        $returnValue = new common_configuration_Report($validity, $message, $this);
        

        return $returnValue;
    }

    /**
     * Short description of method getExpectedValue
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getExpectedValue()
    {
        $returnValue = (string) '';

        
        return $this->expectedValue;
        

        return (string) $returnValue;
    }

    /**
     * Short description of method setExpectedValue
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string expectedValue
     * @return mixed
     */
    public function setExpectedValue($expectedValue)
    {
        
        $this->expectedValue = $expectedValue;
        
    }

    /**
     * Short description of method getValue
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getValue()
    {
        $returnValue = (string) '';

        
        $returnValue = ini_get($this->getName());
        

        return (string) $returnValue;
    }

}