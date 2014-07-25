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
 * TAO - tao/helpers/form/elements/class.Template.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 08.02.2011, 16:55:36 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Represents a FormElement entity
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/helpers/form/class.FormElement.php');

/* user defined includes */
// section 127-0-1-1--74f64380:12e057353c9:-8000:0000000000004F30-includes begin
// section 127-0-1-1--74f64380:12e057353c9:-8000:0000000000004F30-includes end

/* user defined constants */
// section 127-0-1-1--74f64380:12e057353c9:-8000:0000000000004F30-constants begin
// section 127-0-1-1--74f64380:12e057353c9:-8000:0000000000004F30-constants end

/**
 * Short description of class tao_helpers_form_elements_Template
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements
 */
abstract class tao_helpers_form_elements_Template
    extends tao_helpers_form_FormElement
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * the template parth
     *
     * @access protected
     * @var string
     */
    protected $path = '';

    /**
     * Short description of attribute values
     *
     * @access protected
     * @var array
     */
    protected $values = array();

    /**
     * The prefix is used to recognize the form fields inside the template.
     * So, all the field's name you want to retreive the value should be
     *
     * @access protected
     * @var string
     */
    protected $prefix = '';

    /**
     * Short description of attribute variables
     *
     * @access protected
     * @var array
     */
    protected $variables = array();

    // --- OPERATIONS ---

    /**
     * Se the template file path
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string path
     * @return mixed
     */
    public function setPath($path)
    {
        // section 127-0-1-1--74f64380:12e057353c9:-8000:0000000000004F3D begin
        
    	$this->path = $path;
    	
        // section 127-0-1-1--74f64380:12e057353c9:-8000:0000000000004F3D end
    }

    /**
     * set the values of the element
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array values
     * @return mixed
     */
    public function setValues($values)
    {
        // section 127-0-1-1--74f64380:12e057353c9:-8000:0000000000004F40 begin
        
    	if(is_array($values)){
    		$this->values = $values;
    	}
    	
        // section 127-0-1-1--74f64380:12e057353c9:-8000:0000000000004F40 end
    }

    /**
     * Get the values of the element
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return taoResults_models_classes_aray
     */
    public function getValues()
    {
        $returnValue = null;

        // section 127-0-1-1--74f64380:12e057353c9:-8000:0000000000004F43 begin
        
        $returnValue = $this->values;
        
        // section 127-0-1-1--74f64380:12e057353c9:-8000:0000000000004F43 end

        return $returnValue;
    }

    /**
     * Short description of method getPrefix
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function getPrefix()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--74f64380:12e057353c9:-8000:0000000000004F54 begin
        
        //prevent to use empty prefix. By default the name is used!
        if(empty($this->prefix) && !empty($this->name)){
        	$this->prefix = $this->name . '_';
        }
        
        $returnValue = $this->prefix;
        
        // section 127-0-1-1--74f64380:12e057353c9:-8000:0000000000004F54 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setPrefix
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string prefix
     * @return mixed
     */
    public function setPrefix($prefix)
    {
        // section 127-0-1-1--74f64380:12e057353c9:-8000:0000000000004F56 begin
        
    	$this->prefix = $prefix;
    	
        // section 127-0-1-1--74f64380:12e057353c9:-8000:0000000000004F56 end
    }

    /**
     * Short description of method setVariables
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array variables
     * @return mixed
     */
    public function setVariables($variables)
    {
        // section 127-0-1-1--74f64380:12e057353c9:-8000:0000000000004F5C begin
        
    	if(!is_array($variables)){
    		$variables = array($variables);
    	}
    	$this->variables = $variables;
    	
        // section 127-0-1-1--74f64380:12e057353c9:-8000:0000000000004F5C end
    }

} /* end of class tao_helpers_form_elements_Template */

?>