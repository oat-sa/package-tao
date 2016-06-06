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
 * Short description of class tao_helpers_form_validators_FileSize
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 
 */
class tao_helpers_form_validators_FileSize
    extends tao_helpers_form_Validator
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---
    public function setOptions(array $options)
    {
        parent::setOptions($options);

        if (isset($this->options['min']) && isset($this->options['max']) ) {
            $this->setMessage(__('Invalid file size (minimum %1$s bytes, maximum %2$s bytes)', $this->options['min'], $this->options['max']));
        } elseif (isset($this->options['max'])) {
            $this->setMessage(__('The uploaded file is too large (maximum %s bytes)', $this->options['max']));
            $this->options['min'] = 0;
        } else {
            throw new common_Exception("Please set 'min' and/or 'max' options!");
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

        
		
		if(is_array($values)){
			if(isset($values['size'])){
				if($values['size'] >= $this->options['min'] && $values['size'] <= $this->options['max']){
					$returnValue = true;
				}
			}else{
				$returnValue = true;
			}
		}else{
			$returnValue = true;
		}
		
        

        return (bool) $returnValue;
    }

}

?>