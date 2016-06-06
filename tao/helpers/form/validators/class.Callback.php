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
 * Short description of class tao_helpers_form_validators_Callback
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 
 */
class tao_helpers_form_validators_Callback
    extends tao_helpers_form_Validator
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    public function setOptions(array $options)
    {
        parent::setOptions($options);

        if(!isset($this->options['function'])
            && !((isset($this->options['class']) || isset($this->options['object']))
                && isset($this->options['method']))
        ){
            throw new Exception("Please define a callback function or method");
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

        
		
		if(isset($this->options['function'])){
			$function = $this->options['function'];
			if(function_exists($function)){
				$callback = array($function);
			} else {
				throw new common_Exception("callback function does not exist");
			}
		}
		else if(isset($this->options['class'])){
			$class = $this->options['class'];
			$method = $this->options['method'];
			if(class_exists($class) && method_exists($class, $method)){
					$callback = array($class, $method);
				} else {
					throw new common_Exception("callback methode does not exist");
			}
		}
		else if(isset($this->options['object'])){
			$object = $this->options['object'];
			$method = $this->options['method'];
			if(method_exists($object, $method)){
				$callback = array($object, $method);
			} else {
				throw new common_Exception("callback methode does not exist");
			}
		}
		if (isset($this->options['param'])) {
			$returnValue = (bool)call_user_func($callback, $values, $this->options['param']);
		} else {
			$returnValue = (bool)call_user_func($callback, $values);
		}
        

        return (bool) $returnValue;
    }

}

?>