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
 * @subpackage common_ext
 */
class common_ext_ExtensionsManager
{

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

        foreach ($this->extensions as $ext) {
        	if ($ext->isInstalled()) {
        		$returnValue[$ext->getID()] = $ext;
        	}
        }

        return (array) $returnValue;
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

        foreach ($this->extensions as $ext) {
        	if ($ext->isEnabled()) {
        		$returnValue[$ext->getID()] = $ext;
        	}
        }

        return (array) $returnValue;
    }

    /**
     * Add (it actually installs) an extension on the platform from a
     * ZIP archive containing it.
     *
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string id The ID that will be used by the platform to identify the extension.
     * @param  string extensionsZipPath The path to the ZIP file containing the source code of the extension.
     * @throws common_ext_ExtensionException If the extension cannot be installed correctly.
     */
    public function addExtension($id, $extensionsZipPath)
    {
		$fileUnzip = new fileUnzip($package_zip);
		$fileUnzip->unzipAll(EXTENSION_PATH);
		$newExt = $this->getExtensionById($id);
		$extInstaller = new common_ext_ExtensionInstaller($newExt);
		$extInstaller->install();
    }

    /**
     * remove Extension from the database, filesystem is not change
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  extension
     */
    public function removeExtension($extension)
    {
		foreach($this->getExtensionList() as $ext) {
			$required = $ext->getRequiredExtensions();

			throw new Exception('Extension removal not implemented.');
		}
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
			$extensionLoader = new common_ext_ExtensionLoader($extension);

			//handle dependances requirement
			foreach ($extension->getManifest()->getDependencies() as $ext) {
				if(!array_key_exists($ext, $this->extensions) && $ext != 'generis') {
					throw new common_ext_ExtensionException('Required Extension is Missing : ' . $ext);
				}
			}
			
			$extensionLoader->load();
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
		$this->loadInstalledExtensions();
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
				try {
					$ext = $this->getExtensionById($extId);
					if (!$ext->isInstalled()) {
						$returnValue[] = $ext;
					}
				} catch (common_ext_ExtensionException $exception) {
					common_Logger::d($extId.' is not an extension');
				}
			}
		}
		
		return $returnValue;
    }

    /**
     * modify the configuration.
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  array configurationArray array(extensionid => configuration)
     */
    public function modifyConfigurations($configurationArray)
    {
		foreach ($configurationArray as $id => $configuration) {
			$ext = $this->getExtensionById($id);
			$configuration->save($ext);
		}
    }

    /**
     * Reset the manager in order to take into account current extensions states
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     */
    public function reset()
    {
		$this->loadInstalledExtensions();
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

		foreach ($this->getInstalledExtensions() as $ext) {
			$returnValue = array_merge($returnValue, $ext->getManifest()->getModels());
		}
		$returnValue = array_unique($returnValue);

        return (array) $returnValue;
    }

    /**
     * Short description of method getUpdatableModels
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return array
     */
    public function getUpdatableModels()
    {
        $returnValue = array();

    	foreach ($this->getInstalledExtensions() as $ext) {
			foreach ($ext->getManifest()->getModelsRights() as $model=>$right){
				/*
				 *
				 * TODO
				 * We manage update, add read, delete ..
				 * if the variable exist, the model is updatable!
				 * use a code in the next investigation, such as unix right
				 *
				 */
				$ns = common_ext_NamespaceManager::singleton()->getNamespace ($model.'#');
				if ($ns == null) {
					throw new common_ext_ExtensionException("Could not get namespace for model ".$model);
				}
				$modelId = $ns->getModelId();
				if (!isset($returnValue[$modelId])){
					$returnValue[$modelId] = $model;
				}
			}
		}

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
        $returnValue = null;

        if (empty($id)) {
        	throw new common_ext_ExtensionException('No id specified for getExtensionById()');
        }
        if (!isset($this->extensions[$id])) {
        	$this->extensions[$id] = new common_ext_Extension($id, false);
        }
        // loads the extension if it hasn't been loaded yet
        $this->extensions[$id]->load();
        
        $returnValue = $this->extensions[$id];

        return $returnValue;
    }

    /**
     * Short description of method loadInstalledExtensions
     *
     * @access private
     * @author Joel Bout, <joel@taotesting.com>
     * @return mixed
     */
    private function loadInstalledExtensions()
    {
        $this->extensions = array();
        
    	$db = core_kernel_classes_DbWrapper::singleton();
		$query = 'SELECT * FROM "extensions"';
		$result = $db->query($query);

		while ($row = $result->fetch()){
			$id = $row["id"];
			$extension = new common_ext_Extension($id, true, $row);
			$this->extensions[$id] = $extension;
		}
    }

}

?>