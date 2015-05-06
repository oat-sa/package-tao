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
?>
<?php

/**
 * Tests Controller provide actions performed from url resolution
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoTests
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
class taoTests_actions_Tests extends tao_actions_SaSModule {

	protected function getClassService() {
		return taoTests_models_classes_TestsService::singleton();
	}
	
	/**
	 * constructor: initialize the service and the default data
	 */
	public function __construct()
	{

		parent::__construct();

		//the service is initialized by default
		$this->service = taoTests_models_classes_TestsService::singleton();
		$this->defaultData();
	}

/*
 * conveniance methods
 */

	/**
	 * get the main class
	 * @return core_kernel_classes_Classes
	 */
	protected function getRootClass()
	{
		return $this->service->getRootclass();
	}


/*
 * controller actions
 */


	/**
	 * edit a test instance
	 * @return void
	 */
	public function editTest()
	{
		$clazz = $this->getCurrentClass();
		$test = $this->getCurrentInstance();
		$testModel = $this->service->getTestModel($test);
		// workaround because of bug:
		$testModel = $testModel == '' ? null : $testModel;

		$formContainer = new tao_actions_form_Instance($clazz, $test);
		$myForm = $formContainer->getForm();
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$propertyValues = $myForm->getValues();

				// don't hande the testmodel via bindProperties
				if(array_key_exists(PROPERTY_TEST_TESTMODEL, $propertyValues)){
					$modelUri = $propertyValues[PROPERTY_TEST_TESTMODEL];
					unset($propertyValues[PROPERTY_TEST_TESTMODEL]);
					if (!empty($modelUri)) {
						$testModel = new core_kernel_classes_Resource($modelUri);
						$this->service->setTestModel($test, $testModel);
					}
				} else {
					common_Logger::w('No testmodel on test form', 'taoTests');
				}

				//then save the property values as usual
				$binder = new tao_models_classes_dataBinding_GenerisFormDataBinder($test);
				$test = $binder->bind($propertyValues);

				//edit process label:
				$this->service->onChangeTestLabel($test);

				$this->setData('message', __('Test saved'));
				$this->setData('reload', true);
			}
		}
		$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($test->getUri()));

//		$myForm->removeElement(tao_helpers_Uri::encode(TEST_TESTCONTENT_PROP));

		// render authoring
		if (!empty($testModel)) {
			$modelImpl = $this->service->getTestModelImplementation($testModel);
			if (!empty($modelImpl)) {
				$this->setData('authoring', $modelImpl->getAuthoring($test));
			}
		}

		$this->setData('uri', tao_helpers_Uri::encode($test->getUri()));
		$this->setData('classUri', tao_helpers_Uri::encode($clazz->getUri()));
		$this->setData('formTitle', __('Test properties'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form_test.tpl');
	}

	/**
	 * Edit a test model (edit a class)
	 * @return void
	 */
	public function editTestClass()
	{
		$clazz = $this->getCurrentClass();

		if($this->hasRequestParameter('property_mode')){
			$this->setSessionAttribute('property_mode', $this->getRequestParameter('property_mode'));
		}

		$myForm = $this->editClass($clazz, $this->service->getRootclass());
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				if($clazz instanceof core_kernel_classes_Resource){
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->getUri()));
				}
				$this->setData('message', __('Class saved'));
				$this->setData('reload', true);
			}
		}
		$this->setData('formTitle', __('Edit test class'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl');
	}

	/**
	 * delete a test or a test class
	 * called via ajax
	 * @return void
	 */
	public function delete()
	{
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}

		$deleted = false;
		if($this->getRequestParameter('uri')){
			$deleted = $this->service->deleteTest($this->getCurrentInstance());
		}
		else{
			$deleted = $this->service->deleteTestClass($this->getCurrentClass());
		}

		echo json_encode(array('deleted'	=> $deleted));
	}



	/**
	 * display the authoring  template
	 * @return void
	 */
	public function authoring()
	{
		$this->setData('error', false);
		try{

			//get process instance to be authored
			 $test = $this->getCurrentInstance();
			 $processDefinition = $test->getUniquePropertyValue(new core_kernel_classes_Property(TEST_TESTCONTENT_PROP));
			$this->setData('processUri', tao_helpers_Uri::encode($processDefinition->getUri()));
		}
		catch(Exception $e){
			$this->setData('error', true);
			$this->setData('errorMessage', $e);
		}
		$this->setView('authoring/process_authoring_tool.tpl');
	}
}
?>
