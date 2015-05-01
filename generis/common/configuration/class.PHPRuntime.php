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
 * Short description of class common_configuration_PHPRuntime
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package generis
 
 */
class common_configuration_PHPRuntime
    extends common_configuration_BoundableComponent
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

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
        $message = null;
        $min = $this->getMin();
        $max = $this->getMax();
        $current = $this->getValue();
        
        if (!empty($min) && !empty($max)){
            // min & max are specifed.
            if (version_compare($current, $min, '>=') && version_compare($current, $max, '<=')){
                $validity = common_configuration_Report::VALID;
                $message = "PHP Version (${current}) is between ${min} and ${max}.";
            }
            else {
                $validity = common_configuration_Report::INVALID;
                $message = "PHP Version (${current} is not between ${min} and ${max}.)";
            }
        }
        else if (!empty($min) && empty($max)){
            if (version_compare($current, $min, '>=')){
                $validity = common_configuration_Report::VALID;
                $message = "PHP Version (${current}) is higher or equal to ${min}.";
            }
            else{
                $validity = common_configuration_Report::INVALID;
                $message = "PHP Version (${current}) is lower than ${min}.";
            } 
        }
        else if (empty($min) && !empty($max)){
            if (version_compare($current, $max, '<=')){
                $validity = common_configuration_Report::VALID;
                $message = "PHP Version (${current}) is lesser than ${max}.";
            }
            else{
                $validity = common_configuration_Report::INVALID;
                $message = "PHP Version (${current}) is greater than ${max}.";
            }
        }
        
        $returnValue = new common_configuration_Report($validity, $message, $this);
        

        return $returnValue;
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

        
        $returnValue = phpversion();
        

        return (string) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string min
     * @param  string max
     * @param  boolean optional
     * @return mixed
     */
    public function __construct($min, $max, $optional = false)
    {
        
        parent::__construct($min, $max, 'tao.configuration.phpruntime', $optional);
        
    }

}