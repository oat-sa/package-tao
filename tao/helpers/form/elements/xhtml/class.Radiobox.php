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
 * Short description of class tao_helpers_form_elements_xhtml_Radiobox
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 
 */
class tao_helpers_form_elements_xhtml_Radiobox
    extends tao_helpers_form_elements_Radiobox
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method render
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function render()
    {
        $returnValue = (string) '';

        
		
		$i = 0;
		
		if(!isset($this->attributes['noLabel'])){
			$returnValue .= "<span class='form_desc'>". _dh($this->getDescription())."</span>";
		}
		else{
			unset($this->attributes['noLabel']);
		}
		$returnValue .= '<div class="form_radlst">';
		foreach($this->options as $optionId => $optionLabel){
			 $returnValue .= "<input type='radio' name='{$this->name}' id='{$this->name}_{$i}' value='{$optionId}' ";
			 $returnValue .= $this->renderAttributes();
			 if($this->value == $optionId){
			 	$returnValue .= " checked='checked' ";
			 }
			 $returnValue .= " /><label class='elt_desc' for='{$this->name}_{$i}'>"._dh($optionLabel)."</label><br />";
			 $i++;
		}
		$returnValue .= "</div>";
		
        

        return (string) $returnValue;
    }

}

?>