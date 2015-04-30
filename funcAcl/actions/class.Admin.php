<?php
use oat\tao\helpers\ControllerHelper;
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
     * Access to this functionality is inherited from
     * an included role
     * 
     * @var string
     */
    const ACCESS_INHERITED = 'inherited';
    
    /**
     * Full access to this functionalities and children
     * 
     * @var string
     */
    const ACCESS_FULL = 'full';
    
    /**
     * Partial access to thie functionality means
     * some children are at least partial accessible
     * 
     * @var string
     */
    const ACCESS_PARTIAL = 'partial';
    
    /**
     * No access to this functionality or any of its children
     * 
     * @var string
     */
    const ACCESS_NONE = 'none';

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
        usort($roles, function($a, $b) {
        	return strcmp($a['label'],$b['label']);
        });

        $this->setData('roles', $roles);
        $this->setView('list.tpl');
    }

    public function getModules() {
        if (!tao_helpers_Request::isAjax()){
            throw new Exception("wrong request mode");
        } else {
            $role = new core_kernel_classes_Class($this->getRequestParameter('role'));
            
            $included = array();
            foreach (tao_models_classes_RoleService::singleton()->getIncludedRoles($role) as $includedRole) {
                $included[$includedRole->getUri()] = $includedRole->getLabel();
            }
            
            $extManager = common_ext_ExtensionsManager::singleton();

            $extData = array();
            foreach ($extManager->getInstalledExtensions() as $extension){
                if ($extension->getId() != 'generis') {
                    $extData[] = $this->buildExtensionData($extension, $role->getUri(), array_keys($included));
                }
            }
            
            usort($extData, function($a, $b) {
                return strcmp($a['label'],$b['label']);
            });
            
            
            $this->returnJson(array(
            	'extensions' => $extData,
                'includedRoles' => $included
            ));
        }
    }
    
    protected function buildExtensionData(common_ext_Extension $extension, $roleUri, $includedRoleUris) {
        $extAccess = funcAcl_helpers_Cache::getExtensionAccess($extension->getId());
        $extAclUri = funcAcl_models_classes_AccessService::singleton()->makeEMAUri($extension->getId());
        $atLeastOneAccess = false;
        $allAccess = in_array($roleUri, $extAccess);
        $inherited = count(array_intersect($includedRoleUris, $extAccess)) > 0;
        
        $controllers = array();
        foreach (ControllerHelper::getControllers($extension->getId()) as $controllerClassName) {
            $controllerData = $this->buildControllerData($controllerClassName, $roleUri, $includedRoleUris);
            $atLeastOneAccess = $atLeastOneAccess || $controllerData['access'] != self::ACCESS_NONE;
            $controllers[] = $controllerData;
        }
        
        usort($controllers, function($a, $b) {
        	return strcmp($a['label'],$b['label']);
        });
        
        $access = $inherited ? 'inherited'
            : ($allAccess ? 'full'
                : ($atLeastOneAccess ? 'partial' : 'none'));
        
        return array(
            'uri' => $extAclUri,
            'label' => $extension->getName(),
            'access' => $access,
            'modules' => $controllers
        );
        
    }

    protected function buildControllerData($controllerClassName, $roleUri, $includedRoleUris) {
        
        $modUri = funcAcl_helpers_Map::getUriForController($controllerClassName);
        
        $moduleAccess = funcAcl_helpers_Cache::getControllerAccess($controllerClassName);
        $uri = explode('#', $modUri);
        list($type, $extId, $modId) = explode('_', $uri[1]);
        
        $access = self::ACCESS_NONE;
        if (count(array_intersect($includedRoleUris, $moduleAccess['module'])) > 0) {
            $access = self::ACCESS_INHERITED;
        } elseif (true === in_array($roleUri, $moduleAccess['module'])){
            $access = self::ACCESS_FULL;
        } else {
            // have a look at actions.
            foreach ($moduleAccess['actions'] as $roles) {
                if (in_array($roleUri, $roles) || count(array_intersect($includedRoleUris, $roles)) > 0){
                    $access = self::ACCESS_PARTIAL;
                    break;
                }
            }
        }
        
        return array(
            'uri' => $modUri,
            'label' => $modId,
            'access' => $access
        );
    }
    
    /**
     * Shows the access to the actions of a controller for a specific role
     * 
     * @throws Exception
     */
    public function getActions()
    {
        if (!tao_helpers_Request::isAjax()) {
            throw new Exception("wrong request mode");
        } else {
            $role = new core_kernel_classes_Resource($this->getRequestParameter('role'));
            $included = array();
            foreach (tao_models_classes_RoleService::singleton()->getIncludedRoles($role) as $includedRole) {
                $included[] = $includedRole->getUri();
            }
            $module = new core_kernel_classes_Resource($this->getRequestParameter('module'));
            
            $controllerClassName = funcAcl_helpers_Map::getControllerFromUri($module->getUri());
            $controllerAccess = funcAcl_helpers_Cache::getControllerAccess($controllerClassName);
            
            $actions = array();
            foreach (ControllerHelper::getActions($controllerClassName) as $actionName) {
                $uri = funcAcl_helpers_Map::getUriForAction($controllerClassName, $actionName);
                $part = explode('#', $uri);
                list($type, $extId, $modId, $actId) = explode('_', $part[1]);
                
                $allowedRoles = isset($controllerAccess['actions'][$actionName])
                    ? array_merge($controllerAccess['module'], $controllerAccess['actions'][$actionName])
                    : $controllerAccess['module'];
                
                $access = count(array_intersect($included, $allowedRoles)) > 0
                    ? self::ACCESS_INHERITED
                    : (in_array($role->getUri(), $allowedRoles)
                        ? self::ACCESS_FULL
                        : self::ACCESS_NONE);
                
                $actions[$actId] = array(
                    'uri' => $uri,
                    'access' => $access
                );
            }
            
            ksort($actions);
            
            $this->returnJson($actions);    
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
        if (!tao_helpers_Request::isAjax()) {
            throw new Exception("wrong request mode");
        } else {
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

}