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
 * Short description of class common_ext_Extension
 *
 * @access public
 * @author lionel.lecaque@tudor.lu
 * @package common
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 * @subpackage ext
 */
class common_ext_Extension
{

    /**
     * Short description of attribute id
     *
     * @access private
     * @var string
     */
    private $id = '';

    /**
     * Short description of attribute manifest
     *
     * @access public
     * @var Manifest
     */
    public $manifest = null;

    /**
     * configuration array read from db
     *
     * @access private
     * @var array
     */
    private $dbConfig = null;

    /**
     * configuration array read from file
     *
     * @access private
     * @var array
     */
    private $fileConfig = null;

    /**
     * Whenever or not an extension has already been loaded
     *
     * @access private
     * @var boolean
     */
    protected $loaded = false;

    /**
     * Should not be called directly, please use ExtensionsManager
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string id
     * @param  boolean installed
     * @param  array data array to preload the dbconfiguration
     * @return mixed
     */
    public function __construct($id, $installed = false, $data = null)
    {
		$this->id = $id;
		$this->installed = $installed;
    	$manifestFile = $this->getDir().MANIFEST_NAME;
		if(is_file($manifestFile)){
			$this->manifest = new common_ext_Manifest($manifestFile);
		} else {
			//Here the extension is set unvalided to not be displayed by the view
			throw new common_ext_ManifestNotFoundException("Extension Manifest not found for extension '${id}'.", $id);
		}
		$this->dbConfig = $data;
    }

    /**
     * returns the path to the config file
     * used for instalation specific configurations
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    private function getConfigFilePath()
    {
        $returnValue = (string) '';

        $returnValue = $this->getDir().'includes'.DIRECTORY_SEPARATOR.'config.php';

        return (string) $returnValue;
    }

    /**
     * returns the id of the extension
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    public function getID()
    {
        $returnValue = (string) '';

        return $this->id;

        return (string) $returnValue;
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
        $returnValue = array();

        $returnValue = $this->manifest->getConstants();

        return (array) $returnValue;
    }

    /**
     * returns all configuration key/value pairs
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @return array
     */
    private function getConfigs()
    {
        $returnValue = array();

        if(is_null($this->dbConfig)) {
        	$db = core_kernel_classes_DbWrapper::singleton();
			$query = "SELECT loaded,\"loadAtStartUp\",ghost FROM extensions WHERE id = ?";
			$sth = $db->prepare($query);
			$success = $sth->execute(array($this->id));

			if ($success && $row = $sth->fetch()){
				$this->dbConfig = $row;
				$sth->closeCursor();	
			} else {
				common_Logger::w('Unable to load dbconfig for '.$this->getID());
				$this->dbConfig = array();
			}
			
        }
        if (is_null($this->fileConfig)) {
			$this->fileConfig = array();
        	$configFile = $this->getConfigFilePath();
			if (file_exists($configFile)) {
				$data = include $configFile;
				if (is_array($data)) {
					$this->fileConfig = $data;
				}
			}
        }
        $returnValue = array_merge($this->dbConfig, $this->fileConfig);

        return (array) $returnValue;
    }

    /**
     * sets a configuration value
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string key
     * @param  value
     * @return mixed
     */
    public function setConfig($key, $value)
    {
		$this->fileConfig[$key] = $value;
		$handle = fopen($this->getConfigFilePath(), 'w');
        $success = fwrite($handle, '<?php return '.common_Utils::toPHPVariableString($this->fileConfig).';');
        fclose($handle);
        if (!$success) {
			throw new common_exception_Error('Unable to write config for extension '.$this->getID());
        }
    }

    /**
     * retrieves a configuration value
	 * returns null if not found
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string key
     * @return mixed
     */
    public function getConfig($key)
    {
        $returnValue = null;

        $config = $this->getConfigs();
        if (isset($config[$key])) {
        	$returnValue = $config[$key]; 
        } else {
        	common_Logger::w('Unknown config key '.$key.' used for extension '.$this->getID());
        }

        return $returnValue;
    }

    /**
     * removes a configuration entry
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string key
     * @return mixed
     */
    public function unsetConfig($key)
    {
        unset($this->fileConfig[$key]);
        $handle = fopen($this->getConfigFilePath(), 'w');
        fwrite($handle, '<? return '.common_Utils::toPHPVariableString($this->fileConfig).';');
        fclose($handle);
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
        $returnValue = (string) '';

        $returnValue = $this->manifest->getVersion();

        return (string) $returnValue;
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
        $returnValue = (string) '';

        $returnValue = $this->manifest->getAuthor();

        return (string) $returnValue;
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
        $returnValue = (string) '';

        $returnValue = $this->manifest->getName();

        return (string) $returnValue;
    }

    /**
     * returns whenever or not the extension is enabled
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return boolean
     */
    public function isEnabled()
    {
        $returnValue = (bool) false;

    	if ($this->isInstalled()) {
        	$returnValue = !$this->getConfig('ghost');
        }

        return (bool) $returnValue;
    }

    /**
     * returns whenever or not the extension is installed
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return boolean
     */
    public function isInstalled()
    {
        $returnValue = (bool) false;

        $returnValue = $this->installed;

        return (bool) $returnValue;
    }

    /**
     * returns the base dir of the extension
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    public function getDir()
    {
        $returnValue = (string) '';

		$returnValue = EXTENSION_PATH.$this->getID().DIRECTORY_SEPARATOR;

        return (string) $returnValue;
    }

    /**
     * Retrieves a constant from the manifest.php file of the extension.
     *
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string key
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

        if (file_exists($this->getConstant('DIR_ACTIONS'))) {
			$dir = new DirectoryIterator($this->getConstant('DIR_ACTIONS'));
		    foreach ($dir as $fileinfo) {
				if(preg_match('/^class\.[^.]*\.php$/', $fileinfo->getFilename())) {
					$module = substr($fileinfo->getFilename(), 6, -4);
					$class = $this->getID().'_actions_'.$module;
					if (class_exists($class)) {
						if (is_subclass_of($class, 'Module')) {
							$returnValue[$module] = $class;
						} else {
							common_Logger::w($class.' does not inherit Module');
						}
					} else {
						common_Logger::w($class.' not found for file \''.$fileinfo->getFilename().'\'');
					}
				}
			}
        }

        return (array) $returnValue;
    }

    /**
     * returns a module by ID
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string id
     * @return Module
     */
    public function getModule($id)
    {
        $returnValue = null;

    	$className = $this->getID().'_actions_'.$id;
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

        $returnValue = array();
        foreach ($this->getManifest()->getDependencies() as $id) {
        	$returnValue[] = $id;
        	$dependence = common_ext_ExtensionsManager::singleton()->getExtensionById($id);
        	$returnValue = array_unique(array_merge($returnValue, $dependence->getDependencies()));
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
        $returnValue = null;

        $returnValue = $this->manifest;

        return $returnValue;
    }

    /**
     * Get the Management Role of the Extension. Returns null in case of no
     * Role for the Extension.
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return core_kernel_classes_Resource
     */
    public function getManagementRole()
    {
        $returnValue = null;

        $manifest = $this->getManifest();
        $returnValue = $manifest->getManagementRole();

        return $returnValue;
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
		$manifest = $this->getManifest();
		return $manifest->getOptimizableClasses();
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
		$manifest = $this->getManifest();
		return $manifest->getOptimizableProperties();
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
		if (!$this->loaded) {
			$loader = new common_ext_ExtensionLoader($this);
			$loader->load();
			$this->loaded = true;
		}
		
	}
}