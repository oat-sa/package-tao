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
 * Short description of class tao_helpers_form_elements_xhtml_Label
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 
 */
class tao_helpers_form_elements_xhtml_Label
    extends tao_helpers_form_elements_Label
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

        
		
		if(isset($this->attributes['class'])){
			$classes = explode(' ', $this->attributes['class']);
			if(!isset($this->attributes['no-format'])){
				if(!in_array('form-elt-info', $classes)){
					$classes[] = 'form-elt-info';
				}
			}
			if(!in_array('form-elt-container', $classes)){
				$classes[] = 'form-elt-container';
			}
			$this->attributes['class'] = implode(' ', $classes);
		}
		else{
			if(isset($this->attributes['no-format'])){
				$this->attributes['class'] = 'form-elt-container';
			}
			else{
				$this->attributes['class'] = 'form-elt-info form-elt-container';
			}
		}
		unset($this->attributes['no-format']);
		
		$returnValue .= "<span class='form_desc'>";
		if(!empty($this->description)){
			$returnValue .=  _dh($this->getDescription());
		}
		$returnValue .= "</span>";
		$returnValue .= "<span ";
		$returnValue .= $this->renderAttributes();
		$returnValue .= " >";
		$returnValue .= isset($this->attributes['htmlentities']) && !$this->attributes['htmlentities'] ? $this->value : _dh($this->value); 
		$returnValue .= "</span>";
		
        

        return (string) $returnValue;
    }

}

?>