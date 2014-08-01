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
 * Copyright (c) 2007-2010 (original work) Public Research Centre Henri Tudor & University of Luxembourg) (under the project TAO-QUAL);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
require_once dirname(__FILE__) . '/wfEngineServiceTest.php';
require_once dirname(__FILE__) . '/TranslationProcess/TranslationProcessHelper.php';

/**
 * Test the execution of a complex translation process
 * 
 * @author Somsack Sipasseuth, <taosupport@tudor.lu>
 * @package wfEngine
 
 */
class TranslationProcessExecutionTestCase extends wfEngineServiceTest {
	
	/**
	 * @var wfEngine_models_classes_ActivityExecutionService the tested service
	 */
	protected $service = null;
	
	/**
	 * @var core_kernel_classes_Resource
	 */
	protected $currentUser = null;
	protected $processDefinition = null;
	
	/**
	 * @var core_kernel_classes_Property
	 */
	protected $userProperty = null;
	
	/**
	 * @var core_kernel_versioning_Repo
	 */
	protected $defaultRepository = null;
	
	/**
	 * @var array()
	 */
	protected $config = array();
	protected $processLabel = array();
	protected $userLogins = array();
	protected $users = array();
	protected $roles = array();
	protected $vars = array();
	protected $units = array();
	protected $processExecutions = array();
	
	public function __construct(){
		
		parent::__construct();
		
		$this->config = array(
			'execute' => false,
			'delete' => false
		);

		$this->userPassword = '123456';
		$this->processLabel = array(
			'CBA' => 'CBA Translation Process',
			'PBA' => 'PBA Translation Process',
			'Booklet' => 'Booklet Translation Process',
			'BQ' => 'BQ Translation Process'
		);

		$this->createUsers = true;
		$this->createProcess = true;
		$this->langCountries = array(
			'LU' => array('fr', 'de', 'lb'),
			'DE' => array('de'),
			'CA' => array('fr', 'en')
		);
		$this->unitNames = array('Unit03');
		$this->userProperty = new core_kernel_classes_Property(LOCAL_NAMESPACE . '#translationUser');
		
	}

	/**
	 * initialize a test method
	 */
	public function setUp(){
		parent::setUp();
	}
	
	public function tearDown() {
		
    }
	
	/**
	 * Recursive create users from their logins:
	 */
	private function createUsers($usersLogin){
		foreach($usersLogin as $logins){
			if(is_string($logins)){
				 $createdUser = $this->createUser($logins);
				 $this->assertIsA($createdUser, 'core_kernel_classes_Resource');
				 $createdUser->setLabel($logins);
				 $this->users[$logins] = $createdUser;
			}else{
				$this->createUsers($logins);
			}
		}
	}
	
	private function getAuthorizedUsersByCountryLanguage($countryCode, $languageCode, $translatorsNb = 0){
		
		$returnValue = array();
		
		if(!empty($this->userLogins)){
			if(isset($this->userLogins[$countryCode])){
				if(isset($this->userLogins[$countryCode][$languageCode])){
					
					$npmLogin = $this->userLogins[$countryCode]['NPM'];
					$reconcilerLogin =  $this->userLogins[$countryCode][$languageCode]['reconciler'];
					$verifierLogin =  $this->userLogins[$countryCode][$languageCode]['verifier'];
					
					$translators = array();
					if($translatorsNb > 0){
						if(!isset($this->userLogins[$countryCode][$languageCode]['translator'])){
							$this->fail('no translators found for the country/language');
						}
						$translatorLogins = $this->userLogins[$countryCode][$languageCode]['translator'];

						for($i = 0; $i < $translatorsNb; $i++){
							if(isset($translatorLogins[$i+1])){
								$translatorLogin = $translatorLogins[$i+1];
								if(isset($this->users[$translatorLogin])){
									$translators[] = $this->users[$translatorLogin]->getUri();
								}
							}
						}
					}
					
					if(isset($this->users[$npmLogin]) && isset($this->users[$reconcilerLogin]) && isset($this->users[$verifierLogin])){
						$returnValue = array(
							'npm' => $this->users[$npmLogin]->getUri(),
							'reconciler' => $this->users[$reconcilerLogin]->getUri(),
							'verifier' => $this->users[$verifierLogin]->getUri(),
							'translators' => $translators
						);
					}
					
				}
			}
		}
		
		return $returnValue;
	}

	/**
	 * Test generation of users:
	 */
	public function testCreateUsers(){
		
		error_reporting(E_ALL);
		if($this->createUsers){
			
			$roleService = wfEngine_models_classes_RoleService::singleton();
			
			$usec = time();
			
			$this->userLogins = array();
			$this->users = array();	
			
			//create roles and users:
			$roleService = tao_models_classes_RoleService::singleton();
			$wfRole = new core_kernel_classes_Resource(INSTANCE_ROLE_WORKFLOW);
			
			$this->roles = array();
			$this->roles['consortium']	= $roleService->addRole('consortium - '.$usec, $wfRole);
			$this->roles['NPM']			= $roleService->addRole('NPMs - '.$usec, $wfRole);
			$this->roles['translator']	= $roleService->addRole('translators - '.$usec, $wfRole);
			$this->roles['reconciler']	= $roleService->addRole('reconcilers - '.$usec, $wfRole);
			$this->roles['verifier']	= $roleService->addRole('verifiers - '.$usec, $wfRole);
			$this->roles['developer']	= $roleService->addRole('developers - '.$usec, $wfRole);
			$this->roles['testDeveloper'] = $roleService->addRole('test developers - '.$usec, $wfRole);
			
			$classRole =  new core_kernel_classes_Class(CLASS_ROLE);
			$translatorClass = $classRole->createSubClass('translatorsTestClass');
			foreach ($this->roles as $role) {
				$role->setType($translatorClass);
				$role->removeType($classRole);
			}
			
			//create the country code and language code properties to the wf user class:
			$propUserCountryCode = $this->createTranslationProperty('CountryCode', '', '', $translatorClass);
			$propUserLangCode = $this->createTranslationProperty('LanguageCode', '', '', $translatorClass);
			
			//generate users' logins:
			$this->userLogins = array();
			$this->roleLogins = array();
			
			$this->userLogins['consortium'] = array();
			$memberConsortium = 1;
			for($i = 1; $i <= $memberConsortium; $i++){
				$this->userLogins['consortium'][$i] = 'consortium_'.$i.'_'.$usec;//the process admin
				$this->roleLogins[$this->roles['consortium']->getUri()][] = $this->userLogins['consortium'][$i];
			}
			
			$this->userLogins['developer'] = array();
			$nbDevelopers = 6;
			for($i = 1; $i <= $nbDevelopers; $i++){
				$this->userLogins['developer'][$i] = 'developer_'.$i.'_'.$usec;//ETS_01, ETS_02, etc.
				$this->roleLogins[$this->roles['developer']->getUri()][] = $this->userLogins['developer'][$i];
			}
			
			$this->userLogins['testDeveloper'] = array();
			$nbTestDevelopers = 3;
			for($i = 1; $i <= $nbTestDevelopers; $i++){
				$this->userLogins['testDeveloper'][$i] = 'testDeveloper_'.$i.'_'.$usec;//test creators
				$this->roleLogins[$this->roles['testDeveloper']->getUri()][] = $this->userLogins['testDeveloper'][$i];
			}
			
			$nbTranslatorsByCountryLang = 3;
			foreach($this->langCountries as $countryCode => $languageCodes){
				$this->userLogins[$countryCode] = array();
				
				//one NPM by country
				$this->userLogins[$countryCode]['NPM'] = 'NPM_'.$countryCode.'_'.$usec;
				$this->roleLogins[$this->roles['NPM']->getUri()][] = $this->userLogins[$countryCode]['NPM'];
				
				foreach($languageCodes as $languageCode){
					
					//one reconciler and verifier by country-language
					$this->userLogins[$countryCode][$languageCode] = array(
						'translator' => array(),
						'reconciler' => 'reconciler_'.$countryCode.'_'.$languageCode.'_'.$usec,
						'verifier' => 'verifier_'.$countryCode.'_'.$languageCode.'_'.$usec
					);
					$this->roleLogins[$this->roles['reconciler']->getUri()][] = $this->userLogins[$countryCode][$languageCode]['reconciler'];
					$this->roleLogins[$this->roles['verifier']->getUri()][] = $this->userLogins[$countryCode][$languageCode]['verifier'];
					
					//as many translators as wanted:
					for($i = 1; $i <= $nbTranslatorsByCountryLang; $i++){
						$this->userLogins[$countryCode][$languageCode]['translator'][$i] = 'translator_'.$countryCode.'_'.$languageCode.'_'.$i.'_'.$usec;
						$this->roleLogins[$this->roles['translator']->getUri()][] = $this->userLogins[$countryCode][$languageCode]['translator'][$i];
					}
				}
			}
			
			$this->createUsers($this->userLogins);
			
			foreach($this->roleLogins as $roleUri => $usrs){
				$role = new core_kernel_classes_Resource($roleUri);
				$userUris = array();
				foreach($usrs as $login){
					if(isset($this->users[$login])){
						$matchesarray = array();
						if(preg_match_all('/translator_(.[^_]*)_(.[^_]*)_/', $login, $matchesarray)>0){
							$countryCode = $matchesarray[1][0];
							$langCode = $matchesarray[2][0];
							$this->assertTrue($this->users[$login]->setPropertyValue($propUserCountryCode, $countryCode));
							$this->assertTrue($this->users[$login]->setPropertyValue($propUserLangCode, $langCode));
						}
						$userUris[] = $this->users[$login]->getUri();
					}
				}
				$this->assertTrue($roleService->setRoleToUsers($role, $userUris));
			}
		}		
	}
	
	private function getFileName($unitLabel, $countryCode, $langCode, $type, core_kernel_classes_Resource $user = null){
		
		$fileName = $unitLabel.'_'.strtoupper($countryCode).'_'.strtolower($langCode);
		if(!is_null($user)){
			$fileName .= '_'.$user->getLabel();
		}
		$fileName .= '.'.strtolower($type);
		
		return $fileName;
	}
	
	private function createItemFile($type, $content = '', $user = null){
		
		$returnValue = null;
		
		$processVariableService = wfEngine_models_classes_VariableService::singleton();
		$unit = $processVariableService->get('unitUri');
		$countryCode = (string) $processVariableService->get('countryCode');
		$languageCode = (string) $processVariableService->get('languageCode');
		$this->assertFalse(empty($unit));
		$this->assertFalse(empty($countryCode));
		$this->assertFalse(empty($languageCode));
		
		if($unit instanceof core_kernel_classes_Resource && !empty($countryCode) && !empty($languageCode)){
			
			//create a working file for that user:
			$fileName = $this->getFileName($unit->getLabel(), $countryCode, $languageCode, $type, $user);
			$file = $this->getOneRepository()->createFile($filename);
			$this->assertIsA($file, 'core_kernel_versioning_File');
			
			//set file content:
			if(!empty($content)){
				$this->assertTrue($file->setContent($content));
			}else{
				$this->assertTrue($file->setContent(strtoupper($type) . '" for country "' . $countryCode . '" and language "' . $languageCode . '" : \n'));
			}
			
			$this->assertTrue($file->add());
			$this->assertTrue($file->commit());
			
			$unit->setPropertyValue($this->properties[TranslationProcessHelper::getPropertyName($type, $countryCode, $languageCode)], $file);
			
			if(!is_null($user)){
				$file->setPropertyValue($this->userProperty, $user);
			}
			
			$this->files[$fileName] = $file;
			
			$returnValue = $file;
		}
		
		return $returnValue;
	}
	
	private function getItemFile(core_kernel_classes_Resource $item, $type, $countryCode, $langCode, core_kernel_classes_Resource $user = null){
		
		$returnValue = null;
		
		if(!isset($this->properties[TranslationProcessHelper::getPropertyName($type, $countryCode, $langCode)])){
			$this->fail("The item property does not exist for the item {$item->getLabel()} ({$item->getUri()}) : $type, $countryCode, $langCode ");
			return $returnValue;
		}
		
		$file = null;
		if(in_array(strtolower($type), array('xliff_working', 'vff_working'))){
			if(is_null($user)){
				$this->fail('no user given');
				return $returnValue;
			}
			
			$values = $item->getPropertyValues($this->properties[TranslationProcessHelper::getPropertyName($type, $countryCode, $langCode)]);
			foreach($values as $uri){
				if(common_Utils::isUri($uri)){
					$aFile = new core_kernel_versioning_File($uri);
					$assignedUser = $aFile->getUniquePropertyValue($this->userProperty);
					if($assignedUser->getUri() == $user->getUri()){
						$file = $aFile;
						break;
					}
				}
			}
			
		}else{
			$values = $item->getPropertyValues($this->properties[TranslationProcessHelper::getPropertyName($type, $countryCode, $langCode)]);
			$this->assertEqual(count($values), 1);
			$file = new core_kernel_versioning_File(reset($values));
		}
		
		if(!is_null($file) && $file->isVersioned()){
			$returnValue = $file;
		}else{
			$this->fail("Cannot get the versioned {$type} file in {$countryCode}_{$langCode} for the item {$item->getLabel()} ({$item->getUri()})");
		}
		
		return $returnValue;
	}
	
	// Get a repository of the TAO instance
	private function getOneRepository(){
		
		$repository = null;
		
		if(!is_null($this->defaultRepository) && $this->defaultRepository instanceof core_kernel_versioning_Repository){
			$repository = $this->defaultRepository;
		}else{
			$versioningRepositoryClass = new core_kernel_classes_Class(CLASS_GENERIS_VERSIONEDREPOSITORY);
			$repositories = $versioningRepositoryClass->getInstances();
			$repository = null;

			if (!count($repositories)) {
				throw new Exception('no default repository exists in TAO');
			}else {
				$repository = array_pop($repositories);
				$repository = new core_kernel_versioning_Repository($repository->getUri());
				$this->defaultRepository = $repository;
			}
		}
		
		
		return $repository;
	}
	
	public function createTranslationProperty($type, $countryCode = '', $langCode = '', $class = null){
		
		$property = null;
		
		if(is_null($class) && !is_null($this->itemClass)){
			$class = $this->itemClass;
		}
		
		if(!is_null($class)){
			
			$label = TranslationProcessHelper::getPropertyName($type, $countryCode, $langCode);
			$uri = LOCAL_NAMESPACE.'#'.$label;
			$property = new core_kernel_classes_Property($uri);
			
			if(!$property->exists()){
				$propertyClass = new core_kernel_classes_Class(RDF_PROPERTY,__METHOD__);
				$propertyInstance = $propertyClass->createInstance($label, '', $uri);
				$property = new core_kernel_classes_Property($propertyInstance->getUri(),__METHOD__);
			}

			if(!$class->setProperty($property)){
				throw new common_Exception('problem creating property : cannot set property to class');
			}
			
			$this->properties[$label] = $property;
		}else{
			throw new common_Exception('problem creating property : no target class given');
		}
		
		return $property;
	}
	
	public function testCreateUnits(){
		
		set_time_limit(300);
		
		$this->itemClass = null;
		$this->units = array();
		$this->properties = array();
		$this->files = array();
		
		$classUri = LOCAL_NAMESPACE.'#TranslationItemsClass';
		$translationClass = new core_kernel_classes_Class($classUri);
		if(!$translationClass->exists()){
			$itemClass = new core_kernel_classes_Class(TAO_ITEM_CLASS);
			$translationClass = $itemClass->createSubClass('Translation Items', 'created for translation process execution test case', $classUri);
			$this->assertIsA($translationClass, 'core_kernel_classes_Class');
		}
		$this->itemClass = $translationClass;
		
		foreach($this->unitNames as $unitName){
			
			//create unit:
			$this->units[$unitName] = $translationClass->createInstance($unitName, 'created for translation process execution test case');
			$this->assertNotNull($this->units[$unitName]);
		}
		
//		var_dump($this->units, $this->properties, $this->files);
		
	}
	
	private function populateVariables($varCodes){
		
		$processVariableService = wfEngine_models_classes_VariableService::singleton();
		foreach ($varCodes as $varCode) {
			if (!isset($this->vars[$varCode])) {
				$this->vars[$varCode] = $processVariableService->getProcessVariable($varCode, true);
			}
		}
		
	}
	
	public function testCreatePBAProcess(){
		
		$authoringService = wfAuthoring_models_classes_ProcessService::singleton();
		$activityService = wfEngine_models_classes_ActivityService::singleton();
		$connectorService = wfAuthoring_models_classes_ConnectorService::singleton();
		$cardinalityService = wfEngine_models_classes_ActivityCardinalityService::singleton();
		
		$varCodes = array(
			'unitUri', //to be initialized
			'countryCode', //to be initialized
			'languageCode', //to be initialized
			'npm', //define the *unique* NPM that can access the activity
			'translatorsCount',//the number of translator, used in split connector
			'translator',//serialized array (the system variable) that will be split during parallel branch creation
			'reconciler',//define the *unique* reconciler that can access the activity
			'verifier',
			'translatorSelected',
			'translationFinished',
			'finalCheck',
			'doc',//holds the current doc svn revision number
			'doc_working',//holds the current doc svn revision number
			'vff',
			'vff_working'
		);
		
		$this->populateVariables($varCodes);
		
		$aclUser = new core_kernel_classes_Resource(INSTANCE_ACL_USER);
		$aclRole = new core_kernel_classes_Resource(INSTANCE_ACL_ROLE);
		
		$processDefinition = $authoringService->createProcess($this->processLabel['PBA'], 'For Unit test');
		$this->assertIsA($processDefinition, 'core_kernel_classes_Resource');
		
		//set process initialization rights:
		$this->assertTrue($authoringService->setAcl($processDefinition, $aclRole, $this->roles['consortium']));
		

		//define activities and connectors

		//Select translators:
		$activitySelectTranslators = $authoringService->createActivity($processDefinition, 'Select Translator');
		$this->assertNotNull($activitySelectTranslators);
		$authoringService->setFirstActivity($processDefinition, $activitySelectTranslators);
		$activityService->setAcl($activitySelectTranslators, $aclUser, $this->vars['npm']);
		$activityService->setControls($activitySelectTranslators, array(INSTANCE_CONTROL_FORWARD));
		
		$connectorSelectTranslators = $authoringService->createConnector($activitySelectTranslators);
		$this->assertNotNull($connectorSelectTranslators);
		
		//translate:
		$activityTranslate = $authoringService->createActivity($processDefinition, 'Translate');
		$this->assertNotNull($activityTranslate);
		$activityService->setAcl($activityTranslate, $aclUser, $this->vars['translator']);
		$activityService->setControls($activityTranslate, array(INSTANCE_CONTROL_FORWARD));
		
		$result = $authoringService->setParallelActivities($connectorSelectTranslators, array($activityTranslate->getUri() => $this->vars['translatorsCount']));
		$this->assertTrue($result);
		$this->assertTrue($connectorService->setSplitVariables($connectorSelectTranslators, array($activityTranslate->getUri() => $this->vars['translator'])));
		
		$nextActivities = $connectorService->getNextActivities($connectorSelectTranslators);
		$this->assertEqual(count($nextActivities), 1);
		$cardinality = reset($nextActivities);
		$this->assertTrue($cardinalityService->isCardinality($cardinality));
		$this->assertEqual($cardinalityService->getDestination($cardinality)->getUri(), $activityTranslate->getUri());
		$this->assertEqual($cardinalityService->getCardinality($cardinality)->getUri(), $this->vars['translatorsCount']->getUri());
		
		$connectorTranslate = $authoringService->createConnector($activityTranslate);
		$this->assertNotNull($connectorTranslate);
		
		//reconciliation:
		$activityReconciliation = $authoringService->createJoinActivity($connectorTranslate, null, 'Reconciliation', $activityTranslate);
		$prevActivities = $connectorService->getPreviousActivities($connectorTranslate);
		$this->assertEqual(count($prevActivities), 1);
		$cardinality = reset($prevActivities);
		$this->assertTrue($cardinalityService->isCardinality($cardinality));
		$this->assertEqual($cardinalityService->getSource($cardinality)->getUri(), $activityTranslate->getUri());
		$this->assertEqual($cardinalityService->getCardinality($cardinality)->getUri(), $this->vars['translatorsCount']->getUri());
		
		$this->assertNotNull($activityReconciliation);
		$activityService->setAcl($activityReconciliation, $aclUser, $this->vars['reconciler']);
		$activityService->setControls($activityReconciliation, array(INSTANCE_CONTROL_FORWARD));
		
		$connectorReconciliation = $authoringService->createConnector($activityReconciliation);
		$this->assertNotNull($connectorReconciliation);
		
		//verify translations
		$activityVerifyTranslations = $authoringService->createSequenceActivity($connectorReconciliation, null, 'Verify Translations');
		$this->assertNotNull($activityVerifyTranslations);
		$activityService->setAcl($activityVerifyTranslations, $aclUser, $this->vars['verifier']);
		$activityService->setControls($activityVerifyTranslations, array(INSTANCE_CONTROL_FORWARD));
		
		$connectorVerifyTranslations = $authoringService->createConnector($activityVerifyTranslations);
		$this->assertNotNull($connectorVerifyTranslations);

		//correct verification
		$activityCorrectVerification = $authoringService->createSequenceActivity($connectorVerifyTranslations, null, 'Correct Verification Issues');
		$this->assertNotNull($activityCorrectVerification);
		$activityService->setAcl($activityCorrectVerification, $aclUser, $this->vars['reconciler']);
		$activityService->setControls($activityCorrectVerification, array(INSTANCE_CONTROL_FORWARD));
		
		$connectorCorrectVerification = $authoringService->createConnector($activityCorrectVerification);
		$this->assertNotNull($connectorCorrectVerification);
		
		//final check :
		$activityFinalCheck = $authoringService->createSequenceActivity($connectorCorrectVerification, null, 'Final Check');
		$this->assertNotNull($activityFinalCheck);
		$activityService->setAcl($activityFinalCheck, $aclRole, $this->roles['testDeveloper']);
		$activityService->setControls($activityFinalCheck, array(INSTANCE_CONTROL_BACKWARD, INSTANCE_CONTROL_FORWARD));
		
		$connectorFinalCheck = $authoringService->createConnector($activityFinalCheck);
		$this->assertNotNull($connectorFinalCheck);
		
		//if final check ok, go to scoring definition :
		$transitionRule = $authoringService->createTransitionRule($connectorFinalCheck, '^finalCheck == 1');
		$this->assertNotNull($transitionRule);
		
		$activityFinalize = $authoringService->createConditionalActivity($connectorFinalCheck, 'then', null, 'Completed');//if ^finalCheck == 1
		$this->assertNotNull($activityFinalize);
		$activityService->setAcl($activityFinalize, $aclRole, $this->roles['testDeveloper']);
		$activityService->setControls($activityFinalize, array(INSTANCE_CONTROL_FORWARD));
		$activityService->setHidden($activityFinalize, true);
		
		//if not ok, can go to optional activity to review corrections:
		$activityFinalCheckElse = $authoringService->createConditionalActivity($connectorFinalCheck, 'else', $activityCorrectVerification);//if ^finalCheck != 1
		$this->assertNotNull($activityFinalCheckElse);
		$this->assertEqual($activityFinalCheckElse->getUri(), $activityCorrectVerification->getUri());
		
		$this->processDefinition['PBA'] = $processDefinition;
	}
	
	public function testCreateBookletProcess(){
		
		$authoringService = wfAuthoring_models_classes_ProcessService::singleton();
		$activityService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityService');
		$processVariableService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_VariableService');
		
		$varCodes = array(
			'unitUri', //to be initialized
			'countryCode', //to be initialized
			'languageCode', //to be initialized
			'reconciler',//define the *unique* reconciler that can access the activity
			'verifier',
			'layoutCorrection',
			'translationFinished',
			'layoutCheck',
			'finalCheck',
			'TDsignOff',
			'countrySignOff',
			'pdf',//holds the current pdf svn revision number
			'vff'
		);
		
		$this->populateVariables($varCodes);
		
		$aclUser = new core_kernel_classes_Resource(INSTANCE_ACL_USER);
		$aclRole = new core_kernel_classes_Resource(INSTANCE_ACL_ROLE);
		
		$processDefinition = $authoringService->createProcess($this->processLabel['Booklet'], 'For Unit test');
		$this->assertIsA($processDefinition, 'core_kernel_classes_Resource');
		
		//set process initialization rights:
		$this->assertTrue($authoringService->setAcl($processDefinition, $aclRole, $this->roles['consortium']));
		

		//define activities and connectors

		//Select translators:
		$activityReviewBooklets = $authoringService->createActivity($processDefinition, 'Review Assembled Booklets');
		$this->assertNotNull($activityReviewBooklets);
		$authoringService->setFirstActivity($processDefinition, $activityReviewBooklets);
		$activityService->setAcl($activityReviewBooklets, $aclUser, $this->vars['reconciler']);
		$activityService->setControls($activityReviewBooklets, array(INSTANCE_CONTROL_FORWARD));
		
		$connectorReviewBooklets = $authoringService->createConnector($activityReviewBooklets);
		$this->assertNotNull($connectorReviewBooklets);
		
		//Layout corrections :
		$activityLayoutCorrections = $authoringService->createSequenceActivity($connectorReviewBooklets, null, 'Layout Corrections');
		$this->assertNotNull($activityLayoutCorrections);
		$activityService->setAcl($activityLayoutCorrections, $aclRole, $this->roles['testDeveloper']);
		$activityService->setControls($activityLayoutCorrections, array(INSTANCE_CONTROL_FORWARD));
		
		$connectorLayoutCorrections = $authoringService->createConnector($activityLayoutCorrections);
		$this->assertNotNull($connectorLayoutCorrections);
		
		//final optical check:
		$transitionRule = $authoringService->createTransitionRule($connectorLayoutCorrections, '^layoutCheck == 1');
		$this->assertNotNull($transitionRule);
		
		$activityOpticalCheck = $authoringService->createConditionalActivity($connectorLayoutCorrections, 'then', null, 'Final Optical Check');//if ^layoutCheck == 1
		$this->assertNotNull($activityOpticalCheck);
		$activityService->setAcl($activityOpticalCheck, $aclUser, $this->vars['verifier']);
		$activityService->setControls($activityOpticalCheck, array(INSTANCE_CONTROL_FORWARD));
		$connectorOpticalCheck = $authoringService->createConnector($activityOpticalCheck);
		$this->assertNotNull($connectorOpticalCheck);
		
		//if not ok, return to review assembled booklets:
		$activityOpticalCheckElse = $authoringService->createConditionalActivity($connectorLayoutCorrections, 'else', $activityReviewBooklets);//if ^layoutCheck != 1
		$this->assertNotNull($activityOpticalCheckElse);
		$this->assertEqual($activityOpticalCheckElse->getUri(), $activityReviewBooklets->getUri());
		
		//final sign off (TD):
		$transitionRule = $authoringService->createTransitionRule($connectorOpticalCheck, '^finalCheck == 1');
		$this->assertNotNull($transitionRule);
		
		$activityTDsignOff = $authoringService->createConditionalActivity($connectorOpticalCheck, 'then', null, 'Test Developer Sign off');//if ^TDsignOff == 1
		$this->assertNotNull($activityTDsignOff);
		$activityService->setAcl($activityTDsignOff, $aclRole, $this->roles['testDeveloper']);
		$activityService->setControls($activityTDsignOff, array(INSTANCE_CONTROL_FORWARD));
		$connectorTDsignOff = $authoringService->createConnector($activityTDsignOff);
		$this->assertNotNull($connectorTDsignOff);
		
		//if not ok, return to optical check:
		$activityOpticalCheckElse = $authoringService->createConditionalActivity($connectorOpticalCheck, 'else', $activityLayoutCorrections);//if ^finalCheck != 1
		$this->assertNotNull($activityOpticalCheckElse);
		$this->assertEqual($activityOpticalCheckElse->getUri(), $activityLayoutCorrections->getUri());
		
		//country sign off:
		$transitionRule = $authoringService->createTransitionRule($connectorTDsignOff, '^TDsignOff == 1');
		$this->assertNotNull($transitionRule);
		
		$activityCountrySignOff = $authoringService->createConditionalActivity($connectorTDsignOff, 'then', null, 'Country Sign Off');//if ^TDsignOff == 1
		$this->assertNotNull($activityCountrySignOff);
		$activityService->setAcl($activityCountrySignOff, $aclUser, $this->vars['reconciler']);
		$activityService->setControls($activityCountrySignOff, array(INSTANCE_CONTROL_FORWARD));
		$connectorCountrySignOff = $authoringService->createConnector($activityCountrySignOff);
		$this->assertNotNull($connectorCountrySignOff);
		
		//if not ok, return to optical check:
		$activityTDsignOffElse = $authoringService->createConditionalActivity($connectorTDsignOff, 'else', $activityOpticalCheck);//if ^TDsignOff != 1
		$this->assertNotNull($activityTDsignOffElse);
		$this->assertEqual($activityTDsignOffElse->getUri(), $activityOpticalCheck->getUri());
		
		//final activity:
		$transitionRule = $authoringService->createTransitionRule($connectorCountrySignOff, '^countrySignOff == 1');
		$this->assertNotNull($transitionRule);
		
		$activityFinal = $authoringService->createConditionalActivity($connectorCountrySignOff, 'then', null, 'Completed');//if ^countrySignOff == 1
		$this->assertNotNull($activityFinal);
		$activityService->setAcl($activityFinal, $aclUser, $this->vars['reconciler']);
		$activityService->setControls($activityFinal, array(INSTANCE_CONTROL_FORWARD));
		$activityService->setHidden($activityFinal, true);
		
		//if not ok, return to optical check:
		$activityCountrySignOffElse = $authoringService->createConditionalActivity($connectorCountrySignOff, 'else', $activityTDsignOff);//if ^countrySignOff != 1
		$this->assertNotNull($activityCountrySignOffElse);
		$this->assertEqual($activityCountrySignOffElse->getUri(), $activityTDsignOff->getUri());
		
		$this->processDefinition['Booklet'] = $processDefinition;
	}
	
	public function testCreateBQProcess(){
		
		$authoringService = wfAuthoring_models_classes_ProcessService::singleton();
		$activityService = wfEngine_models_classes_ActivityService::singleton();
		$connectorService = wfAuthoring_models_classes_ConnectorService::singleton();
		$processVariableService = wfEngine_models_classes_VariableService::singleton();
		$cardinalityService = wfEngine_models_classes_ActivityCardinalityService::singleton();

		$varCodes = array(
			'unitUri', //to be initialized
			'countryCode', //to be initialized
			'languageCode', //to be initialized
			'npm', //define the *unique* NPM that can access the activity
			'translatorsCount', //the number of translator, used in split connector
			'translator', //serialized array (the system variable) that will be split during parallel branch creation
			'reconciler', //define the *unique* reconciler that can access the activity
			'verifier',
			'translatorSelected',
			'translationFinished',
			'finalCheck',
			'doc', //holds the current doc svn revision number
			'doc_working', //holds the current doc svn revision number
			'vff',
			'vff_working'
		);

		$this->populateVariables($varCodes);

		$aclUser = new core_kernel_classes_Resource(INSTANCE_ACL_USER);
		$aclRole = new core_kernel_classes_Resource(INSTANCE_ACL_ROLE);

		$processDefinition = $authoringService->createProcess($this->processLabel['BQ'], 'For Unit test');
		$this->assertIsA($processDefinition, 'core_kernel_classes_Resource');

		//set process initialization rights:
		$this->assertTrue($authoringService->setAcl($processDefinition, $aclRole, $this->roles['consortium']));


		//define activities and connectors
		//Select translators:
		$activitySelectTranslators = $authoringService->createActivity($processDefinition, 'Select Translator');
		$this->assertNotNull($activitySelectTranslators);
		$authoringService->setFirstActivity($processDefinition, $activitySelectTranslators);
		$activityService->setAcl($activitySelectTranslators, $aclUser, $this->vars['npm']);
		$activityService->setControls($activitySelectTranslators, array(INSTANCE_CONTROL_FORWARD));

		$connectorSelectTranslators = $authoringService->createConnector($activitySelectTranslators);
		$this->assertNotNull($connectorSelectTranslators);

		//translate:
		$activityTranslate = $authoringService->createActivity($processDefinition, 'Translate');
		$this->assertNotNull($activityTranslate);
		$activityService->setAcl($activityTranslate, $aclUser, $this->vars['translator']);
		$activityService->setControls($activityTranslate, array(INSTANCE_CONTROL_FORWARD));

		$result = $authoringService->setParallelActivities($connectorSelectTranslators, array($activityTranslate->getUri() => $this->vars['translatorsCount']));
		$this->assertTrue($result);
		$this->assertTrue($connectorService->setSplitVariables($connectorSelectTranslators, array($activityTranslate->getUri() => $this->vars['translator'])));

		$nextActivities = $connectorService->getNextActivities($connectorSelectTranslators);
		$this->assertEqual(count($nextActivities), 1);
		$cardinality = reset($nextActivities);
		$this->assertTrue($cardinalityService->isCardinality($cardinality));
		$this->assertEqual($cardinalityService->getDestination($cardinality)->getUri(), $activityTranslate->getUri());
		$this->assertEqual($cardinalityService->getCardinality($cardinality)->getUri(), $this->vars['translatorsCount']->getUri());

		$connectorTranslate = $authoringService->createConnector($activityTranslate);
		$this->assertNotNull($connectorTranslate);

		//reconciliation:
		$activityReconciliation = $authoringService->createJoinActivity($connectorTranslate, null, 'Reconciliation', $activityTranslate);
		$prevActivities = $connectorService->getPreviousActivities($connectorTranslate);
		$this->assertEqual(count($prevActivities), 1);
		$cardinality = reset($prevActivities);
		$this->assertTrue($cardinalityService->isCardinality($cardinality));
		$this->assertEqual($cardinalityService->getSource($cardinality)->getUri(), $activityTranslate->getUri());
		$this->assertEqual($cardinalityService->getCardinality($cardinality)->getUri(), $this->vars['translatorsCount']->getUri());

		$this->assertNotNull($activityReconciliation);
		$activityService->setAcl($activityReconciliation, $aclUser, $this->vars['reconciler']);
		$activityService->setControls($activityReconciliation, array(INSTANCE_CONTROL_FORWARD));

		$connectorReconciliation = $authoringService->createConnector($activityReconciliation);
		$this->assertNotNull($connectorReconciliation);

		//verify translations
		$activityVerifyTranslations = $authoringService->createSequenceActivity($connectorReconciliation, null, 'Verify Translations');
		$this->assertNotNull($activityVerifyTranslations);
		$activityService->setAcl($activityVerifyTranslations, $aclUser, $this->vars['verifier']);
		$activityService->setControls($activityVerifyTranslations, array(INSTANCE_CONTROL_FORWARD));

		$connectorVerifyTranslations = $authoringService->createConnector($activityVerifyTranslations);
		$this->assertNotNull($connectorVerifyTranslations);

		//correct verification
		$activityCorrectVerification = $authoringService->createSequenceActivity($connectorVerifyTranslations, null, 'Correct Verification Issues');
		$this->assertNotNull($activityCorrectVerification);
		$activityService->setAcl($activityCorrectVerification, $aclUser, $this->vars['reconciler']);
		$activityService->setControls($activityCorrectVerification, array(INSTANCE_CONTROL_FORWARD));

		$connectorCorrectVerification = $authoringService->createConnector($activityCorrectVerification);
		$this->assertNotNull($connectorCorrectVerification);

		//final check :
		$activityFinalCheck = $authoringService->createSequenceActivity($connectorCorrectVerification, null, 'Final Verification Check');
		$this->assertNotNull($activityFinalCheck);
		$activityService->setAcl($activityFinalCheck, $aclUser, $this->vars['verifier']);
		$activityService->setControls($activityFinalCheck, array(INSTANCE_CONTROL_BACKWARD, INSTANCE_CONTROL_FORWARD));

		$connectorFinalCheck = $authoringService->createConnector($activityFinalCheck);
		$this->assertNotNull($connectorFinalCheck);

		//if final check ok, go to scoring definition :
		$transitionRule = $authoringService->createTransitionRule($connectorFinalCheck, '^finalCheck == 1');
		$this->assertNotNull($transitionRule);

		$activityFinalize = $authoringService->createConditionalActivity($connectorFinalCheck, 'then', null, 'Finalize BQ'); //if ^finalCheck == 1
		$this->assertNotNull($activityFinalize);
		$activityService->setAcl($activityFinalize, $aclRole, $this->roles['developer']);
		$activityService->setControls($activityFinalize, array(INSTANCE_CONTROL_FORWARD));

		//if not ok, can go to optional activity to review corrections:
		$activityFinalCheckElse = $authoringService->createConditionalActivity($connectorFinalCheck, 'else', $activityCorrectVerification); //if ^finalCheck != 1
		$this->assertNotNull($activityFinalCheckElse);
		$this->assertEqual($activityFinalCheckElse->getUri(), $activityCorrectVerification->getUri());

		$this->processDefinition['BQ'] = $processDefinition;
		
	}
	
	
	public function testCreateCBAProcess(){
		
		if(!$this->createProcess){
			return;
		}
		
		$authoringService = wfAuthoring_models_classes_ProcessService::singleton();
		$activityService = wfEngine_models_classes_ActivityService::singleton();
		$connectorService = wfAuthoring_models_classes_ConnectorService::singleton();
		$processVariableService = wfEngine_models_classes_VariableService::singleton();
		$cardinalityService = wfEngine_models_classes_ActivityCardinalityService::singleton();
		
		//create some process variables:
		$varCodes = array(
			'unitUri', //to be initialized
			'countryCode', //to be initialized
			'languageCode', //to be initialized
			'npm', //define the *unique* NPM that can access the activity
			'translatorsCount',//the number of translator, used in split connector
			'translator',//serialized array (the system variable) that will be split during parallel branch creation
			'reconciler',//define the *unique* reconciler that can access the activity
			'verifier',
			'translatorSelected',
			'translationFinished',
			'TDreview',
			'layoutCheck',
			'finalCheck',
			'opticalCheck',
			'TDsignOff',
			'countrySignOff',
			'xliff',//holds the current xliff svn revision number
			'vff',//holds the current vff svn revision number
			'xliff_working',//holds the current working xliff svn revision number
			'vff_working'//holds the current working vff svn revision number
		);
		//"workingFiles" holds the working versions of the xliff and vff files, plus their revision number, in an serialized array()
		//during translation: workingFiles = array('user'=>#007, 'xliff' => array('uri' => #123456, 'revision'=>3), 'vff'=> array('uri' => #456789, 'revision'=>5))
		
		$this->populateVariables($varCodes);
		
		$aclUser = new core_kernel_classes_Resource(INSTANCE_ACL_USER);
		$aclRole = new core_kernel_classes_Resource(INSTANCE_ACL_ROLE);
		
		$processDefinition = $authoringService->createProcess($this->processLabel['CBA'], 'For Unit test');
		$this->assertIsA($processDefinition, 'core_kernel_classes_Resource');
		
		//set process initialization rights:
		$this->assertTrue($authoringService->setAcl($processDefinition, $aclRole, $this->roles['consortium']));
		

		//define activities and connectors

		//Select translators:
		$activitySelectTranslators = $authoringService->createActivity($processDefinition, 'Select Translator');
		$this->assertNotNull($activitySelectTranslators);
		$authoringService->setFirstActivity($processDefinition, $activitySelectTranslators);
		$activityService->setAcl($activitySelectTranslators, $aclUser, $this->vars['npm']);
		$activityService->setControls($activitySelectTranslators, array(INSTANCE_CONTROL_FORWARD));
		
		$connectorSelectTranslators = $authoringService->createConnector($activitySelectTranslators);
		$this->assertNotNull($connectorSelectTranslators);
		
		//translate:
		$activityTranslate = $authoringService->createActivity($processDefinition, 'Translate');
		$this->assertNotNull($activityTranslate);
		$activityService->setAcl($activityTranslate, $aclUser, $this->vars['translator']);
		$activityService->setControls($activityTranslate, array(INSTANCE_CONTROL_FORWARD));
		
		$result = $authoringService->setParallelActivities($connectorSelectTranslators, array($activityTranslate->getUri() => $this->vars['translatorsCount']));
		$this->assertTrue($result);
		$this->assertTrue($connectorService->setSplitVariables($connectorSelectTranslators, array($activityTranslate->getUri() => $this->vars['translator'])));
		
		$nextActivities = $connectorService->getNextActivities($connectorSelectTranslators);
		$this->assertEqual(count($nextActivities), 1);
		$cardinality = reset($nextActivities);
		$this->assertTrue($cardinalityService->isCardinality($cardinality));
		$this->assertEqual($cardinalityService->getDestination($cardinality)->getUri(), $activityTranslate->getUri());
		$this->assertEqual($cardinalityService->getCardinality($cardinality)->getUri(), $this->vars['translatorsCount']->getUri());
		
		$connectorTranslate = $authoringService->createConnector($activityTranslate);
		$this->assertNotNull($connectorTranslate);
		
		//reconciliation:
		$activityReconciliation = $authoringService->createJoinActivity($connectorTranslate, null, 'Reconciliation', $activityTranslate);
		$prevActivities = $connectorService->getPreviousActivities($connectorTranslate);
		$this->assertEqual(count($prevActivities), 1);
		$cardinality = reset($prevActivities);
		$this->assertTrue($cardinalityService->isCardinality($cardinality));
		$this->assertEqual($cardinalityService->getSource($cardinality)->getUri(), $activityTranslate->getUri());
		$this->assertEqual($cardinalityService->getCardinality($cardinality)->getUri(), $this->vars['translatorsCount']->getUri());
		
		$this->assertNotNull($activityReconciliation);
		$activityService->setAcl($activityReconciliation, $aclUser, $this->vars['reconciler']);
		$activityService->setControls($activityReconciliation, array(INSTANCE_CONTROL_FORWARD));
		
		$connectorReconciliation = $authoringService->createConnector($activityReconciliation);
		$this->assertNotNull($connectorReconciliation);
		
		//verify translations
		$activityVerifyTranslations = $authoringService->createSequenceActivity($connectorReconciliation, null, 'Verify Translations');
		$this->assertNotNull($activityVerifyTranslations);
		$activityService->setAcl($activityVerifyTranslations, $aclUser, $this->vars['verifier']);
		$activityService->setControls($activityVerifyTranslations, array(INSTANCE_CONTROL_FORWARD));
		
		$connectorVerifyTranslations = $authoringService->createConnector($activityVerifyTranslations);
		$this->assertNotNull($connectorVerifyTranslations);

		//correct verification
		$activityCorrectVerification = $authoringService->createSequenceActivity($connectorVerifyTranslations, null, 'Correct Verification Issues');
		$this->assertNotNull($activityCorrectVerification);
		$activityService->setAcl($activityCorrectVerification, $aclUser, $this->vars['reconciler']);
		$activityService->setControls($activityCorrectVerification, array(INSTANCE_CONTROL_FORWARD));
		
		$connectorCorrectVerification = $authoringService->createConnector($activityCorrectVerification);
		$this->assertNotNull($connectorCorrectVerification);
		
		//TD review : 
		$activityTDreview = $authoringService->createSequenceActivity($connectorCorrectVerification, null, 'TD review');
		$this->assertNotNull($activityTDreview);
		$activityService->setAcl($activityTDreview, $aclRole, $this->roles['testDeveloper']);
		$activityService->setControls($activityTDreview, array(INSTANCE_CONTROL_FORWARD));
		$connectorTDreview = $authoringService->createConnector($activityTDreview);
		$this->assertNotNull($connectorTDreview);
		
		//if TD review not ok, return to correct verification issues:
		$transitionRule = $authoringService->createTransitionRule($connectorTDreview, '^TDreview == 1');
		$this->assertNotNull($transitionRule);
		
		$activityCorrectVerificationBis = $authoringService->createConditionalActivity($connectorTDreview, 'else', $activityCorrectVerification);//if ^TDreview != 1
		$this->assertEqual($activityCorrectVerification->getUri(), $activityCorrectVerificationBis->getUri());
		
		//correct layout :
		$activityCorrectLayout = $authoringService->createConditionalActivity($connectorTDreview, 'then', null, 'Correct Layout Issues');//if ^TDreview == 1
		$this->assertNotNull($activityCorrectLayout);
		$activityService->setAcl($activityCorrectLayout, $aclRole, $this->roles['developer']);
		$activityService->setControls($activityCorrectLayout, array(INSTANCE_CONTROL_FORWARD));
		
		$connectorCorrectLayout = $authoringService->createConnector($activityCorrectLayout);
		$this->assertNotNull($connectorCorrectLayout);
		
		//if correct layout needs verification :
		$transitionRule = $authoringService->createTransitionRule($connectorCorrectLayout, '^layoutCheck == 1');
		$this->assertNotNull($transitionRule);
		
		$activityVerification = $authoringService->createConditionalActivity($connectorCorrectLayout, 'else', null, 'Verification Followup');//if ^layoutCheck != 1
		$this->assertNotNull($activityVerification);
		$activityService->setAcl($activityVerification, $aclUser, $this->vars['verifier']);
		$activityService->setControls($activityVerification, array(INSTANCE_CONTROL_FORWARD));
		$connectorVerification = $authoringService->createConnector($activityVerification);
		$this->assertNotNull($connectorVerification);
		
		//final check :
		$activityFinalCheck = $authoringService->createConditionalActivity($connectorCorrectLayout, 'then', null, 'Final Check');//if ^layoutCheck == 1
		$this->assertNotNull($activityFinalCheck);
		$activityService->setAcl($activityFinalCheck, $aclRole, $this->roles['testDeveloper']);
		$activityService->setControls($activityFinalCheck, array(INSTANCE_CONTROL_FORWARD));
		
		$connectorFinalCheck = $authoringService->createConnector($activityFinalCheck);
		$this->assertNotNull($connectorFinalCheck);
		
		//if final check ok, go to scoring definition :
		$transitionRule = $authoringService->createTransitionRule($connectorFinalCheck, '^finalCheck == 1');
		$this->assertNotNull($transitionRule);
		
		$activityScoringDefinition = $authoringService->createConditionalActivity($connectorFinalCheck, 'then', null, 'Scoring Definition and Testing');//if ^finalCheck == 1
		$this->assertNotNull($activityScoringDefinition);
		$activityService->setAcl($activityScoringDefinition, $aclUser, $this->vars['reconciler']);
		$activityService->setControls($activityScoringDefinition, array(INSTANCE_CONTROL_FORWARD));
		
		$connectorScoringDefinition = $authoringService->createConnector($activityScoringDefinition);
		$this->assertNotNull($connectorScoringDefinition);
		
		//if not ok, return to correct layout : 
		$activityCorrectLayoutBis = $authoringService->createConditionalActivity($connectorFinalCheck, 'else', $activityCorrectLayout);//if ^finalCheck != 1
		$this->assertEqual($activityCorrectLayout->getUri(), $activityCorrectLayoutBis->getUri());
		
		//verification : 
		$transitionRule = $authoringService->createTransitionRule($connectorVerification, '^opticalCheck == 1');
		$this->assertNotNull($transitionRule);
		$activityFinalCheckBis = $authoringService->createConditionalActivity($connectorVerification, 'then', $activityFinalCheck);//if ^opticalCheck == 1
		$this->assertEqual($activityFinalCheckBis->getUri(), $activityFinalCheck->getUri());
		$activityCorrectLayoutBis = $authoringService->createConditionalActivity($connectorVerification, 'else', $activityCorrectLayout);//if ^opticalCheck != 1
		$this->assertEqual($activityCorrectLayoutBis->getUri(), $activityCorrectLayout->getUri());
		
		//scoring verification:
		$activityScoringVerification = $authoringService->createSequenceActivity($connectorScoringDefinition, null, 'Scoring Verification');
		$this->assertNotNull($activityScoringVerification);
		$activityService->setAcl($activityScoringVerification, $aclUser, $this->vars['verifier']);
		$activityService->setControls($activityScoringVerification, array(INSTANCE_CONTROL_FORWARD));
		
		$connectorScoringVerification = $authoringService->createConnector($activityScoringVerification);
		$this->assertNotNull($connectorScoringVerification);
		
		//final sign off :
		$activityTDSignOff = $authoringService->createSequenceActivity($connectorScoringVerification, null, 'Test Developer Sign Off');
		$this->assertNotNull($activityTDSignOff);
		$activityService->setAcl($activityTDSignOff, $aclRole, $this->roles['testDeveloper']);
		$activityService->setControls($activityTDSignOff, array(INSTANCE_CONTROL_FORWARD));
		
		$connectorTDSignOff = $authoringService->createConnector($activityTDSignOff);
		$this->assertNotNull($connectorTDSignOff);
		
		//link back to final check:
		$transitionRule = $authoringService->createTransitionRule($connectorTDSignOff, '^TDsignOff == 1');
		$this->assertNotNull($transitionRule);
		$authoringService->createConditionalActivity($connectorTDSignOff, 'else', $activityFinalCheck);
		
		//sign off :
		$activityCountrySignOff = $authoringService->createConditionalActivity($connectorTDSignOff, 'then', null, 'Country Sign Off');
		$activityService->setAcl($activityCountrySignOff, $aclUser, $this->vars['reconciler']);
		$activityService->setControls($activityCountrySignOff, array(INSTANCE_CONTROL_FORWARD));
		
		//complete the process:
		$connectorCountrySignOff = $authoringService->createConnector($activityCountrySignOff);
		$this->assertNotNull($connectorCountrySignOff);
		
		$transitionRule = $authoringService->createTransitionRule($connectorCountrySignOff, '^countrySignOff == 1');
		$this->assertNotNull($transitionRule);
		
		$activityFinal = $authoringService->createConditionalActivity($connectorCountrySignOff, 'then', null, 'Completed');
		$activityService->setAcl($activityFinal, $aclUser, $this->vars['reconciler']);
		$activityService->setControls($activityFinal, array(INSTANCE_CONTROL_FORWARD));
		$activityService->setHidden($activityFinal, true);
		
		$activityTDSignOffBis = $authoringService->createConditionalActivity($connectorCountrySignOff, 'else', $activityTDSignOff);
		$this->assertEqual($activityTDSignOff->getUri(), $activityTDSignOffBis->getUri());
		
		//end of process definition
		
		$this->processDefinition['CBA'] = $processDefinition;
		
	}
	
	private function getProcessDefinition($type){
		
		$returnValue = null;
		
		if (!isset($this->processDefinition[$type]) || !$this->processDefinition[$type] instanceof core_kernel_classes_Resource) {
			$processClass = new core_kernel_classes_Class(CLASS_PROCESS);
			$translationProcesses = $processClass->searchInstances(array(RDFS_LABEL => (string) $this->processLabel[$type]), array('like' => false));
			if (!empty($translationProcesses)) {
				$returnValue = array_pop($translationProcesses);
			}
		}else{
			$returnValue = $this->processDefinition[$type];
		}
		
		return $returnValue;
		
	}
	
	public function testExecuteProcesses(){
		
		set_time_limit(300);
		
		$simulationOptions = array(
			'repeatBack' => 0,//O: do not back when possible
			'repeatLoop' => 1,
			'translations' => 2,//must be >= 1
			'stopProbability' => 0,
			'execute' => isset($this->config['execute'])?(bool)$this->config['execute']:false
		);
		
		$processCBA = $this->getProcessDefinition('CBA');
		$processPBA = $this->getProcessDefinition('PBA');
		$processBooklet = $this->getProcessDefinition('Booklet');
		$processBQ = $this->getProcessDefinition('BQ');
		
		$processInstancesClass = new core_kernel_classes_Class(CLASS_PROCESSINSTANCES);
		$this->assertIsA($this->createTranslationProperty('unitUri', '', '', $processInstancesClass), 'core_kernel_classes_Property');
		$this->assertIsA($this->createTranslationProperty('countryCode', '', '', $processInstancesClass), 'core_kernel_classes_Property');
		$this->assertIsA($this->createTranslationProperty('languageCode', '', '', $processInstancesClass), 'core_kernel_classes_Property');
		
		$i = 0;
		foreach($this->units as $unit){
			foreach ($this->langCountries as $countryCode => $languageCodes){
				foreach ($languageCodes as $langCode){
					
					$this->out("executes translation processes for {$unit->getLabel()}/{$countryCode}/{$langCode}:", true);
					$this->assertIsA($unit, 'core_kernel_classes_Resource');
					
//					$simulationOptions['stopProbability'] = 0.6 + $i*0.2;
					
					//exec PBA process:
					if($i%4 == 0){
						if ($processPBA instanceof core_kernel_classes_Resource) {
							$this->out("executes {$processPBA->getLabel()} for {$unit->getLabel()}/{$countryCode}/{$langCode}:", true);
							$this->executeProcessPBA($processPBA, $unit->getUri(), $countryCode, $langCode, $simulationOptions);
						}else{
							$this->fail('No PBA process definition found to be executed');
						}
					}
					
					//exec Booklet process:
					if($i%4 == 1){
						if ($processBooklet instanceof core_kernel_classes_Resource) {
							$this->out("executes {$processBooklet->getLabel()} for {$unit->getLabel()}/{$countryCode}/{$langCode}:", true);
							$this->executeProcessBooklet($processBooklet, $unit->getUri(), $countryCode, $langCode, $simulationOptions);
						}else{
							$this->fail('No Booklet process definition found to be executed');
						}
					}
					
					//exec CBA process:
					if($i%4 == 2){
						if ($processCBA instanceof core_kernel_classes_Resource) {
							$this->out("executes {$processCBA->getLabel()} for {$unit->getLabel()}/{$countryCode}/{$langCode}:", true);
							$this->executeProcessCBA($processCBA, $unit->getUri(), $countryCode, $langCode, $simulationOptions);
						}else{
							$this->fail('No process definition found to be executed');
						}
					}
					//exec BQ process:
					if($i%4 == 3){
						if ($processBQ instanceof core_kernel_classes_Resource) {
							$this->out("executes {$processBQ->getLabel()} for {$unit->getLabel()}/{$countryCode}/{$langCode}:", true);
							$this->executeProcessBQ($processBQ, $unit->getUri(), $countryCode, $langCode, $simulationOptions);
						}else{
							$this->fail('No BQ process definition found to be executed');
						}
					}
					
					$i++;
					
					if($i>4){
						break(3);
					}
				}
			}
		}
		
	}
	
	private function executeProcessPBA($processDefinition, $unitUri, $countryCode, $languageCode, $simulationOptions){
		
		$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
		$processDefinitionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessDefinitionService');
		$loginProperty = new core_kernel_classes_Property(PROPERTY_USER_LOGIN);
		
		$processExecName = 'Test Translation Process Execution';
		$processExecComment = 'created by '.__CLASS__.'::'.__METHOD__;
		
		$users = $this->getAuthorizedUsersByCountryLanguage($countryCode, $languageCode);
		
		if(empty($users)){
			$this->fail("cannot find the authorized npm, verifier and reconciler for this country-language : {$countryCode}/{$languageCode}");
			return;
		}
		
		//check that the xliff and vff exist for the given country-language:
		$unit = new core_kernel_classes_Resource($unitUri);
		
		$vffRevision = 0;
		$xliffRevision = 0;

		$initVariables = array(
			$this->vars['unitUri']->getUri() => $unit->getUri(),
			$this->vars['countryCode']->getUri() => $countryCode,
			$this->vars['languageCode']->getUri() => $languageCode,
			$this->vars['npm']->getUri() => $users['npm'],
			$this->vars['reconciler']->getUri() => $users['reconciler'],
			$this->vars['verifier']->getUri() => $users['verifier'],
			$this->vars['xliff']->getUri() => $xliffRevision,
			$this->vars['vff']->getUri() => $vffRevision,
		);
		
		$this->changeUser($this->userLogins['consortium'][1]);
		$this->assertTrue($processDefinitionService->checkAcl($processDefinition, $this->currentUser));
		
		$processInstance = $processExecutionService->createProcessExecution($processDefinition, $processExecName, $processExecComment, $initVariables);
		$this->assertEqual($processDefinition->getUri(), $processExecutionService->getExecutionOf($processInstance)->getUri());
		
		$processInstance->setPropertyValue($this->properties[TranslationProcessHelper::getPropertyName('unitUri')], $unit);
		$processInstance->setPropertyValue($this->properties[TranslationProcessHelper::getPropertyName('countryCode')], $countryCode);
		$processInstance->setPropertyValue($this->properties[TranslationProcessHelper::getPropertyName('languageCode')], $languageCode);
		
		$this->assertTrue($processExecutionService->checkStatus($processInstance, 'started'));

		$this->out(__METHOD__, true);
		$this->processExecutions[$processInstance->getUri()] = $processInstance;
			
		$currentActivityExecutions = $processExecutionService->getCurrentActivityExecutions($processInstance);
		$this->assertEqual(count($currentActivityExecutions), 1);
		
		if(isset($simulationOptions['execute']) && $simulationOptions['execute'] === false) return;
		
		$this->out("<strong>Forward transitions:</strong>", true);
		
		$nbTranslators = (isset($simulationOptions['translations']) && intval($simulationOptions['translations'])>=1 )?intval($simulationOptions['translations']):2;//>=1
		$nbLoops = isset($simulationOptions['repeatLoop'])?intval($simulationOptions['repeatLoop']):1;
		$nbBacks = isset($simulationOptions['repeatBack'])?intval($simulationOptions['repeatBack']):0;
		$stopProbability = isset($simulationOptions['stopProbability'])?floatval($simulationOptions['stopProbability']):0;
		
		$loopsCounter = array();
		
		$indexActivityTranslate = 2;//the index of the activity in the process definition
		$iterations = $indexActivityTranslate + $nbTranslators +3;
		$this->changeUser($this->userLogins[$countryCode]['NPM']);
		$selectedTranslators = array();
		
		$i = 1;
		$activityIndex = $i;
		while($activityIndex <= $iterations){
			
			$activityExecutions = $processExecutionService->getCurrentActivityExecutions($processInstance);
			$activityExecution = null;
			$activity = null;
			if($activityIndex >= $indexActivityTranslate && $activityIndex < $indexActivityTranslate+$nbTranslators){
				$this->assertEqual(count($activityExecutions), $nbTranslators);
				//parallel translation branch:
				foreach($activityExecutions as $activityExec){
					if(!$activityExecutionService->isFinished($activityExec)){
						$activityExecution = $activityExec;
						break;
					}
				}
			}else{
				$this->assertEqual(count($activityExecutions), 1);
				$activityExecution = reset($activityExecutions);
			}
			
			$activity = $activityExecutionService->getExecutionOf($activityExecution);
			
			$this->out("<strong>Iteration {$i} : activity no{$activityIndex} : ".$activity->getLabel()."</strong>", true);
			$this->out("current user : ".$this->currentUser->getOnePropertyValue($loginProperty).' "'.$this->currentUser->getUri().'"', true);
			
			$this->checkAccessControl($activityExecution);
			
			$currentActivityExecution = null;
			
			//for loop managements:
			$goto = 0;
			
			if($activityIndex >= $indexActivityTranslate && $activityIndex < $indexActivityTranslate+$nbTranslators){
				
				//we are executing the translation activity:
				$this->assertFalse(empty($selectedTranslators));
				$theTranslator = null;
				foreach($selectedTranslators as $translatorUri){
					$translator = new core_kernel_classes_Resource($translatorUri);
					if($activityExecutionService->checkAcl($activityExecution, $translator)){
						$theTranslator = $translator;
						break;
					}
				}

				$this->assertNotNull($theTranslator);
				$login = (string) $theTranslator->getUniquePropertyValue($loginProperty);
				$this->assertFalse(empty($login));

				$this->bashCheckAcl($activityExecution, array($login));
				$this->changeUser($login);

				$currentActivityExecution = $this->initCurrentActivityExecution($activityExecution);
				
				//execute service:
				$this->assertTrue($this->executeServiceTranslate(array(
					'translatorUri' => $theTranslator->getUri()
				)));
				
			}else{
			
				//switch to activity's specific check:
				switch ($activityIndex) {
					case 1: {
						
						$login = $this->userLogins[$countryCode]['NPM'];
						$this->assertFalse(empty($login));
						$this->bashCheckAcl($activityExecution, array($login));

						$this->changeUser($login);
						$currentActivityExecution = $this->initCurrentActivityExecution($activityExecution);
						
						//execute service:
						$translators = $this->getAuthorizedUsersByCountryLanguage($countryCode, $languageCode, $nbTranslators);
						$selectedTranslators = $translators['translators'];
						$this->assertTrue($this->executeServiceSelectTranslators($selectedTranslators));

						break;
					}
					case $indexActivityTranslate + $nbTranslators:
					case $indexActivityTranslate + $nbTranslators +2:{
						//reconciliation:
						//correct verification issues:
						$login = $this->userLogins[$countryCode][$languageCode]['reconciler'];
						
						$this->assertFalse(empty($login));
						$this->bashCheckAcl($activityExecution, array($login), array_rand($this->users, 5));
						$this->changeUser($login);
						$currentActivityExecution = $this->initCurrentActivityExecution($activityExecution);
						
						break;
					}
					case $indexActivityTranslate + $nbTranslators +1:{
						//verify translations :
						$login = $this->userLogins[$countryCode][$languageCode]['verifier'];
						
						$this->assertFalse(empty($login));
						$this->bashCheckAcl($activityExecution, array($login), array_rand($this->users, 5));
						$this->changeUser($login);
						$currentActivityExecution = $this->initCurrentActivityExecution($activityExecution);
						
						break;
					}	
					case $indexActivityTranslate + $nbTranslators +3:{
						//final check by TD: TD sign off:
						$developersLogins = $this->userLogins['testDeveloper'];
						$this->bashCheckAcl($activityExecution, $developersLogins);
						
						$this->changeUser($developersLogins[array_rand($developersLogins)]);
						$currentActivityExecution = $this->initCurrentActivityExecution($activityExecution);
						
						if(!isset($loopsCounter['finalCheck'])){
							
							$loopsCounter = array();//reinitialize the loops counter
							
							$loopsCounter['finalCheck'] = $nbLoops;
							$this->assertTrue($this->executeServiceFinalSignOff(false));
							$goto = $indexActivityTranslate + $nbTranslators +2;
						}else{
							$this->assertTrue($this->executeServiceFinalSignOff(true));
						}
						
						break;
					}
				}
				
			}
			
			//transition to next activity
			$transitionResult = $processExecutionService->performTransition($processInstance, $currentActivityExecution);
			$goto = intval($goto);
			if($activityIndex == $iterations && !$goto){
				//process finished:
				$this->assertEqual(count($transitionResult), 0);
				$this->assertTrue($processExecutionService->isFinished($processInstance));
			}else if($activityIndex >= $indexActivityTranslate && $activityIndex < $indexActivityTranslate+$nbTranslators){
				//translate activities:
				$this->assertFalse($transitionResult);
				$this->assertTrue($processExecutionService->isPaused($processInstance));
			}else{
				$this->assertEqual(count($transitionResult), 0);
				$this->assertTrue($processExecutionService->isPaused($processInstance));
			}
			
			//manage next activity index:
			if($goto){
				$activityIndex = $goto;
			}else{
				$activityIndex++;
			}
			
			//increment iteration counts:
			$i++;
			
			$this->out("activity status : ".$activityExecutionService->getStatus($currentActivityExecution)->getLabel());
			$this->out("process status : ".$processExecutionService->getStatus($processInstance)->getLabel());
			
			$rand = rand(0, $iterations);
			$prob = $activityIndex * $stopProbability;
			if($rand < $prob){
				$this->out("process instance stopped by probability");
				break;
			}
		}
		
		$activityExecutionsData = $processExecutionService->getAllActivityExecutions($processInstance);
//		var_dump($activityExecutionsData);
		
		$executionHistory = $processExecutionService->getExecutionHistory($processInstance);
		$this->assertEqual(count($executionHistory), $i);//there is one hidden activity
	}
	
	private function executeProcessBQ($processDefinition, $unitUri, $countryCode, $languageCode, $simulationOptions){
		
			
		$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
		$processDefinitionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessDefinitionService');
		$loginProperty = new core_kernel_classes_Property(PROPERTY_USER_LOGIN);
		
		$processExecName = 'Test Translation Process Execution';
		$processExecComment = 'created by '.__CLASS__.'::'.__METHOD__;
		
		$users = $this->getAuthorizedUsersByCountryLanguage($countryCode, $languageCode);
		
		if(empty($users)){
			$this->fail("cannot find the authorized npm, verifier and reconciler for this country-language : {$countryCode}/{$languageCode}");
			return;
		}
		
		//check that the xliff and vff exist for the given country-language:
		$unit = new core_kernel_classes_Resource($unitUri);
		
		$vffRevision = 0;
		$xliffRevision = 0;
		
		$initVariables = array(
			$this->vars['unitUri']->getUri() => $unit->getUri(),
			$this->vars['countryCode']->getUri() => $countryCode,
			$this->vars['languageCode']->getUri() => $languageCode,
			$this->vars['npm']->getUri() => $users['npm'],
			$this->vars['reconciler']->getUri() => $users['reconciler'],
			$this->vars['verifier']->getUri() => $users['verifier'],
			$this->vars['xliff']->getUri() => $xliffRevision,
			$this->vars['vff']->getUri() => $vffRevision,
		);
		
		$this->changeUser($this->userLogins['consortium'][1]);
		$this->assertTrue($processDefinitionService->checkAcl($processDefinition, $this->currentUser));
		
		$processInstance = $processExecutionService->createProcessExecution($processDefinition, $processExecName, $processExecComment, $initVariables);
		$this->assertEqual($processDefinition->getUri(), $processExecutionService->getExecutionOf($processInstance)->getUri());
		
		$processInstance->setPropertyValue($this->properties[TranslationProcessHelper::getPropertyName('unitUri')], $unit);
		$processInstance->setPropertyValue($this->properties[TranslationProcessHelper::getPropertyName('countryCode')], $countryCode);
		$processInstance->setPropertyValue($this->properties[TranslationProcessHelper::getPropertyName('languageCode')], $languageCode);
		
		$this->assertTrue($processExecutionService->checkStatus($processInstance, 'started'));

		$this->out(__METHOD__, true);
		$this->processExecutions[$processInstance->getUri()] = $processInstance;
			
		$currentActivityExecutions = $processExecutionService->getCurrentActivityExecutions($processInstance);
		$this->assertEqual(count($currentActivityExecutions), 1);
		
		if(isset($simulationOptions['execute']) && $simulationOptions['execute'] === false) return;
		
		$this->out("<strong>Forward transitions:</strong>", true);
		
		$nbTranslators = (isset($simulationOptions['translations']) && intval($simulationOptions['translations'])>=1 )?intval($simulationOptions['translations']):2;//>=1
		$nbLoops = isset($simulationOptions['repeatLoop'])?intval($simulationOptions['repeatLoop']):1;
		$nbBacks = isset($simulationOptions['repeatBack'])?intval($simulationOptions['repeatBack']):0;
		$stopProbability = isset($simulationOptions['stopProbability'])?floatval($simulationOptions['stopProbability']):0;
		
		$loopsCounter = array();
		
		$indexActivityTranslate = 2;//the index of the activity in the process definition
		$iterations = $indexActivityTranslate + $nbTranslators +4	;
		$this->changeUser($this->userLogins[$countryCode]['NPM']);
		$selectedTranslators = array();
		
		$i = 1;
		$activityIndex = $i;
		while($activityIndex <= $iterations){
			
			$activityExecutions = $processExecutionService->getCurrentActivityExecutions($processInstance);
			$activityExecution = null;
			$activity = null;
			if($activityIndex >= $indexActivityTranslate && $activityIndex < $indexActivityTranslate+$nbTranslators){
				$this->assertEqual(count($activityExecutions), $nbTranslators);
				//parallel translation branch:
				foreach($activityExecutions as $activityExec){
					if(!$activityExecutionService->isFinished($activityExec)){
						$activityExecution = $activityExec;
						break;
					}
				}
			}else{
				$this->assertEqual(count($activityExecutions), 1);
				$activityExecution = reset($activityExecutions);
			}
			
			$activity = $activityExecutionService->getExecutionOf($activityExecution);
			
			$this->out("<strong>Iteration {$i} : activity no{$activityIndex} : ".$activity->getLabel()."</strong>", true);
			$this->out("current user : ".$this->currentUser->getOnePropertyValue($loginProperty).' "'.$this->currentUser->getUri().'"', true);
			
			$this->checkAccessControl($activityExecution);
			
			$currentActivityExecution = null;
			
			//for loop managements:
			$goto = 0;
			
			if($activityIndex >= $indexActivityTranslate && $activityIndex < $indexActivityTranslate+$nbTranslators){
				
				//we are executing the translation activity:
				$this->assertFalse(empty($selectedTranslators));
				$theTranslator = null;
				foreach($selectedTranslators as $translatorUri){
					$translator = new core_kernel_classes_Resource($translatorUri);
					if($activityExecutionService->checkAcl($activityExecution, $translator)){
						$theTranslator = $translator;
						break;
					}
				}

				$this->assertNotNull($theTranslator);
				$login = (string) $theTranslator->getUniquePropertyValue($loginProperty);
				$this->assertFalse(empty($login));

				$this->bashCheckAcl($activityExecution, array($login));
				$this->changeUser($login);

				$currentActivityExecution = $this->initCurrentActivityExecution($activityExecution);
				
				//execute service:
				$this->assertTrue($this->executeServiceTranslate(array(
					'translatorUri' => $theTranslator->getUri()
				)));
				
			}else{
			
				//switch to activity's specific check:
				switch ($activityIndex) {
					case 1: {
						
						$login = $this->userLogins[$countryCode]['NPM'];
						$this->assertFalse(empty($login));
						$this->bashCheckAcl($activityExecution, array($login));

						$this->changeUser($login);
						$currentActivityExecution = $this->initCurrentActivityExecution($activityExecution);
						
						//execute service:
						$translators = $this->getAuthorizedUsersByCountryLanguage($countryCode, $languageCode, $nbTranslators);
						$selectedTranslators = $translators['translators'];
						$this->assertTrue($this->executeServiceSelectTranslators($selectedTranslators));

						break;
					}
					case $indexActivityTranslate + $nbTranslators:
					case $indexActivityTranslate + $nbTranslators +2:{
						//reconciliation:
						//correct verification issues:
						$login = $this->userLogins[$countryCode][$languageCode]['reconciler'];
						
						$this->assertFalse(empty($login));
						$this->bashCheckAcl($activityExecution, array($login), array_rand($this->users, 5));
						$this->changeUser($login);
						$currentActivityExecution = $this->initCurrentActivityExecution($activityExecution);
						
						break;
					}
					case $indexActivityTranslate + $nbTranslators +1:{
						//verify translations :
						$login = $this->userLogins[$countryCode][$languageCode]['verifier'];
						
						$this->assertFalse(empty($login));
						$this->bashCheckAcl($activityExecution, array($login), array_rand($this->users, 5));
						$this->changeUser($login);
						$currentActivityExecution = $this->initCurrentActivityExecution($activityExecution);
						
						break;
					}	
					case $indexActivityTranslate + $nbTranslators +3:{
						//final verificationc check :
						$login = $this->userLogins[$countryCode][$languageCode]['verifier'];
						
						$this->assertFalse(empty($login));
						$this->bashCheckAcl($activityExecution, array($login), array_rand($this->users, 5));
						$this->changeUser($login);
						$currentActivityExecution = $this->initCurrentActivityExecution($activityExecution);
						
						if(!isset($loopsCounter['finalCheck'])){
							$loopsCounter = array();//reinitialize the loops counter
							$loopsCounter['finalCheck'] = $nbLoops;
							$this->assertTrue($this->executeServiceFinalSignOff(false));
							$goto = $indexActivityTranslate + $nbTranslators +2;
						}else{
							$this->assertTrue($this->executeServiceFinalSignOff(true));
						}
						
						break;
					}
					case $indexActivityTranslate + $nbTranslators +4:{
						//finalize BQ :
						$developersLogins = $this->userLogins['developer'];
						$this->bashCheckAcl($activityExecution, $developersLogins);
						
						$j = 1;
						foreach(array_rand($developersLogins, 3) as $k){
							
							$this->out("developer no$j ".$developersLogins[$k]." corrects layout", true);
							$this->changeUser($developersLogins[$k]);
							$currentActivityExecution = $this->initCurrentActivityExecution($activityExecution);
							
							//check if all developers can access the activity, even after it has been taken:
							$this->bashCheckAcl($activityExecution, $developersLogins, array_rand($this->users, 8));
							
							$j++;
						}
						
						$this->changeUser($developersLogins[array_rand($developersLogins)]);
						
						break;
					}	
				}
			}
			
			//transition to next activity
			$transitionResult = $processExecutionService->performTransition($processInstance, $currentActivityExecution);
			$goto = intval($goto);
			if($activityIndex == $iterations){
				//process finished:
				$this->assertEqual(count($transitionResult), 0);
				$this->assertTrue($processExecutionService->isFinished($processInstance));
			}else if($activityIndex >= $indexActivityTranslate && $activityIndex < $indexActivityTranslate+$nbTranslators){
				//translate activities:
				$this->assertFalse($transitionResult);
				$this->assertTrue($processExecutionService->isPaused($processInstance));
			}else{
				$this->assertEqual(count($transitionResult), 0);
				$this->assertTrue($processExecutionService->isPaused($processInstance));
			}
			
			//manage next activity index:
			if($goto){
				$activityIndex = $goto;
			}else{
				$activityIndex++;
			}
			
			//increment iteration counts:
			$i++;
			
			$this->out("activity status : ".$activityExecutionService->getStatus($currentActivityExecution)->getLabel());
			$this->out("process status : ".$processExecutionService->getStatus($processInstance)->getLabel());
			
			$rand = rand(0, $iterations);
			$prob = $activityIndex * $stopProbability;
			if($rand < $prob){
				$this->out("process instance stopped by probability");
				break;
			}
		}
		
		$activityExecutionsData = $processExecutionService->getAllActivityExecutions($processInstance);
//		var_dump($activityExecutionsData);
		
		$executionHistory = $processExecutionService->getExecutionHistory($processInstance);
		$this->assertEqual(count($executionHistory), $i-1);//there is no hidden activity
	}
	
	private function executeProcessCBA($processDefinition, $unitUri, $countryCode, $languageCode, $simulationOptions){
		
		$activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();
		$processExecutionService = wfEngine_models_classes_ProcessExecutionService::singleton();
		$processDefinitionService = wfEngine_models_classes_ProcessDefinitionService::singleton();
		$loginProperty = new core_kernel_classes_Property(PROPERTY_USER_LOGIN);
		
		$processExecName = 'Test Translation Process Execution';
		$processExecComment = 'created by '.__CLASS__.'::'.__METHOD__;
		
		$users = $this->getAuthorizedUsersByCountryLanguage($countryCode, $languageCode);
		
		if(empty($users)){
			$this->fail("cannot find the authorized npm, verifier and reconciler for this country-language : {$countryCode}/{$languageCode}");
			return;
		}
		
		//check that the xliff and vff exist for the given country-language:
		$unit = new core_kernel_classes_Resource($unitUri);
		
		$vffRevision = 0;
		$xliffRevision = 0;
		
		$initVariables = array(
			$this->vars['unitUri']->getUri() => $unit->getUri(),
			$this->vars['countryCode']->getUri() => $countryCode,
			$this->vars['languageCode']->getUri() => $languageCode,
			$this->vars['npm']->getUri() => $users['npm'],
			$this->vars['reconciler']->getUri() => $users['reconciler'],
			$this->vars['verifier']->getUri() => $users['verifier'],
			$this->vars['xliff']->getUri() => $xliffRevision,
			$this->vars['vff']->getUri() => $vffRevision,
		);
		
		$this->changeUser($this->userLogins['consortium'][1]);
		$this->assertTrue($processDefinitionService->checkAcl($processDefinition, $this->currentUser));
		
		$processInstance = $processExecutionService->createProcessExecution($processDefinition, $processExecName, $processExecComment, $initVariables);
		$this->assertEqual($processDefinition->getUri(), $processExecutionService->getExecutionOf($processInstance)->getUri());
		
		$processInstance->setPropertyValue($this->properties[TranslationProcessHelper::getPropertyName('unitUri')], $unit);
		$processInstance->setPropertyValue($this->properties[TranslationProcessHelper::getPropertyName('countryCode')], $countryCode);
		$processInstance->setPropertyValue($this->properties[TranslationProcessHelper::getPropertyName('languageCode')], $languageCode);
		
		$this->assertTrue($processExecutionService->checkStatus($processInstance, 'started'));

		$this->out(__METHOD__, true);
		$this->processExecutions[$processInstance->getUri()] = $processInstance;
			
		$currentActivityExecutions = $processExecutionService->getCurrentActivityExecutions($processInstance);
		$this->assertEqual(count($currentActivityExecutions), 1);
		
		if(isset($simulationOptions['execute']) && $simulationOptions['execute'] === false) return;
		
		$this->out("<strong>Forward transitions:</strong>", true);
		
		$nbTranslators = (isset($simulationOptions['translations']) && intval($simulationOptions['translations'])>=1 )?intval($simulationOptions['translations']):2;//>=1
		$nbLoops = isset($simulationOptions['repeatLoop'])?intval($simulationOptions['repeatLoop']):1;
		$nbBacks = isset($simulationOptions['repeatBack'])?intval($simulationOptions['repeatBack']):0;
		$stopProbability = isset($simulationOptions['stopProbability'])?floatval($simulationOptions['stopProbability']):0;
		
		$loopsCounter = array();
		
		$indexActivityTranslate = 2;//the index of the activity in the process definition
		$indexActivityOffset = $indexActivityTranslate + $nbTranslators;
		$iterations = $indexActivityOffset +10;
		$gotoManifest = array(
			$indexActivityOffset +3 => array($indexActivityOffset+2, $indexActivityOffset+4),
			$indexActivityOffset +4 => array($indexActivityOffset+5, $indexActivityOffset+6),
			$indexActivityOffset +5 => array($indexActivityOffset+4, $indexActivityOffset+6),
			$indexActivityOffset +6 => array($indexActivityOffset+4, $indexActivityOffset+7),
			$indexActivityOffset +9 => array($indexActivityOffset+6, $indexActivityOffset+10),
			$indexActivityOffset +10 => array($indexActivityOffset+9, $indexActivityOffset+11)
		);
		
		$this->changeUser($this->userLogins[$countryCode]['NPM']);
		$selectedTranslators = array();
		
		$i = 1;
		$activityIndex = $i;
		while($activityIndex <= $iterations){
			
			$activityExecutions = $processExecutionService->getCurrentActivityExecutions($processInstance);
			$activityExecution = null;
			$activity = null;
			if($activityIndex >= $indexActivityTranslate && $activityIndex < $indexActivityOffset){
				$this->assertEqual(count($activityExecutions), $nbTranslators);
				//parallel translation branch:
				foreach($activityExecutions as $activityExec){
					if(!$activityExecutionService->isFinished($activityExec)){
						$activityExecution = $activityExec;
						break;
					}
				}
			}else{
				$this->assertEqual(count($activityExecutions), 1);
				$activityExecution = reset($activityExecutions);
			}
			
			$activity = $activityExecutionService->getExecutionOf($activityExecution);
			
			$this->out("<strong>Iteration {$i} : activity no{$activityIndex} : ".$activity->getLabel()."</strong>", true);
			$this->out("current user : ".$this->currentUser->getOnePropertyValue($loginProperty).' "'.$this->currentUser->getUri().'"', true);
			
			$this->checkAccessControl($activityExecution);
			
			$currentActivityExecution = null;
			
			//for loop managements:
			$goto = 0;
			
			if($activityIndex >= $indexActivityTranslate && $activityIndex < $indexActivityOffset){
				
				//we are executing the translation activity:
				$this->assertFalse(empty($selectedTranslators));
				$theTranslator = null;
				foreach($selectedTranslators as $translatorUri){
					$translator = new core_kernel_classes_Resource($translatorUri);
					if($activityExecutionService->checkAcl($activityExecution, $translator)){
						$theTranslator = $translator;
						break;
					}
				}

				$this->assertNotNull($theTranslator);
				$login = (string) $theTranslator->getUniquePropertyValue($loginProperty);
				$this->assertFalse(empty($login));

				$this->bashCheckAcl($activityExecution, array($login));
				$this->changeUser($login);

				$currentActivityExecution = $this->initCurrentActivityExecution($activityExecution);
				
				//execute service:
				$this->assertTrue($this->executeServiceTranslate(array(
					'translatorUri' => $theTranslator->getUri()
				)));
				
			}else{
				
				//check ACL:
				switch ($activityIndex) {
					case 1: {
						
						$login = $this->userLogins[$countryCode]['NPM'];
						$this->assertFalse(empty($login));
						$this->bashCheckAcl($activityExecution, array($login));

						$this->changeUser($login);
						$currentActivityExecution = $this->initCurrentActivityExecution($activityExecution);
						
						//execute service:
						$translators = $this->getAuthorizedUsersByCountryLanguage($countryCode, $languageCode, $nbTranslators);
						$selectedTranslators = $translators['translators'];
						$this->assertTrue($this->executeServiceSelectTranslators($selectedTranslators));

						break;
					}
					case $indexActivityOffset:
					case $indexActivityOffset +2:
					case $indexActivityOffset +7:
					case $indexActivityOffset +10:{
						//reconciliation:
						//correct verification issues:
						//scoring definition and testing:
						//country sign off:
						$login = $this->userLogins[$countryCode][$languageCode]['reconciler'];
						
						$this->assertFalse(empty($login));
						$this->bashCheckAcl($activityExecution, array($login), array_rand($this->users, 5));
						$this->changeUser($login);
						$currentActivityExecution = $this->initCurrentActivityExecution($activityExecution);
						
						break;
					}
					case $indexActivityOffset +1:
					case $indexActivityOffset +5:
					case $indexActivityOffset +8:{
						//verify translations :
						////verification followup :
						//scoring verification :
						$login = $this->userLogins[$countryCode][$languageCode]['verifier'];
						
						$this->assertFalse(empty($login));
						$this->bashCheckAcl($activityExecution, array($login), array_rand($this->users, 5));
						$this->changeUser($login);
						$currentActivityExecution = $this->initCurrentActivityExecution($activityExecution);
						
						break;
					}	
					case $indexActivityOffset +4:{
						
						//correct layout, by developers:
						
						$developersLogins = $this->userLogins['developer'];
						$this->bashCheckAcl($activityExecution, $developersLogins);
						
						$j = 1;
						foreach(array_rand($developersLogins, 3) as $k){
							
							$this->out("developer no$j ".$developersLogins[$k]." corrects layout", true);
							$this->changeUser($developersLogins[$k]);
							$currentActivityExecution = $this->initCurrentActivityExecution($activityExecution);
							
							//check if all developers can access the activity, even after it has been taken:
							$this->bashCheckAcl($activityExecution, $developersLogins, array_rand($this->users, 8));
							
							$j++;
						}
						
						$this->changeUser($developersLogins[array_rand($developersLogins)]);
						
						break;
					}
					case $indexActivityOffset +3:
					case $indexActivityOffset +6:
					case $indexActivityOffset +9:{
						
						//final check:
						$developersLogins = $this->userLogins['testDeveloper'];
						$this->bashCheckAcl($activityExecution, $developersLogins);
						
						$j = 1;
						foreach(array_rand($developersLogins, 2) as $k){
							
							$this->out("test developer no$j ".$developersLogins[$k]." makes final check", true);
							$this->changeUser($developersLogins[$k]);
							$currentActivityExecution = $this->initCurrentActivityExecution($activityExecution);
							
							//check if all developers can access the activity, even after it has been taken:
							$this->bashCheckAcl($activityExecution, $developersLogins, array_rand($this->users, 5));
							
							$j++;
						}
						
						$this->changeUser($developersLogins[array_rand($developersLogins)]);
						
						break;
					}
				}
				
				$loopName = '';
				switch ($activityIndex) {
					case $indexActivityOffset +3: {
						if(empty($loopName)) $loopName = 'TDreview';
					}
					case $indexActivityOffset +4: {
						if(empty($loopName)) $loopName = 'layoutCheck';
					}
					case $indexActivityOffset +5: {
						if(empty($loopName)) $loopName = 'opticalCheck';
					}
					case $indexActivityOffset +6: {
						if(empty($loopName)) $loopName = 'finalCheck';
					}
					case $indexActivityOffset +9: {
						if(empty($loopName)) $loopName = 'TDsignOff';
					}
					case $indexActivityOffset +10: {
						if(empty($loopName)) $loopName = 'countrySignOff';

						if (!isset($loopsCounter[$loopName])) {
	//						$loopsCounter = array(); //reinitialize the loops counter
							$loopsCounter[$loopName] = $nbLoops;
							$goto = $gotoManifest[$activityIndex][0];//go backward
							$this->executeServicePositionVariable($loopName, false, "execute service {$activity->getLabel()}");
						} else {
							$goto = $gotoManifest[$activityIndex][1];//go forward
							$this->executeServicePositionVariable($loopName, true, "execute service {$activity->getLabel()}");
						}
						
						break;
					}
					
				}
			}
			
			//transition to next activity
			$transitionResult = $processExecutionService->performTransition($processInstance, $currentActivityExecution);
			$goto = intval($goto);
			if($activityIndex == $indexActivityTranslate + $nbTranslators +9 && $goto == $indexActivityTranslate + $nbTranslators +6){
				//the same users are authorized to execute the current and the next activity (final check and correct layout)
				$this->assertEqual(count($transitionResult), 1);
				$this->assertTrue($processExecutionService->checkStatus($processInstance, 'resumed'));
			}else if($activityIndex == $iterations && $goto == $iterations + 1){
				//test finished:
				$this->assertEqual(count($transitionResult), 0);
				$this->assertTrue($processExecutionService->isFinished($processInstance));
			}else if($activityIndex >= $indexActivityTranslate && $activityIndex < $indexActivityTranslate+$nbTranslators){
				//translate activities:
				$this->assertFalse($transitionResult);
				$this->assertTrue($processExecutionService->isPaused($processInstance));
			}else{
				$this->assertEqual(count($transitionResult), 0);
				$this->assertTrue($processExecutionService->isPaused($processInstance));
			}
			
			//manage next activity index:
			if($goto){
				$activityIndex = $goto;
			}else{
				$activityIndex++;
			}
			
			//increment iteration counts:
			$i++;
			
			$this->out("activity status : ".$activityExecutionService->getStatus($currentActivityExecution)->getLabel());
			$this->out("process status : ".$processExecutionService->getStatus($processInstance)->getLabel());
			
			$rand = rand(0, $iterations);
			$prob = $activityIndex * $stopProbability;
			if($rand < $prob){
				$this->out("process instance stopped by probability");
				break;
			}
			
			if($i>30){
				break;
			}
		}
		
		$activityExecutionsData = $processExecutionService->getAllActivityExecutions($processInstance);
//		var_dump($activityExecutionsData);
		
		$executionHistory = $processExecutionService->getExecutionHistory($processInstance);
		$this->assertEqual(count($executionHistory), $i);//one hidden activity
		
		
	}
	
	private function initCurrentActivityExecution($activityExecution, $started = true){
		
		$processExecutionService = wfEngine_models_classes_ProcessExecutionService::singleton();
		$activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();
		$processInstance = $activityExecutionService->getRelatedProcessExecution($activityExecution);
		
		//init execution
		$activityExecution = $processExecutionService->initCurrentActivityExecution($processInstance, $activityExecution, $this->currentUser);
		$this->assertNotNull($activityExecution);
		$activityExecStatus = $activityExecutionService->getStatus($activityExecution);
		$this->assertNotNull($activityExecStatus);
		if($started){
			$this->assertEqual($activityExecStatus->getUri(), INSTANCE_PROCESSSTATUS_STARTED);
		}else{
			$this->assertEqual($activityExecStatus->getUri(), INSTANCE_PROCESSSTATUS_RESUMED);
		}
		
		return $activityExecution;
	}
	
	private function bashCheckAcl($activityExecution, $authorizedUsers, $unauthorizedUsers = array()){
		
		$currentUser = $this->currentUser;
		
		if(empty($unauthorizedUsers)){
			$allLogins = array_keys($this->users);//all logins
			$unauthorizedUsers = array_diff($allLogins, $authorizedUsers);
		}else{
			$unauthorizedUsers = array_diff($unauthorizedUsers, $authorizedUsers);
		}
		
		
		$activityExecutionService = wfEngine_models_classes_ActivityExecutionService::singleton();
		$processInstance = $activityExecutionService->getRelatedProcessExecution($activityExecution);
		
		foreach($unauthorizedUsers as $login){
			$this->assertTrue($this->changeUser($login));
			$this->assertFalse($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
		}
		
		foreach($authorizedUsers as $login){
			$this->assertTrue($this->changeUser($login));
			$this->assertTrue($activityExecutionService->checkAcl($activityExecution, $this->currentUser, $processInstance));
		}
		
		//relog initial user:
		$currentLogin = (string) $currentUser->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_LOGIN));
		$this->assertTrue($this->changeUser($currentLogin));
	}
	
	/*
	 * Available process vars:
	 * 'unitUri', //to be initialized
		'countryCode', //to be initialized
		'languageCode', //to be initialized
		'npm', //define the *unique* NPM that can access the activity
		'translatorsCount',//the number of translator, used in split connector
		'translator',//serialized array (the system variable) that will be split during parallel branch creation
		'reconciler',//define the *unique* reconciler that can access the activity
		'verifier',
		'translatorSelected',
		'translationFinished',
		'layoutCheck',
		'finalCheck'
	 */
	private function executeServiceSelectTranslators($translators = array()){
		
		$returnValue = false;
		
		$this->out("execute service select translators :", true);
		
		$processVariableService = wfEngine_models_classes_VariableService::singleton();
		$unit = $processVariableService->get('unitUri');
		$countryCode = (string) $processVariableService->get('countryCode');
		$languageCode = (string) $processVariableService->get('languageCode');
		$this->assertFalse(empty($unit));
		$this->assertFalse(empty($countryCode));
		$this->assertFalse(empty($languageCode));
		
		if($unit instanceof core_kernel_classes_Resource && !empty($countryCode) && !empty($languageCode)){
			
			$xliffFileContent = '';
			$vffFileContent = '';

			//push values:
			$pushedVars = array();
			foreach($translators as $translator){
				$translatorResource = null;
				if($translator instanceof core_kernel_classes_Resource){
					$pushedVars[] = $translator->getUri();
					$translatorResource = $translator;
				}else if(is_string($translator) && common_Utils::isUri($translator)){
					$pushedVars[] = $translator;
					$translatorResource = new core_kernel_classes_Resource($translator);
				}
				$this->out("selected translator : {$translatorResource->getLabel()} ({$translatorResource->getUri()})");

			}
			$this->assertTrue(count($pushedVars) > 0);

			$processVariableService = wfEngine_models_classes_VariableService::singleton();
			$this->assertTrue($processVariableService->push('translatorsCount', count($pushedVars)));
			$returnValue = $processVariableService->push('translator', serialize($pushedVars));

		}
		return $returnValue;
	}
	
	private function executeServiceTranslate($options = array()){
		
		$returnValue = false;
		
		$this->out('executing service translate ', true);
		
		$processVariableService = wfEngine_models_classes_VariableService::singleton();
				
		$valid = true;
		if($valid){
			$this->assertTrue($processVariableService->push('translationFinished', 1));
			$returnValue = true;
		}
		
		return $returnValue;
		
	}
	
	private function executeProcessBooklet($processDefinition, $unitUri, $countryCode, $languageCode, $simulationOptions){
		
		$activityExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ActivityExecutionService');
		$processExecutionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessExecutionService');
		$processDefinitionService = tao_models_classes_ServiceFactory::get('wfEngine_models_classes_ProcessDefinitionService');
		$loginProperty = new core_kernel_classes_Property(PROPERTY_USER_LOGIN);

		$processExecName = 'Test Translation Process Execution';
		$processExecComment = 'created by ' . __CLASS__ . '::' . __METHOD__;

		$users = $this->getAuthorizedUsersByCountryLanguage($countryCode, $languageCode);

		if (empty($users)) {
			$this->fail("cannot find the authorized npm, verifier and reconciler for this country-language : {$countryCode}/{$languageCode}");
			return;
		}

		//check that the xliff and vff exist for the given country-language:
		$unit = new core_kernel_classes_Resource($unitUri);

		$vffRevision = 0;
		$xliffRevision = 0;

		$initVariables = array(
			$this->vars['unitUri']->getUri() => $unit->getUri(),
			$this->vars['countryCode']->getUri() => $countryCode,
			$this->vars['languageCode']->getUri() => $languageCode,
			$this->vars['npm']->getUri() => $users['npm'],
			$this->vars['reconciler']->getUri() => $users['reconciler'],
			$this->vars['verifier']->getUri() => $users['verifier'],
			$this->vars['xliff']->getUri() => $xliffRevision,
			$this->vars['vff']->getUri() => $vffRevision,
		);

		$this->changeUser($this->userLogins['consortium'][1]);
		$this->assertTrue($processDefinitionService->checkAcl($processDefinition, $this->currentUser));

		$processInstance = $processExecutionService->createProcessExecution($processDefinition, $processExecName, $processExecComment, $initVariables);
		$this->assertEqual($processDefinition->getUri(), $processExecutionService->getExecutionOf($processInstance)->getUri());

		$processInstance->setPropertyValue($this->properties[TranslationProcessHelper::getPropertyName('unitUri')], $unit);
		$processInstance->setPropertyValue($this->properties[TranslationProcessHelper::getPropertyName('countryCode')], $countryCode);
		$processInstance->setPropertyValue($this->properties[TranslationProcessHelper::getPropertyName('languageCode')], $languageCode);

		$this->assertTrue($processExecutionService->checkStatus($processInstance, 'started'));

		$this->out(__METHOD__, true);
		$this->processExecutions[$processInstance->getUri()] = $processInstance;

		$currentActivityExecutions = $processExecutionService->getCurrentActivityExecutions($processInstance);
		$this->assertEqual(count($currentActivityExecutions), 1);
		
		if(isset($simulationOptions['execute']) && $simulationOptions['execute'] === false) return;
		
		$this->out("<strong>Forward transitions:</strong>", true);

//		$nbTranslators = (isset($simulationOptions['translations']) && intval($simulationOptions['translations']) >= 1 ) ? intval($simulationOptions['translations']) : 2; //>=1
		$nbLoops = isset($simulationOptions['repeatLoop']) ? intval($simulationOptions['repeatLoop']) : 1;
		$nbBacks = isset($simulationOptions['repeatBack']) ? intval($simulationOptions['repeatBack']) : 0;
		$stopProbability = isset($simulationOptions['stopProbability']) ? floatval($simulationOptions['stopProbability']) : 0;

		$loopsCounter = array();

		$iterations = 5;
		$this->changeUser($this->userLogins[$countryCode]['NPM']);

		$i = 1;
		$activityIndex = $i;
		while ($activityIndex <= $iterations) {
			
			$activityExecution = null;
			$activity = null;
			
			$activityExecutions = $processExecutionService->getCurrentActivityExecutions($processInstance);
			$this->assertEqual(count($activityExecutions), 1);
			$activityExecution = reset($activityExecutions);
			$activity = $activityExecutionService->getExecutionOf($activityExecution);

			$this->out("<strong>Iteration {$i} : activity no{$activityIndex} : " . $activity->getLabel() . "</strong>", true);
			$this->out("current user : " . $this->currentUser->getOnePropertyValue($loginProperty) . ' "' . $this->currentUser->getUri() . '"', true);

			$this->checkAccessControl($activityExecution);

			$currentActivityExecution = null;

			//for loop managements:
			$goto = 0;
			
			$login = '';
			//access control checking:
			switch ($activityIndex) {
				case 1:
				case 5: {
						//reconciliation:
						//correct verification issues:
						$login = $this->userLogins[$countryCode][$languageCode]['reconciler'];
					}
				case 3: {
						//verify translations :
						if(empty($login)) $login = $this->userLogins[$countryCode][$languageCode]['verifier'];

						$this->assertFalse(empty($login));
						$this->bashCheckAcl($activityExecution, array($login), array_rand($this->users, 5));
						$this->changeUser($login);
						$currentActivityExecution = $this->initCurrentActivityExecution($activityExecution);

						break;
					}
				case 2:
				case 4:{
						//final check by TD: TD sign off:
						$developersLogins = $this->userLogins['testDeveloper'];
						$this->bashCheckAcl($activityExecution, $developersLogins);
						$this->changeUser($developersLogins[array_rand($developersLogins)]);
						$currentActivityExecution = $this->initCurrentActivityExecution($activityExecution);

						break;
					}
			}
			
			//execute services:
			$loopName = '';
			
			switch ($activityIndex) {
				case 1:{
					//let it be
					break;
				}
				case 2:{
					if(empty($loopName)) $loopName = 'LayoutCheck';
				}
				case 3:{
					if(empty($loopName)) $loopName = 'FinalCheck';
				}
				case 4:{
					if(empty($loopName)) $loopName = 'TDsignOff';
				}
				case 5:{
					if(empty($loopName)) $loopName = 'CountrySignOff';
					
					$serviceName = 'executeServiceBooklet'.$loopName;
					if(!method_exists($this, $serviceName)){
						throw new Exception('the method does not exist : '.$serviceName);
						break;
					}
					
					if (!isset($loopsCounter[$loopName])) {
//						$loopsCounter = array(); //reinitialize the loops counter
						$loopsCounter[$loopName] = $nbLoops;
						$this->assertTrue($this->$serviceName(false));
						$goto = $activityIndex -1;//go back
					} else {
						$this->assertTrue($this->$serviceName(true));
					}
					break;
				}
			}

			//transition to next activity
			$transitionResult = $processExecutionService->performTransition($processInstance, $currentActivityExecution);
			$goto = intval($goto);
			if($activityIndex == $iterations && !$goto){
				//process finished:
				$this->assertEqual(count($transitionResult), 0);
				$this->assertTrue($processExecutionService->isFinished($processInstance));
			}else{
				$this->assertEqual(count($transitionResult), 0);
				$this->assertTrue($processExecutionService->isPaused($processInstance));
			}

			//manage next activity index:
			if ($goto) {
				$activityIndex = $goto;
			} else {
				$activityIndex++;
			}

			//increment iteration counts:
			$i++;

			$this->out("activity status : " . $activityExecutionService->getStatus($currentActivityExecution)->getLabel());
			$this->out("process status : " . $processExecutionService->getStatus($processInstance)->getLabel());

			$rand = rand(0, $iterations);
			$prob = $activityIndex * $stopProbability;
			if ($rand < $prob) {
				$this->out("process instance stopped by probability");
				break;
			}
		}

		$activityExecutionsData = $processExecutionService->getAllActivityExecutions($processInstance);
//		var_dump($activityExecutionsData);

		$executionHistory = $processExecutionService->getExecutionHistory($processInstance);
		$this->assertEqual(count($executionHistory), $i); //there is one hidden activity
		
	}
	
	private function executeServiceDownloadFile($type, core_kernel_classes_Resource $user = null){
		
		$returnValue = '';
		
		$type = strtolower($type);
		
		$this->out("downloading {$type} file : ", true);
		
		$processVariableService = wfEngine_models_classes_VariableService::singleton();
		$unit = $processVariableService->get('unitUri');
		$countryCode = (string) $processVariableService->get('countryCode');
		$languageCode = (string) $processVariableService->get('languageCode');
		$this->assertFalse(empty($unit));
		$this->assertFalse(empty($countryCode));
		$this->assertFalse(empty($languageCode));
		
		if($unit instanceof core_kernel_classes_Resource && !empty($countryCode) && !empty($languageCode)){
			
			$file = $this->getItemFile($unit, $type, $countryCode, $languageCode, $user);
			if(is_null($file)){
				$this->fail("cannot find {$type} file of the unit {$unit->getLabel()}");
			}else{
				$returnValue = $file->getFileContent();
			}

			$this->out("downloaded {$type} file : \n ".$returnValue);
		}
		
		return $returnValue;
	}
	
	private function executeServiceUploadFile($type, $content, $user){
		
		$returnValue = false;
		
		$this->out("uploading {$type} file : ", true);
		
		$processVariableService = wfEngine_models_classes_VariableService::singleton();
		$unit = $processVariableService->get('unitUri');
		$countryCode = (string) $processVariableService->get('countryCode');
		$languageCode = (string) $processVariableService->get('languageCode');
		$this->assertFalse(empty($unit));
		$this->assertFalse(empty($countryCode));
		$this->assertFalse(empty($languageCode));
		
		if($unit instanceof core_kernel_classes_Resource && !empty($countryCode) && !empty($languageCode)){
			
			$type = strtolower($type);
			$file = $this->getItemFile($unit, $type, $countryCode, $languageCode, $user);
			if(is_null($file)){
				$this->fail("cannot find {$type} file of the unit {$unit->getLabel()}");
			}else{
				$this->out('inserting new content : '.$content);
				
				$this->assertTrue($file->setContent($content));
				$returnValue = $file->commit();

				//update the file revision number in the process context:
				//@TODO: use $file->getVersion() instead when implemented
				$revisionNumber = intval($file->getVersion());
				$processVariableService->edit($type, $revisionNumber);
				
				$this->out("{$type} file uploaded.");
			}
		}
		
		
		return $returnValue;
	}
	
	//deprecated:
	private function executeServiceReplaceTranslator($replacement = null){
		
		$returnValue = false;
		
		$translatorRole = $this->roles['translator'];
		
		if($replacement instanceof core_kernel_classes_Resource){
			if($replacement->hasType(new core_kernel_classes_Class($translatorRole->getUri()))){
				$processVariableService = wfEngine_models_classes_VariableService::singleton();
				$returnValue = $processVariableService->edit('translator', $replacement->getUri());
			}
		}
		
		//if return false, no replacement assigned!
		
		return $returnValue;
	}
	
	private function executeServiceLayoutCheck($outputCode = 0){
		
		$returnValue = false;
		
		$this->out('executing service layout check with output code : '.$outputCode, true);
		
		$outputCode = intval($outputCode);
		if(in_array($outputCode, array(0, 1, 2, 3))){
			$processVariableService = wfEngine_models_classes_VariableService::singleton();
			$returnValue = $processVariableService->edit('layoutCheck', $outputCode);
		}else{
			$this->fail('wrong output code for layout check activity');
		}
		
		return $returnValue;
	}
	
	private function executeServiceFinalSignOff($ok = false){
		
		return $this->executeServicePositionVariable('finalCheck', $ok, "execute service final sign off");
		
	}
	
	private function executeServiceBookletLayoutCheck($ok = false){
		
		return $this->executeServicePositionVariable('layoutCheck', $ok, "execute service layout check");
		
	}
	
	private function executeServiceBookletFinalCheck($ok = false){
		
		return $this->executeServicePositionVariable('finalCheck', $ok, "execute service final check");
		
	}
	
	private function executeServiceBookletTDsignOff($ok = false){
		
		return $this->executeServicePositionVariable('TDsignOff', $ok, "execute service TD sign off");
		
	}
	
	private function executeServiceBookletCountrySignOff($ok = false){
		
		return $this->executeServicePositionVariable('countrySignOff', $ok, "execute service country sign off");
		
	}
	
	private function executeServicePositionVariable($variableCode, $ok, $msg){
		
		$returnValue = false;
		
		$this->out($msg, true);
		
		$processVariableService = wfEngine_models_classes_VariableService::singleton();
		$variable = $processVariableService->getProcessVariable($variableCode);
		if(!is_null($variable)){
			$returnValue = $processVariableService->edit($variableCode, (bool) $ok ? 1 : 0);
		}else{
			throw new Exception("the process variable with the code {$variableCode} does not exist");
		}
		
		
		return $returnValue;
		
	}
	
	public function testDeleteCreatedResources(){
		
		if(isset($this->config['delete']) && $this->config['delete'] === false){
			$this->out('Skip resources deletion');
			return;
		}
		
		if(!empty($this->properties)){
			foreach($this->properties as $prop){
				$this->assertTrue($prop->delete());
			}
		}
		
		if(!is_null($this->itemClass)){
			$this->itemClass->delete();
		}
		
		if(!empty($this->units)){
			foreach($this->units as $unit){
				$this->assertTrue($unit->delete());
			}
		}
		
		if(!empty($this->files)){
			foreach($this->files as $file){
				$this->assertTrue($file->delete());
			}
		}
		
		if(!empty($this->users)){
			foreach($this->users as $user){
				$this->assertTrue($user->delete());
			}
		}
		
		if(!empty($this->roles)){
			foreach($this->roles as $role){
				$this->assertTrue($role->delete());
			}
		}
		
		$processExecutionService = wfEngine_models_classes_ProcessExecutionService::singleton();
		foreach($this->processExecutions as $processInstance){
			if($processInstance instanceof core_kernel_classes_Resource){
				$this->assertTrue($processInstance->exists());
				$this->assertTrue($processExecutionService->deleteProcessExecution($processInstance));
				$this->assertFalse($processInstance->exists());
			}
		}
		
		foreach($this->processDefinition as $process){
			if($process instanceof core_kernel_classes_Resource) {
				$authoringService = wfAuthoring_models_classes_ProcessService::singleton();
				$this->assertTrue($authoringService->deleteProcess($process));
				$this->assertFalse($process->exists());
			}
		}
		
		
		if(!empty($this->vars)){
			foreach($this->vars as $code => $variable){
				$deleted = $variable->delete();
				$this->assertTrue($deleted);
				if(!$deleted) var_dump($code);
			}
		}
		
	}
}
?>
