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
 *               2013-2014 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

/**
 *
 *
 * Installation main class
 *
 * @access public
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @package tao
 */

class tao_install_Installator{

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

			if(isset($installData['extensions'])) {
			    $extensionIDs = is_array($installData['extensions'])
			     ? $installData['extensions']
			     : explode(',',$installData['extensions']);
			} else {
			    $extensionIDs = array('taoCe');
			}
			
			/*
			 *  1 - Check configuration with checks described in the manifest.
			 */
			$configChecker = tao_install_utils_ChecksHelper::getConfigChecker($extensionIDs);
			
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
// 			$dbCreatorClassName = tao_install_utils_DbCreator::getClassNameForDriver($installData['db_driver']);
// 			$dbCreator = new $dbCreatorClassName(
// 				$installData['db_host'],
// 				$installData['db_user'],
// 				$installData['db_pass'],
// 				$installData['db_driver'],
// 				$installData['db_name']
// 			);
			$dbName = $installData['db_name'];
			if($installData['db_driver'] == 'pdo_oci'){
				$installData['db_name'] = $installData['db_host'];
				$installData['db_host'] = '';
			}
			$dbConfiguration = array(
						'driver' => $installData['db_driver'],
						'host' => $installData['db_host'],
						'dbname' => $installData['db_name'],
						'user' => $installData['db_user'],
						'password' => $installData['db_pass'],
	
			);
			if($installData['db_driver'] == 'pdo_mysql'){
			    $dbConfiguration['dbname'] = '';
			}
			if($installData['db_driver'] == 'pdo_oci'){
				$dbConfiguration['wrapperClass'] = 'Doctrine\DBAL\Portability\Connection';
				$dbConfiguration['portability'] = \Doctrine\DBAL\Portability\Connection::PORTABILITY_ALL;
				$dbConfiguration['fetch_case'] = PDO::CASE_LOWER;
			
			}
			$dbCreator = new tao_install_utils_DbalDbCreator($dbConfiguration);
			
			common_Logger::d("DbCreator spawned", 'INSTALL');
	           
			/*
			 *   3 - Load the database schema
			 */
	
			// If the database already exists, drop all tables
			if ($dbCreator->dbExists($dbName)) {
				
				try {
				    //If the target Sgbd is mysql select the database after creating it
				    if ($installData['db_driver'] == 'pdo_mysql'){
				        $dbCreator->setDatabase($installData['db_name']);
				    }
					$dbCreator->cleanDb($dbName);
					
				} catch (Exception $e){
					common_Logger::i('Problem cleaning db will try to erase the whole db');
					try {
					$dbCreator->destroyTaoDatabase($dbName);
					} catch (Exception $e){
						common_Logger::i('isssue during db cleaning : ' . $e->getMessage());
					}
				}
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
				
				//If the target Sgbd is mysql select the database after creating it
				if ($installData['db_driver'] == 'pdo_mysql'){
				    $dbCreator->setDatabase($installData['db_name']);
				}

			}
	
			// Create tao tables
			$dbCreator->initTaoDataBase();	
            common_Logger::i('Created tables', 'INSTALL');
            
			$storedProcedureFile = $this->options['install_path'].'db/tao_stored_procedures_' . str_replace('pdo_', '', $installData['db_driver']) . '.sql';
			if (file_exists($storedProcedureFile) && is_readable($storedProcedureFile)){
				common_Logger::i('Installing stored procedures for ' . $installData['db_driver'], 'INSTALL');
				$dbCreator->loadProc($storedProcedureFile);
			}
			else {
			    common_Logger::e('Could not find storefile : ' . $storedProcedureFile);
			}
			
			/*
			 *  4 - Create the local namespace
			 */
// 			common_Logger::i('Creating local namespace', 'INSTALL');
// 			$dbCreator->addLocalModel('8',$installData['module_namespace']);
// 			$dbCreator->addModels();
			
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
                'FILES_PATH'                => $installData['file_path'],
				'ROOT_URL'					=> $installData['module_url'],
				'DEFAULT_LANG'				=> $installData['module_lang'],
				'DEBUG_MODE'				=> ($installData['module_mode'] == 'debug') ? true : false,
			    'TIME_ZONE'                  => $installData['timezone']
			));
			
			/*
			 * 5b - Prepare the file/cache folder (FILES_PATH/GENERIS_CACHE_PATH not yet defined)
			 * @todo solve this more elegantly
			 */
			helpers_File::emptyDirectory($installData['file_path']);
			mkdir($installData['file_path'] . 'generis' . DIRECTORY_SEPARATOR. 'cache', 0700, true);
			
			/*
			 * 6 - Run the extensions bootstrap
			 */
			common_Logger::d('Running the extensions bootstrap', 'INSTALL');
			require_once $this->options['root_path'] . 'generis/common/inc.extension.php';

			// Init model creator and create the Generis User.
			$modelCreator = new tao_install_utils_ModelCreator(LOCAL_NAMESPACE);
			$modelCreator->insertGenerisUser(helpers_Random::generateString(8));

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
			
			$toInstall = array();
			foreach ($extensionIDs as $id) {
				try {
					$ext = common_ext_ExtensionsManager::singleton()->getExtensionById($id);
					
					if (!common_ext_ExtensionsManager::singleton()->isInstalled($ext->getId())) {
					    common_Logger::d('Extension ' . $id . ' needs to be installed');
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
				    common_Logger::d('Considering extension ' . $key);
					$installed	= array_keys(common_ext_ExtensionsManager::singleton()->getInstalledExtensions());
					$missing	= array_diff(array_keys($extension->getDependencies()), $installed);
					if (count($missing) == 0) {
						try {
						    $importLocalData = ($installData['import_local'] == true);
							$extinstaller = new tao_install_ExtensionInstaller($extension, $importLocalData);
							
							set_time_limit(300);
							
							$extinstaller->install();
							common_Logger::w('Extension '.$key.' installed');
						} catch (common_ext_ExtensionException $e) {
							common_Logger::w('Exception('.$e->getMessage().') during install for extension "'.$extension->getId().'"');
							throw new tao_install_utils_Exception("An error occured during the installation of extension '" . $extension->getId() . "'.");
						}
						unset($toInstall[$key]);
						$modified = true;
					} else {
						$missing = array_diff($missing, array_keys($toInstall));
						foreach ($missing as $extID) {
						    common_Logger::d('Extension ' . $extID . ' is required but missing, added to install list');
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
				'password'		=> core_kernel_users_AuthAdapter::getPasswordHash()->encrypt($installData['user_pass1']),
				'userLastName'	=> $installData['user_lastname'],
				'userFirstName'	=> $installData['user_firstname'],
				'userMail'		=> $installData['user_email'],
				'userDefLg'		=> 'http://www.tao.lu/Ontologies/TAO.rdf#Lang'.$installData['module_lang'],
				'userUILg'		=> 'http://www.tao.lu/Ontologies/TAO.rdf#Lang'.$installData['module_lang'],
                'userTimezone'  => TIME_ZONE
			));
	
			/*
			 *  11 - Secure the install for production mode
			 */
			if($installData['module_mode'] == 'production'){
				$extensions = common_ext_ExtensionsManager::singleton()->getInstalledExtensions();
				common_Logger::i('Securing tao for production', 'INSTALL');
				
				// 11.1 Remove Generis User
				$dbCreator->removeGenerisUser();
				
				// 11.2 Protect TAO dist
	 			$shield = new tao_install_utils_Shield(array_keys($extensions));
	 			$shield->disableRewritePattern(array("!/test/", "!/doc/"));
                                $shield->denyAccessTo(array(
                                    'views/sass',
                                    'views/js/test',
                                    'views/build'
                                ));
	 			$shield->protectInstall();
			}

			/*
			 *  12 - Create the version file
			 */
			common_Logger::d('Creating TAO version file', 'INSTALL');
			file_put_contents(ROOT_PATH.'version', TAO_VERSION);
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
	 	return 'tao_' . helpers_Random::generateString(8);
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