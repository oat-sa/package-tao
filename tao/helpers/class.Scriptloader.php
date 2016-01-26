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
 * The scriptloader helper enables you to load web resources dynamically. It
 * now CSS and JS resources.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 
 */
class tao_helpers_Scriptloader
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute CSS
     *
     * @access public
     * @var string
     */
    const CSS = 'css';

    /**
     * Short description of attribute JS
     *
     * @access public
     * @var string
     */
    const JS = 'js';

    /**
     * Short description of attribute jsFiles
     *
     * @access private
     * @var array
     */
    private static $jsFiles = array();

    /**
     * Short description of attribute cssFiles
     *
     * @access private
     * @var array
     */
    private static $cssFiles = array();

    /**
     * Short description of attribute jsVars
     *
     * @access protected
     * @var array
     */
    protected static $jsVars = array();

    // --- OPERATIONS ---

    /**
     * Short description of method contextInit
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string extension
     * @param  string module
     * @param  string action
     * @return mixed
     */
    public static function contextInit($extension, $module, $action)
    {
        
		
		$basePath = '/'.$extension.'/views/';
		
		//load module scripts
		$jsModuleFile = $basePath.self::JS.'/controllers/'.strtolower($module).'/'.$action.'.'.self::JS;
		
		$cssModuleFile = $basePath.self::CSS.'/'.$module.'.'.self::CSS;
		$cssModuleDir = $basePath.self::CSS.'/'.$module.'/';
		
		if(file_exists($jsModuleFile)){
			self::addJsFile($jsModuleFile);
		}
		if(file_exists($cssModuleFile)){
			self::addCssFile($cssModuleFile);
		}
		foreach(glob($cssModuleDir.'*.'.self::CSS) as $file){
			self::addCssFile($file);
		}
		
		//
		//@todo load action scripts
		//
		
        
    }

    /**
     * define the paths to look for the scripts
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array paths
     * @param  boolean recursive
     * @param  string filter
     * @return mixed
     */
    public static function setPaths($paths, $recursive = false, $filter = '')
    {
        
		foreach($paths as $path){
    		if(!preg_match("/\/$/", $path)){
    			$path .= '/';
    		}
			if(empty($filter) || strtolower($filter) == tao_helpers_Scriptloader::CSS){
				foreach(glob($path . "*." . tao_helpers_Scriptloader::CSS) as $cssFile){
					self::$cssFiles[] = $path . $cssFile;
				}
			}
			if(empty($filter) || strtolower($filter) == tao_helpers_Scriptloader::JS){
				foreach(glob($path . "*." . tao_helpers_Scriptloader::JS) as $jsFile){
					self::$jsFiles[] = $path . $jsFile;
				}
			}
			if($recursive){
				$dirs = array();
				foreach(scandir($path) as $file){
					if(is_dir($path.$file) && $file != '.' && $file != '..'){
						$dirs[] = $path.$file;
					}
				}
				if(count($dirs) > 0){
					self::setPaths($dirs, true, $filter);
				}
			}
    	}
        
    }

    /**
     * add a file to load
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string file
     * @param  string type
     * @return mixed
     * @throws Exception
     */
    public static function addFile($file, $type = '')
    {
        
		if(empty($type)){
			if(preg_match("/\.".tao_helpers_Scriptloader::CSS."$/", $file)){
				$type = tao_helpers_Scriptloader::CSS;
			}
			if(preg_match("/\.".tao_helpers_Scriptloader::JS."$/", $file)){
				$type = tao_helpers_Scriptloader::JS;
			}
		}
		switch(strtolower($type)){
			case tao_helpers_Scriptloader::CSS: self::$cssFiles[] = $file; break;
			case tao_helpers_Scriptloader::JS:  self::$jsFiles[]  = $file; break;
			default:
				throw new Exception("Unknown script type for file : ".$file);
		} 
        
    }

    /**
     * add a css file to load
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string file
     * @return mixed
     */
    public static function addCssFile($file)
    {
        
		self::addFile($file, tao_helpers_Scriptloader::CSS);
        
    }

    /**
     * add a js file to load
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string file
     * @return mixed
     */
    public static function addJsFile($file)
    {
        
		self::addFile($file, tao_helpers_Scriptloader::JS);
        
    }

    /**
     * add an array of css files to load
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array files
     * @return mixed
     */
    public static function addCssFiles($files = array())
    {
        
		foreach($files as $file){
			self::addFile($file, tao_helpers_Scriptloader::CSS);
		}
        
    }

    /**
     * add an array of css files to load
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array files
     * @return mixed
     */
    public static function addJsFiles($files = array())
    {
        
		foreach($files as $file){
			self::addFile($file, tao_helpers_Scriptloader::JS);
		}
        
    }

    /**
     * Short description of method addJsVar
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string name
     * @param  string value
     * @return mixed
     */
    public static function addJsVar($name, $value = '')
    {
        
        
    	self::$jsVars[$name] = $value;
    	
        
    }

    /**
     * Short description of method addJsVars
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array vars
     * @return mixed
     */
    public static function addJsVars($vars)
    {
        
        
    	if(is_array($vars)){
	    	foreach($vars as $name => $value){
	    		if(is_int($name)){
	    			$name = 'var_'.$name;
	    		}
				self::addJsVar($name, $value);
			}
    	}
        
        
    }
    
    public static function getJsFiles(){
        return self::$jsFiles;
    }

    /**
     * render the html to load the resources
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string filter
     * @return string
     */
    public static function render($filter = '')
    {
        $returnValue = (string) '';

        
		if(empty($filter) || strtolower($filter) == tao_helpers_Scriptloader::CSS){
			foreach(self::$cssFiles as $file){
				$returnValue .= "\t<link rel='stylesheet' type='text/css' href='{$file}' />\n";
			}
		}
		if(empty($filter) || strtolower($filter) == tao_helpers_Scriptloader::JS){
			if(count(self::$jsVars) > 0){
				$returnValue .= "\t<script type='text/javascript'>\n";
				foreach(self::$jsVars as $name => $value){
					$returnValue .= "\tvar {$name} = '{$value}';\n";
				}
				$returnValue .= "\t</script>\n";
			}
			foreach(self::$jsFiles as $file){
				$returnValue .= "\t<script type='text/javascript' src='{$file}' ></script>\n";
			}
		}
        

        return (string) $returnValue;
    }

}

?>