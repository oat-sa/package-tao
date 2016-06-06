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
 * 				 2013-2014 (update and modification) Open Assessment Technologies SA;
 *
 */


/**
 * Short description of class common_ext_Extension
 *
 * @access public
 * @author lionel.lecaque@tudor.lu
 * @package generis
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 
 */
class common_ext_Extension
{
    /**
     * Filename of the manifest
     * 
     * @var string
     */
    const MANIFEST_NAME = 'manifest.php';
    
    /**
     * Short description of attribute id
     *
     * @access private
     * @var string
     */
    private $id = '';

    /**
     * The manifest of the extension
     *
     * @access public
     * @var common_ext_Manifest
     */
    public $manifest;

    /**
     * Whenever or not an extension has already been loaded
     *
     * @access private
     * @var boolean
     */
    protected $loaded = false;
    
    private $configPersistence;

    /**
     * Should not be called directly, please use ExtensionsManager
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     *
     * @param string $id
     * @param string $deprecated1
     * @param string $deprecated2
     *
     * @throws common_ext_ExtensionException
     * @throws common_ext_ManifestNotFoundException
     */
    public function __construct($id, $deprecated1 = null, $deprecated2 = null)
    {
		$this->id = $id;
    	$manifestFile = $this->getDir().self::MANIFEST_NAME;
		if(is_file($manifestFile)){
			$this->manifest = new common_ext_Manifest($manifestFile);
		} else {
			//Here the extension is set unvalided to not be displayed by the view
			throw new common_ext_ManifestNotFoundException("Extension Manifest not found for extension '${id}'.", $id);
		}
    }

    /**
     * returns the id of the extension
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * returns all constants of the extension
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return array
     */
    public function getConstants()
    {
        return (array) $this->manifest->getConstants();
    }

    /**
     * CONFIGURATION 
     */

    /**
     * Returns the KV persistence to use for configurations
     * @return common_persistence_KeyValuePersistence
     */
    private function getConfigPersistence()
    {
        return common_ext_ConfigDriver::singleton();
    }
    
    /**
     * checks if a configuration value exists
     *
     * @param  string $key
     * @return boolean
     */
    public function hasConfig($key)
    {
        return $this->getConfigPersistence()->exists($this->getId().'/'.$key);
    }
    
    /**
     * sets a configuration value
     *
     * @param  string $key
     * @param  $value
     * @return boolean
     */
    public function setConfig($key, $value)
    {
        return $this->getConfigPersistence()->set($this->getId().'/'.$key, $value);
    }

    /**
     * retrieves a configuration value
	 * returns false if not found
     *
     * @param  string $key
     * @return mixed
     */
    public function getConfig($key)
    {
        return $this->getConfigPersistence()->get($this->getId().'/'.$key);
    }

    /**
     * removes a configuration entry
     *
     * @param  string $key
     * @return mixed
     */
    public function unsetConfig($key)
    {
        return $this->getConfigPersistence()->del($this->getId().'/'.$key);
    }

    /**
     * returns the version of the extension
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    public function getVersion()
    {
        return (string) $this->manifest->getVersion();
    }

    /**
     * returns the author of the extension
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    public function getAuthor()
    {
        return (string) $this->manifest->getAuthor();
    }

    /**
     * returns the name of the extension
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    public function getName()
    {
        return (string) $this->manifest->getName();
    }

    /**
     * returns the base dir of the extension
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     * @throws common_ext_ExtensionException
     */
    public function getDir()
    {
        if ( ! defined( 'EXTENSION_PATH' )) {
            throw new common_ext_ExtensionException( 'System constants are not yet defined' );
        }

        return EXTENSION_PATH . $this->getId() . DIRECTORY_SEPARATOR;
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function hasConstant($key)
    {
        $constants = $this->getConstants();
        return isset($constants[$key]);
    }
    
    /**
     * Retrieves a constant from the manifest.php file of the extension.
     *
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string $key
     * @return mixed
     * @throws common_exception_Error If the constant cannot be found.
     */
    public function getConstant($key)
    {
        $returnValue = null;

        $constants = $this->getConstants();
        if (isset($constants[$key])) {
        	$returnValue = $constants[$key];
        } elseif (defined($key)) {
        	common_logger::w('constant outside of extension called: '.$key);
        	$returnValue = constant($key);
        } else {
        	throw new common_exception_Error('Unknown constant \''.$key.'\' for extension '.$this->id);
        }

        return $returnValue;
    }

    /**
     * get all modules of the extension
     * by searching the actions directory, not the ontology
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return array
     */
    public function getAllModules()
    {
        $returnValue = array();

        // routes
        $namespaces = array();
        foreach ($this->getManifest()->getRoutes() as $mapedPath => $ns) {
            $namespaces[] = trim($ns, '\\');
        }
        if (!empty($namespaces)) {
        	common_Logger::d('Namespace not empty for extension '. $this->getId() );
            $recDir = new RecursiveDirectoryIterator($this->getDir());
            $recIt = new RecursiveIteratorIterator($recDir);
            $regexIt = new RegexIterator($recIt, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
            foreach ($regexIt as $entry) {
                $info = helpers_PhpTools::getClassInfo($entry[0]);
                if (!empty($info['ns'])) {
                    $ns = trim($info['ns'], '\\');
                    if (!empty($info['ns']) && in_array($ns, $namespaces)) {
                        $returnValue[$info['class']] = $ns.'\\'.$info['class'];
                    }
                }
            }
        } 
        // legacy
        if ($this->hasConstant('DIR_ACTIONS') && file_exists($this->getConstant('DIR_ACTIONS'))) {
			$dir = new DirectoryIterator($this->getConstant('DIR_ACTIONS'));
		    foreach ($dir as $fileinfo) {
				if(preg_match('/^class\.[^.]*\.php$/', $fileinfo->getFilename())) {
					$module = substr($fileinfo->getFilename(), 6, -4);
					$returnValue[$module] = $this->getId().'_actions_'.$module;
				}
			}
        }
        
        // validate the classes
        foreach (array_keys($returnValue) as $key) {
            $class = $returnValue[$key];
            if (!class_exists($class)) {
				common_Logger::w($class.' not found');
				unset($returnValue[$key]);
            } elseif (!is_subclass_of($class, 'Module')) {
				common_Logger::w($class.' does not inherit Module');
				unset($returnValue[$key]);
            } 
        }
        
        return (array) $returnValue;
    }

    /**
     * returns a module by ID
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string $id
     * @return Module
     */
    public function getModule($id)
    {
        $returnValue = null;

    	$className = $this->getId().'_actions_'.$id;
		if(class_exists($className)) {
			$returnValue = new $className;
		} else {
			common_Logger::e('could not load '.$className);
		}

        return $returnValue;
    }

    /**
     * returns the extension the current extension
     * depends on recursively
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return array
     */
    public function getDependencies()
    {
        $returnValue = array();
        foreach ($this->getManifest()->getDependencies() as $id => $version) {
        	$returnValue[$id] = $version;
        	$dependence = common_ext_ExtensionsManager::singleton()->getExtensionById($id);
        	$returnValue = array_merge($returnValue, $dependence->getDependencies());
        }

        return (array) $returnValue;
    }

    /**
     * returns the manifest of the extension
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return common_ext_Manifest
     */
    public function getManifest()
    {
        return $this->manifest;
    }

    /**
     * Get the Management Role of the Extension. Returns null in case of no
     * Role for the Extension.
     * 
     * Removing all generis references from framework, please use the Manifest::getManagementRoleUri()
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return core_kernel_classes_Resource
     * @deprecated
     * @see common_ext_Manifest::getManagementRoleUri()
     */
    public function getManagementRole()
    {
        return $this->getManifest()->getManagementRole();
    }
    
    /**
     * Get an array of Class URIs (as strings) that are considered optimizable by the Extension.
     * 
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return array
     */
    public function getOptimizableClasses()
	{
		return $this->getManifest()->getOptimizableClasses();
	}
	
	public function getPhpNamespace()
	{
	    return $this->getManifest()->getPhpNamespace();
	}	

	/**
	 * Get an array of Property URIs (as strings) that are considered optimizable by the Extension.
	 *
	 * @access public
	 * @author Jerome Bogaerts <jerome@taotesting.com>
	 * @return array
	 */
	public function getOptimizableProperties()
	{
		return $this->getManifest()->getOptimizableProperties();
	}

	/**
	 * Whenever or not the extension and it's constants have been loaded
	 * @return boolean
	 */
	public function isLoaded()
	{
		return $this->loaded;
	}
	
	/**
	 * Loads the extension if it hasn't been loaded (using load), yet
	 */
	public function load()
	{
		if (!$this->isLoaded()) {
			$loader = new common_ext_ExtensionLoader($this);
			$loader->load();
			$this->loaded = true;
		}
		
	}
}
