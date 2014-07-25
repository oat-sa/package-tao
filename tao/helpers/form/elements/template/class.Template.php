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
 * TAO - tao/helpers/form/elements/template/class.Template.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 02.01.2012, 11:53:23 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements_template
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_helpers_form_elements_Template
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/helpers/form/elements/class.Template.php');

/* user defined includes */
// section 127-0-1-1--74f64380:12e057353c9:-8000:0000000000004F32-includes begin
// section 127-0-1-1--74f64380:12e057353c9:-8000:0000000000004F32-includes end

/* user defined constants */
// section 127-0-1-1--74f64380:12e057353c9:-8000:0000000000004F32-constants begin
// section 127-0-1-1--74f64380:12e057353c9:-8000:0000000000004F32-constants end

/**
 * Short description of class tao_helpers_form_elements_template_Template
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage helpers_form_elements_template
 */
class tao_helpers_form_elements_template_Template
    extends tao_helpers_form_elements_Template
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method feed
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function feed()
    {
        // section 127-0-1-1-1ee974ce:13456771564:-8000:000000000000348A begin
    	$values = array();
    	$prefix = preg_quote($this->getPrefix(), '/');
    	foreach($_POST as $key => $value){
    		if(preg_match("/^$prefix/", $key)){
    			$values[str_replace($this->getPrefix(), '', $key)] = $value;
    		}
    	}
    	$this->setValues($values);
        // section 127-0-1-1-1ee974ce:13456771564:-8000:000000000000348A end
    }

    /**
     * Short description of method render
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function render()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--74f64380:12e057353c9:-8000:0000000000004F35 begin
        
        if(file_exists($this->path) && is_readable($this->path)){
        	
	        extract($this->variables);
	      
	        ob_start();
	        
	        common_Logger::i('including \''.$this->path.'\' into form', array('TAO'));
	        
	       include $this->path;
	        
	        $returnValue = ob_get_contents();
	        
	        ob_end_clean();
	        
	        //clean the extracted variables
	        foreach($this->variables as $key => $name){
	        	unset($$key);
	        }
        	
        }
        
        // section 127-0-1-1--74f64380:12e057353c9:-8000:0000000000004F35 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getEvaluatedValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function getEvaluatedValue()
    {
        // section 127-0-1-1--19ea91f3:1349db91b83:-8000:00000000000034A4 begin
        return $this->getValues();
        // section 127-0-1-1--19ea91f3:1349db91b83:-8000:00000000000034A4 end
    }

} /* end of class tao_helpers_form_elements_template_Template */

?>