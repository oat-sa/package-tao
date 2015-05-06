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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */

/**
 *  Process Controler provide actions to edit a process
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package wfEngine
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class wfAuthoring_actions_Service extends tao_actions_TaoModule {
	
	/**
	 * constructor: initialize the service and the default data
	 * @return Groups
	 */
	public function __construct()
	{
		
		parent::__construct();
		
		//the service is initialized by default
		$this->service = tao_models_classes_InteractiveServiceService::singleton();
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
		return $this->service->getRootClass();
	}
	
	public function editInstance() {
		$clazz = $this->getCurrentClass();
		$instance = $this->getCurrentInstance();
		$myFormContainer = new wfAuthoring_actions_form_InstanceServiceDefinition($clazz, $instance);
		$myForm = $myFormContainer->getForm();
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$values = $myForm->getValues();
				// save properties
				$binder = new tao_models_classes_dataBinding_GenerisFormDataBinder($instance);
				$instance = $binder->bind($values);
				$message = __('Instance saved');
				
				$this->setData('message',$message);
				$this->setData('reload', true);
			}
		}

		$this->setData('formTitle', __('Edit Instance'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl', 'tao');
	}
	
}
?>