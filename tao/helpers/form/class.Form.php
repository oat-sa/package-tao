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
     * @param  string $name
     * @param  array $options
     */
    public function __construct($name = '', array $options = array())
    {

		$this->name = $name;
		$this->options = $options;

    }

    /**
     * set the form name
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string $name
     */
    public function setName($name)
    {
		$this->name = $name;
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
        return (string) $this->name;
    }

    /**
     * set the form options
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array $options
     * @return mixed
     */
    public function setOptions(array $options)
    {
		$this->options = $options;
    }

    /**
     * get an element of the form identified by it's name
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string $name
     * @return tao_helpers_form_FormElement
     */
    public function getElement($name)
    {
        $returnValue = null;



		foreach($this->elements as $element){
			if($element->getName() == $name){
				$returnValue = $element;
				break;
			}
		}
		if (is_null($returnValue)) {
			common_Logger::w('Element with name \''.$name.'\' not found');
		}


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
        return $this->elements;
    }

    /**
     * Define the list of form elements
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array $elements
     */
    public function setElements(array $elements)
    {
		$this->elements = $elements;
    }

    /**
     * Remove an element identified by it's name.
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string $name
     * @return boolean
     */
    public function removeElement($name)
    {
        $returnValue = false;

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

        return $returnValue;
    }

    /**
     * Add an element to the form
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  tao_helpers_form_FormElement $element
     */
    public function addElement( tao_helpers_form_FormElement $element)
    {

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


    }

    /**
     * Define the form actions for a context.
     * The different contexts are top and bottom.
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     *
     * @param  array $actions
     * @param  string $context
     *
     * @throws Exception
     */
    public function setActions($actions, $context = 'bottom')
    {

		$this->actions[$context] = array();

		foreach($actions as $action){
			if( ! $action instanceof tao_helpers_form_FormElement){
				throw new Exception(" the actions parameter must only contains instances of tao_helpers_form_FormElement ");
			}
			$this->actions[$context][] = $action;
		}

    }

    /**
     * Get the defined actions for a context
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string $context
     * @return array
     */
    public function getActions($context = 'bottom')
    {
        $returnValue = array();

		if(isset($this->actions[$context])){
			$returnValue = $this->actions[$context];
		}

        return (array) $returnValue;
    }

	/**
	 * Get specific action element from context
	 * @param $name
	 * @param string $context
	 *
	 * @return mixed
	 */
	public function getAction($name, $context = 'bottom'){

		foreach($this->getActions($context) as $element){
			if($element->getName() == $name){
				$returnValue = $element;
				break;
			}
		}
		if (is_null($returnValue)) {
			common_Logger::w('Action with name \''.$name.'\' not found');
		}

		return $returnValue;
	}

    /**
     * Set the decorator of the type defined in parameter.
     * The different types are element, error, group.
     * By default it uses the element decorator.
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     *
     * @param tao_helpers_form_Decorator $decorator
     * @param string $type type
     *
     * @return mixed
     * @internal param decorator $Decorator
     */
    public function setDecorator( tao_helpers_form_Decorator $decorator, $type = 'element')
    {
		$this->decorators[$type] = $decorator;
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
		foreach($decorators as $type => $decorator){
			$this->setDecorator($decorator, $type);
		}
    }

    /**
     * Get the decorator of the type defined in parameter.
     * The different types are element, error, group.
     * By default it uses the element decorator.
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string $type
     * @return tao_helpers_form_Decorator
     */
    public function getDecorator($type = 'element')
    {
        $returnValue = null;


		if(array_key_exists($type, $this->decorators)){
			$returnValue  = $this->decorators[$type];
		}


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
        $returnValue = '';


		foreach($this->elements as $element){

			 if($this->getElementGroup($element->getName()) !== ''){
			 	continue;	//render grouped elements after
			 }

			 if(!is_null($this->getDecorator()) && !($element instanceof tao_helpers_form_elements_Hidden)){
			 	$returnValue .= $this->getDecorator()->preRender();
			 }

             if(!$this->isValid() && $element->getError()) {
                $element->addClass('error');
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
			 if(!$this->isValid() && $element->getError()){
			 	if(!is_null($this->getDecorator('error'))){
			 		$returnValue .= $this->getDecorator('error')->preRender();
			 	}

			 	$returnValue .= nl2br($element->getError());

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
				$this->getDecorator('group')->setOption('id', $groupName);
				if(isset($group['options'])){
					if(isset($group['options']['class'])){
						$currentClasses = array_map('trim', explode(' ',$this->getDecorator('group')->getOption('cssClass')));
						if(!in_array($group['options']['class'], $currentClasses)){
							$currentClasses[] = $group['options']['class'];
							$this->getDecorator('group')->setOption('cssClass', implode(' ', array_unique($currentClasses)));
						}
					}
				}
				$returnValue .= $this->getDecorator('group')->preRender();
			}
			$returnValue .= $group['title'];
			if(!is_null($subGroupDecorator)){
				$returnValue .= $subGroupDecorator->preRender();
			}
			foreach($group['elements'] as $elementName){
				 if($this->getElementGroup($elementName) === $groupName && $element = $this->getElement( $elementName )){

				 	if(!is_null($this->getDecorator())){// && !($element instanceof tao_helpers_form_elements_Hidden) ){

					 	$returnValue .= $this->getDecorator()->preRender();
					 }

					 //render element
                     if ( ! $this->isValid() && $element->getError()) {
                         $element->addClass( 'error' );
                     }
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
					 if(!$this->isValid() && $element->getError()){
					 	if(!is_null($this->getDecorator('error'))){
					 		$returnValue .= $this->getDecorator('error')->preRender();
					 	}
					 	$returnValue .= nl2br($element->getError());
						if(!is_null($this->getDecorator('error'))){
					 		$returnValue .= $this->getDecorator('error')->postRender();
					 	}
					 }

					 if(!is_null($this->getDecorator())){// && !($element instanceof tao_helpers_form_elements_Hidden) ){
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


        return $returnValue;
    }

    /**
     * render the form actions by context
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string $context
     * @return string
     */
    public function renderActions($context = 'bottom')
    {
        $returnValue = (string) '';

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
        $returnValue = false;

		foreach($this->elements as $element){
			if($element instanceof tao_helpers_form_elements_File){
				 $returnValue = true;
				 break;
			}
		}

        return $returnValue;
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
        return $this->valid;
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
        return  $this->submited;
    }

    /**
     * Update manually the values of the form
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array $values
     */
    public function setValues($values)
    {

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

    }

    /**
     * Get the current values of the form
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string $groupName
     * @return array
     */
    public function getValues($groupName = '')
    {
        $returnValue = array();

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

        return $returnValue;
    }

    /**
     * get the current value of the element identified by the name in parameter
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string $name
     * @return boolean
     */
    public function getValue($name)
    {
		foreach($this->elements as $element){
			if($element->getName() == $name){
				return  $element->getEvaluatedValue();
			}
		}

        return false;
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
        return $this->groups;
    }

    /**
     * Short description of method setGroups
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array $groups
     * @return mixed
     */
    public function setGroups($groups)
    {
    	$this->groups = $groups;
    }

    /**
     * Create a logical group of elements
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string $groupName
     * @param  string $groupTitle
     * @param  array $elements array of form elements or their identifiers
     * @param  array $options
     * @return mixed
     * @throws common_Exception
     */
    public function createGroup($groupName, $groupTitle = '', array $elements = array(), array $options = array())
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
     * @param  string $groupName
     * @param  string $elementName
     */
    public function addToGroup($groupName, $elementName = '')
    {

		if(isset($this->groups[$groupName])){
			if(isset($this->groups[$groupName]['elements'])){
				if(!in_array($elementName, $this->groups[$groupName]['elements'])){
					$this->groups[$groupName]['elements'][] = $elementName;
				}
			}
		}

    }

    /**
     * get the group where is an element
     *
     * @access protected
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string $elementName
     * @return string
     */
    protected function getElementGroup($elementName)
    {
        $returnValue =  '';

		foreach($this->groups as $groupName => $group){
				if(in_array($elementName, $group['elements'])){
					$returnValue = $groupName;
					break;
				}
		}

        return $returnValue;
    }

    /**
     * remove the group identified by the name in parameter
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string $groupName
     */
    public function removeGroup($groupName)
    {

		if(isset($this->groups[$groupName])){
			foreach($this->groups[$groupName]['elements'] as $element){
				$this->removeElement($element);
			}
			unset($this->groups[$groupName]);
		}

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

}