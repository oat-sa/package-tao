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
 * Short description of class tao_helpers_form_validators_Length
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 
 */
class tao_helpers_form_validators_Length
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
		
		if( isset($this->options['min']) && isset($this->options['max']) ){
			$this->message = __('Invalid field length')." (minimum ".$this->options['min'].", maximum ".$this->options['max'].")";
		}
		else if( isset($this->options['min']) && !isset($this->options['max']) ){
			$this->message = __('This field is too short')." (minimum ".$this->options['min'].")";
		}
		else if( !isset($this->options['min']) && isset($this->options['max']) ){
			$this->message = __('This field is too long')." (maximum ".$this->options['max'].")";
		}
		else{
			throw new Exception("Please set 'min' and/or 'max' options!");
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

        
        $returnValue = true;
        
		$values = is_array($values) ? $values : array($values);
		foreach ($values as $value) {
			if (isset($this->options['min']) && mb_strlen($value) < $this->options['min']) {
				if (isset($this->options['allowEmpty']) &&  $this->options['allowEmpty'] && empty($value)) {
					continue;
				} else {
					$returnValue = false;
					break;
				}
			}
			if (isset($this->options['max']) && mb_strlen($value) > $this->options['max']) {
				$returnValue = false;
				break;
			}
		}
        

        return (bool) $returnValue;
    }

}

?>