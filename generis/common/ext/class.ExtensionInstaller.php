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
 *			   2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */


/**
 * Short description of class common_ext_ExtensionInstaller
 *
 * @access public
 * @author lionel.lecaque@tudor.lu
 * @package common
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 * @subpackage ext
 */
class common_ext_ExtensionInstaller
	extends common_ext_ExtensionHandler
{
	// --- ASSOCIATIONS ---


	// --- ATTRIBUTES ---

	/**
	 * States if local data must be installed or not.
	 *
	 * @access private
	 * @var boolean
	 */
	private $localData = false;

	// --- OPERATIONS ---

	/**
	 * install an extension
	 *
	 * @access public
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @return void
	 */
	public function install()
	{
		// section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017C4 begin
		
		common_Logger::i('Installing '.$this->extension->getID(), 'INSTALL');
		
		if ($this->extension->getID() == 'generis') {
			throw new common_ext_ForbiddenActionException('Tried to install generis using the ExtensionInstaller',
														  $this->extension->getID());
		}
		
		try{
			// not yet installed? 
			if ($this->extension->isInstalled()) {
				throw new common_ext_AlreadyInstalledException('Problem installing extension ' . $this->extension->getID() .' : Already installed',
															   $this->extension->getID());
			}
			else{
				// we purge the whole cache.
				$cache = common_cache_FileCache::singleton();
				$cache->purge();	
			
				//check dependances
				if(!$this->checkRequiredExtensions()){
					// unreachable code
				}
					
				// deprecated, but might still be used
				$this->installWriteConfig();
				$this->installOntology();
				$this->installRegisterExt();
				$this->installLoadConstants();
				$this->installExtensionModel();
					
				core_kernel_classes_Session::singleton()->update();
					
				$this->installCustomScript();
					
				if ($this->getLocalData() == true){
					$this->installLocalData();
				}
					
				// Method to be overriden by subclasses
				// to extend the installation mechanism.
				$this->extendedInstall();
			}
				
		}catch (common_ext_ExtensionException $e){
			// Rethrow
			throw $e;
		}

		// section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017C4 end
	}

	/**
	 * writes the config based on the config.sample
	 *
	 * @access protected
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @return void
	 */
	protected function installWriteConfig()
	{
		// section 127-0-1-1-6cdd9365:137e5078659:-8000:0000000000001A26 begin
		$sampleFile	= $this->extension->getDir().'includes/config.php.sample';
		$finalFile	= $this->extension->getDir().'includes/config.php';
		
		if (file_exists($sampleFile)) {
			common_Logger::d('Writing config '.$finalFile.' for '.$this->extension->getID(), 'INSTALL');
			$myConfigWriter = new tao_install_utils_ConfigWriter(
				$sampleFile,
				$finalFile
			);
			$myConfigWriter->createConfig();
			
			// @todo solve this
			if ($this->extension->getID() == 'tao') {
				require_once($finalFile);
			}
		} elseif (file_exists($finalFile)){
		    helpers_File::remove($finalFile);
		}
		
		// section 127-0-1-1-6cdd9365:137e5078659:-8000:0000000000001A26 end
	}

	/**
	 * inserts the datamodels
	 * specified in the Manifest
	 *
	 * @access protected
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @return void
	 */
	protected function installOntology()
	{
		// section 127-0-1-1-6cdd9365:137e5078659:-8000:0000000000001A24 begin
		// insert model
		$modelCreator = new tao_install_utils_ModelCreator(LOCAL_NAMESPACE);
		foreach ($this->extension->getManifest()->getInstallModelFiles() as $rdfpath) {
			if (file_exists($rdfpath)){
				if (is_readable($rdfpath)){
					$xml = simplexml_load_file($rdfpath);
					$attrs = $xml->attributes('xml', true);
					if(!isset($attrs['base']) || empty($attrs['base'])){
						throw new common_ext_InstallationException('The namespace of '.$rdfpath.' has to be defined with the "xml:base" attribute of the ROOT node');
					}
					$ns = (string) $attrs['base'];
					if($ns != 'LOCAL_NAMESPACE##'){
					    //import the model in the ontology
					    common_Logger::d('Inserting model '.$rdfpath.' for '.$this->extension->getID(), 'INSTALL');
					    $modelCreator->insertModelFile($ns, $rdfpath);
					  
					}else{
					    common_Logger::d('Inserting model '.$rdfpath.' for '.$this->extension->getID() . ' in LOCAL NAMESPACE', 'INSTALL');
					    $modelCreator->insertLocalModelFile($rdfpath);
					}
					foreach ($this->getTranslatedModelFiles($rdfpath) as $translation) {
						$modelCreator->insertModelFile($ns, $translation);
					}
				}
				else{
					throw new common_ext_InstallationException("Unable to load ontology in '${rdfpath}' because the file is not readable.");
				}
			}
			else{
				throw new common_ext_InstallationException("Unable to load ontology in '${rdfpath}' because the file does not exist.");
			}
		}
		// section 127-0-1-1-6cdd9365:137e5078659:-8000:0000000000001A24 end
	}

	/**
	 * returns the paths of the translations of a specified ontology file
	 *
	 * @access private
	 * @author Joel Bout, <joel@taotesting.com>
	 * @return array absolute paths to the translated rdf files
	 */
	private function getTranslatedModelFiles($rdfpath) {
		$returnValue = array();
		$localesPath = $this->extension->getDir() . 'locales' . DIRECTORY_SEPARATOR;
		if (file_exists($localesPath)) {
			$fileName = basename($rdfpath);
			foreach (new DirectoryIterator($localesPath) as $fileinfo) {
				if (!$fileinfo->isDot() && $fileinfo->isDir() && $fileinfo->getFilename() != '.svn') {
					$candidate = $fileinfo->getPathname() . DIRECTORY_SEPARATOR . $fileName;
					if (file_exists($candidate)) {
						$returnValue[] = $candidate;
					} 
				} 
			}
		}
		return $returnValue;
	}

	/**
	 * Registers the Extension with the extensionManager
	 *
	 * @access protected
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @return void
	 */
	protected function installRegisterExt()
	{
		// section 127-0-1-1-6cdd9365:137e5078659:-8000:0000000000001A28 begin
		common_Logger::d('Registering '.$this->extension->getID(), 'INSTALL');
		
		//add extension to db
		$db = core_kernel_classes_DbWrapper::singleton();
		$sql = "INSERT INTO extensions (id, name, version, loaded, \"loadAtStartUp\") VALUES ('".$this->extension->getID()."', '".$this->extension->getName()."', '".$this->extension->getVersion()."', 1, 1);";
		$db->exec($sql);
		
		common_Logger::d($this->extension->getID() . ' registered', 'INSTALL');
		
		//flush Manager
		common_ext_ExtensionsManager::singleton()->reset();
		common_Logger::d('Extension manager flush');
		
		// section 127-0-1-1-6cdd9365:137e5078659:-8000:0000000000001A28 end
	}

	/**
	 * Executes custom install scripts 
	 * specified in the Manifest
	 *
	 * @access protected
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @return void
	 */
	protected function installCustomScript()
	{
		// section 127-0-1-1-6cdd9365:137e5078659:-8000:0000000000001A2C begin
		//install script
		foreach ($this->extension->getManifest()->getInstallPHPFiles() as $script) {
			common_Logger::d('Running custom install script '.$script.' for ext '.$this->extension->getID(), 'INSTALL');
			require_once $script;
		}
		// section 127-0-1-1-6cdd9365:137e5078659:-8000:0000000000001A2C end
	}

	/**
	 * Installs example files and other non essential content
	 *
	 * @access protected
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @return void
	 */
	protected function installLocalData()
	{
		// section 127-0-1-1-6cdd9365:137e5078659:-8000:0000000000001A22 begin
		$localData = $this->extension->getManifest()->getLocalData();
		if(isset($localData['rdf'])){
			$modelCreator = new tao_install_utils_ModelCreator(LOCAL_NAMESPACE);
			foreach ($localData['rdf'] as $rdfpath) {
				if(file_exists($rdfpath)){
					common_Logger::d('Inserting local data rdf '.$rdfpath.' for '.$this->extension->getID(), 'INSTALL');
					$modelCreator->insertLocalModelFile($rdfpath);
				}
			}
		}
		if(isset($localData['php'])) {
			$scripts = $localData['php'];
			$scripts = is_array($scripts) ? $scripts : array($scripts);
			foreach ($scripts as $script) {
				common_Logger::d('Running local data script '.$script.' for ext '.$this->extension->getID(), 'INSTALL');
				require_once $script;
			}
		}
		// section 127-0-1-1-6cdd9365:137e5078659:-8000:0000000000001A22 end
	}

	/**
	 * Loads the /extension_folder/includes/constants.php file of the extension.
	 *
	 * @access public
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @return void
	 */
	public function installLoadConstants()
	{
		// section 127-0-1-1--38c6d12c:13cf1e375c3:-8000:0000000000001FCE begin
		common_Logger::i("Loading constants for extension '" . $this->extension->getID() . "'");
		$extLoader = new common_ext_ExtensionLoader($this->extension);
		$extLoader->load();
		// section 127-0-1-1--38c6d12c:13cf1e375c3:-8000:0000000000001FCE end
	}

	/**
	 * Instantiate the Extension/Module/Action model in the persistent memory of
	 *
	 * @access public
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @return void
	 */
	public function installExtensionModel()
	{
		// section 127-0-1-1--38c6d12c:13cf1e375c3:-8000:0000000000001FD1 begin
		common_Logger::i("Spawning Extension/Module/Action model for extension '" . $this->extension->getID() . "'");
		tao_helpers_funcACL_Model::spawnExtensionModel($this->extension);
		// section 127-0-1-1--38c6d12c:13cf1e375c3:-8000:0000000000001FD1 end
	}

	/**
	 * check required extensions are not missing
	 *
	 * @access protected
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @return boolean
	 */
	protected function checkRequiredExtensions()
	{
		$returnValue = (bool) false;

		// section -87--2--3--76--570dd3e1:12507aae5fa:-8000:00000000000023A2 begin
		$extensionManager = common_ext_ExtensionsManager::singleton();
		$installedExtArray = $extensionManager->getInstalledExtensions();
		foreach ($this->extension->getDependencies() as $requiredExt) {
			if(!array_key_exists($requiredExt,$installedExtArray)){
				throw new common_ext_MissingExtensionException('Extension '. $requiredExt . ' is needed by the extension to be installed but is missing.',
															   $requiredExt);
			}
		}
		$returnValue = true;
		// section -87--2--3--76--570dd3e1:12507aae5fa:-8000:00000000000023A2 end

		return (bool) $returnValue;
	}

	/**
	 * Instantiate a new ExtensionInstaller for a given Extension.
	 *
	 * @access public
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @param  Extension extension The extension to install
	 * @param  boolean localData Import local data or not.
	 * @return mixed
	 */
	public function __construct( common_ext_Extension $extension, $localData = true)
	{
		// section -64--88-56-1--cf3e319:137e64d7097:-8000:0000000000001A2B begin
		parent::__construct($extension);
		$this->setLocalData($localData);
		// section -64--88-56-1--cf3e319:137e64d7097:-8000:0000000000001A2B end
	}

	/**
	 * Sets localData field.
	 *
	 * @access public
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @param  boolean value
	 * @return mixed
	 */
	public function setLocalData($value)
	{
		// section -64--88-56-1--cf3e319:137e64d7097:-8000:0000000000001A3A begin
		$this->localData = $value;
		// section -64--88-56-1--cf3e319:137e64d7097:-8000:0000000000001A3A end
	}

	/**
	 * Retrieve localData field
	 *
	 * @access public
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @return boolean
	 */
	public function getLocalData()
	{
		$returnValue = (bool) false;

		// section -64--88-56-1--cf3e319:137e64d7097:-8000:0000000000001A3D begin
		$returnValue = $this->localData;
		// section -64--88-56-1--cf3e319:137e64d7097:-8000:0000000000001A3D end

		return (bool) $returnValue;
	}

	/**
	 * Short description of method extendedInstall
	 *
	 * @access public
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @return void
	 */
	public function extendedInstall()
	{
		// section 127-0-1-1--38c6d12c:13cf1e375c3:-8000:0000000000001FD6 begin
		return;
		// section 127-0-1-1--38c6d12c:13cf1e375c3:-8000:0000000000001FD6 end
	}

} /* end of class common_ext_ExtensionInstaller */

?>