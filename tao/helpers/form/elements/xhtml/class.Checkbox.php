<?php
/*  
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
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - tao/helpers/form/elements/xhtml/class.Checkbox.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 02.01.2012, 11:12:09 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements_xhtml
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_helpers_form_elements_Checkbox
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/helpers/form/elements/class.Checkbox.php');

/* user defined includes */
// section 127-0-1-1-3ed01c83:12409dc285c:-8000:00000000000019EC-includes begin
// section 127-0-1-1-3ed01c83:12409dc285c:-8000:00000000000019EC-includes end

/* user defined constants */
// section 127-0-1-1-3ed01c83:12409dc285c:-8000:00000000000019EC-constants begin
// section 127-0-1-1-3ed01c83:12409dc285c:-8000:00000000000019EC-constants end

/**
 * Short description of class tao_helpers_form_elements_xhtml_Checkbox
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements_xhtml
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
        // section 127-0-1-1-7109ddcd:1344660e25c:-8000:0000000000003486 begin
		$expression = "/^".preg_quote($this->name, "/")."(.)*[0-9]+$/";
    	$this->setValues(array());
		foreach($_POST as $key => $value){
			if(preg_match($expression, $key)){
				$this->addValue(tao_helpers_Uri::decode($value));
			}
		}
        // section 127-0-1-1-7109ddcd:1344660e25c:-8000:0000000000003486 end
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

        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:00000000000019FC begin
		
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
		
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:00000000000019FC end

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
        // section 127-0-1-1-bed3971:124720c750d:-8000:0000000000001A9A begin
		$this->addValue($value);
        // section 127-0-1-1-bed3971:124720c750d:-8000:0000000000001A9A end
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
        // section 127-0-1-1--19ea91f3:1349db91b83:-8000:000000000000349E begin
        return array_map("tao_helpers_Uri::decode", $this->getValues());
        //return array_map("tao_helpers_Uri::decode", $this->getRawValue());
        // section 127-0-1-1--19ea91f3:1349db91b83:-8000:000000000000349E end
    }

} /* end of class tao_helpers_form_elements_xhtml_Checkbox */

?>