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
 * Short description of class tao_helpers_form_elements_Template
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 
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
        
        
    	$this->path = $path;
    	
        
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
        
        
    	if(is_array($values)){
    		$this->values = $values;
    	}
    	
        
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

        
        
        $returnValue = $this->values;
        
        

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

        
        
        //prevent to use empty prefix. By default the name is used!
        if(empty($this->prefix) && !empty($this->name)){
        	$this->prefix = $this->name . '_';
        }
        
        $returnValue = $this->prefix;
        
        

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
        
        
    	$this->prefix = $prefix;
    	
        
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
        
        
    	if(!is_array($variables)){
    		$variables = array($variables);
    	}
    	$this->variables = $variables;
    	
        
    }

}

?>