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
 * This decorator wrap the decorated element inside a tag.
 * Usually an xhtml tag.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 
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

        
		if(!empty($this->tag)){
			$returnValue .= "</{$this->tag}>";
		}
        

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

        
		if(isset($this->$key)){
			$returnValue = $this->$key;
		}
        

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

        
		
		$this->$key = $value;
		
        

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
        
		if(isset($options['tag'])){
			$this->tag = $options['tag'];
		}
		if(isset($options['cssClass'])){
			$this->cssClass = $options['cssClass'];
		}
		if(isset($options['id'])){
			$this->id = $options['id'];
		}
        
    }

} /* end of class tao_helpers_form_xhtml_TagWrapper */

?>