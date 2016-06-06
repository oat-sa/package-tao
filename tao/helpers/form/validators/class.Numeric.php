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
 * 
 * The validators enable you to perform a validation callback on a form element.
 * It's provide a model of validation and must be overriden.
 *
 * @author Jehan Bihin, <jehan.bihin@tudor.lu>
 * @package tao
 
 */
class tao_helpers_form_validators_Numeric extends tao_helpers_form_Validator
{

    /**
     * Short description of method evaluate
     *
     * @access public
     * @author Jehan Bihin, <jehan.bihin@tudor.lu>
     * @param
     *            values
     * @return boolean
     */
    public function evaluate($values)
    {
        $returnValue = (bool) false;
        
        $rowValue = $values;
        $value = tao_helpers_Numeric::parseFloat($rowValue);
        if (empty($rowValue)) {
            $returnValue = true; // no need to go further. To check if not empty, use the NotEmpty validator
            return $returnValue;
        }
        if (! is_numeric($rowValue) || $value != $rowValue) {
            $this->setMessage(__('The value of this field must be numeric'));
            $returnValue = false;
        } else {
            if (isset($this->options['min']) || isset($this->options['max'])) {
                
                if (isset($this->options['min']) && isset($this->options['max'])) {
                    
                    if ($this->options['min'] <= $value && $value <= $this->options['max']) {
                        $returnValue = true;
                    } else {
                        $this->setMessage(__('Invalid field range (minimum value: %1$s, maximum value: %2$s)', $this->options['min'], $this->options['max']));
                    }
                } elseif (isset($this->options['min']) && ! isset($this->options['max'])) {
                    if ($this->options['min'] <= $value) {
                        $returnValue = true;
                    } else {
                        $this->setMessage(__('Invalid field range (minimum value: %s)',$this->options['min']));
                    }
                } elseif (! isset($this->options['min']) && isset($this->options['max'])) {
                    if ($value <= $this->options['max']) {
                        $returnValue = true;
                    } else {
                        $this->setMessage(__('Invalid field range (maximum value: %s)', $this->options['max']));
                    }
                }
            } else {
                $returnValue = true;
            }
        }
        
        // Test less, greater, equal to another
        if ($returnValue && isset($this->options['integer2_ref']) && $this->options['integer2_ref'] instanceof tao_helpers_form_FormElement) {
            $secondElement = $this->options['integer2_ref'];
            switch ($this->options['comparator']) {
                case '>':
                case 'sup':
                    if ($value > $secondElement->getRawValue()) {
                        $returnValue = true;
                    } else {
                        $returnValue = false;
                    }
                    break;
                
                case '<':
                case 'inf':
                    if ($value < $secondElement->getRawValue()) {
                        $returnValue = true;
                    } else {
                        $returnValue = false;
                    }
                    break;
                
                case '=':
                case 'equal':
                    if ($value == $secondElement->getRawValue()) {
                        $returnValue = true;
                    } else {
                        $returnValue = false;
                    }
                    break;
            }
        }
        
        return (bool) $returnValue;
    }
}

?>