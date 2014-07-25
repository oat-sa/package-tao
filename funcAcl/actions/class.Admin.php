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
 * This controller provide the actions to manage the ACLs
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 
 *
 */
class funcAcl_actions_Admin extends tao_actions_CommonModule {

	/**
	 * Constructor performs initializations actions
	 * @return void
	 */
	public function __construct(){
		parent::__construct();
		$this->defaultData();
	}

	/**
	 * Show the list of roles
	 * @return void
	 */
	public function index(){
		$rolesc = new core_kernel_classes_Class(CLASS_ROLE);
		$roles = array();
		foreach ($rolesc->getInstances(true) as $id => $r) {
			$roles[] = array('id' => $id, 'label' => $r->getLabel());
		}

		$this->setData('roles', $roles);
		$this->setView('list.tpl');
	}

	public function getModules() {
		if (!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		else{
			$role = new core_kernel_classes_Class($this->getRequestParameter('role'));
			$profile = array();
			
			$extManager = common_ext_ExtensionsManager::singleton();
			$extensions = $extManager->getInstalledExtensions();
			$accessService = funcAcl_models_classes_AccessService::singleton();

			$extAccess = funcAcl_helpers_Cache::retrieveExtensions();
			foreach ($extensions as $extId => $ext){
				$extAclUri = $accessService->makeEMAUri($extId);
				$atLeastOneAccess = false;
				$allAccess = in_array($role->getUri(), $extAccess[$extAclUri]);
				
				$profile[$extId] = array('modules' => array(), 
										 'has-access' => false,
										 'has-allaccess' => $allAccess, 
										 'uri' => $extAclUri
				);
				
				foreach (funcAcl_helpers_Model::getModules($extId) as $modUri => $module){
					$moduleAccess = funcAcl_helpers_funcACL::getReversedAccess($module);
					$uri = explode('#', $modUri);
					list($type, $extId, $modId) = explode('_', $uri[1]);
					
					$profile[$extId]['modules'][$modId] = array('has-access' => false,
													 'has-allaccess' => false,
													 'uri' => $module->getUri());
					
					if (true === in_array($role->getUri(), $moduleAccess['module'])){
						$profile[$extId]['modules'][$modId]['has-allaccess'] = true;
						$atLeastOneAccess = true;
					}
					else {
						// have a look at actions.
						foreach ($moduleAccess['actions'] as $roles){
							if (in_array($role->getUri(), $roles)){
								$profile[$extId]['modules'][$modId]['has-access'] = true;
								$atLeastOneAccess = true;
							}
						}
					}
				}
				
				if (!$allAccess && $atLeastOneAccess){
					$profile[$extId]['has-access'] = true;
				}
			}
			
			if (!empty($profile['generis'])){
				unset($profile['generis']);
			}
			
			echo json_encode($profile);
		}
	}

	public function getActions() {
		if (!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		else{
			$role = new core_kernel_classes_Resource($this->getRequestParameter('role'));
			$module = new core_kernel_classes_Resource($this->getRequestParameter('module'));
			$moduleAccess = funcAcl_helpers_funcACL::getReversedAccess($module);
			
			$actions = array();
			foreach (funcAcl_helpers_Model::getActions($module) as $action) {
				$uri = explode('#', $action->getUri());
				list($type, $extId, $modId, $actId) = explode('_', $uri[1]);
				
				$actions[$actId] = array('uri' => $action->getUri(),
										 'has-access' => false);
				
				if (isset($moduleAccess['actions'][$action->getUri()])){
					$grantedRoles = $moduleAccess['actions'][$action->getUri()];
					if (true === in_array($role->getUri(), $grantedRoles)){
						$actions[$actId]['has-access'] = true;
					}
				}
			}
			
			ksort($actions);
			echo json_encode($actions);	
		}
	}

	public function removeExtensionAccess() {
		if (!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		else{
			$role = $this->getRequestParameter('role');
			$uri = $this->getRequestParameter('uri');
			$extensionService = funcAcl_models_classes_ExtensionAccessService::singleton();
			$extensionService->remove($role, $uri);
			echo json_encode(array('uri' => $uri));	
		}
	}

	public function addExtensionAccess() {
		if (!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		else{
			$role = $this->getRequestParameter('role');
			$uri = $this->getRequestParameter('uri');
			$extensionService = funcAcl_models_classes_ExtensionAccessService::singleton();
			$extensionService->add($role, $uri);
			echo json_encode(array('uri' => $uri));
		}
	}

	public function removeModuleAccess() {
		if (!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		else{
			$role = $this->getRequestParameter('role');
			$uri = $this->getRequestParameter('uri');
			$moduleService = funcAcl_models_classes_ModuleAccessService::singleton();
			$moduleService->remove($role, $uri);
			echo json_encode(array('uri' => $uri));	
		}
	}

	public function addModuleAccess() {
		if (!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		else{
			$role = $this->getRequestParameter('role');
			$uri = $this->getRequestParameter('uri');
			$moduleService = funcAcl_models_classes_ModuleAccessService::singleton();
			$moduleService->add($role, $uri);
			echo json_encode(array('uri' => $uri));	
		}
	}

	public function removeActionAccess() {
		if (!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		else{
			$role = $this->getRequestParameter('role');
			$uri = $this->getRequestParameter('uri');
			$actionService = funcAcl_models_classes_ActionAccessService::singleton();
			$actionService->remove($role, $uri);
			echo json_encode(array('uri' => $uri));	
		}
	}

	public function addActionAccess() {
		if (!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		else{
			$role = $this->getRequestParameter('role');
			$uri = $this->getRequestParameter('uri');
			$actionService = funcAcl_models_classes_ActionAccessService::singleton();
			$actionService->add($role, $uri);
			echo json_encode(array('uri' => $uri));	
		}
	}

	public function moduleToActionAccess() {
		if (!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		else{
			$role = $this->getRequestParameter('role');
			$uri = $this->getRequestParameter('uri');
			$actionService = funcAcl_models_classes_ActionAccessService::singleton();
			$actionService->moduleToActionAccess($role, $uri);
			echo json_encode(array('uri' => $uri));	
		}
	}

	public function moduleToActionsAccess() {
		if (!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		else{
			$role = $this->getRequestParameter('role');
			$uri = $this->getRequestParameter('uri');
			$actionService = funcAcl_models_classes_ActionAccessService::singleton();
			$actionService->moduleToActionsAccess($role, $uri);
			echo json_encode(array('uri' => $uri));	
		}
	}

	public function actionsToModuleAccess() {
		if (!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		else{
			$role = $this->getRequestParameter('role');
			$uri = $this->getRequestParameter('uri');
			$moduleService = funcAcl_models_classes_ModuleAccessService::singleton();
			$moduleService->actionsToModuleAccess($role, $uri);
			echo json_encode(array('uri' => $uri));	
		}
	}

	public function getRoles() {
		if (!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		else{
			$useruri = tao_helpers_Uri::decode($this->getRequestParameter('useruri'));
			$roleService = funcAcl_models_classes_RoleService::singleton();
			$roles = $roleService->getRoles($useruri);
			echo json_encode($roles);
		}
	}

	public function attachRole() {
		if (!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		else{
			$roleuri = tao_helpers_Uri::decode($this->getRequestParameter('roleuri'));
			$useruri = tao_helpers_Uri::decode($this->getRequestParameter('useruri'));
			$roleService = funcAcl_models_classes_RoleService::singleton();
			$roleService->attachUser($useruri, $roleuri);
			echo json_encode(array('success' => true, 'id' => tao_helpers_Uri::encode($roleuri)));
		}
	}

	public function unattachRole() {
		if (!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		else{
			$roleuri = tao_helpers_Uri::decode($this->getRequestParameter('roleuri'));
			$useruri = tao_helpers_Uri::decode($this->getRequestParameter('useruri'));
			$roleService = funcAcl_models_classes_RoleService::singleton();
			$roleService->unattachUser($useruri, $roleuri);
			echo json_encode(array('success' => true, 'id' => tao_helpers_Uri::encode($roleuri)));	
		}
	}
}
?>