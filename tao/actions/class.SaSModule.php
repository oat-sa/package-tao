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
 * This controller provides backward compatibility for legacy
 * Stand-alone Services of the workflow engine
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 
 *
 */
abstract class tao_actions_SaSModule extends tao_actions_RdfController {
	
	/**
	 * Whenever or not the call was made in standAlone mode
	 * 
	 * @var boolean
	 */
	private $isStandAlone;
	
	public function __construct() {
		parent::__construct();
		if ($this->hasRequestParameter('standalone') && $this->getRequestParameter('standalone')) {
			tao_helpers_Context::load('STANDALONE_MODE');
			$this->isStandAlone = true;
            $this->setData('client_config_url', $this->getClientConfigUrl());
			common_Logger::d('Standalone mode set');
		} else {
			$this->isStandAlone = false;
		}
	}

	public function setView($path, $extensionID = null) {
		// override non AJAX calls for SAS
		if(!$this->isStandAlone || tao_helpers_Request::isAjax()){
			parent::setView($path, $extensionID);
		} else {
		    $this->setData('client_config_url', $this->getClientConfigUrl());
		    $this->setData('includeTemplate', $path);
			$this->setData('includeExtension', $extensionID);
			parent::setView('sas.tpl', 'tao');
		}
    }

	/**
	 * Returns the root class of the module
	 * @return core_kernel_classes_Class
	 */
	protected function getRootClass() {
		return $this->getClassService()->getRootClass();
	}
	
	protected function getDataKind()
	{
		return Camelizer::camelize(explode(' ', strtolower(trim($this->getRootClass()->getLabel()))), false);
	}
	
	/**
	 * Service of class or instance selection with a tree.
	 * @return void
	 */
	public function sasSelect()
	{

		$kind = $this->getDataKind();
		
		$context = Context::getInstance();
		$module = $context->getModuleName();
		
		$this->setData('treeName', __('Select'));
		$this->setData('dataUrl', _url('sasGetOntologyData'));
		$this->setData('editClassUrl', tao_helpers_Uri::url('sasSet', $module));
		
		if($this->getRequestParameter('selectInstance') == 'true'){
			$this->setData('editInstanceUrl', tao_helpers_Uri::url('sasSet', $module));
			$this->setData('editClassUrl', false);
		}
		else{
			$this->setData('editInstanceUrl', false);
			$this->setData('editClassUrl', tao_helpers_Uri::url('sasSet', $module));
		}
		
		$this->setData('classLabel', $this->getRootClass()->getLabel());
		
		$this->setView("sas/select.tpl", 'tao');
	}
	
	/**
	 * Save the uri or the classUri in parameter into the workflow engine by using the dedicated seervice
	 * @return void
	 */
	public function sasSet()
	{
		$message = __('Error');
		
		//set the class uri
		if($this->hasRequestParameter('classUri')){
			$clazz = $this->getCurrentClass();
			if(!is_null($clazz)){
				$this->setVariables(array($this->getDataKind().'ClassUri' => $clazz->getUri()));
				$message = $clazz->getLabel().' '.__('class selected');
			}
		}
		
		//set the instance uri
		if($this->hasRequestParameter('uri')){
			$instance = $this->getCurrentInstance();
			if(!is_null($instance)){
				$this->setVariables(array($this->getDataKind().'Uri' => $instance->getUri()));
				$message = $instance->getLabel().' '.__($this->getDataKind()).' '.__('selected');
			}
		}
		$this->setData('message', $message);
		
		//only for the notification
		$this->setView('messages.tpl', 'tao');
	}
	
	/**
	 * Add a new instance
	 * @return void
	 */
	public function sasAddInstance()
	{
		try {
			$clazz = $this->getCurrentClass();
		} catch (common_Exception $e) {
			$clazz = $this->getRootClass();
		}
		// @todo call the correct service
		$instance = $this->getClassService()->createInstance($clazz);
		if(!is_null($instance) && $instance instanceof core_kernel_classes_Resource){
			
			//init variable service:
			$this->setVariables(array($this->getDataKind().'Uri' => $instance->getUri()));
			
			$params = array(
				'uri'		=> tao_helpers_Uri::encode($instance->getUri()),
				'classUri'	=> tao_helpers_Uri::encode($clazz->getUri()),
				'standalone' => $this->isStandAlone
			);
			$this->redirect(_url('sasEditInstance', null, null, $params));
		}
	}
	
	
	/**
	 * Edit an instances 
	 * @return void
	 */
	public function sasEditInstance()
	{
		$clazz = $this->getCurrentClass();
		$instance = $this->getCurrentInstance();
		
		$formContainer = new tao_actions_form_Instance($clazz, $instance);
		$myForm = $formContainer->getForm();
		
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$binder = new tao_models_classes_dataBinding_GenerisFormDataBinder($instance);
				$instance = $binder->bind($myForm->getValues());
				$this->setData('message', __('Resource saved'));
			}
		}
		
		$this->setData('uri', tao_helpers_Uri::encode($instance->getUri()));
		$this->setData('classUri', tao_helpers_Uri::encode($clazz->getUri()));
		$this->setData('formTitle', __('Edit'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl', 'tao');
	}
	
	/**
	 * Delete an instance
	 * @return void
	 */
	public function sasDeleteInstance()
	{
		$clazz = $this->getCurrentClass();
		$instance = $this->getCurrentInstance();
		
		$this->setData('label', $instance->getLabel());
		
		$this->setData('uri', tao_helpers_Uri::encode($instance->getUri()));
		$this->setData('classUri', tao_helpers_Uri::encode($clazz->getUri()));
		$this->setView('sas/delete.tpl', 'tao');
	}
	
	// Below this line, basic functionalities copied from TaoModule
	
	/**
	 * get the current item class regarding the classUri' request parameter
	 * prevent exception by returning the root class if no class is selected
	 *  
	 * @return core_kernel_classes_Class the item class
	 */
	protected function getCurrentClass()
	{
		$classUri = tao_helpers_Uri::decode($this->getRequestParameter('classUri'));
		if ($this->isStandAlone && (is_null($classUri) || empty($classUri))) {
			return $this->getRootClass();
		} else {
			return parent::getCurrentClass();
		}
	}
	
	/**
	 * simplified Version of TaoModule function
	 * 
	 * @return void
	 */
	public function sasGetOntologyData() {
		if(!tao_helpers_Request::isAjax()){
			throw new common_exception_IsAjaxAction(__FUNCTION__); 
		}
		
		$showInstances = $this->hasRequestParameter('hideInstances')
			? !(bool)$this->getRequestParameter('hideInstances')
			: true;

		$hideNode = $this->hasRequestParameter('classUri');
		$clazz = $this->hasRequestParameter('classUri') ? $this->getCurrentClass() : $this->getRootClass();
		
		if($this->hasRequestParameter('offset')){
			$options['offset'] = $this->getRequestParameter('offset');
		}
		$limit = $this->hasRequestParameter('limit') ? $this->getRequestParameter('limit') : 0;
		$offset = $this->hasRequestParameter('offset') ? $this->getRequestParameter('offset') : 0;
		
		$factory = new tao_models_classes_GenerisTreeFactory();
		$tree = $factory->buildTree($clazz, $showInstances, array($clazz->getUri()), $limit, $offset);
		
		$returnValue = $hideNode ? ($tree['children']) : $tree;
		echo json_encode($returnValue);
	}
	
    protected function setVariables($variables) {
        common_ext_ExtensionsManager::singleton()->getExtensionById('wfEngine')->load();
        
        $variableService = wfEngine_models_classes_VariableService::singleton();

    	$cleaned = array();
    	foreach ($variables as $key => $value) {
    		$cleaned[$key] = (is_object($value) && $value instanceof core_kernel_classes_Resource) ? $value->getUri() : $value;
    	}
		return $variableService->save($cleaned);
    }
}
?>
