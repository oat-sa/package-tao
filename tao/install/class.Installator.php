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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 *
 *
 * Installation main class
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package tao
 * @subpackage install
 */

class tao_install_Installator{

	static $defaultExtensions = array('tao','filemanager','taoItems','wfEngine','taoResults','taoTests','taoDelivery','taoGroups','taoSubjects', 'taoQTI', 'taoQtiTest', 'taoOpenWebItem', 'taoWfTest', 'taoSimpleDelivery', 'taoQtiCommon');

    protected $options = array();
	
	private $toInstall = array();
	
	private $escapedChecks = array();

	public function __construct($options)
	{
		if(!isset($options['root_path'])){
			throw new tao_install_utils_Exception("root_path option must be defined to perform installation.");
		}
		if(!isset($options['install_path'])){
			throw new tao_install_utils_Exception("install_path option must be defined to perform installation.");
		}

		$this->options = $options;

		if(substr($this->options['root_path'], -1) != DIRECTORY_SEPARATOR){
			$this->options['root_path'] .= DIRECTORY_SEPARATOR;
		}
		if(substr($this->options['install_path'], -1) != DIRECTORY_SEPARATOR){
			$this->options['install_path'] .= DIRECTORY_SEPARATOR;
		}
		
	}


	/**
	 * Run the TAO install from the given data
	 * @throws tao_install_utils_Exception
	 * @param $installData data coming from the install form
	 * @see tao_install_form_Settings
	 */
	public function install(array $installData)
	{
		$installData['module_namespace'] = preg_replace('/[^a-zA-Z0-9\/\.\-\_:]/','_',$installData['module_namespace']);

		try
		{
			/*
			 * 0 - Check input parameters. 
			 */
			common_Logger::i("Checking install data");
			self::checkInstallData($installData);
			
			common_Logger::i("Starting TAO install", 'INSTALL');
	        
			// Sanitize $installData if needed.
			if(!preg_match("/\/$/", $installData['module_url'])){
				$installData['module_url'] .= '/';
			}
			
			/*
			 *  1 - Check configuration with checks described in the manifest.
			 */
			$distribManifest = new common_distrib_Manifest(dirname(__FILE__) . '/../distributions.php');
			$distrib = $distribManifest->getDistributions();
			$distrib = $distrib[1]; // At the moment we only use the Open Source Distribution by default.
			$configChecker = $distrib->getConfigChecker();
			
			// Silence checks to have to be escaped.
			foreach ($configChecker->getComponents() as $c){
				if (method_exists($c, 'getName') && in_array($c->getName(), $this->getEscapedChecks())){
					$configChecker->silent($c);
				}
			}
			
			$reports = $configChecker->check();
			foreach ($reports as $r){
				$msg = $r->getMessage();
				$component = $r->getComponent();
				common_Logger::i($msg);

				if ($r->getStatus() !== common_configuration_Report::VALID && !$component->isOptional()){
					throw new tao_install_utils_Exception($msg);
				}
			}
			
			/*
			 *  2 - Test DB connection (done by the constructor)
			 */
			common_Logger::i("Spawning DbCreator", 'INSTALL');
			$dbCreatorClassName = tao_install_utils_DbCreator::getClassNameForDriver($installData['db_driver']);
			$dbCreator = new $dbCreatorClassName(
				$installData['db_host'],
				$installData['db_user'],
				$installData['db_pass'],
				$installData['db_driver'],
				$installData['db_name']
			);
			common_Logger::d("DbCreator spawned", 'INSTALL');
	
			/*
			 *   3 - Load the database schema
			 */
	
			// If the database already exists, drop all tables
			if ($dbCreator->dbExists($installData['db_name'])) {
				$dbCreator->cleanDb ($installData['db_name']);
				common_Logger::i("Dropped all tables", 'INSTALL');
			}
			// Else create it
			else {
				try {
					$dbCreator->createDatabase($installData['db_name']);
					common_Logger::i("Created database ".$installData['db_name'], 'INSTALL');
				} catch (Exception $e){
					throw new tao_install_utils_Exception('Unable to create the database, make sure that '.$installData['db_user'].' is granted to create databases. Otherwise create the database with your super user and give to  '.$installData['db_user'].' the right to use it.');
				}
				// If the target Sgbd is mysql select the database after creating it
				if ($installData['db_driver'] == 'mysql'){
					$dbCreator->setDatabase ($installData['db_name']);
				}
			}
	
			// Create tao tables
            common_Logger::i('db_driver : ' . $installData['db_driver'], 'INSTALL');
            if ($installData['db_driver'] == 'pdo_sqlsrv'){

                common_Logger::i('MS SQL DRIVER, load specific file', 'INSTALL');
                $dbCreator->setDatabase ($installData['db_name']);
                $dbCreator->load($this->options['install_path'].'db/tao_sqlsrv.sql', array('DATABASE_NAME' => $installData['db_name']));

            }
            else {
                $dbCreator->load($this->options['install_path'].'db/tao.sql', array('DATABASE_NAME' => $installData['db_name']));

            }
            common_Logger::i('Created tables', 'INSTALL');
			$storedProcedureFile = $this->options['install_path'].'db/tao_stored_procedures_' . str_replace('pdo_', '', $installData['db_driver']) . '.sql';
			if (file_exists($storedProcedureFile) && is_readable($storedProcedureFile)){
				common_Logger::i('Installing stored procedures for ' . $installData['db_driver'], 'INSTALL');
				$dbCreator->loadProc($storedProcedureFile);
			}
			
			/*
			 *  4 - Create the local namespace
			 */
			common_Logger::i('Creating local namespace', 'INSTALL');
			$dbCreator->execute("INSERT INTO models VALUES ('8', '{$installData['module_namespace']}', '{$installData['module_namespace']}#')");
	
			/*
			 *  5 - Create the generis config files
			 */
			
			common_Logger::d('Writing db config', 'INSTALL');
			$dbConfigWriter = new tao_install_utils_ConfigWriter(
					$this->options['root_path'].'generis/common/conf/sample/db.conf.php',
					$this->options['root_path'].'generis/common/conf/db.conf.php'
			);
			$dbConfigWriter->createConfig();
			$dbConfigWriter->writeConstants(array(
				'DATABASE_LOGIN'	=> $installData['db_user'],
				'DATABASE_PASS' 	=> $installData['db_pass'],
				'DATABASE_URL'	 	=> $installData['db_host'],
				'SGBD_DRIVER' 		=> $installData['db_driver'],
				'DATABASE_NAME' 	=> $installData['db_name']
			));
			
			common_Logger::d('Writing generis config', 'INSTALL');
			$generisConfigWriter = new tao_install_utils_ConfigWriter(
				$this->options['root_path'].'generis/common/conf/sample/generis.conf.php',
				$this->options['root_path'].'generis/common/conf/generis.conf.php'
			);
			
			$generisConfigWriter->createConfig();
			$generisConfigWriter->writeConstants(array(
				'LOCAL_NAMESPACE'			=> $installData['module_namespace'],
				'GENERIS_INSTANCE_NAME'		=> $installData['instance_name'],
				'GENERIS_SESSION_NAME'		=> self::generateSessionName(),
				'ROOT_PATH'					=> $this->options['root_path'],
				'ROOT_URL'					=> $installData['module_url'],
				'DEFAULT_LANG'				=> $installData['module_lang'],
				'DEBUG_MODE'				=> ($installData['module_mode'] == 'debug') ? true : false,
				'SYS_USER_LOGIN'			=> self::generateRandomAlphaNumToken(8),
				'SYS_USER_PASS'				=> md5(self::generateRandomAlphaNumToken(16))
			));
			
			/*
			 * 6 - Run the extensions bootstrap
			 */
			common_Logger::d('Running the extensions bootstrap', 'INSTALL');
			require_once $this->options['root_path'] . 'generis/common/inc.extension.php';

			// Init model creator and create the Generis User.
			$modelCreator = new tao_install_utils_ModelCreator(LOCAL_NAMESPACE);
			$modelCreator->insertGenerisUser(SYS_USER_LOGIN, SYS_USER_PASS);

			/*
			 * 7 - Add languages
			 */
			$models = $modelCreator->getLanguageModels();
	        foreach ($models as $ns => $modelFiles){
	            foreach ($modelFiles as $file){
	                common_Logger::d("Inserting language description model '".$file."'", 'INSTALL');
	            	$modelCreator->insertLocalModel($file);
	            }
	        }

			/*
			 * 8 - Finish Generis Install
			 */
			$generis = common_ext_ExtensionsManager::singleton()->getExtensionById('generis');
			$generisInstaller = new common_ext_GenerisInstaller($generis);
			$generisInstaller->install();
			
	        /*
			 * 9 - Install the extensions
			 */
			if(isset($installData['extensions'])) {
				$extensionIDs = explode(',',$installData['extensions']); 
			} else {
				$extensionIDs = self::$defaultExtensions;
			}
			$toInstall = array();
			foreach ($extensionIDs as $id) {
				try {
					$ext = common_ext_ExtensionsManager::singleton()->getExtensionById($id);
					if (!$ext->isInstalled()) {
						$toInstall[$id] = $ext;
					}
				} catch (common_ext_ExtensionException $e) {
					common_Logger::w('Extension '.$id.' not found');
				}
			}
			while (!empty($toInstall)) {
				$modified = false;
				foreach ($toInstall as $key => $extension) {
					// if all dependencies are installed
					$installed	= array_keys(common_ext_ExtensionsManager::singleton()->getInstalledExtensions());
					$missing	= array_diff($extension->getDependencies(), $installed);
					if (count($missing) == 0) {
						try {
						    $importLocalData = ($installData['import_local'] == true);
							$extinstaller = new tao_install_ExtensionInstaller($extension, $importLocalData);
							
							set_time_limit(60);
							
							$extinstaller->install();
						} catch (common_ext_ExtensionException $e) {
							common_Logger::w('Exception('.$e->getMessage().') during install for extension "'.$extension->getID().'"');
							throw new tao_install_utils_Exception("An error occured during the installation of extension '" . $extension->getID() . "'.");
						}
						unset($toInstall[$key]);
						$modified = true;
					} else {
						$missing = array_diff($missing, array_keys($toInstall));
						foreach ($missing as $extID) {
							$toInstall[$extID] = common_ext_ExtensionsManager::singleton()->getExtensionById($extID);
							$modified = true;
						}
					}
				}
				// no extension could be installed, and no new requirements was added
				if (!$modified) {
					throw new common_exception_Error('Unfulfilable/Cyclic reference found in extensions');
				}
			}
	
			/*
			 *  10 - Insert Super User
			 */
			common_Logger::i('Spawning SuperUser '.$installData['user_login'], 'INSTALL');
			$modelCreator->insertSuperUser(array(
				'login'			=> $installData['user_login'],
				'password'		=> md5($installData['user_pass1']),
				'userLastName'	=> $installData['user_lastname'],
				'userFirstName'	=> $installData['user_firstname'],
				'userMail'		=> $installData['user_email'],
				'userDefLg'		=> 'http://www.tao.lu/Ontologies/TAO.rdf#Lang'.$installData['module_lang'],
				'userUILg'		=> 'http://www.tao.lu/Ontologies/TAO.rdf#Lang'.$installData['module_lang']
			));
	
			/*
			 *  11 - Secure the install for production mode
			 */
			if($installData['module_mode'] == 'production'){
				$extensions = common_ext_ExtensionsManager::singleton()->getInstalledExtensions();
				common_Logger::i('Securing tao for production', 'INSTALL');
				
				// 11.1 Remove Generis User
				$dbCreator->execute('DELETE FROM "statements" WHERE "subject" = \'http://www.tao.lu/Ontologies/TAO.rdf#installator\' AND "modelID"=6');
	
				// 11.2 Protect TAO dist
	 			$shield = new tao_install_utils_Shield(array_keys($extensions));
	 			$shield->disableRewritePattern(array("!/test/", "!/doc/"));
	 			$shield->protectInstall();
			}

			/*
			 *  12 - Create the version file
			 */
			common_Logger::d('Creating version file for TAO', 'INSTALL');
			file_put_contents(ROOT_PATH.'version', TAO_VERSION);
			
	        /*
	         * 13 - Miscellaneous
	         */
	        // Localize item content for demo items.
	        $dbCreator->execute("UPDATE statements SET l_language = '" . $installData['module_lang'] . "' WHERE predicate = 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemContent'");
	        common_Logger::i('Installation completed', 'INSTALL');	
		}
		catch(Exception $e){
			// In any case, we transmit a single exception type (at the moment)
			// for a clearer API for client code.
            common_Logger::e('Error Occurs : ' . $e->getMessage() . $e->getTraceAsString(), 'INSTALL');
			throw new tao_install_utils_Exception($e->getMessage(), 0, $e);
		}
	}
	
	/**
     * Generate an alphanum token to be used as a PHP session name.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
	public static function generateSessionName(){
	 	return 'tao_' . self::generateRandomAlphaNumToken(8);
	}
	
	/**
     * Generate a random alphanum token of a given length.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param int $length The length of the token to generate.
     * @return string
     */
	public static function generateRandomAlphaNumToken($length){
		$token = '';
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $maxIndex = strlen($chars) - 1;
        
	    for ($i = 0; $i < $length; $i++) {
	    	$token .= $chars[rand(0, $maxIndex)];
	 	}
	 	
	 	return $token;
	}

	/**
	 * Check the install data information such as
	 * - instance name
	 * - database driver
	 * - ...
	 * 
	 * If a parameter of the $installData is not valid regarding the install
	 * business rules, an MalformedInstall
	 * 
	 * @param array $installData
	 */
	public static function checkInstallData(array $installData){
		// instance name
		if (empty($installData['instance_name'])){
			$msg = "Missing install parameter 'instance_name'.";
			throw new tao_install_utils_MalformedParameterException($msg);
		}
		else if (!is_string($installData['instance_name'])){
			$msg = "Malformed install parameter 'instance_name'. It must be a string.";
			throw new tao_install_utils_MalformedParameterException($msg);
		}
		else if (1 === preg_match('/\s/u', $installData['instance_name'])){
			$msg = "Malformed install parameter 'instance_name'. It cannot contain spacing characters (tab, backspace).";
			throw new tao_install_utils_MalformedParameterException($msg);
		}
	}
	
	/**
	 * Tell the Installator instance to not take into account
	 * a Configuration Check with ID = $id.
	 * 
	 * @param string $id The identifier of the check to escape.
	 */
	public function escapeCheck($id){
		$checks = $this->getEscapedChecks();
		array_push($checks, $id);
		$checks = array_unique($checks);
		$this->setEscapedChecks($checks);
	}
	
	/**
	 * Obtain an array of Configuration Check IDs to be escaped by
	 * the Installator.
	 * 
	 * @return array 
	 */
	public function getEscapedChecks(){
		return $this->escapedChecks;
	}
	
	/**
	 * Set the array of Configuration Check IDs to be escaped by
	 * the Installator.
	 * 
	 * @param array $escapedChecks An array of strings.
	 * @return void
	 */
	public function setEscapedChecks(array $escapedChecks){
		$this->escapedChecks = $escapedChecks;
	}
	
	/**
	 * Informs you if a given Configuration Check ID corresponds
	 * to a Check that has to be escaped.
	 */
	public function isEscapedCheck($id){
		return in_array($id, $this->getEscapedChecks());
	}
}
?>
