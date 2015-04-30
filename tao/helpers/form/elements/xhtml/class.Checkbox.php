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
 * Short description of class tao_helpers_form_elements_xhtml_Checkbox
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 
 */
class tao_helpers_form_elements_xhtml_Checkbox
    extends tao_helpers_form_elements_Checkbox
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method feed
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function feed()
    {
        
		$expression = "/^".preg_quote($this->name, "/")."(.)*[0-9]+$/";
    	$this->setValues(array());
		foreach($_POST as $key => $value){
			if(preg_match($expression, $key)){
				$this->addValue(tao_helpers_Uri::decode($value));
			}
		}
        
    }

    /**
     * Short description of method render
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function render()
    {
        $returnValue = (string) '';

        
		
		$i = 0;
		if(!isset($this->attributes['noLabel'])){
			$returnValue .= "<span class='form_desc'>"._dh($this->getDescription())."</span>";
		}
		else{
			unset($this->attributes['noLabel']);
		}
		$checkAll = false;
		if(isset($this->attributes['checkAll'])){
			$checkAll = (bool)$this->attributes['checkAll'];
			unset($this->attributes['checkAll']);
		}
		$checked = 0;
		$returnValue .= '<div class="form_radlst">';
		foreach($this->options as $optionId => $optionLabel){
			 $returnValue .= "<input type='checkbox' value='{$optionId}' name='{$this->name}_{$i}' id='{$this->name}_{$i}' ";
			 $returnValue .= $this->renderAttributes();
			
			 if(in_array($optionId, $this->values)){
			 	$returnValue .= " checked='checked' ";	
			 	$checked++;
			 }
			 $returnValue .= " />&nbsp;<label class='elt_desc' for='{$this->name}_{$i}'>"._dh($optionLabel)."</label><br />";
			 $i++;
		}
		$returnValue .= "</div>";
		
		//add a small link 
		if($checkAll){
			if($checked == count($this->options)){
				$returnValue .= "<span class='checker-container'><a id='{$this->name}_checker' class='box-checker box-checker-uncheck' href='#'>".__('Uncheck All')."</a></span>";
			}
			else{
				$returnValue .= "<span class='checker-container'><a id='{$this->name}_checker' class='box-checker' href='#'>".__('Check All')."</a></span>";
			}
		}
		
        

        return (string) $returnValue;
    }

    /**
     * Short description of method setValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string value
     * @return mixed
     */
    public function setValue($value)
    {
        
		$this->addValue($value);
        
    }

    /**
     * Short description of method getEvaluatedValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function getEvaluatedValue()
    {
        
        return array_map("tao_helpers_Uri::decode", $this->getValues());
        //return array_map("tao_helpers_Uri::decode", $this->getRawValue());
        
    }

}

?>