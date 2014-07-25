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
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 22.12.2009, 16:53:45 with ArgoUML PHP module 
 * (last revised $Date: 2009-04-11 21:57:46 +0200 (Sat, 11 Apr 2009) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements_xhtml
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_helpers_form_elements_Htmlarea
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/helpers/form/elements/class.Htmlarea.php');

/* user defined includes */
// section 127-0-1-1-3ed01c83:12409dc285c:-8000:00000000000019EA-includes begin
// section 127-0-1-1-3ed01c83:12409dc285c:-8000:00000000000019EA-includes end

/* user defined constants */
// section 127-0-1-1-3ed01c83:12409dc285c:-8000:00000000000019EA-constants begin
// section 127-0-1-1-3ed01c83:12409dc285c:-8000:00000000000019EA-constants end

/**
 * Short description of class tao_helpers_form_elements_xhtml_Htmlarea
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements_xhtml
 */
class tao_helpers_form_elements_xhtml_Htmlarea
    extends tao_helpers_form_elements_Htmlarea
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute CSS_CLASS
     *
     * @access public
     * @var string
     */
    const CSS_CLASS = 'html-area';

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

        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:00000000000019F4 begin

		if(array_key_exists('class', $this->attributes)){
			if(strstr($this->attributes['class'], self::CSS_CLASS) !== false){
				$this->attributes['class'] .= ' ' . self::CSS_CLASS;
			}
		}
		else{
			$this->attributes['class'] = self::CSS_CLASS;
		}
		 
		 if(!isset($this->attributes['noLabel'])){
			$returnValue .= "<label class='form_desc' for='{$this->name}'>". _dh($this->getDescription())."</label>";
		}
		else{
			unset($this->attributes['noLabel']);
		}
		$returnValue .= "<textarea name='{$this->name}' id='{$this->name}' ";
		$returnValue .= $this->renderAttributes();
		$returnValue .= ">"._dh($this->value)."</textarea>";
		
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:00000000000019F4 end

        return (string) $returnValue;
    }

} /* end of class tao_helpers_form_elements_xhtml_Htmlarea */

?>