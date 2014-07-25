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
 * TAO - tao/helpers/form/xhtml/class.Form.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 08.02.2011, 17:25:47 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage helpers_form_xhtml
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Represents a form. It provides the default behavior for form management and
 * be overridden for any rendering mode.
 * A form is composed by a set of FormElements.
 *
 * The form data flow is:
 * 1. add the elements to the form instance
 * 2. run evaluate (initElements, update states (submited, valid, etc), update
 * )
 * 3. render form
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/helpers/form/class.Form.php');

/* user defined includes */
// section 127-0-1-1--54ddf4d1:12404ee79c9:-8000:00000000000018EF-includes begin
// section 127-0-1-1--54ddf4d1:12404ee79c9:-8000:00000000000018EF-includes end

/* user defined constants */
// section 127-0-1-1--54ddf4d1:12404ee79c9:-8000:00000000000018EF-constants begin
// section 127-0-1-1--54ddf4d1:12404ee79c9:-8000:00000000000018EF-constants end

/**
 * Short description of class tao_helpers_form_xhtml_Form
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage helpers_form_xhtml
 */
class tao_helpers_form_xhtml_Form
    extends tao_helpers_form_Form
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getValues
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string groupName
     * @return array
     */
    public function getValues($groupName = '')
    {
        $returnValue = array();

        // section 127-0-1-1-4c3c2ff9:1242ef00aa7:-8000:0000000000001A1A begin
		foreach($this->elements as $element){
			if(empty($groupName)
					|| !isset($this->groups[$groupName])
					|| in_array($element->getName(), $this->groups[$groupName]['elements'])) {
				
				$returnValue[tao_helpers_Uri::decode($element->getName())] = $element->getEvaluatedValue();
			}
		}
		unset($returnValue['uri']);
		unset($returnValue['classUri']);
        // section 127-0-1-1-4c3c2ff9:1242ef00aa7:-8000:0000000000001A1A end

        return (array) $returnValue;
    }

    /**
     * Short description of method evaluate
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function evaluate()
    {
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001A33 begin
		
		$this->initElements();
		
		if(isset($_POST["{$this->name}_sent"])){
			
			$this->submited = true;
			
			//set posted values
			foreach($this->elements as $id => $element){
				$this->elements[$id]->feed();
			}
			
			$this->validate();
			
		}
			
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001A33 end
    }

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

        // section 127-0-1-1--54ddf4d1:12404ee79c9:-8000:00000000000018F0 begin
		
		(strpos($_SERVER['REQUEST_URI'], '?') > 0) ? $action = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?')) : $action = $_SERVER['REQUEST_URI'];
		
		// Defensive code, prevent double leading slashes issue.
		if (substr($action, 0, 2) == '//'){
			$action = substr($action, 1);
		}
		
		$returnValue .= "<div class='xhtml_form'>\n";
		
		$returnValue .= "<form method='post' id='{$this->name}' name='{$this->name}' action='$action' ";
		if($this->hasFileUpload()){
			$returnValue .= "enctype='multipart/form-data' ";
		}
		$returnValue .= ">\n";
		
		$returnValue .= "<input type='hidden' name='{$this->name}_sent' value='1' />\n";
		
		
		$returnValue .= $this->renderActions('top');
		
		if(!empty($this->error)){
			$returnValue .= '<div class="xhtml_form_error ui-state-error ui-corner-all">'.$this->error.'</div>';
		}
		
		$returnValue .= $this->renderElements();
		
		$returnValue .= $this->renderActions('bottom');
		
		$returnValue .= "</form>\n";
        $returnValue .= "</div>\n";
		
        // section 127-0-1-1--54ddf4d1:12404ee79c9:-8000:00000000000018F0 end

        return (string) $returnValue;
    }

    /**
     * Short description of method validate
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return boolean
     */
    protected function validate()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7ebefbff:12428eef00b:-8000:00000000000019E2 begin
		
		$this->valid = true;
		
		foreach($this->elements as $element){
			if(!$element->validate()){
				$this->valid = false;
			}
		}
		
        // section 127-0-1-1-7ebefbff:12428eef00b:-8000:00000000000019E2 end

        return (bool) $returnValue;
    }

} /* end of class tao_helpers_form_xhtml_Form */

?>
