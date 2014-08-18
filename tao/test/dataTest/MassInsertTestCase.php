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
?>
<?php
require_once dirname(__FILE__) . '/../../../tao/test/TaoTestRunner.php';
include_once dirname(__FILE__) . '/../../includes/raw_start.php';

class MassInsertTestCase extends UnitTestCase {

	/**
	 * CHANGE IT MANNUALLY to see step by step the output
	 * @var boolean
	 */
	const OUTPUT = false;

	/**
	 * @var wfEngine_models_classes_ActivityExecutionService the tested service
	 */
	protected $service = null;

	/**
	 * @var wfEngine_models_classes_UserService
	 */
	protected $userService = null;

	/**
	 * @var core_kernel_classes_Resource
	 */
	protected $subjectService = null;

	/**
	 * @var array
	 */
	protected $languagesUri = array();

	/*
	 * Define the execution parameters
	 */

	// Number of subjects to create
	protected $subjectNumber = 1000;
	// Number of groups to create
	protected $groupNumber = 1;
	// Number of tests to create
	protected $testNumber = 1;
	// Number of items to create
	protected $itemNumber = 25;
	// Number of wf User to create
	protected $wfUserNumber = 0 ;

	// Languages available in the TAO platform
	protected $languages = array();
	// Groups available in the TAO platform
	protected $groups = array();
	// Subjects available in the TAO platform
	protected $subjects = array();
	// Tests available in the TAO platform
	protected $tests = array();

	public function setUp(){

		TaoTestRunner::initTest();
		error_reporting(E_ALL);

		$this->loadConstants();
		
		$classLanguage = new core_kernel_classes_Class(CLASS_LANGUAGES);
		$this->languages = $classLanguage->getInstances();
		$this->testService = taoWfTest_models_classes_WfTestService::singleton();
		$this->deliveryService = taoDelivery_models_classes_DeliveryService::singleton();
		$this->itemService = taoItems_models_classes_ItemsService::singleton();
		$this->subjectService = taoSubjects_models_classes_SubjectsService::singleton();
	}
	
	protected function loadConstants(){
		common_ext_ExtensionsManager::singleton()->loadExtensions();
		return true;
	}
	
	public function testCreateGroups(){

		if ($this->groupNumber){

			//$groupClass = new core_kernel_classes_Class(TAO_GROUP_CLASS);
			$TopGroupClass = new core_kernel_classes_Class(TAO_GROUP_CLASS);
			$groupClass = $TopGroupClass->createSubClass("Simulated (TC)", "Simulated Test Case Group Class", LOCAL_NAMESPACE."#SimulatedTestCaseGroupClass");
			$valueProp = new core_kernel_classes_Property(RDF_VALUE);
			$propertyLabel = new core_kernel_classes_Property(RDFS_LABEL);
			$propertyComment = new core_kernel_classes_Property(RDFS_COMMENT);

			for ($i=1; $i<=$this->groupNumber; $i++){

				// create a Subject
				$groupInstance = $groupClass->createInstance();

				// Add label and comment properties functions of the languages available on the TAO platform
				foreach ($this->languages as $lg){
					$lgCode = $lg->getOnePropertyValue($valueProp);
					$lgLabel = $lg->getLabel();
					$groupInstance->setPropertyValueByLg ($propertyLabel, "Group label{$i} {$lgLabel}={$lgCode}", $lgCode);
					$groupInstance->setPropertyValueByLg ($propertyComment, "Group {$i} Comment {$lgLabel}={$lgCode}", $lgCode);

				}
			}

			$this->groups = $groupClass->getInstances ();
			$groupLabels = array();

			//check groups for language dependent properties.

//			$expectedArray = array(	'DE' => 'German=DE',
//					'FR' => 'French=FR',
//					'LU' => 'Luxembourgish=LU',
//					'SE' => 'Swedish=SE',
//					'EN' => 'English=EN');

			//foreach on $this->groups seem to create trouble with
			//testAssociateSubjectGroup test case.

			$groupsToTest = $this->groups;
			foreach ($groupsToTest as $group){
				$usedLgs = $group->getUsedLanguages($propertyLabel);
				foreach ($usedLgs as $lg) {
					$result[$lg] = $group->getPropertyValuesByLg($propertyLabel,$lg)->get(0);
				}


//				foreach ($expectedArray as $k => $v){
//					$this->assertTrue(strpos($result[$k], $v));
//				}
			}
		}
	}

	public function testCreateSubjects(){

		if ($this->subjectNumber){

			// Create the subject class
			//$subjectClass = new core_kernel_classes_Class(TAO_SUBJECT_CLASS);
			$TopSubjectClass = new core_kernel_classes_Class(TAO_SUBJECT_CLASS);
			$subjectClass = $TopSubjectClass->createSubClass("Simulated (TC)", "Simulated Test Case Subject Class", LOCAL_NAMESPACE."#SimulatedTestCaseSubjectClass");

			// Define usefull properties
			$propertyLoginProp = new core_kernel_classes_Property(PROPERTY_USER_LOGIN);
			$propertyPasswordProp = new core_kernel_classes_Property(PROPERTY_USER_PASSWORD);
			$propertyFirstNameProp = new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userFirstName');
			$propertyLastNameProp = new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userLastName');
			$propertyUserDefLgProp = new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userDefLg');
			$propertyUserUILgProp = new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userUILg');
			$propertyUserRolesProp = new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userRoles');
			$propertyLabel = new core_kernel_classes_Property(RDFS_LABEL);
			$propertyComment = new core_kernel_classes_Property(RDFS_COMMENT);
			$propertyRdfTypeProp = new core_kernel_classes_Property(RDF_TYPE);
			$valueProp = new core_kernel_classes_Property(RDF_VALUE);
			$roleDelivery = new core_kernel_classes_Resource(INSTANCE_ROLE_DELIVERY);

			// Create N subjects
			for ($i=1; $i <= $this->subjectNumber; $i++){

				//if($i<=10) {$i++;continue;}

				$login = "s{$i}";
				$password = "123456";
				$firstName = "first name {$i}";
				$lastName = "last name {$i}";

				$languageUri = 'http://www.tao.lu/Ontologies/TAO.rdf#LangEN';//all in english

				// create a Subject
				//$subjectInstanceLabel = "subject {$i}";
				//$subjectInstanceComment = "subject {$i} comment";
				$subjectInstance = $subjectClass->createInstance();

				// Use setProperty to be compliant with the old API
				$subjectInstance->setPropertyValue ($propertyLoginProp, $login);
				$subjectInstance->setPropertyValue ($propertyPasswordProp, core_kernel_users_AuthAdapter::getPasswordHash()->encrypt($password));
				$subjectInstance->setPropertyValue ($propertyFirstNameProp, $firstName);
				$subjectInstance->setPropertyValue ($propertyLastNameProp, $lastName);
				$subjectInstance->setPropertyValue ($propertyUserDefLgProp, $languageUri);
				$subjectInstance->setPropertyValue ($propertyUserUILgProp, $languageUri);
				$subjectInstance->setPropertyValue ($propertyUserRolesProp, $roleDelivery->getUri());

				// Add label and comment properties functions of the languages available on the TAO platform

				foreach ($this->languages as $lg){
					$lgCode = $lg->getOnePropertyValue($valueProp);
					$lgLabel = $lg->getLabel();
					$subjectInstance->setPropertyValueByLg ($propertyLabel, "Subject label{$i} {$lgLabel}={$lgCode}", $lgCode);
					$subjectInstance->setPropertyValueByLg ($propertyComment, "Subject {$i} Comment {$lgLabel}={$lgCode}", $lgCode);
				}

			}

			$this->subjects = $subjectClass->getInstances ();

			//check subjects for language dependent properties.
//			$expectedArray = array(	'DE' => 'German=DE',
//					'FR' => 'French=FR',
//					'LU' => 'Luxembourgish=LU',
//					'SE' => 'Swedish=SE',
//					'EN' => 'English=EN');

			//foreach on $this->subjects seem to create trouble with
			//testAssociateSubjectGroup test case.

			$subjectToTest = $this->subjects;
			foreach ($subjectToTest as $subject){
				$usedLgs = $subject->getUsedLanguages($propertyLabel);
				foreach ($usedLgs as $lg) {
					$result[$lg] = $subject->getPropertyValuesByLg($propertyLabel,$lg)->get(0);
				}


//				foreach ($expectedArray as $k => $v){
//					$this->assertTrue(strpos($result[$k], $v));
//				}
			}
		}
	}


	public function testAssociateSubjectGroup (){
		if(count($this->groups)){

			// Define usefull properties
			$groupMemberProperty = new core_kernel_classes_Property (TAO_GROUP_MEMBERS_PROP);

			// How many subjects by group
			$step = 1;
			$slice = count($this->subjects)/count($this->groups);
			$i = 0;

			$group = current($this->groups);
			foreach ($this->subjects as $subject){
				$group->setPropertyValue ($groupMemberProperty, $subject->getUri());

				$i++;
				if ($i>($step*$slice)-1){
					$group = next($this->groups);
					$step++;
				}
			}
		}
	}

	public function testCreateWfUsers(){

	    $userService = wfEngine_models_classes_UserService::singleton();
	    $class = new core_kernel_classes_Class(CLASS_GENERIS_USER);

	    for ($i=1; $i<=$this->wfUserNumber; $i++){
	        $properties = array(
	                        PROPERTY_USER_LOGIN => 'wf'. $i,
	                        PROPERTY_USER_PASSWORD => '123456',
	                        PROPERTY_USER_FIRSTNAME => 'Generated',
	                        PROPERTY_USER_LASTNAME => 'Generated',
	                        PROPERTY_USER_ROLES => INSTANCE_ROLE_WORKFLOW
	                        );
	        $user = $class->createInstanceWithProperties($properties);
	    }
	}

	public function testCreateTests () {

		if (!$this->testNumber){
			return;
		}

		// Define usefull properties
		$testActiveProperty = new core_kernel_classes_Property(TEST_ACTIVE_PROP);

		// Get the test class
		//$testClass = new core_kernel_classes_Class(TAO_TEST_CLASS);
		$TopTestClass = new core_kernel_classes_Class(TAO_TEST_CLASS);
		$testClass = $TopTestClass->createSubClass("Simulated (TC)", "Simulated Test Case Test Class", LOCAL_NAMESPACE."#SimulatedTestCaseTestClass");
		
		$itemClass = new core_kernel_classes_Class(TAO_ITEM_CLASS);
		$allItems = $itemClass->getInstances(true);
		$items = array();
		if(count($allItems)){
			$missingItemCount = $this->itemNumber - count($allItems);
			$items = array_values($allItems);
			reset($allItems);
			while($missingItemCount > 0){
				$nextItem = current($allItems);
				if(next($allItems) === false){
					$nextItem = reset($allItems);
				}
				$items[] = $this->itemService->cloneInstance($nextItem, $itemClass);
				$missingItemCount--;
			}
		}
		
		for ($i=0; $i<$this->testNumber; $i++){

			// Create a test instance
			$test = $this->testService->createInstance($testClass, "AutoInsert Test {$i}");

			// Associate an item to the test
			$this->testService->setTestItems($test, $items);

			// Active the test
			$test->setPropertyValue($testActiveProperty, GENERIS_TRUE);
		}

		$this->tests = $testClass->getInstances();

		// Create a delivery
		$topDeliveryClass = new core_kernel_classes_Class(TAO_DELIVERY_CLASS);
		$deliveryClass = $topDeliveryClass->createSubClass("Simulated (TC)", "Simulated Test Case Test Class", LOCAL_NAMESPACE."#SimulatedTestCaseDeliveryClass");
		$delivery = $this->deliveryService->createInstance($deliveryClass, 'AutoInsert Delivery');
		// Set the groups
		$groupsParam = array(); foreach($this->groups as $group) $groupsParam[]= $group->getUri();
		$this->deliveryService->setDeliveryGroups($delivery, $groupsParam);
		// Set the tests
		$testsParam = array(); foreach($this->tests as $t) $testsParam[]= $t;
		$this->deliveryService->setDeliveryTests($delivery, $testsParam);
	}

}
?>
