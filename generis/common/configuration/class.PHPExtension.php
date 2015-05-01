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
 * Short description of class common_configuration_PHPExtension
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package generis
 
 */
class common_configuration_PHPExtension
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

        
        $name = $this->getName();
        $min = $this->getMin();
        $max = $this->getMax();
        $validity = null;
        $message = null;
        
        if (extension_loaded($name)){
            $current = $this->getValue();
            
            if (!empty($min) && !empty($max)){
                // Both min and max are specified.
                if (version_compare($current, $min, '>=') && version_compare($current, $max, '<=')){
                    $validity = common_configuration_Report::VALID;
                    $message = "PHP Extension '${name}' version (${current}) is between ${min} and ${max}.";
                }
                else{
                    $validity = common_configuration_Report::INVALID;
                    $message = "PHP Extension '${name}' version (${current}) is not between ${min} and ${max}.";
                }
            }
            else if (!empty($min) && empty($max)){
                // Only min is specified.
                if (version_compare($current, $min, '>=')){
                    $validity = common_configuration_Report::VALID;
                    $message = "PHP Extension '${name}' version (${current}) is greater or equal to ${min}.";
                }
                else{
                    $validity = common_configuration_Report::INVALID;
                    $message = "PHP Extension '${name}' version (${current}) is lesser than ${min}.";
                }
            }
            else if (empty($min) && !empty($max)){
                // Only max is specified.
                if (version_compare($current, $max, '<=')){
                    $validity = common_configuration_Report::VALID;
                    $message = "PHP Extension '${name}' version (${current}) is lesser or equal to ${max}.";
                }
                else{
                    $validity = common_configuration_Report::INVALID;
                    $message = "PHP Extension '${name}' version (${current}) is greater than ${max}.";
                }
            }
            else{
                // No min and max are provided, we just check the
                // existence of the extension (already done).
                $validity = common_configuration_Report::VALID;
                $message = "PHP Extension '${name}' is loaded.";
            }
        }
        else{
            $validity = common_configuration_Report::UNKNOWN;
            $message = "PHP Extension '${name}' could not be found.";
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

        
        $returnValue = phpversion($this->getName());
        

        return (string) $returnValue;
    }

}