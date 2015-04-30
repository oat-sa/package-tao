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
 * Copyright (c) 2013 Open Assessment Technologies S.A.
 *
 */

 /** 
 * ResultServer Controller provide actions performed from url resolution
 * 
 * @package taoResultServer
 * @license GPLv2  
 */
 
class taoResultServer_actions_ResultServerMgt extends tao_actions_SaSModule {
	
	public function getClassService() {
		return taoResultServer_models_classes_ResultServerAuthoringService::singleton();
	}
	
	/**
	 * constructor: initialize the service and the default data
	 * @return Delivery
	 */
	public function __construct(){
    	parent::__construct();
		$this->service = taoResultServer_models_classes_ResultServerAuthoringService::singleton();
		$this->defaultData();
		
	}
	/**
	 * get the selected resultServer from the current context (from the uri and classUri parameter in the request)
	 * @return core_kernel_classes_Resource $resultServer
	 */
	private function getCurrentResultServer(){
		return $this->getCurrentInstance();
	}
	/**
	 * @see TaoModule::getRootClass
	 * @return core_kernel_classes_Classes
	 */
	protected function getRootClass(){
		return $this->service->getResultServerClass();
	}
	/**
	 * Render json data to populate the result servers tree 
	 * 'modelType' must be in the request parameters
	 * @return void
     * @throws Exception
     */
    public function getResultServers(){
		
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$options = array(
			'subclasses' => true, 
			'instances' => true, 
			'highlightUri' => '', 
			'labelFilter' => '', 
			'chunk' => false
		);
		if($this->hasRequestParameter('filter')){
			$options['labelFilter'] = $this->getRequestParameter('filter');
		}
		if($this->hasRequestParameter('classUri')){
			$clazz = $this->getCurrentClass();
			$options['chunk'] = true;
		}
		else{
			$clazz = $this->service->getResultServerClass();
		}
        echo json_encode( $this->service->toTree($clazz , $options));
	}
	
	/**
	 * Edit a delivery instance
	 * @return void
	 */
	public function editResultServer(){
		$clazz = $this->getCurrentClass();
		
		$resultServer = $this->getCurrentResultServer();
		
		$formContainer = new tao_actions_form_Instance($clazz, $resultServer);
		$myForm = $formContainer->getForm();
		
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$binder = new tao_models_classes_dataBinding_GenerisFormDataBinder($resultServer);
				
				$resultServer = $binder->bind($myForm->getValues());
		        $this->setData("selectNode", tao_helpers_Uri::encode($resultServer->getUri()));
				$this->setData('message', __('Result Server saved'));
				$this->setData('reload', true);
			}
		}
		
		$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($resultServer->getUri()));
		$this->setData('formTitle', __('Edit ResultServer'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form_resultserver.tpl');
	}
	
	/**
	 * Add a resultServer instance        
	 * @return void
     * @throws Exception
     */
    public function addResultServer(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->getCurrentClass();
		$resultServer = $this->service->createInstance($clazz, $this->service->createUniqueLabel($clazz));
		if(!is_null($resultServer) && $resultServer instanceof core_kernel_classes_Resource){
			echo json_encode(array(
				'label'	=> $resultServer->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($resultServer->getUri())
			));
		}
	}
	
	/**
	 * Add a resultServer subclass
	 * @return void
     * @throws Exception
     */
    public function addResultServerClass(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->service->createResultServerClass($this->getCurrentClass());
		if(!is_null($clazz) && $clazz instanceof core_kernel_classes_Class){
			echo json_encode(array(
				'label'	=> $clazz->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clazz->getUri())
			));
		}
	}
	
	/**
	 * Delete a resultServer or a resultServer class
	 * @return void
     * @throws Exception
     */
    public function delete(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$deleted = false;
		if($this->getRequestParameter('uri')){
			$deleted = $this->service->deleteResultServer($this->getCurrentResultServer());
		}
		else{
			$deleted = $this->service->deleteResultServerClass($this->getCurrentClass());
		}
		
		echo json_encode(array('deleted'	=> $deleted));
	}
	
	/**
	 * Duplicate a resultServer instance
	 * @return void
     * @throws Exception
     */
    public function cloneResultServer(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$resultServer = $this->getCurrentResultServer();
		$clazz = $this->getCurrentClass();
		
		$clone = $this->service->createInstance($clazz);
		if(!is_null($clone)){
			
			foreach($clazz->getProperties() as $property){
				foreach($resultServer->getPropertyValues($property) as $propertyValue){
					$clone->setPropertyValue($property, $propertyValue);
				}
			}
			$clone->setLabel($resultServer->getLabel()."'");
			echo json_encode(array(
				'label'	=> $clone->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clone->getUri())
			));
		}
	}
    public function migrate(){
        
        $resultStorages = $this->service->getResultStorages();
        $this->setData('availableRStorage', $resultStorages['r']);
        $this->setData('availableWStorage', $resultStorages['w']);
        $this->setView('migrate.tpl');
    }
    
    public function migrateData(){

        $sourceStorage= $this->getRequestParameter('source');
        $targetStorage= $this->getRequestParameter('target');
        $opType= $this->getRequestParameter('operation');
        
              
        //check params
      
        $status = $this->service->migrateData($sourceStorage, $targetStorage, $opType);
		$this->returnJson( $status );        
    }
}
