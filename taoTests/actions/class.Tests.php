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
use oat\tao\model\lock\LockManager;

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
 * controller actions
 */


	/**
	 * edit a test instance
	 * @requiresRight id READ
	 */
	public function editTest()
	{
    	$test = new core_kernel_classes_Resource($this->getRequestParameter('id'));
	    if (!$this->isLocked($test)) {

	        // my lock
	        $lock = LockManager::getImplementation()->getLockData($test);
	        if (!is_null($lock) && $lock->getOwnerId() == common_session_SessionManager::getSession()->getUser()->getIdentifier()) {
	            $this->setData('lockDate', $lock->getCreationTime());
	            $this->setData('id', $lock->getResource()->getUri());
	        }
	        
    		$clazz = $this->getCurrentClass();
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
    
    		        $this->setData("selectNode", tao_helpers_Uri::encode($test->getUri()));
    				$this->setData('message', __('Test saved'));
    				$this->setData('reload', true);
    			}
    		}
    
    		$myForm->removeElement(tao_helpers_Uri::encode(TEST_TESTCONTENT_PROP));
    
    		$this->setData('uri', tao_helpers_Uri::encode($test->getUri()));
    		$this->setData('classUri', tao_helpers_Uri::encode($clazz->getUri()));
    		$this->setData('formTitle', __('Test properties'));
    		$this->setData('myForm', $myForm->render());
    		$this->setView('Tests/editTest.tpl');
	    }
	}

	/**
	 * delete a test or a test class
	 * called via ajax
	 * @return void
     * @throws Exception
	 * @requiresRight id WRITE 
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
			return $this->forward('deleteClass', null, null, (array('id' => $this->getRequestParameter('id'))));
		}

		echo json_encode(array('deleted'	=> $deleted));
	}



	/**
	 * Redirect the test's authoring
	 * @requiresRight id WRITE
	 */
	public function authoring()
	{
        $test = new core_kernel_classes_Resource($this->getRequestParameter('id'));
        if (!$this->isLocked($test)) {
            $testModel = $this->service->getTestModel($test);
            if(!is_null($testModel)){
                $testModelImpl = $this->service->getTestModelImplementation($testModel);
                $authoringUrl = $testModelImpl->getAuthoringUrl($test);
                if(!empty($authoringUrl)){
                    $userId = common_session_SessionManager::getSession()->getUser()->getIdentifier();
                    LockManager::getImplementation()->setLock($test, $userId);
                    return $this->forwardUrl($authoringUrl);
                }
            }
            throw new common_exception_NoImplementation();
        }
	}
	
	/**
	 * overwrite the parent moveInstance to add the requiresRight only in Tests
	 * @see tao_actions_TaoModule::moveInstance()
	 * @requiresRight uri WRITE
	 * @requiresRight destinationClassUri WRITE
	 */
	public function moveInstance()
	{
	    parent::moveInstance();
	}
	
	/**
	 * overwrite the parent cloneInstance to add the requiresRight only in Tests
	 * @see tao_actions_TaoModule::cloneInstance()
	 * @requiresRight uri READ
	 * @requiresRight classUri WRITE
	 */
	public function cloneInstance()
	{
	    return parent::cloneInstance();
	}
}
