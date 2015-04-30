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


/**
 * The ExtensionsManager class is dedicated to Extensions Management. It provides
 * methods to know if an extension is enabled/disabled, obtain the list of currently
 * available/installed extensions, the models that have to be loaded to run the extensions,
 * obtain a reference on a particular test case.
 *
 * @access public
 * @authorlionel@taotesting.com
 * @package generis
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 
 */
class common_ext_ExtensionsManager
{
    const EXTENSIONS_CONFIG_KEY = 'installation';

    public static $RESERVED_WORDS = array(
    	'config', 'data', 'vendor', 'tests'
    );

    /**
     * The extensions currently loaded. The array contains
     * references on common_ext_Extension class instances.
     *
     * @access private
     * @var array
     */
    private $extensions = array();

    /**
     * Singleton instance of common_ext_ExtensionsManager
     *
     * @access private
     * @var common_ext_ExtensionsManager
     */
    private static $instance = null;

    /**
     * Obtain a reference on a unique common_ext_ExtensionsManager
     * class instance.
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return common_ext_ExtensionsManager
     */
    public static function singleton()
    {
        $returnValue = null;

		if (!isset(self::$instance)) {
			self::$instance = new self();
		}
		$returnValue = self::$instance;

        return $returnValue;
    }

    /**
     * Get the set of currently installed extensions. This method
     * returns an array of common_ext_Extension.
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return array
     */
    public function getInstalledExtensions()
    {
        $returnValue = array();

        $installed = $this->getExtensionById('generis')->getConfig(self::EXTENSIONS_CONFIG_KEY);
        if (is_array($installed)) {
            foreach (array_keys($installed) as $extId) {
                $returnValue[$extId] = $this->getExtensionById($extId);
            }
        }

        return (array) $returnValue;
    }

    /**
     * Load all extensions that have to be loaded
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     */
    public function loadExtensions()
    {
		foreach($this->extensions as $extension) {
			//handle dependances requirement
			foreach ($extension->getManifest()->getDependencies() as $ext => $version) {
				if(!array_key_exists($ext, $this->extensions) && $ext != 'generis') {
					throw new common_ext_ExtensionException('Required Extension is Missing : ' . $ext);
				}
			}
			$extension->load();
		}
    }

    /**
     * Creates a new instance of common_ext_ExtensionsManager
     *
     * @access private
     * @author Joel Bout, <joel@taotesting.com>
     */
    private function __construct()
    {
    }

    /**
     * Call a service to retrieve list of extensions that may be installed.
     * This method returns an array of common_ext_Extension.
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return array
     */
    public function getAvailableExtensions()
    {
        $returnValue = array();
		$dir = new DirectoryIterator(ROOT_PATH);
		foreach ($dir as $fileinfo) {
			if ($fileinfo->isDir() && !$fileinfo->isDot() && substr($fileinfo->getBasename(), 0, 1) != '.') {
				$extId = $fileinfo->getBasename();
				if (!in_array($extId, self::$RESERVED_WORDS) && !$this->isInstalled($extId)) {
    				try {
    					$ext = $this->getExtensionById($extId);
    					$returnValue[] = $ext;
    				} catch (common_ext_ExtensionException $exception) {
    					common_Logger::d($extId.' is not an extension');
    				}
				}
			}
		}
		
		return $returnValue;
    }

    /**
     * Short description of method getModelsToLoad
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return array
     */
    public function getModelsToLoad()
    {
        $returnValue = array();

		foreach ($this->getEnabledExtensions() as $ext) {
			$returnValue = array_merge($returnValue, $ext->getManifest()->getModels());
		}
		$returnValue = array_unique($returnValue);

        return (array) $returnValue;
    }

    /**
     * Get an extension by Id. If the extension is not yet loaded, it will be
     * loaded using common_ext_Extension::load.
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string id The id of the extension.
     * @return common_ext_Extension A common_ext_Extension instance or null if it does not exist.
     * @throws common_ext_ExtensionException If the provided id is empty.
     */
    public function getExtensionById($id)
    {
        if (!is_string($id) || strlen($id) == 0) {
        	throw new common_ext_ExtensionException('No id specified for getExtensionById()');
        }
        if (!isset($this->extensions[$id])) {
        	$this->extensions[$id] = new common_ext_Extension($id, false);
        }
        // loads the extension if it hasn't been loaded yet
        $this->extensions[$id]->load();
        
        return $this->extensions[$id];
    }
    
    public function isEnabled($extensionId)
    {
        $exts = $this->getExtensionById('generis')->getConfig(self::EXTENSIONS_CONFIG_KEY);
        return isset($exts[$extensionId]['enabled']) ? $exts[$extensionId]['enabled'] : false;
    }
    
    public function isInstalled($extensionId)
    {
        $exts = $this->getExtensionById('generis')->getConfig(self::EXTENSIONS_CONFIG_KEY);
        return isset($exts[$extensionId]);
    }
    
    public function getInstalledVersion($extensionId)
    {
        $exts = $this->getExtensionById('generis')->getConfig(self::EXTENSIONS_CONFIG_KEY);
        return isset($exts[$extensionId]) ? $exts[$extensionId]['installed'] : null;
    }
    
    

    public function setEnabled($extensionId, $enabled = true)
    {
        $exts = $this->getExtensionById('generis')->getConfig(self::EXTENSIONS_CONFIG_KEY);
        if (!isset($exts[$extensionId])) {
            throw new common_exception_Error('Extension '.$extensionId.' unkown, cannot enable/disable');
        }
        $exts[$extensionId]['enabled'] = (boolean) $enabled;
        return $this->getExtensionById('generis')->setConfig(self::EXTENSIONS_CONFIG_KEY, $exts);
    }
    
    /**
     * Get the set of currently enabled extensions. This method
     * returns an array of common_ext_Extension.
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return array
     */
    public function getEnabledExtensions()
    {
        $returnValue = array();

        $enabled = $this->getExtensionById('generis')->getConfig(self::EXTENSIONS_CONFIG_KEY);
        foreach ($this->getInstalledExtensions() as $ext) {
            if (isset($enabled[$ext->getId()]) && $enabled[$ext->getId()]['enabled']) {
                $returnValue[$ext->getId()] = $ext;
            }
        }
    
        return (array) $returnValue;
    }
    
    /**
     * Add the end of an installation register the new extension
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param common_ext_Extension $extension
     * @return boolean
     */
    public function registerExtension(common_ext_Extension $extension)
    {
        $entry = array(
        	'installed' => $extension->getManifest()->getVersion(),
            'enabled' => false 
        );
        $extensions = $this->getExtensionById('generis')->getConfig(self::EXTENSIONS_CONFIG_KEY);
        $extensions[$extension->getId()] = $entry;
        return $this->getExtensionById('generis')->setConfig(self::EXTENSIONS_CONFIG_KEY, $extensions);
    }
    
    /**
     * Add the end of an uninstallation unregister the extension
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param common_ext_Extension $extension
     * @return boolean
     */
    public function unregisterExtension(common_ext_Extension $extension)
    {
        $extensions = $this->getExtensionById('generis')->getConfig(self::EXTENSIONS_CONFIG_KEY);
        unset($extensions[$extension->getId()]);
        $this->getExtensionById('generis')->setConfig(self::EXTENSIONS_CONFIG_KEY, $extensions);
    }
}