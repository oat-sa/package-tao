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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2013 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * Short description of class funcAcl_helpers_funcACL
 *
 * @access public
 * @author Jehan Bihin
 * @package tao
 
 */
class funcAcl_helpers_funcACL
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Array for the Actions/Modules with Roles
     *
     * @access public
     * @since 2.2
     * @var mixed
     */
    public static $rolesByActions = null;

    // --- OPERATIONS ---
	
    /**
     * Test if the Module and Action of this module is accessible by the current
     * (session), via roles
     *
     * @access public
     * @author Jehan Bihin
     * @param  string extension
     * @param  string module
     * @param  string action
     * @return boolean
     * @since 2.2
     */
    public static function hasAccess($action, $module, $extension)
    {
        $returnValue = (bool) false;

        
		if (empty($extension) || empty($module) || empty($action)) {
			$context = Context::getInstance();
			if (empty($extension)) {
				$extension = $context->getExtensionName();
			}
			if (empty($module)) {
				$module	= $context->getModuleName();
			}
			if (empty($action)) {
				$action	= $context->getActionName();
			}
		}
		
		//Get the Roles of the current User
		$roles = core_kernel_classes_Session::singleton()->getUserRoles();
		
		//Find the Module and, if necessary, the Action
		$accessService = funcAcl_models_classes_AccessService::singleton();
		$extensionUri = $accessService->makeEMAUri($extension);
		$moduleUri = $accessService->makeEMAUri($extension, $module);
		$actionUri = $accessService->makeEMAUri($extension, $module, $action);
		$moduleResource = new core_kernel_classes_Resource($moduleUri);
		
		
		$extAccess = funcAcl_helpers_Cache::retrieveExtensions();
		//Test if we have a role giving access to the extension.
		foreach ($roles as $r){
			if (isset($extAccess[$extensionUri]) && in_array($r, $extAccess[$extensionUri])){
				$returnValue = true;
				break;
			}
		}
		
		//Get the access list (reversed)
		$moduleAccess = funcAcl_helpers_funcACL::getReversedAccess($moduleResource);
		
		//Test if we have a role giving access to the module.
		foreach ($roles as $r){
			if (in_array($r, $moduleAccess['module'])){
				$returnValue = true;
				break;
			}
		}
		
		//Maybe an access for the action?
		foreach ($roles as $r){
			if (isset($moduleAccess['actions'][$actionUri])){
				$actionRoles = $moduleAccess['actions'][$actionUri];
				if (in_array($r, $actionRoles)){
					$returnValue = true;
					break;
				}
			}
		}
		
		if (false === $returnValue) {
			common_Logger::i('Access denied to '.$extension.'::'.$module.'::'.$action.' for user \''
			                 .core_kernel_classes_Session::singleton()->getUserUri().'\'');
		}
        

        return (bool) $returnValue;
    }

    /**
     * get the array of Actions/Modules with associted Roles
     *
     * @access public
     * @author Jehan Bihin
     * @return array
     * @since 2.2
     */
    public static function getRolesByActions()
    {
        $returnValue = array();

        
		if (is_null(self::$rolesByActions)) {
			try {
				self::$rolesByActions = common_cache_FileCache::singleton()->get('RolesByActions');
			}
			catch (common_cache_NotFoundException $e) {
				common_Logger::i('read roles by action failed, recalculating');
				self::$rolesByActions = self::buildRolesByActions();
			}
		}
		$returnValue = self::$rolesByActions;
        

        return (array) $returnValue;
    }

    /**
     * Create the array of Actions/Modules and give the associated Roles
     *
     * @access public
     * @author Jehan Bihin
     * @return mixed
     * @since 2.2
     */
    public static function buildRolesByActions()
    {
        
		$reverse_access = array();
		// alternate:

		self::$rolesByActions = null;
		$roles = new core_kernel_classes_Class(CLASS_ROLE);

		foreach ($roles->getInstances(true) as $role) {
			$extensionAccess = $role->getPropertyValues(new core_kernel_classes_Property(PROPERTY_ACL_GRANTACCESS));
			$moduleAccess = $role->getPropertyValues(new core_kernel_classes_Property(PROPERTY_ACL_MODULE_GRANTACCESS));
			$actionAccess = $role->getPropertyValues(new core_kernel_classes_Property(PROPERTY_ACL_ACTION_GRANTACCESS));
			foreach ($extensionAccess as $extensionURI) {
				if (!isset($reverse_access[$extensionURI])) {
					$reverse_access[$extensionURI] =  array('modules' => array(), 'roles' => array());
				}
				$reverse_access[$extensionURI]['roles'][] = $role->getUri();
			}
			foreach ($moduleAccess as $moduleURI) {
				$moduleRessource = new core_kernel_classes_Resource($moduleURI);
				$arr = $moduleRessource->getPropertiesValues(array(
					new core_kernel_classes_Property(PROPERTY_ACL_MODULE_ID),
					new core_kernel_classes_Property(PROPERTY_ACL_MODULE_EXTENSION)
				));
				if (!isset($arr[PROPERTY_ACL_MODULE_ID]) || !isset($arr[PROPERTY_ACL_MODULE_EXTENSION])) {
					common_Logger::e('Module '.$moduleURI.' not found for role '.$role->getLabel());
					continue;
				}
				$ext = (string)current($arr[PROPERTY_ACL_MODULE_EXTENSION]);
				$mod = (string)current($arr[PROPERTY_ACL_MODULE_ID]);
				if (!isset($reverse_access[$ext])) {
					$reverse_access[$ext] = array('modules' => array(), 'roles' => array());
				}
				if (!isset($reverse_access[$ext]['modules'][$mod])) {
					$reverse_access[$ext]['modules'][$mod] = array('actions' => array(), 'roles' => array());
				}
				$reverse_access[$ext]['modules'][$mod]['roles'][] = $role->getUri();
			}
			foreach ($actionAccess as $actionURI) {
				$actionRessource = new core_kernel_classes_Resource($actionURI);
				$arr = $actionRessource->getPropertiesValues(array(
					new core_kernel_classes_Property(PROPERTY_ACL_ACTION_ID),
					new core_kernel_classes_Property(PROPERTY_ACL_ACTION_MEMBEROF)
				));
				if (!isset($arr[PROPERTY_ACL_ACTION_ID]) || !isset($arr[PROPERTY_ACL_ACTION_MEMBEROF])) {
					common_Logger::e('Action '.$actionURI.' not found or incomplete');
					continue;
				}
				$act = (string)current($arr[PROPERTY_ACL_ACTION_ID]);
				// @todo cache module id/extension
				$moduleRessource = current($arr[PROPERTY_ACL_ACTION_MEMBEROF]);
				$arr = $moduleRessource->getPropertiesValues(array(
					new core_kernel_classes_Property(PROPERTY_ACL_MODULE_ID),
					new core_kernel_classes_Property(PROPERTY_ACL_MODULE_EXTENSION)
				));
				$ext = (string)current($arr[PROPERTY_ACL_MODULE_EXTENSION]);
				$mod = (string)current($arr[PROPERTY_ACL_MODULE_ID]);
				if (!isset($reverse_access[$ext])) {
					$reverse_access[$ext] = array('modules' => array(), 'roles' => array());
				}
				if (!isset($reverse_access[$ext]['modules'][$mod])) {
					$reverse_access[$ext]['modules'][$mod] = array('actions' => array(), 'roles' => array());
				}
				if (!isset($reverse_access[$ext]['modules'][$mod][$act])) {
					$reverse_access[$ext][$mod]['actions'][$act] = array();
				}
				$reverse_access[$ext][$mod]['actions'][$act][] = $role->getUri();
			}
		}

		common_cache_FileCache::singleton()->put($reverse_access, 'RolesByActions');
		return $reverse_access;
        
    }

    /**
     * Clear Cache
     *
     * @access public
     * @author Jehan Bihin
     * @return mixed
     * @since 2.2
     */
    public static function removeRolesByActions()
    {
        
			common_cache_FileCache::singleton()->remove('RolesByActions');
			self::$rolesByActions = null;
        
    }

    /**
     * Returns the roles that grant access to a specific action
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource action
     * @return array
     */
    public static function getRolesByAction( core_kernel_classes_Resource $action)
    {
        $returnValue = array();

        
	    // @todo: don't use uri
        $uri = explode('#', $action->getUri());
		$uri = explode('_', $uri[1], 4);
		$ext = $uri[1];
		$mod = $uri[2];
		$act = $uri[3];
		$cache = self::getRolesByActions();
		if (isset($cache[$ext]['modules'][$mod]['actions'][$act])) {
			foreach ($cache[$ext]['modules'][$mod]['actions'][$act] as $role) {
				$returnValue[] = new core_kernel_classes_Resource($role);
			}
		}
        

        return (array) $returnValue;
    }

    /**
     * Returns the roles that grant access to a specific module
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource module
     * @return array
     */
    public static function getRolesByModule( core_kernel_classes_Resource $module)
    {
        $returnValue = array();

        
        // @todo: don't use uri
        $uri = explode('#', $module->getUri());
		$uri = explode('_', $uri[1]);
		$ext = $uri[1];
		$mod = $uri[2];
		$cache = self::getRolesByActions();
		if (isset($cache[$ext]['modules'][$mod]['roles'])) {
			foreach ($cache[$ext]['modules'][$mod]['roles'] as $role) {
				$returnValue[] = new core_kernel_classes_Resource($role);
			}
		}
        

        return (array) $returnValue;
    }

    /**
     * Get a pre-compiled access manifest for ACL checkup at the Module level.
     * returned table has the following structure:
     *
     * + array['module'] -> an array of Generis Role URIs that have a global
     * to the module
     * + array['actions'] -> an associative array where keys are the Action URIs
     * values are arrays of Generis Role URIs that have an access to the action.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource module The Module (as a Generis Resource) you want to get the ACLs.
     * @return array
     */
    public static function getReversedAccess( core_kernel_classes_Resource $module)
    {
        $returnValue = array();

        
        try{
        	$returnValue = funcAcl_helpers_Cache::retrieveModule($module);
        }
        catch (common_cache_Exception $e) {
        	// The cached version could not be retrieved.
        	// We ask the cache to build it and we serve it.
        	funcAcl_helpers_Cache::cacheModule($module);
        	$returnValue = funcAcl_helpers_Cache::retrieveModule($module);
        }
        

        return (array) $returnValue;
    }

}