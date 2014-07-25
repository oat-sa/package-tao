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
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package tao
 * @subpackage helpers_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This class provide a container for a specific form instance.
 * It's subclasses instanciate a form and it's elements to be used as a
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('tao/helpers/form/class.FormContainer.php');

/**
 * A decorator is an helper used for aspect oriented rendering.
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('tao/helpers/form/interface.Decorator.php');

/* user defined includes */
// section 10-13-1-45--48e788d1:123dcd97db5:-8000:00000000000018A4-includes begin
// section 10-13-1-45--48e788d1:123dcd97db5:-8000:00000000000018A4-includes end

/* user defined constants */
// section 10-13-1-45--48e788d1:123dcd97db5:-8000:00000000000018A4-constants begin
// section 10-13-1-45--48e788d1:123dcd97db5:-8000:00000000000018A4-constants end

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
 * @abstract
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package tao
 * @subpackage helpers_form
 */
abstract class tao_helpers_form_Form
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd :     // generateAssociationEnd :     // generateAssociationEnd :     // generateAssociationEnd : 1    // generateAssociationEnd :     // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * the form name
     *
     * @access protected
     * @var string
     */
    protected $name = '';

    /**
     * the list of element composing the form
     *
     * @access protected
     * @var array
     */
    protected $elements = array();

    /**
     * the actions of the form by context
     *
     * @access protected
     * @var array
     */
    protected $actions = array();

    /**
     * if the form is valid or not
     *
     * @access public
     * @var boolean
     */
    public $valid = false;

    /**
     * if the form has been submited or not
     *
     * @access protected
     * @var boolean
     */
    protected $submited = false;

    /**
     * It represents the logicall groups
     *
     * @access protected
     * @var array
     */
    protected $groups = array();

    /**
     * The list of Decorator linked to the form
     *
     * @access protected
     * @var array
     */
    protected $decorators = array();

    /**
     * The form's options
     *
     * @access protected
     * @var array
     */
    protected $options = array();

    /**
     * Global form error message
     *
     * @access public
     * @var string
     */
    public $error = '';

    // --- OPERATIONS ---

    /**
     * the form constructor
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string name
     * @param  array options
     * @return mixed
     */
    public function __construct($name = '', $options = array())
    {
        // section 127-0-1-1--54ddf4d1:12404ee79c9:-8000:0000000000001912 begin
		$this->name = $name;
		$this->options = $options;
        // section 127-0-1-1--54ddf4d1:12404ee79c9:-8000:0000000000001912 end
    }

    /**
     * set the form name
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string name
     * @return mixed
     */
    public function setName($name)
    {
        // section 127-0-1-1-2209c6ee:1266b4e4079:-8000:0000000000001E39 begin
		
		$this->name = $name;
		
        // section 127-0-1-1-2209c6ee:1266b4e4079:-8000:0000000000001E39 end
    }

    /**
     * Get the form name
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    public function getName()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--54ddf4d1:12404ee79c9:-8000:0000000000001918 begin
		
		$returnValue = $this->name;
		
        // section 127-0-1-1--54ddf4d1:12404ee79c9:-8000:0000000000001918 end

        return (string) $returnValue;
    }

    /**
     * set the form options
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array options
     * @return mixed
     */
    public function setOptions($options)
    {
        // section 127-0-1-1-2209c6ee:1266b4e4079:-8000:0000000000001E36 begin
		
		$this->options = $options;
		
        // section 127-0-1-1-2209c6ee:1266b4e4079:-8000:0000000000001E36 end
    }

    /**
     * get an element of the form identified by it's name
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string name
     * @return tao_helpers_form_FormElement
     */
    public function getElement($name)
    {
        $returnValue = null;

        // section 127-0-1-1-34faf2f6:126dcb3a83d:-8000:0000000000001EAB begin
		
		foreach($this->elements as $element){
			if($element->getName() == $name){
				$returnValue = $element;
				break;
			}
		}
		if (is_null($returnValue)) {
			common_Logger::w('Element with name \''.$name.'\' not found');			
		}
        // section 127-0-1-1-34faf2f6:126dcb3a83d:-8000:0000000000001EAB end

        return $returnValue;
    }

    /**
     * get all the form elements
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return array
     */
    public function getElements()
    {
        $returnValue = array();

        // section 10-13-1-45--48e788d1:123dcd97db5:-8000:00000000000018AC begin
		
		$returnValue = $this->elements;
		
        // section 10-13-1-45--48e788d1:123dcd97db5:-8000:00000000000018AC end

        return (array) $returnValue;
    }

    /**
     * Define the list of form elements
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array elements
     * @return mixed
     */
    public function setElements($elements)
    {
        // section 10-13-1-45--48e788d1:123dcd97db5:-8000:00000000000018B1 begin
		
		$this->elements = $elements;
		
        // section 10-13-1-45--48e788d1:123dcd97db5:-8000:00000000000018B1 end
    }

    /**
     * Remove an element identified by it's name.
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string name
     * @return boolean
     */
    public function removeElement($name)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-740c50e3:12704c0ea0d:-8000:0000000000001ECE begin
		
		foreach($this->elements as $index => $element){
			if($element->getName() == $name){
				unset($this->elements[$index]);
				$groupName = $this->getElementGroup($name);
				if(!empty($groupName)){
					if(isset($this->groups[$groupName]['elements'][$name])){
						unset($this->groups[$groupName]['elements'][$name]);
					}
				}
				$returnValue = true;
			}
		}
		
        // section 127-0-1-1-740c50e3:12704c0ea0d:-8000:0000000000001ECE end

        return (bool) $returnValue;
    }

    /**
     * Add an element to the form
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  FormElement element
     * @return mixed
     */
    public function addElement( tao_helpers_form_FormElement $element)
    {
        // section 10-13-1-45--48e788d1:123dcd97db5:-8000:00000000000018AE begin
		$elementPosition = -1;
		foreach($this->elements as $i => $elt){
			if($elt->getName() == $element->getName()){
				$elementPosition = $i;
				break;
			}
		}
		
		if($elementPosition >= 0){
			$this->elements[$elementPosition] = $element;
		}else{
			$this->elements[] = $element;
		}
		
        // section 10-13-1-45--48e788d1:123dcd97db5:-8000:00000000000018AE end
    }

    /**
     * Define the form actions for a context.
     * The different contexts are top and bottom.
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array actions
     * @param  string context
     * @return mixed
     */
    public function setActions($actions, $context = 'bottom')
    {
        // section 127-0-1-1-5e86b639:12689c55756:-8000:0000000000001E49 begin
		
		$this->actions[$context] = array();
		
		foreach($actions as $action){
			if( ! $action instanceof tao_helpers_form_FormElement){
				throw new Exception(" the actions parameter must only contains instances of tao_helpers_form_FormElement ");
			}
			$this->actions[$context][] = $action;
		}
		
        // section 127-0-1-1-5e86b639:12689c55756:-8000:0000000000001E49 end
    }

    /**
     * Get the defined actions for a context
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string context
     * @return array
     */
    public function getActions($context = 'bottom')
    {
        $returnValue = array();

        // section 127-0-1-1--41373b28:1268dca6296:-8000:0000000000001E6A begin
		
		if(isset($this->actions[$context])){
			$returnValue = $this->actions[$context];
		}
		
        // section 127-0-1-1--41373b28:1268dca6296:-8000:0000000000001E6A end

        return (array) $returnValue;
    }

    /**
     * Set the decorator of the type defined in parameter.
     * The different types are element, error, group.
     * By default it uses the element decorator.
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Decorator decorator
     * @param  string type
     * @return mixed
     */
    public function setDecorator( tao_helpers_form_Decorator $decorator, $type = 'element')
    {
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001961 begin
		
		$this->decorators[$type] = $decorator;
		
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001961 end
    }

    /**
     * Set the form decorators
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array decorators
     * @return mixed
     */
    public function setDecorators($decorators)
    {
        // section 127-0-1-1-5e86b639:12689c55756:-8000:0000000000001E3E begin
		
		foreach($decorators as $type => $decorator){
			$this->setDecorator($decorator, $type);
		}
		
        // section 127-0-1-1-5e86b639:12689c55756:-8000:0000000000001E3E end
    }

    /**
     * Get the decorator of the type defined in parameter.
     * The different types are element, error, group.
     * By default it uses the element decorator.
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string type
     * @return tao_helpers_form_Decorator
     */
    public function getDecorator($type = 'element')
    {
        $returnValue = null;

        // section 127-0-1-1-42952c74:1268930e800:-8000:0000000000001E52 begin
		
		if(array_key_exists($type, $this->decorators)){
			$returnValue  = $this->decorators[$type];
		}
		
        // section 127-0-1-1-42952c74:1268930e800:-8000:0000000000001E52 end

        return $returnValue;
    }

    /**
     * render all the form elements
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    public function renderElements()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001983 begin
		foreach($this->elements as $element){
			 
			 if($this->getElementGroup($element->getName()) != ''){
			 	continue;	//render grouped elements after  
			 }
			 
			 if(!is_null($this->getDecorator()) && !($element instanceof tao_helpers_form_elements_Hidden)){
			 	$returnValue .= $this->getDecorator()->preRender();
			 }
			 
			 //render element
			 $returnValue .= $element->render();
			 
			 //render element help
			 $help = trim($element->getHelp());
			 if(!empty($help)){
				 if(!is_null($this->getDecorator('help'))){
			 		$returnValue .= $this->getDecorator('help')->preRender();
			 	}
			 	
			 	$returnValue .= $help;
			 	
				if(!is_null($this->getDecorator('help'))){
			 		$returnValue .= $this->getDecorator('help')->postRender();
			 	}
			 }
			 
			 //render error message
			 if(!$this->isValid() && $element->getError() != ''){
			 	if(!is_null($this->getDecorator('error'))){
			 		$returnValue .= $this->getDecorator('error')->preRender();
			 	}
			 	
			 	$returnValue .= $element->getError();
			 	
				if(!is_null($this->getDecorator('error'))){
			 		$returnValue .= $this->getDecorator('error')->postRender();
			 	}
			 }
			 
			 if(!is_null($this->getDecorator()) && !($element instanceof tao_helpers_form_elements_Hidden)){
			 	$returnValue .= $this->getDecorator()->postRender();
			 }
		}
		
		$subGroupDecorator = null;
		if(!is_null($this->getDecorator('group'))){
			$decoratorClass = get_class($this->getDecorator('group'));
			$subGroupDecorator = new $decoratorClass();
		}
		
		//render group
		foreach($this->groups as $groupName => $group){
		
			if(!is_null($this->getDecorator('group'))){
				$this->getDecorator('group')->setOption('id', tao_helpers_Display::textCleaner($groupName));
				if(isset($group['options'])){
					if(isset($group['options']['class'])){
						$currentClasses = explode(' ',$this->getDecorator('group')->getOption('cssClass'));
						if(!in_array($group['options']['class'], $currentClasses)){
							$currentClasses[] = $group['options']['class'];
							$this->getDecorator('group')->setOption('cssClass', implode(' ', $currentClasses));
						}
					}
				}
				$returnValue .= $this->getDecorator('group')->preRender();
			}
			$returnValue .= $group['title'];
			if(!is_null($subGroupDecorator)){
				$returnValue .= $subGroupDecorator->preRender();
			}
			
			foreach($this->elements as $element){
				 if($this->getElementGroup($element->getName()) == $groupName){
				 
				 	if(!is_null($this->getDecorator()) && !($element instanceof tao_helpers_form_elements_Hidden) ){
					 	$returnValue .= $this->getDecorator()->preRender();
					 }
					 
					 //render element
					 $returnValue .= $element->render();
					 
					 //render element help
					 $help = trim($element->getHelp());
					 if(!empty($help)){
						 if(!is_null($this->getDecorator('help'))){
					 		$returnValue .= $this->getDecorator('help')->preRender();
					 	}
					 	
					 	$returnValue .= $help;
					 	
						if(!is_null($this->getDecorator('help'))){
					 		$returnValue .= $this->getDecorator('help')->postRender();
					 	}
					 }
					 
					 //render error message
					 if(!$this->isValid() && $element->getError() != ''){
					 	if(!is_null($this->getDecorator('error'))){
					 		$returnValue .= $this->getDecorator('error')->preRender();
					 	}
					 	$returnValue .= $element->getError();
						if(!is_null($this->getDecorator('error'))){
					 		$returnValue .= $this->getDecorator('error')->postRender();
					 	}
					 }
					 
					 if(!is_null($this->getDecorator()) && !($element instanceof tao_helpers_form_elements_Hidden) ){
					 	$returnValue .= $this->getDecorator()->postRender();
					 }
				 }
			}
			if(!is_null($subGroupDecorator)){
				$returnValue .= $subGroupDecorator->postRender();
			}
			if(!is_null($this->getDecorator('group'))){
				$returnValue .= $this->getDecorator('group')->postRender();
				$this->getDecorator('group')->setOption('id', '');
			}
		}
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001983 end

        return (string) $returnValue;
    }

    /**
     * render the form actions by context
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string context
     * @return string
     */
    public function renderActions($context = 'bottom')
    {
        $returnValue = (string) '';

        // section 127-0-1-1-5e86b639:12689c55756:-8000:0000000000001E4C begin
		
		if(isset($this->actions[$context])){
			
			$decorator = null;
			if(!is_null($this->getDecorator('actions-'.$context))){
			 	$decorator = $this->getDecorator('actions-'.$context);
			}
			else if(!is_null($this->getDecorator('actions'))){
			 	$decorator = $this->getDecorator('actions');
			}
			
			if(!is_null($decorator)){
				$returnValue .= $decorator->preRender();
			}
	
			foreach($this->actions[$context] as $action){
				$returnValue .= $action->render();
			}
			 
			if(!is_null($decorator)){
				$returnValue .= $decorator->postRender();
			}
		
		}
		
        // section 127-0-1-1-5e86b639:12689c55756:-8000:0000000000001E4C end

        return (string) $returnValue;
    }

    /**
     * initialize the elements set
     *
     * @access protected
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return mixed
     */
    protected function initElements()
    {
        // section 127-0-1-1-79c612e8:1244dcac11b:-8000:0000000000001A4E begin
        // section 127-0-1-1-79c612e8:1244dcac11b:-8000:0000000000001A4E end
    }

    /**
     * Check if the form contains a file upload element
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return boolean
     */
    public function hasFileUpload()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-3453b76:1254af40027:-8000:0000000000001CCD begin
		
		foreach($this->elements as $element){
			if($element instanceof tao_helpers_form_elements_File){
				 $returnValue = true;
				 break;
			}
		}
		
        // section 127-0-1-1-3453b76:1254af40027:-8000:0000000000001CCD end

        return (bool) $returnValue;
    }

    /**
     * Enables you to know if the form is valid
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return boolean
     */
    public function isValid()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7ebefbff:12428eef00b:-8000:00000000000019D3 begin
		$returnValue = $this->valid;
        // section 127-0-1-1-7ebefbff:12428eef00b:-8000:00000000000019D3 end

        return (bool) $returnValue;
    }

    /**
     * Enables you to know if the form has been submited
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return boolean
     */
    public function isSubmited()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7ebefbff:12428eef00b:-8000:00000000000019E0 begin
		$returnValue = $this->submited;
        // section 127-0-1-1-7ebefbff:12428eef00b:-8000:00000000000019E0 end

        return (bool) $returnValue;
    }

    /**
     * Update manually the values of the form
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array values
     * @return mixed
     */
    public function setValues($values)
    {
        // section 127-0-1-1-5e86b639:12689c55756:-8000:0000000000001E43 begin
		
		foreach($values as $key => $value){
			foreach($this->elements as $element){
				if($element->getName() == $key){
					if( $element instanceof tao_helpers_form_elements_Checkbox ||
						(method_exists($element, 'setValues') && is_array($value)) ){
						$element->setValues($value);
					}
					else{
						$element->setValue($value);
					}
					break;
				}
			}
			
		}
		
        // section 127-0-1-1-5e86b639:12689c55756:-8000:0000000000001E43 end
    }

    /**
     * Get the current values of the form
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string groupName
     * @return array
     */
    public function getValues($groupName = '')
    {
        $returnValue = array();

        // section 127-0-1-1-7ebefbff:12428eef00b:-8000:00000000000019E6 begin
		
		foreach($this->elements as $element){
			if(!empty($groupName)){
				if(isset($this->groups[$groupName])){
					if(!in_array($element->getName(), $this->groups[$groupName]['elements'])){
						continue;
					}
				}
			}
			$returnValue[$element->getName()] = $element->getValue();
		}
		
        // section 127-0-1-1-7ebefbff:12428eef00b:-8000:00000000000019E6 end

        return (array) $returnValue;
    }

    /**
     * get the current value of the element identified by the name in parameter
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string name
     * @return boolean
     */
    public function getValue($name)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--6132c277:1244e864521:-8000:0000000000001A59 begin
		foreach($this->elements as $element){
			if($element->getName() == $name){
				return  $element->getEvaluatedValue();
			}
		}
        // section 127-0-1-1--6132c277:1244e864521:-8000:0000000000001A59 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getGroups
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return array
     */
    public function getGroups()
    {
        $returnValue = array();

        // section 127-0-1-1--1c40cb28:129a733b4d1:-8000:000000000000208B begin
        
        $returnValue = $this->groups;
        
        // section 127-0-1-1--1c40cb28:129a733b4d1:-8000:000000000000208B end

        return (array) $returnValue;
    }

    /**
     * Short description of method setGroups
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array groups
     * @return mixed
     */
    public function setGroups($groups)
    {
        // section 127-0-1-1--1c40cb28:129a733b4d1:-8000:000000000000208D begin
        
    	$this->groups = $groups;
    	
        // section 127-0-1-1--1c40cb28:129a733b4d1:-8000:000000000000208D end
    }

    /**
     * Create a logical group of elements
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string groupName
     * @param  string groupTitle
     * @param  array elements array of form elements or their identifiers
     * @param  array options
     * @return mixed
     */
    public function createGroup($groupName, $groupTitle = '', $elements = array(), $options = array())
    {
        $identifier = array();
        foreach ($elements as $element) {
            if ($element instanceof tao_helpers_form_FormElement) {
                $identifier[] = $element->getName();
            } elseif (is_string($element)) {
                $identifier[] = $element;
            } else {
                throw new common_Exception('Unknown element of type '.gettype($element).' in '.__FUNCTION__);
            }
        }
		$this->groups[$groupName] = array(
			'title' 	=> (empty($groupTitle)) ? $groupName : $groupTitle,
			'elements'	=> $identifier,
			'options'	=> $options
		);
    }

    /**
     * add an element to a group
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string groupName
     * @param  string elementName
     * @return mixed
     */
    public function addToGroup($groupName, $elementName = '')
    {
        // section 127-0-1-1--5420fa6f:12481873cb2:-8000:0000000000001ACA begin
		
		if(isset($this->groups[$groupName])){
			if(isset($this->groups[$groupName]['elements'])){
				if(!in_array($elementName, $this->groups[$groupName]['elements'])){
					$this->groups[$groupName]['elements'][] = $elementName;
				}
			}
		}
		
        // section 127-0-1-1--5420fa6f:12481873cb2:-8000:0000000000001ACA end
    }

    /**
     * get the group where is an element
     *
     * @access protected
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string elementName
     * @return string
     */
    protected function getElementGroup($elementName)
    {
        $returnValue = (string) '';

        // section 127-0-1-1--5420fa6f:12481873cb2:-8000:0000000000001ACF begin
		foreach($this->groups as $groupName => $group){
				if(in_array($elementName, $group['elements'])){
					$returnValue = $groupName;
					break;
				}
		}
        // section 127-0-1-1--5420fa6f:12481873cb2:-8000:0000000000001ACF end

        return (string) $returnValue;
    }

    /**
     * remove the group identified by the name in parameter
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string grouName
     * @return boolean
     */
    public function removeGroup($grouName)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-86dc9fc:12705e3a3bc:-8000:0000000000001ED1 begin
		
		if(isset($this->groups[$grouName])){
			foreach($this->groups[$grouName]['elements'] as $element){
				$this->removeElement($element);
			}
			unset($this->groups[$grouName]);
		}
		
        // section 127-0-1-1-86dc9fc:12705e3a3bc:-8000:0000000000001ED1 end

        return (bool) $returnValue;
    }

    /**
     * evaluate the form inside the current context. Must be overridden, for
     * rendering mode: for example, it's used to populate and validate the data
     * the http request for an xhtml context
     *
     * @abstract
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return mixed
     */
    public abstract function evaluate();

    /**
     * Render the form. Must be overridden for each rendering mode.
     *
     * @abstract
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    public abstract function render();

} /* end of abstract class tao_helpers_form_Form */

?>