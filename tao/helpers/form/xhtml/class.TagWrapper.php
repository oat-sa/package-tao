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
 * This decorator wrap the decorated element inside a tag.
 * Usually an xhtml tag.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage helpers_form_xhtml
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * A decorator is an helper used for aspect oriented rendering.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/helpers/form/interface.Decorator.php');

/* user defined includes */
// section 127-0-1-1-3ed01c83:12409dc285c:-8000:000000000000196F-includes begin
// section 127-0-1-1-3ed01c83:12409dc285c:-8000:000000000000196F-includes end

/* user defined constants */
// section 127-0-1-1-3ed01c83:12409dc285c:-8000:000000000000196F-constants begin
// section 127-0-1-1-3ed01c83:12409dc285c:-8000:000000000000196F-constants end

/**
 * This decorator wrap the decorated element inside a tag.
 * Usually an xhtml tag.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage helpers_form_xhtml
 */
class tao_helpers_form_xhtml_TagWrapper
        implements tao_helpers_form_Decorator
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute tag
     *
     * @access protected
     * @var string
     */
    protected $tag = 'div';

    /**
     * Short description of attribute id
     *
     * @access protected
     * @var string
     */
    protected $id = '';

    /**
     * Short description of attribute cssClass
     *
     * @access protected
     * @var string
     */
    protected $cssClass = '';

    // --- OPERATIONS ---

    /**
     * Short description of method preRender
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function preRender()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001952 begin
		if(!empty($this->tag)){
			$returnValue .= "<{$this->tag}";
			if(!empty($this->id)){
				$returnValue .= " id='{$this->id}' ";	
			}
			if(!empty($this->cssClass)){
				$returnValue .= " class='{$this->cssClass}' ";	
			}
			$returnValue .= ">";
		}
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001952 end

        return (string) $returnValue;
    }

    /**
     * Short description of method postRender
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function postRender()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001954 begin
		if(!empty($this->tag)){
			$returnValue .= "</{$this->tag}>";
		}
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001954 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getOption
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string key
     * @return string
     */
    public function getOption($key)
    {
        $returnValue = (string) '';

        // section 127-0-1-1--704cb8ff:125262de5fb:-8000:0000000000001C79 begin
		if(isset($this->$key)){
			$returnValue = $this->$key;
		}
        // section 127-0-1-1--704cb8ff:125262de5fb:-8000:0000000000001C79 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setOption
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string key
     * @param  string value
     * @return boolean
     */
    public function setOption($key, $value)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--704cb8ff:125262de5fb:-8000:0000000000001C7C begin
		
		$this->$key = $value;
		
        // section 127-0-1-1--704cb8ff:125262de5fb:-8000:0000000000001C7C end

        return (bool) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array options
     * @return mixed
     */
    public function __construct($options = array())
    {
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001976 begin
		if(isset($options['tag'])){
			$this->tag = $options['tag'];
		}
		if(isset($options['cssClass'])){
			$this->cssClass = $options['cssClass'];
		}
		if(isset($options['id'])){
			$this->id = $options['id'];
		}
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001976 end
    }

} /* end of class tao_helpers_form_xhtml_TagWrapper */

?>