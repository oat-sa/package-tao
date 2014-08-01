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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */

/**
 * Short description of class wfAuthoring_actions_form_validators_VariableCode
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package wfAuthoring
 
 */
class wfAuthoring_actions_form_validators_VariableCode
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
		
		if(isset($this->options['uri'])){
    		$this->message = __("Code already used");
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
		if(isset($this->options['uri'])){
			$variableService = wfEngine_models_classes_VariableService::singleton();
			$processVar = $variableService->getProcessVariable($values);
			if(!is_null($processVar)) {
				if ($this->options['uri'] != $processVar->getUri()) {
					$returnValue = false;
				}
			}
		}
		
        

        return (bool) $returnValue;
    }

} /* end of class wfAuthoring_actions_form_validators_VariableCode */

?>