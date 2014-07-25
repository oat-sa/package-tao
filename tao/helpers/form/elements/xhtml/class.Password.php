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
 * TAO - tao/helpers/form/elements/xhtml/class.Password.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 05.01.2012, 12:00:53 with ArgoUML PHP module 
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
 * include tao_helpers_form_elements_Password
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/helpers/form/elements/class.Password.php');

/* user defined includes */
// section 127-0-1-1-750eebc6:13440d254e0:-8000:0000000000006081-includes begin
// section 127-0-1-1-750eebc6:13440d254e0:-8000:0000000000006081-includes end

/* user defined constants */
// section 127-0-1-1-750eebc6:13440d254e0:-8000:0000000000006081-constants begin
// section 127-0-1-1-750eebc6:13440d254e0:-8000:0000000000006081-constants end

/**
 * Short description of class tao_helpers_form_elements_xhtml_Password
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements_xhtml
 */
class tao_helpers_form_elements_xhtml_Password
    extends tao_helpers_form_elements_Password
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method feed
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function feed()
    {
        // section 127-0-1-1-750eebc6:13440d254e0:-8000:0000000000006086 begin
        
    	if (isset($_POST[$this->name]) && is_array($_POST[$this->name])) {
    		$this->setValue(array_values($_POST[$this->name]));
		}
        // section 127-0-1-1-750eebc6:13440d254e0:-8000:0000000000006086 end
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

        // section 127-0-1-1-750eebc6:13440d254e0:-8000:0000000000006083 begin
        if(!isset($this->attributes['noLabel'])){
			$returnValue .= "<label class='form_desc' for='{$this->name}'>". _dh($this->getDescription()).
			(strlen($this->value) == 0 ? '' : ' (change)').
			"</label>";
		}
		else{
			unset($this->attributes['noLabel']);
		}
		$returnValue .= "<input type='password' name='{$this->name}[]' id='{$this->name}' ";
		$returnValue .= $this->renderAttributes();
		$returnValue .= ' value=""  /><br /><br />';
		$returnValue .= "<label class='form_desc'></label>";
		$returnValue .= "<input type='password' name='{$this->name}[]' id='{$this->name}' ";
		$returnValue .= $this->renderAttributes();
		$returnValue .= ' value=""  />';
        // section 127-0-1-1-750eebc6:13440d254e0:-8000:0000000000006083 end

        return (string) $returnValue;
    }

    /**
     * returns the md5 hash of the password
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function getEvaluatedValue()
    {
        $returnValue = null;

        // section 127-0-1-1--78214b38:13447752615:-8000:0000000000003488 begin
    	$arr = $this->getRawValue();
    	$returnValue = md5(array_shift($arr));
        // section 127-0-1-1--78214b38:13447752615:-8000:0000000000003488 end

        return $returnValue;
    }

} /* end of class tao_helpers_form_elements_xhtml_Password */

?>