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
 * This class is a simple "search and replace" PHP-Like template renderer. 
 * It parses a file with php short tags and replace the variables by the
 * in attributes
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItems
 
 */
class taoItems_models_classes_TemplateRenderer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute context
     *
     * @access protected
     * @var array
     */
    protected static $context = array();

    /**
     * ClearFW Renderer
     *
     * @access private
     */
    private $renderer = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string templatePath
     * @param  array variables
     * @return mixed
     */
    public function __construct($templatePath, $variables = array())
    {
        
        
    	if (!file_exists($templatePath)
    		|| !is_readable($templatePath)
    		|| !preg_match("/\.tpl\.php$/", basename($templatePath))) {
    		
    			common_Logger::w('Template ',$templatePath.' not found');
    			throw new InvalidArgumentException("Unable to load the template file from $templatePath");
    	}
    	
		if(!tao_helpers_File::securityCheck($templatePath)){
			throw new Exception("Security warning: $templatePath is not safe.");
		}
    	
    	$this->renderer = new Renderer($templatePath, $variables);
    	
        
    }

    /**
     * Short description of method setContext
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  array parameters
     * @param  string prefix
     * @return mixed
     */
    public static function setContext($parameters, $prefix = '')
    {
        
        
    	self::$context = array();
    	
    	foreach($parameters as $key => $value){
    		self::$context[$prefix . $key] = $value;
    	}
    	
        
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
        
        $this->renderer->setTemplate($templatePath);
        
    }

    /**
     * adds or replaces the data for a specific key
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string key
     * @param  value
     * @return mixed
     */
    public function setData($key, $value)
    {
        
        $this->renderer->setData($key, $value);
        
    }

    /**
     * Short description of method render
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return string
     */
    public function render()
    {
        $returnValue = (string) '';

        
        $this->renderer->setMultipleData(self::$context);
    	$returnValue = $this->renderer->render();
        

        return (string) $returnValue;
    }

}