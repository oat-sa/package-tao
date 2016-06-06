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
 * Copyright (c) 2006-2009 (original work) Public Research Centre Henri Tudor (under the project FP6-IST-PALETTE);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
require_once dirname(__FILE__).'/helpers/RendererHelper.php';

/**
 * Renderer class
 * 
 * @author J�r�me Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
class Renderer
{
	/**
	 * @var string base directory containing the views themes
	 */
	private $template = null;
	
	/**
	 * @var array associtaiv array of variables that will be replaced in the template
	 */
	private $variables = array();
	
	/**
	 * Constructor with optional parameters
	 * 
	 * @param string $templatePath template to use
	 * @param array $variables
	 */
	public function __construct($templatePath = null, $variables = array())
	{
		$this->template		= $templatePath;
		$this->variables	= $variables;
	}
	
    /**
     * sets the template to be used
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string templatePath
     * @return mixed
     */
    public function setTemplate($templatePath)
    {
        $this->template = $templatePath;
    }

    /**
     * adds or replaces the data for a specific key
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string key
     * @param  mixed value
     */
    public function setData($key, $value)
    {
        $this->variables[$key] = $value;
    }
	
    /**
     * adds or replaces the data for multiple keys
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  array array associativ array of data
     */
    public function setMultipleData($array)
    {
    	foreach ($array as $key => $value) {
    		$this->variables[$key] = $value;
    	}
    }
    
    /**
     * Whenever or not a template has been specified
     * 
     * @return boolean
     */
    public function hasTemplate()
    {
    	return !is_null($this->template);
    }
    
	/**
	 * Renders the template
	 * 
	 * @return string the rendered view 
	 */
    public function render()
    {
        
		if (!$this->hasTemplate()) {
			throw new common_Exception('Cannot render without template');
		}
    	
        extract($this->variables);
        RenderContext::pushContext($this->variables);
        
        ob_start();
        include $this->template;
        $returnValue = ob_get_contents();
        
        ob_end_clean();
        
        //clean the extracted variables
        foreach($this->variables as $key => $name){
        	unset($$key);
        }
        RenderContext::popContext();
        
		return $returnValue;
    }
}
?>