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
 * compares two form elements
 * possible options:
 * 'reference' FormElement, the form element to compare to
 * 'invert' boolean, validates only if values are not equal
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 
 */
class tao_helpers_form_validators_Equals
    extends tao_helpers_form_Validator
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array options
     * @return mixed
     */
    public function __construct($options = array())
    {
        
        parent::__construct($options);
    	if(!isset($this->options['reference']) || !$this->options['reference'] instanceof tao_helpers_form_FormElement){
			throw new common_Exception("No FormElement provided as reference for Equals validator");
		}
        $reference = $this->options['reference'];
		if (isset($this->options['invert']) && $this->options['invert']) {
			$this->message = __('This should not equal ').$reference->getDescription();
		} else {
			$this->message = __('This should equal ').$reference->getDescription();
		}
        
    }

    /**
     * Short description of method evaluate
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  values
     * @return boolean
     */
    public function evaluate($values)
    {
        $returnValue = (bool) false;

        
        $invert = isset($this->options['invert']) ? $this->options['invert'] : false;
        $reference = $this->options['reference'];
		$equals = ($values == $reference->getRawValue());
		$returnValue = $invert ? !$equals : $equals;
        

        return (bool) $returnValue;
    }

}

?>