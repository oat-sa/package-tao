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
 * This class is a simple "search and replace" PHP-Like template renderer. 
 * It parses a file with php short tags and replace the variables by the
 * in attributes
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItems
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-649cc98e:12ad7cf4ab2:-8000:0000000000002592-includes begin
// section 127-0-1-1-649cc98e:12ad7cf4ab2:-8000:0000000000002592-includes end

/* user defined constants */
// section 127-0-1-1-649cc98e:12ad7cf4ab2:-8000:0000000000002592-constants begin
// section 127-0-1-1-649cc98e:12ad7cf4ab2:-8000:0000000000002592-constants end

/**
 * This class is a simple "search and replace" PHP-Like template renderer. 
 * It parses a file with php short tags and replace the variables by the
 * in attributes
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItems
 * @subpackage models_classes
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
        // section 127-0-1-1-649cc98e:12ad7cf4ab2:-8000:00000000000025A1 begin
        
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
    	
        // section 127-0-1-1-649cc98e:12ad7cf4ab2:-8000:00000000000025A1 end
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
        // section 127-0-1-1-3c043620:12bd493a38b:-8000:000000000000272E begin
        
    	self::$context = array();
    	
    	foreach($parameters as $key => $value){
    		self::$context[$prefix . $key] = $value;
    	}
    	
        // section 127-0-1-1-3c043620:12bd493a38b:-8000:000000000000272E end
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
        // section 10-30-1--78--43051535:13d25564359:-8000:0000000000003C81 begin
        $this->renderer->setTemplate($templatePath);
        // section 10-30-1--78--43051535:13d25564359:-8000:0000000000003C81 end
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
        // section 10-30-1--78--43051535:13d25564359:-8000:0000000000003C7D begin
        $this->renderer->setData($key, $value);
        // section 10-30-1--78--43051535:13d25564359:-8000:0000000000003C7D end
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

        // section 127-0-1-1-649cc98e:12ad7cf4ab2:-8000:00000000000025A5 begin
        $this->renderer->setMultipleData(self::$context);
    	$returnValue = $this->renderer->render();
        // section 127-0-1-1-649cc98e:12ad7cf4ab2:-8000:00000000000025A5 end

        return (string) $returnValue;
    }

} /* end of class taoItems_models_classes_TemplateRenderer */

?>