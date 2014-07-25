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
 * TAO - tao\helpers\form\elements\xhtml\class.Readonly.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 19.05.2011, 15:13:39 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements_xhtml
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_helpers_form_elements_Readonly
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('tao/helpers/form/elements/class.Readonly.php');

/* user defined includes */
// section 10-13-1--128-66e372da:130084c89b7:-8000:0000000000002E48-includes begin
// section 10-13-1--128-66e372da:130084c89b7:-8000:0000000000002E48-includes end

/* user defined constants */
// section 10-13-1--128-66e372da:130084c89b7:-8000:0000000000002E48-constants begin
// section 10-13-1--128-66e372da:130084c89b7:-8000:0000000000002E48-constants end

/**
 * Short description of class tao_helpers_form_elements_xhtml_Readonly
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements_xhtml
 */
class tao_helpers_form_elements_xhtml_Readonly
    extends tao_helpers_form_elements_Readonly
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method render
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return string
     */
    public function render()
    {
        $returnValue = (string) '';

        // section 10-13-1--128-66e372da:130084c89b7:-8000:0000000000002E49 begin
		if(!isset($this->attributes['noLabel'])){
			$returnValue .= "<span class='form_desc'>"._dh($this->getDescription())."</span>";
		}
		else{
			unset($this->attributes['noLabel']);
		}
		
		$returnValue .= "<input type='text' readonly='readonly' disabled='disabled' name='{$this->name}' id='{$this->name}' ";
		$returnValue .= $this->renderAttributes();
		$returnValue .= ' value="'._dh($this->value).'"  />';
        // section 10-13-1--128-66e372da:130084c89b7:-8000:0000000000002E49 end

        return (string) $returnValue;
    }

} /* end of class tao_helpers_form_elements_xhtml_Readonly */

?>