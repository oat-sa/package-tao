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
 * Short description of class tao_helpers_form_xhtml_Form
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 
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

        
		foreach($this->elements as $element){
			if(empty($groupName)
					|| !isset($this->groups[$groupName])
					|| in_array($element->getName(), $this->groups[$groupName]['elements'])) {
				
				$returnValue[tao_helpers_Uri::decode($element->getName())] = $element->getEvaluatedValue();
			}
		}
		unset($returnValue['uri']);
		unset($returnValue['classUri']);
        

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
        
		
		$this->initElements();
		
		if(isset($_POST["{$this->name}_sent"])){
			
			$this->submited = true;
			
			//set posted values
			foreach($this->elements as $id => $element){
				$this->elements[$id]->feed();
			}
			
			$this->validate();
			
		}
			
        
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
		
		$returnValue .= "<input type='hidden' class='global' name='{$this->name}_sent' value='1' />\n";
		
		
		//$returnValue .= $this->renderActions('top');
		
		if(!empty($this->error)){
			$returnValue .= '<div class="xhtml_form_error">'.$this->error.'</div>';
		}
		
		$returnValue .= $this->renderElements();
		
		$returnValue .= $this->renderActions('bottom');
		
		$returnValue .= "</form>\n";
        $returnValue .= "</div>\n";
		
        

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

        
		
		$this->valid = true;
		
		foreach($this->elements as $element){
			if(!$element->validate()){
				$this->valid = false;
			}
		}
		
        

        return (bool) $returnValue;
    }

}