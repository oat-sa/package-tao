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
 * Short description of class tao_helpers_form_validators_Md5Password
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 
 */
class tao_helpers_form_validators_Md5Password
    extends tao_helpers_form_validators_Password
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getValue
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  values
     * @return string
     */
    public function getValue($values)
    {
        $returnValue = (string) '';

        
		
		$returnValue = md5(parent::getRawValue());
		
        

        return (string) $returnValue;
    }

    /**
     * Short description of method evaluate
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  values
     * @return boolean
     */
    public function evaluate($values)
    {
        $returnValue = (bool) false;

        
    	if (is_array($values) && count($values) == 2) {
        	list($first, $second) = $values;
        	$returnValue = $first == $second;
        	
        } elseif (isset($this->options['password2_ref'])) {
			$secondElement = $this->options['password2_ref'];
			if (is_null($secondElement) || ! $secondElement instanceof tao_helpers_form_FormElement) {
				throw new common_Exception("Please set the reference of the second password element");
			}
			if(md5($values) == $secondElement->getRawValue() && trim($values) != ''){
				$returnValue = true;
			}
        }
        

        return (bool) $returnValue;
    }

}

?>