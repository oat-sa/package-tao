<?php
use oat\oatbox\service\ServiceManager;
use oat\tao\helpers\ControllerHelper;
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
 * 
 */

/**
 * Short description of class funcAcl_helpers_Cache
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package tao
 
 */
class funcAcl_helpers_Cache
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * prefix for the extension cache
     *
     * @var string
     */
    const CACHE_PREFIX_EXTENSION = 'acl_e_';
    
    const SERIAL_PREFIX_MODULE = 'acl';

    /**
     * Serial to store extensions access to
     * 
     * @var string
     */
    const SERIAL_EXTENSIONS = 'acl_extensions';
    // --- OPERATIONS ---

    /**
     * Returns the funcACL Cache implementation
     * @return common_cache_Cache
     */
    private static function getCacheImplementation(){
    	return ServiceManager::getServiceManager()->get('generis/cache');
    }

    /**
     * force recache of a controller
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource module
     * @return void
     */
    public static function cacheModule( core_kernel_classes_Resource $module)
    {
        $controllerClassName = funcAcl_helpers_Map::getControllerFromUri($module->getUri());
        self::flushControllerAccess($controllerClassName);
        self::getControllerAccess($controllerClassName);
    }
    
    /**
     * Return the cached description of the roles
     * that have access to this controller
     * 
     * @param string $controllerClassName
     * @return array
     */
    public static function getControllerAccess($controllerClassName)
    {
        try{
            $returnValue = self::getCacheImplementation()->get(self::SERIAL_PREFIX_MODULE.$controllerClassName);
        } catch (common_cache_Exception $e) {
            
            $extId = funcAcl_helpers_Map::getExtensionFromController($controllerClassName);
            $extension = funcAcl_helpers_Map::getUriForExtension($extId);
            $module = funcAcl_helpers_Map::getUriForController($controllerClassName);
            
            $roleClass = new core_kernel_classes_Class(CLASS_ROLE);
            $accessProperty = new core_kernel_classes_Property(funcAcl_models_classes_AccessService::PROPERTY_ACL_GRANTACCESS);

            $returnValue = array('module' => array(), 'actions' => array());
            
            // roles by extensions
            $roles = $roleClass->searchInstances(array(
                    $accessProperty->getUri() => $extension
                ), array(
                    'recursive' => true, 'like' => false
            ));
            foreach ($roles as $grantedRole) {
                $returnValue['module'][] = $grantedRole->getUri();
            }
            
            // roles by controller
            $filters = array(
                $accessProperty->getUri() => $module
            );
            $options = array('recursive' => true, 'like' => false);
            
            foreach ($roleClass->searchInstances($filters, $options) as $grantedRole){
                $returnValue['module'][] = $grantedRole->getUri();
            }
            
            // roles by action
            foreach (ControllerHelper::getActions($controllerClassName) as $actionName) {
                $actionUri = funcAcl_helpers_Map::getUriForAction($controllerClassName, $actionName);
                $rolesForAction = $roleClass->searchInstances(array(
                    $accessProperty->getUri() => $actionUri
                ), array('recursive' => true, 'like' => false));
                if (!empty($rolesForAction)) {
                    $actionName = funcAcl_helpers_Map::getActionFromUri($actionUri);
                    $returnValue['actions'][$actionName] = array();
                    foreach ($rolesForAction as $roleResource) {
                        $returnValue['actions'][$actionName][] = $roleResource->getUri();
                    }
                }
            }
            
            self::getCacheImplementation()->put($returnValue, self::SERIAL_PREFIX_MODULE.$controllerClassName);
        }
        return $returnValue;
    }
    
    public static function getExtensionAccess($extId)
    {
        try{
            $returnValue = self::getCacheImplementation()->get(self::CACHE_PREFIX_EXTENSION.$extId);
        } catch (common_cache_Exception $e) {
            $returnValue = array();
            $aclExtUri = funcAcl_models_classes_AccessService::singleton()->makeEMAUri($extId);
            $roleClass = new core_kernel_classes_Class(CLASS_ROLE);
            $roles = $roleClass->searchInstances(array(
                funcAcl_models_classes_AccessService::PROPERTY_ACL_GRANTACCESS => $aclExtUri
            ), array(
                'recursive' => true,
                'like' => false
            ));
            foreach ($roles as $grantedRole){
                $returnValue[] = $grantedRole->getUri();
            }
            self::getCacheImplementation()->put($returnValue, self::CACHE_PREFIX_EXTENSION.$extId);
        }
        return $returnValue;
    }

    public static function flushExtensionAccess($extensionId)
    {
    	self::getCacheImplementation()->remove(self::CACHE_PREFIX_EXTENSION.$extensionId);
        foreach (ControllerHelper::getControllers($extensionId) as $controllerClassName) {
            self::flushControllerAccess($controllerClassName);
        }
    }
        
    public static function flushControllerAccess($controllerClassName)
    {
        self::getCacheImplementation()->remove(self::SERIAL_PREFIX_MODULE.$controllerClassName);
    }
    
    /**
     * Short description of method buildModuleSerial
     *
     * @access private
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource module
     * @return string
     */
    private static function buildModuleSerial( core_kernel_classes_Resource $module)
    {
        $returnValue = (string) '';

        
        $uri = explode('#', $module->getUri());
		list($type, $extId) = explode('_', $uri[1]);
        $returnValue = self::SERIAL_PREFIX_MODULE . $extId . urlencode($module->getUri());
        

        return (string) $returnValue;
    }

    /**
     * Short description of method removeModule
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource module
     * @return void
     */
    public static function removeModule( core_kernel_classes_Resource $module)
    {
        
        self::getCacheImplementation()->remove(self::buildModuleSerial($module));
        
    }

}