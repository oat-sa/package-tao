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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * This Module aims at managing the Group class and its instances.
 * 
 * @author Bertrand Chevrier, <bertrand@taotesting.com>
 * @package taoGroups
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoGroups_actions_Groups extends tao_actions_SaSModule {

	/**
	 * Initialize the service and the default data
	 */
	public function __construct()
	{
		
		parent::__construct();
		
		//the service is initialized by default
		$this->service = taoGroups_models_classes_GroupsService::singleton();
		$this->defaultData();
	}

	protected function getClassService()
	{
		return taoGroups_models_classes_GroupsService::singleton();
	}
	
/*
 * controller actions
 */
	
	/**
	 * This action aims at editing the Group class or its sub-classes.
	 * 
	 * It looks for the 'classUri' request parameter to select which class will be edited but also
	 * looks for the 'property_mode' request parameter or the session attribute with the same key 
	 * to idenfity if the advanced or simple mode is in use.
	 * 
	 * This action will generate 4 entries in the request data:
	 * 
	 * * 'message' (string) A message to be displayed to the end users.
	 * * 'reload' (boolean) States if the page must be reloaded or not in the browser.
	 * * 'formTitle' (string) The title of the displayed form.
	 * * 'myForm' (tao_helpers_form_FormContainer) The form to be displayed.
	 * 
	 * The template selected by this action is 'form.tpl' from the tao meta-extension.
	 * 
	 */
	public function editGroupClass()
	{
		$clazz = $this->getCurrentClass();
		
		if($this->hasRequestParameter('property_mode')){
			$this->setSessionAttribute('property_mode', $this->getRequestParameter('property_mode'));
		}
		
		$myForm = $this->editClass($clazz, $this->service->getRootClass());
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				if($clazz instanceof core_kernel_classes_Resource){
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->getUri()));
				}
				$this->setData('message', __('Class saved'));
				$this->setData('reload', true);
			}
		}
		$this->setData('formTitle', __('Edit group class'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl', 'tao');
	}
	
	/**
	 * Edit a group instance
	 * @return void
	 */
	public function editGroup()
	{
		$clazz = $this->getCurrentClass();
		$group = $this->getCurrentInstance();

		$formContainer = new tao_actions_form_Instance($clazz, $group);
		$myForm = $formContainer->getForm();
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$binder = new tao_models_classes_dataBinding_GenerisFormDataBinder($group);
				$group = $binder->bind($myForm->getValues());
				
				$this->setData('message', __('Group saved'));
				$this->setData('reload', true);
			}
		}
		
		$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($group->getUri()));
		
		$memberProperty = new core_kernel_classes_Property(TAO_GROUP_MEMBERS_PROP);
		$memberForm = tao_helpers_form_GenerisTreeForm::buildTree($group, $memberProperty);
		$memberForm->setData('title',	__('Select group test takers'));
		$this->setData('memberForm', $memberForm->render());
		
		if (common_ext_ExtensionsManager::singleton()->isEnabled('taoDelivery')) {
		    $this->setData('deliveryForm', \taoDelivery_helper_Delivery::renderDeliveryTree($group));
		}
		$this->setData('formTitle', __('Edit group'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form_group.tpl');
	}
	
	/**
	 * Delete a group or a group class
	 * @return void
	 */
	public function delete()
	{
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$deleted = false;
		if($this->getRequestParameter('uri')){
			$deleted = $this->service->deleteGroup($this->getCurrentInstance());
		}
		else{
			$deleted = $this->service->deleteGroupClass($this->getCurrentClass());
		}
		
		echo json_encode(array('deleted'	=> $deleted));
	}

}