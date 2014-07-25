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
 * Short description of class tao_helpers_funcACL_funcACL
 *
 * @access public
 * @author Jehan Bihin
 * @package tao
 * @subpackage helpers_funcACL
 */
class tao_helpers_funcACL_funcACL
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
     * Test if the current user has access to the following url
     * if this url is part of the current tao install
     * 
     * @param string $url
     * @return boolean
     */
	public static function hasAccessURL($url) {
		$access = false;
		if (substr($url, 0, strlen(ROOT_URL)) == ROOT_URL) {
			$resolver = new Resolver($url);
			$access = self::hasAccess($resolver->getExtensionFromURL(), $resolver->getModule(), $resolver->getAction());
		} else {
			common_Logger::w('\''.$url.'\' not part of this Tao');
		}
		return $access;
	}
	
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
    public static function hasAccess($extension, $module, $action)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--b28769d:135f11069cc:-8000:000000000000385B begin
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
		$roles[] = new core_kernel_classes_Resource(INSTANCE_ROLE_BASEACCESS);
		
		//Find the Module and, if necessary, the Action
		$accessService = tao_models_classes_funcACL_AccessService::singleton();
		$extensionUri = $accessService->makeEMAUri($extension);
		$moduleUri = $accessService->makeEMAUri($extension, $module);
		$actionUri = $accessService->makeEMAUri($extension, $module, $action);
		$moduleResource = new core_kernel_classes_Resource($moduleUri);
		
		
		$extAccess = tao_helpers_funcACL_Cache::retrieveExtensions();
		//Test if we have a role giving access to the extension.
		foreach ($roles as $r){
			if (isset($extAccess[$extensionUri]) && in_array($r->getUri(), $extAccess[$extensionUri])){
				$returnValue = true;
				break;
			}
		}
		
		//Get the access list (reversed)
		$moduleAccess = tao_helpers_funcACL_funcACL::getReversedAccess($moduleResource);
		
		//Test if we have a role giving access to the module.
		foreach ($roles as $r){
			if (in_array($r->getUri(), $moduleAccess['module'])){
				$returnValue = true;
				break;
			}
		}
		
		//Maybe an access for the action?
		foreach ($roles as $r){
			if (isset($moduleAccess['actions'][$actionUri])){
				$actionRoles = $moduleAccess['actions'][$actionUri];
				if (in_array($r->getUri(), $actionRoles)){
					$returnValue = true;
					break;
				}
			}
		}
		
		if (false === $returnValue) {
			common_Logger::i('Access denied to '.$extension.'::'.$module.'::'.$action.' for user \''
			                 .core_kernel_classes_Session::singleton()->getUserUri().'\'');
		}
        // section 127-0-1-1--b28769d:135f11069cc:-8000:000000000000385B end

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

        // section 127-0-1-1--299b9343:13616996224:-8000:000000000000389B begin
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
        // section 127-0-1-1--299b9343:13616996224:-8000:000000000000389B end

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
        // section 127-0-1-1--299b9343:13616996224:-8000:000000000000389D begin
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
        // section 127-0-1-1--299b9343:13616996224:-8000:000000000000389D end
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
        // section 127-0-1-1-5382e8cb:136ab734ff6:-8000:0000000000003908 begin
			common_cache_FileCache::singleton()->remove('RolesByActions');
			self::$rolesByActions = null;
        // section 127-0-1-1-5382e8cb:136ab734ff6:-8000:0000000000003908 end
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

        // section 127-0-1-1--1ccb663f:138d70cdc8b:-8000:0000000000003B65 begin
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
        // section 127-0-1-1--1ccb663f:138d70cdc8b:-8000:0000000000003B65 end

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

        // section 127-0-1-1--1ccb663f:138d70cdc8b:-8000:0000000000003B68 begin
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
        // section 127-0-1-1--1ccb663f:138d70cdc8b:-8000:0000000000003B68 end

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

        // section 127-0-1-1--37da3151:13cfce0f4f3:-8000:0000000000003C93 begin
        try{
        	$returnValue = tao_helpers_funcACL_Cache::retrieveModule($module);
        }
        catch (common_cache_Exception $e) {
        	// The cached version could not be retrieved.
        	// We ask the cache to build it and we serve it.
        	tao_helpers_funcACL_Cache::cacheModule($module);
        	$returnValue = tao_helpers_funcACL_Cache::retrieveModule($module);
        }
        // section 127-0-1-1--37da3151:13cfce0f4f3:-8000:0000000000003C93 end

        return (array) $returnValue;
    }

}