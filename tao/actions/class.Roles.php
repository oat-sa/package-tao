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
 * Role Controller provide actions performed from url resolution
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoGroups
 
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class tao_actions_Roles extends tao_actions_TaoModule {
	
	
	protected $authoringService = null;
	protected $forbidden = array();
	
	/**
	 * constructor: initialize the service and the default data
	 * @return Role
	 */
	public function __construct()
	{
		
		parent::__construct();
		
		//the service is initialized by default
		$this->service = tao_models_classes_RoleService::singleton();
		$this->defaultData();
	}
	
/*
 * conveniance methods
 */
	
	/**
	 * get the selected group from the current context (from the uri and classUri parameter in the request)
	 * @return core_kernel_classes_Resource $group
	 */
	protected function getCurrentInstance()
	{
		$uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
		if(is_null($uri) || empty($uri)){
			throw new Exception("No valid uri found");
		}
		
		$clazz = $this->getCurrentClass();
		$role = $this->service->getRole($uri);
		if(is_null($role)){
			throw new Exception("No role found for the uri {$uri}");
		}
		
		return $role;
	}
	
	/**
	 * get the main class
	 * @return core_kernel_classes_Classes
	 */
	protected function getRootClass()
	{
		return $this->service->getRoleClass();
	}
	
/*
 * controller actions
 */
	
	/**
	*	forbidden to edit class and create subclass
	*/
	
	/**
	*	index:
	*/
	public function index()
	{
		$this->removeSessionAttribute('uri');
		$this->removeSessionAttribute('classUri');
		
		$this->setView('roles/index.tpl');
	}
	
	/**
	 * Edit a group instance
	 * @return void
	 */
	public function editRole()
	{
		$clazz = $this->getCurrentClass();
		$role = $this->getCurrentInstance();
		
		$formContainer = new tao_actions_form_Role($clazz, $role);
		$myForm = $formContainer->getForm();
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$formValues = $myForm->getValues();
				$roleService = tao_models_classes_RoleService::singleton();
				$includedRolesProperty = new core_kernel_classes_Property(PROPERTY_ROLE_INCLUDESROLE);
				
				// We have to make the difference between the old list
				// of included roles and the new ones.
				$oldIncludedRolesUris = $role->getPropertyValues($includedRolesProperty);
				$newIncludedRolesUris = $formValues[PROPERTY_ROLE_INCLUDESROLE];
				$removeIncludedRolesUris = array_diff($oldIncludedRolesUris, $newIncludedRolesUris);
				$addIncludedRolesUris = array_diff($newIncludedRolesUris, $oldIncludedRolesUris);
				
				// Make the changes according to the detected differences.
				foreach ($removeIncludedRolesUris as $rU){
					$r = new core_kernel_classes_Resource($rU);
					$roleService->unincludeRole($role, $r);
				}
				
				foreach ($addIncludedRolesUris as $aU){
					$r = new core_kernel_classes_Resource($aU);
					$roleService->includeRole($role, $r);
				}
				
				// Let's deal with other properties the usual way.
				unset($formValues[$includedRolesProperty->getUri()]);
				
				$binder = new tao_models_classes_dataBinding_GenerisFormDataBinder($role);
				$role = $binder->bind($myForm->getValues());

				core_kernel_users_Cache::removeIncludedRoles($role); // flush cache for this role.
				
				$this->setData('selectNode', tao_helpers_Uri::encode($role->getUri()));
				$this->setData('message', __('Role saved'));
				$this->setData('reload', true);
			}
		}
		
		$this->setData('uri', tao_helpers_Uri::encode($role->getUri()));
		$this->setData('classUri', tao_helpers_Uri::encode($clazz->getUri()));
		$this->setData('formTitle', 'Edit Role');
		$this->setData('myForm', $myForm->render());
		$this->setView('roles/form.tpl');
	}
	
	public function assignUsers()
	{
	    $role = $this->getCurrentInstance();
	    $prop = new core_kernel_classes_Property(PROPERTY_USER_ROLES);
	    $tree = tao_helpers_form_GenerisTreeForm::buildReverseTree($role, $prop);
	    $tree->setData('title', __('Assign User to role'));
	    $tree->setData('dataUrl', _url('getUsers'));
	    $this->setData('userTree', $tree->render());
		$this->setView('roles/assignUsers.tpl');
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
		else{
			$deleted = false;
			if($this->getRequestParameter('uri')){
				
				$role = $this->getCurrentInstance();
			
				if(!in_array($role->getUri(), $this->forbidden)){
						//check if no user is using this role:
						$userClass = new core_kernel_classes_Class(CLASS_GENERIS_USER);
						$options = array('recursive' => true, 'like' => false);
						$filters = array(PROPERTY_USER_ROLES => $role->getUri());
						$users = $userClass->searchInstances($filters, array());
						if(empty($users)){
							//delete role here:
							$deleted = $this->service->removeRole($role);
						}else{
							//set message error
							throw new Exception(__('This role is still given to one or more users. Please remove the role to these users first.'));
						}
				}else{
					throw new Exception($role->getLabel() . ' could not be deleted');
				}
			}
			
			echo json_encode(array('deleted' => $deleted));	
		}
	}
	
	public function getUsers()
	{
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		else{
			$userService = tao_models_classes_UserService::singleton();
			echo json_encode($userService->toTree(new core_kernel_classes_Class(CLASS_TAO_USER), array()));	
		}
	}
	
	public function editRoleClass()
	{
		$this->removeSessionAttribute('uri');
		$this->index();
	}
	
}
?>
