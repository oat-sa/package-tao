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
 *               2013 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

class taoDelivery_actions_DeliveryServerAuthentification extends tao_actions_CommonModule
{
	public function index()
	{
		
		if($this->hasRequestParameter('errorMessage')){
			$this->setData('errorMessage',$this->getRequestParameter('errorMessage'));
		}
		
		$userService = taoDelivery_models_classes_UserService::singleton();
		
		$myLoginFormContainer = new wfEngine_actions_form_Login();
		$myForm = $myLoginFormContainer->getForm();
		
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$values = $myForm->getValues();
				if($userService->loginUser($values['login'], $values['password'])){
					$this->redirect(_url('index', 'DeliveryServer'));
				}
				else{
					$this->setData('errorMessage', __('Invalid login or password. Please try again.'));
				}
			}
		}
		
		tao_helpers_Scriptloader::addJsFile(BASE_WWW . 'js/login.js');
		$this->setData('form', $myForm->render());
		$this->setView('runtime/login.tpl');
	}


	public function logout(){
		session_destroy();
		$this->redirect(tao_helpers_Uri::url('index', 'DeliveryServerAuthentification'));
	}
}