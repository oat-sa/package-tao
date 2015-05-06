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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoDevTools\actions;

use oat\taoDevTools\forms\UserDebugRoles;

/**
 * This controller provide the actions to manage the user settings
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 * @subpackage actions
 *
 */
class UserDebug extends \tao_actions_CommonModule {

	/**
	 * @access protected
	 * @var tao_models_classes_UserService
	 */
	protected $userService = null;

	/**
	 * initialize the services
	 */
	public function __construct(){
		parent::__construct();
		$this->userService = \tao_models_classes_UserService::singleton();
	}

	/**
	 * Action dedicated to fake roles
	 */
	public function roles(){

	    $currentSession = \common_session_SessionManager::getSession();
	    if ($currentSession instanceof \common_session_RestrictedSession) {
	        $this->setData('roles', $currentSession->getUserRoles());
	        $this->setView('userdebug/restore.tpl');
	    } else {
	        $myFormContainer = new UserDebugRoles();
	        $myForm = $myFormContainer->getForm();
	        if($myForm->isSubmited() && $myForm->isValid()){
				$user = $this->userService->getCurrentUser();
				$filter = $myForm->getValue('rolefilter');
				$userUri = $myForm->getValue('user');
				if ($userUri != \common_session_SessionManager::getSession()->getUserUri()) {
				    throw new \common_exception_Error('Security exception, user to be changed is not the current user');
				}
				$session = new \common_session_RestrictedSession(\common_session_SessionManager::getSession(), $myForm->getValue('rolefilter'));
				\common_session_SessionManager::startSession($session);
				$this->setData('roles', $currentSession->getUserRoles());
				$this->setView('userdebug/restore.tpl');
	        } else {
    	        $this->setData('formTitle'	, __("Restrict Roles"));
    	        $this->setData('myForm'		, $myForm->render());
    	        
    	        $this->setView('form_container.tpl', 'tao');
	        }
	    }
	}
	
	public function restore(){
	    $currentSession = \common_session_SessionManager::getSession();
	    if ($currentSession instanceof \common_session_RestrictedSession) {
	        $currentSession->restoreOriginal();
	    }
	    $this->redirect(_url('index','main','tao'));
	}
}