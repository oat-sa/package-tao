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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoDacSimple\controller;

use oat\taoDacSimple\model\DataBaseAccess;
use oat\taoDacSimple\model\AdminService;
use oat\taoDacSimple\model\PermissionProvider;

/**
 * This controller is used to manage permission administration
 *
 * @author Open Assessment Technologies SA
 * @package taoDacSimple
 * @subpackage actions
 * @license GPL-2.0
 *
 */
class AdminAccessController extends \tao_actions_CommonModule
{

    private $dataAccess = null;

    /**
     * initialize the services
     */
    public function __construct()
    {
        parent::__construct();
        $this->dataAccess = new DataBaseAccess();
    }

    /**
     * A possible entry point to tao
     * @requiresRight id GRANT
     */
    public function instancePermissions()
    {
        return $this->adminPermissions();
    }

    /**
     * A possible entry point to tao
     * @requiresRight id GRANT
     */
    public function classPermissions()
    {
        return $this->adminPermissions();
    }

    /**
     * Manage permissions
     */
    protected function adminPermissions()
    {
        $resource = new \core_kernel_classes_Resource($this->getRequestParameter('id'));
        
        $accessRights = AdminService::getUsersPermissions($resource->getUri());
        $userList = $this->getUserList();
        $roleList = $this->getRoleList();
        
        $this->setData('privileges', PermissionProvider::getRightLabels());
        
        $userData = array();
        foreach (array_keys($accessRights) as $uri) {
            if (isset($userList[$uri])) {
                $userData[$uri] = array(
                    'label' => $userList[$uri],
                    'isRole' => false
                );
                unset($userList[$uri]);
            } elseif (isset($roleList[$uri])) {
                $userData[$uri] = array(
                    'label' => $roleList[$uri],
                    'isRole' => true
                );
                unset($roleList[$uri]);
            } else {
                \common_Logger::d('unknown user '.$uri);
            }
        }
        
        $this->setData('users', $userList);
        $this->setData('roles', $roleList);
        
        $this->setData('userPrivileges', $accessRights);
        $this->setData('userData', $userData);
        
        
        $this->setData('uri', $resource->getUri());
        $this->setData('label', _dh($resource->getLabel()));
        
        $this->setView('AdminAccessController/index.tpl');
    }


    /**
     * get the list of users
     * @param array $resourceIds
     * @return array key => value with key = user Uri and value = user Label
     */
    protected function getUserList()
    {
        $userService = \tao_models_classes_UserService::singleton();
        $users = array();
        foreach ($userService->getAllUsers() as $user) {
            $users[$user->getUri()] = _dh($user->getLabel());
        }
        
        return $users;
    }

    /**
     * get the list of roles
     * @param array $resourceIds
     * @return array key => value with key = user Uri and value = user Label
     */
    protected function getRoleList()
    {
        $roleService = \tao_models_classes_RoleService::singleton();
        
        $roles = array();
        foreach ($roleService->getAllRoles() as $role) {
            $roles[$role->getUri()] = _dh($role->getLabel());
        }

        return $roles;
    }

    /**
     * add privileges for a group of users on resources. It works for add or modify privileges
     * @return bool
     */
    public function savePermissions()
    {
        $users = $this->getRequest()->getParameter('users');
        $resourceIds = (array)$this->getRequest()->getParameter('resource_id');
        $recursive = ($this->getRequest()->getParameter('recursive') === "1");

        // cleanup uri param
        if ($this->hasRequestParameter('uri')) {
            $resourceId = $this->getRequest()->getParameter('uri');
        } else {
            $resourceId = (string)$this->getRequest()->getParameter('resource_id');
        }

        // cleanup privilege param
        if ($this->hasRequestParameter('privileges')) {
            $privileges = $this->getRequestParameter('privileges');
        } else {
            $privileges = array();
            foreach ($this->getRequest()->getParameter('users') as $userId => $data) {
                unset($data['type']);
                $privileges[$userId] = array_keys($data);
            }
        }
        
        // Check if there is still a owner on this resource
        if (!$this->validatePermissions($privileges)) {
            \common_Logger::e('Cannot save a list without a fully privileged user');
            return $this->returnJson(array(
            	'success' => false
            ), 500);
        }

        //get resource
        $clazz = new \core_kernel_classes_Class($resourceId);
        $resources = array($clazz);
        if($recursive){
            $resources = array_merge($resources, $clazz->getSubClasses(true));
            $resources = array_merge($resources, $clazz->getInstances(true));
        }

        foreach($resources as $resource){
            $permissions = $this->dataAccess->getDeltaPermissions($resource->getUri(),$privileges);
            // add permissions
            foreach ($permissions['add'] as $userId => $privilegeIds) {
                if(count($privilegeIds) > 0){
                    $this->dataAccess->addPermissions($userId, $resource->getUri(), $privilegeIds);
                }
            }
            // remove permissions
            foreach ($permissions['remove'] as $userId => $privilegeIds) {
                if(count($privilegeIds) > 0){
                    $this->dataAccess->removePermissions($userId,$resource->getUri(),$privilegeIds);
                }
            }
        }

        return $this->returnJson(array(
        	'success' => true
        ));
        
    }


    /**
     * Check if the array to save contains a user that has all privileges
     * 
     * @param array $usersPrivileges
     * @return bool
     */
    protected function validatePermissions($usersPrivileges)
    {
        $pp = new PermissionProvider();
        foreach ($usersPrivileges as $user => $options) {
            if (array_diff($options, $pp->getSupportedRights()) === array_diff($pp->getSupportedRights(), $options)) {
                return true;
            }
        }
        return false;
    }

}
