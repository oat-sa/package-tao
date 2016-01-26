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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *               
 * 
 */

/**
 * the generis autoloader
 *
 * @access public
 * @author Joel Bout <joel@taotesting.com>
 * @package generis
 */
class common_legacy_LegacyAutoLoader
{
    private static $singleton = null;
    
    /**
     * 
     * @return common_legacy_LegacyAutoLoader
     */
    private static function singleton() {
        if (self::$singleton == null) {
            self::$singleton = new self();
        }
        return self::$singleton;
    }
    
    private $legacyPrefixes = array();
    
    private $root;
    
    /**
     * protect the cunstructer, singleton pattern
     */
    private function __construct() {
        $this->root = dirname(dirname(dirname(__DIR__))).DIRECTORY_SEPARATOR;
    }
    
    /**
     * Register this instance of ClassLoader as a php autoloader
     *
     * @access public
     * @author Joel Bout <joel@taotesting.com>
     */
    public static function register()
    {
        // init the autloader for generis
        spl_autoload_register(array(self::singleton(), 'autoload'));
    }
    
    /**
     * add support for legacy prefix
     */
    public static function supportLegacyPrefix($prefix, $namespace) {
        self::singleton()->legacyPrefixes[$prefix] = $namespace;
    }
    
    /**
     * Attempt to autload classes in tao
     *
     * @access public
     * @author Joel Bout <joel@taotesting.com>
     * @param  string pClassName
     * @return void
     */
    public function autoload($pClassName)
    {
        if(strpos($pClassName, '_') !== false){
            
    		$tokens = explode("_", $pClassName);
    		$size = count($tokens);
    		$path = '';
    		for ( $i = 0 ; $i<$size-1 ; $i++){
    			$path .= $tokens[$i].'/';
    		}
    		
    		$filePath = '/' . $path . 'class.'.$tokens[$size-1] . '.php';
    		if (file_exists($this->root .'generis'.DIRECTORY_SEPARATOR .$filePath)){
    			require_once $this->root .'generis'.DIRECTORY_SEPARATOR .$filePath;
    			return;
    		}
    		$filePathInterface = '/' . $path . 'interface.'.$tokens[$size-1] . '.php';
    		if (file_exists($this->root .'generis'.DIRECTORY_SEPARATOR .$filePathInterface)){
    			require_once $this->root .'generis'.DIRECTORY_SEPARATOR .$filePathInterface;
    			return;
    		}
    		
    		if (file_exists($this->root .$filePath)){
    			require_once $this->root .$filePath;
    			return;
    		} elseif (file_exists($this->root .$filePathInterface)){
    		        require_once $this->root .$filePathInterface;
    		        return;
    		}
    		
    		$legacyPrefix = false;
    		foreach ($this->legacyPrefixes as $key => $namespace) {
    		    if (substr($pClassName, 0, strlen($key)) == $key) {
    		        $newClass = $namespace.strtr(substr($pClassName, strlen($key)), '_', '\\');
    		        $this->wrapClass($pClassName, $newClass);
    		        return;
    		    }
    		}
    	}
    }
    
    private function wrapClass($legacyClass, $realClass) {
        common_Logger::w('Legacy classname "'.$legacyClass. '" referenced, please use "'.$realClass.'" instead');
        if(preg_match('/[^A-Za-z0-9_\\\\]/', $legacyClass) || preg_match('/[^A-Za-z0-9_\\\\]/', $realClass)){
            throw new Exception('Unknown characters in class name');
        }
        $classDefinition = 'class '.$legacyClass.' extends '.$realClass.' {}';
        eval($classDefinition);
    }
    
}

common_legacy_LegacyAutoLoader::register();
