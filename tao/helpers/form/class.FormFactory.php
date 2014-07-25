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
 * The FormFactory enable you to create ready-to-use instances of the Form
 * It helps you to get the commonly used instances for the default rendering
 * etc.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage helpers_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1--35d6051a:124bac7a23e:-8000:0000000000001B16-includes begin
// section 127-0-1-1--35d6051a:124bac7a23e:-8000:0000000000001B16-includes end

/* user defined constants */
// section 127-0-1-1--35d6051a:124bac7a23e:-8000:0000000000001B16-constants begin
// section 127-0-1-1--35d6051a:124bac7a23e:-8000:0000000000001B16-constants end

/**
 * The FormFactory enable you to create ready-to-use instances of the Form
 * It helps you to get the commonly used instances for the default rendering
 * etc.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage helpers_form
 */
class tao_helpers_form_FormFactory
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The rendering mode of the form.
     *
     * @access protected
     * @var string
     */
    protected static $renderMode = 'xhtml';

    // --- OPERATIONS ---

    /**
     * Define the rendering mode
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string renderMode
     * @return mixed
     */
    public static function setRenderMode($renderMode)
    {
        // section 127-0-1-1--4d0d476d:124bee31dc8:-8000:0000000000001B2E begin
		self::$renderMode = $renderMode;
        // section 127-0-1-1--4d0d476d:124bee31dc8:-8000:0000000000001B2E end
    }

    /**
     * Factors an instance of Form
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string name
     * @param  array options
     * @return tao_helpers_form_Form
     */
    public static function getForm($name = '', $options = array())
    {
        $returnValue = null;

        // section 127-0-1-1--35d6051a:124bac7a23e:-8000:0000000000001B17 begin
		
		//use the right implementation (depending the render mode)
		//@todo refactor this and use a FormElementFactory
		switch(self::$renderMode){
			case 'xhtml':
				
				$myForm = new tao_helpers_form_xhtml_Form($name, $options);
				
				$myForm->setDecorators(array(
					'element'			=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div')),
					'group'				=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-group')),
					'error'				=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-error ui-state-error ui-corner-all')),
					'actions-bottom'	=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-toolbar')),
					'actions-top'		=> new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div', 'cssClass' => 'form-toolbar'))
				));
				
				$myForm->setActions(self::getCommonActions(), 'bottom');
				
				break;
			
			default: 
				throw new common_Exception("render mode {self::$renderMode} not yet supported");
		}
		
		$returnValue = $myForm;
		
        // section 127-0-1-1--35d6051a:124bac7a23e:-8000:0000000000001B17 end

        return $returnValue;
    }

    /**
     * Create dynamically a Form Element instance of the defined type
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string name
     * @param  string type
     * @return tao_helpers_form_FormElement
     */
    public static function getElement($name = '', $type = '')
    {
        $returnValue = null;

        // section 127-0-1-1--35d6051a:124bac7a23e:-8000:0000000000001B21 begin
		
		$eltClass = false;
		
		switch(self::$renderMode){
			case 'xhtml':
				$eltClass = "tao_helpers_form_elements_xhtml_{$type}";

				if(!class_exists($eltClass)){
					common_Logger::w("type ".$type." not yet supported", array('FORM'));
					//throw new Exception("type $type not yet supported");
					return null;
				}
				break;
			default: 
				throw new Exception("render mode {self::$renderMode} not yet supported");
		}
		if($eltClass){
			$returnValue = new $eltClass($name);
			
			if(!$returnValue instanceof tao_helpers_form_FormElement){
				throw new common_Exception("$eltClass must be a tao_helpers_form_FormElement");
			}
		}

        // section 127-0-1-1--35d6051a:124bac7a23e:-8000:0000000000001B21 end

        return $returnValue;
    }

    /**
     * Get an instance of a Validator
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string name
     * @param  array options
     * @return tao_helpers_form_Validator
     */
    public static function getValidator($name, $options = array())
    {
        $returnValue = null;

        // section 127-0-1-1-34d7bcb9:1250bcb34b1:-8000:0000000000001BD2 begin
		
		$clazz = 'tao_helpers_form_validators_'.$name;
		if(class_exists($clazz)){
			$returnValue = new $clazz($options);
		} else {
			common_Logger::w('Unknown validator '.$name, array('TAO', 'FORM'));
		}
		
        // section 127-0-1-1-34d7bcb9:1250bcb34b1:-8000:0000000000001BD2 end

        return $returnValue;
    }

    /**
     * Get the common actions: save and revert
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string context
     * @param  boolean save
     * @return array
     */
    public static function getCommonActions($context = 'bottom', $save = true)
    {
        $returnValue = array();

        // section 127-0-1-1--792ca639:1268e3f82cb:-8000:0000000000001E6C begin
		
		switch($context){
			
			case 'top':
			case 'bottom':
			default:
				$actions = tao_helpers_form_FormFactory::getElement('save', 'Free');
				$value = '';
				if($save){
					$value .=  "<a href='#' class='form-submiter' ><img src='".TAOBASE_WWW."/img/save.png' /> ".__('Save')."</a>";
				}
					
				$actions->setValue($value);
				$returnValue[] = $actions;
				break;
		}
		
        // section 127-0-1-1--792ca639:1268e3f82cb:-8000:0000000000001E6C end

        return (array) $returnValue;
    }

} /* end of class tao_helpers_form_FormFactory */

?>