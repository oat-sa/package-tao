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
     * protect the cunstructer, singleton pattern
     */
    private function __construct() {
    }
    
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
    		if (file_exists(GENERIS_BASE_PATH .$filePath)){
    			require_once GENERIS_BASE_PATH .$filePath;
    			return;
    		}
    		$filePathInterface = '/' . $path . 'interface.'.$tokens[$size-1] . '.php';
    		if (file_exists(GENERIS_BASE_PATH .$filePathInterface)){
    			require_once GENERIS_BASE_PATH .$filePathInterface;
    			return;
    		}
    		
    		if (file_exists(ROOT_PATH .$filePath)){
    			require_once ROOT_PATH .$filePath;
    			return;
    		} elseif (file_exists(ROOT_PATH .$filePathInterface)){
    		        require_once ROOT_PATH .$filePathInterface;
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
    	else{
    		$this->autoloadClearFw($pClassName);
    	}
    }
    
    private function wrapClass($legacyClass, $realClass) {
        if(preg_match('/[^A-Za-z0-9_\\\\]/', $legacyClass) || preg_match('/[^A-Za-z0-9_\\\\]/', $realClass)){
            throw new Exception('Unknown characters in class name');
        }
        $classDefinition = 'class '.$legacyClass.' extends '.$realClass.' {}';
        eval($classDefinition);
    }
    
    protected function autoloadClearFw($pClassName) {
        $packages = array(DIR_CORE,DIR_CORE_HELPERS,DIR_CORE_UTILS);
        foreach($packages as $path) {
            if (file_exists($path. $pClassName . '.class.php')) {
				require_once $path . $pClassName . '.class.php';
				return;
            }
        }
    }
    
}