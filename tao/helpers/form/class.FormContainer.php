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
 * This class provide a container for a specific form instance.
 * It's subclasses instanciate a form and it's elements to be used as a
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package tao
 * @subpackage helpers_form
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
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('tao/helpers/form/class.Form.php');

/* user defined includes */
// section 127-0-1-1-1f533553:1260917dc26:-8000:0000000000001DCE-includes begin
// section 127-0-1-1-1f533553:1260917dc26:-8000:0000000000001DCE-includes end

/* user defined constants */
// section 127-0-1-1-1f533553:1260917dc26:-8000:0000000000001DCE-constants begin
// section 127-0-1-1-1f533553:1260917dc26:-8000:0000000000001DCE-constants end

/**
 * This class provide a container for a specific form instance.
 * It's subclasses instanciate a form and it's elements to be used as a
 *
 * @abstract
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package tao
 * @subpackage helpers_form
 */
abstract class tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * the form instance contained
     *
     * @access protected
     * @var tao_helpers_form_Form
     */
    protected $form = null;

    /**
     * the data of the form
     *
     * @access protected
     * @var array
     */
    protected $data = array();

    /**
     * the form options
     *
     * @access protected
     * @var array
     */
    protected $options = array();

    /**
     * static list of all instanciated forms
     *
     * @access protected
     * @var array
     */
    protected static $forms = array();

    // --- OPERATIONS ---

    /**
     * The constructor, initialize and build the form 
     * regarding the initForm and initElements methods
     * to be overriden
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array data
     * @param  array options
     * @return mixed
     */
    public function __construct($data = array(), $options = array())
    {
        // section 127-0-1-1-1f533553:1260917dc26:-8000:0000000000001DDD begin
		
		$this->data = $data;
		$this->options = $options;
		
		//initialize the form attribute
		$this->initForm();
		
		if(!is_null($this->form)){
			//let the refs of all the forms there 
			self::$forms[$this->form->getName()] = $this->form;
		}
		
		//initialize the elmements of the form
		$this->initElements();
		
		//set the values in case of default values
		if(count($this->data) > 0){
			$this->form->setValues($this->data);
		}
		
		//evaluate the form
		if(!is_null($this->form)){
			$this->form->evaluate();
		}
		
		//validate global form rules
		if(!is_null($this->form)){
			$this->validate();
		}
		
		$returnValue = $this;
		
        // section 127-0-1-1-1f533553:1260917dc26:-8000:0000000000001DDD end
    }

    /**
     * Destructor (remove the current form in the static list)
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return mixed
     */
    public function __destruct()
    {
        // section 127-0-1-1--1c40cb28:129a733b4d1:-8000:0000000000002090 begin
        
    	if(!is_null($this->form)){
			//remove the refs of the contained form
			unset(self::$forms[$this->form->getName()]);
		}
    	
        // section 127-0-1-1--1c40cb28:129a733b4d1:-8000:0000000000002090 end
    }

    /**
     * get the form instance
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return tao_helpers_form_Form
     */
    public function getForm()
    {
        $returnValue = null;

        // section 127-0-1-1-1f533553:1260917dc26:-8000:0000000000001DD9 begin
		
		$returnValue = $this->form;
		
        // section 127-0-1-1-1f533553:1260917dc26:-8000:0000000000001DD9 end

        return $returnValue;
    }

    /**
     * Must be overriden and must instanciate the form instance and put it in
     * form attribute
     *
     * @abstract
     * @access protected
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return mixed
     */
    protected abstract function initForm();

    /**
     * Used to create the form elements and bind them to the form instance
     *
     * @abstract
     * @access protected
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return mixed
     */
    protected abstract function initElements();

    /**
     * Allow global form validation.
     * Override this function to do it.
     *
     * @access protected
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return boolean
     */
    protected function validate()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--485428cc:133267d2802:-8000:0000000000004096 begin
        
        $returnValue = true;
        
        // section 127-0-1-1--485428cc:133267d2802:-8000:0000000000004096 end

        return (bool) $returnValue;
    }

} /* end of abstract class tao_helpers_form_FormContainer */

?>