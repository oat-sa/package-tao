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
    // --- ATTRIBUTES ---

    /**
     * Short description of attribute tag
     *
     * @access protected
     * @var string
     */
    protected $tag = 'div';

    protected $attributes = array();

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
			if (isset($this->attributes['cssClass'])) {
			    // legacy
			    $this->attributes['class'] = $this->attributes['cssClass'].
                    (isset($this->attributes['class']) ? ' '.$this->attributes['class'] : '');
			    unset($this->attributes['cssClass']);
			}
			foreach ($this->attributes as $key => $value) {
			    $returnValue .= ' '.$key.'=\''.$value.'\' ';
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
        if ($key == 'tag') {
            return $this->tag;
        } elseif (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        } else {
            return '';
        }
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
        if ($key == 'tag') {
            $this->tag = $value;
        } else {
            $this->attributes[$key] = $value;
        }
        return true;
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
			unset($options['tag']);
		}
		$this->attributes = $options;
    }

}
