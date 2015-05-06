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
     * Short description of attribute SERIAL_PREFIX_MODULE
     *
     * @access public
     * @var string
     */
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
    	return common_cache_FileCache::singleton();
    }
    
    /**
     * Short description of method cacheExtension
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Extension extension
     * @return void
     */
    public static function cacheExtension( common_ext_Extension $extension)
    {
        
        foreach (funcAcl_helpers_Model::getModules($extension->getId()) as $module){
        	self::cacheModule($module);
        }
        
    }

    /**
     * Short description of method cacheModule
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource module
     * @return void
     */
    public static function cacheModule( core_kernel_classes_Resource $module)
    {
        
        $serial = self::buildModuleSerial($module);
        $roleClass = new core_kernel_classes_Class(CLASS_ROLE);
        $grantedModulesProperty = new core_kernel_classes_Property(PROPERTY_ACL_MODULE_GRANTACCESS);
        $grantedActionsProperty = new core_kernel_classes_Property(PROPERTY_ACL_ACTION_GRANTACCESS);
        $actionIdentifierProperty = new core_kernel_classes_Property(PROPERTY_ACL_ACTION_ID);
        $memberOfProperty = new core_kernel_classes_Property(PROPERTY_ACL_ACTION_MEMBEROF);
        
        $toCache = array('module' => array(), 'actions' => array());

        // retrive roles that grant that module.
        $filters = array($grantedModulesProperty->getUri() => $module->getUri());
        $options = array('recursive' => true, 'like' => false);
        
        foreach ($roleClass->searchInstances($filters, $options) as $grantedRole){
        	$toCache['module'][] = $grantedRole->getUri();
        }
        
        foreach ($roleClass->getInstances(true) as $role){
        	$actions = $role->getPropertyValues($grantedActionsProperty);
        	
        	foreach ($actions as $grantedAction){
	        	try {
					$grantedAction = new core_kernel_classes_Resource($grantedAction);
					$memberOf = $grantedAction->getUniquePropertyValue($memberOfProperty);
					
					if ($memberOf->getUri() == $module->getUri()){
						
						$grantedActionUri = $grantedAction->getUri();
						if (!isset($toCache['actions'][$grantedActionUri])){
							$toCache['actions'][$grantedActionUri] = array();	
						}
						
						$toCache['actions'][$grantedActionUri][] = $role->getUri();
					}
	        	}
	        	catch (Exception $e){
	        		$moduleLabel = $module->getLabel();
	        		$actionLabel = $grantedAction->getLabel();
	        		common_Logger::w("Action '${moduleLabel}/${actionLabel}' has no 'actionMemberOf' property value.");
	        	}
        	}
        }
        
        self::getCacheImplementation()->put($toCache, $serial);
        
    }

    /**
     * Short description of method retrieveModule
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource module
     * @return array
     */
    public static function retrieveModule( core_kernel_classes_Resource $module)
    {
        $returnValue = array();

        
        try{
        	$returnValue = self::getCacheImplementation()->get(self::buildModuleSerial($module));
        }
        catch (common_exception_FileSystemError $e){
        	$msg = "Module cache for ACL not found.";
        	throw new common_cache_Exception($msg);
        }
        

        return (array) $returnValue;
    }

    /**
     * 
     * Enter description here ...
     * @return array
     */
    public static function retrieveExtensions()
    {
        $returnValue = array();
        try{
        	$returnValue = self::getCacheImplementation()->get(self::SERIAL_EXTENSIONS);
        } catch (common_cache_NotFoundException $e){
        	// cache not found, building
        	$roleClass = new core_kernel_classes_Class(CLASS_ROLE);
        	$extensions = common_ext_ExtensionsManager::singleton()->getInstalledExtensions();
        	foreach ($extensions as $ext) {
	        	$aclExtUri = funcAcl_models_classes_AccessService::singleton()->makeEMAUri($ext->getId());
	        	$returnValue[$aclExtUri] = array();
		        $roles = $roleClass->searchInstances(array(
		        	PROPERTY_ACL_GRANTACCESS => $aclExtUri
		        ), array(
		        	'recursive' => true,
		        	'like' => false
		        ));
		        foreach ($roles as $grantedRole){
		        	$returnValue[$aclExtUri][] = $grantedRole->getUri();
		        }
        	}
        	self::getCacheImplementation()->put($returnValue, self::SERIAL_EXTENSIONS);
        }
        return (array) $returnValue;
    }

    /**
     * flushes the cached extension acl roles
     */
    public static function flushExtensionCache(){
    	self::getCacheImplementation()->remove(self::SERIAL_EXTENSIONS);
    }
    
    /**
     * Short description of method flush
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return void
     */
    public static function flush()
    {
        
        self::flushExtensionCache();
    	$cacheDir = GENERIS_CACHE_PATH;
        $matching = self::SERIAL_PREFIX_MODULE;
        if (@is_readable($cacheDir) && @is_dir($cacheDir)){
        	$files = scandir($cacheDir);
        	if ($files !== false){
        		foreach ($files as $f){
        			$canonicalPath = $cacheDir . $f;
        			if ($f[0] != '.' && @is_writable($canonicalPath) && preg_match("/^${matching}/", $f)){
        				unlink($canonicalPath);
        			}
        		}
        	}
        	else{
        		$msg = 'The ACL Cache cannot be scanned.';
        		throw new funcAcl_helpers_CacheException($msg);
        	}
        }
        else{
        	$msg = 'The ACL Cache is not readable or is not a directory.';
        	throw new funcAcl_helpers_CacheException($msg);
        }
        
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

} /* end of class funcAcl_helpers_Cache */

?>