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
 * 
 */
use oat\oatbox\service\ServiceManager;

/**
 * A facade aiming at helping client code to put User data in the Cache memory.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package generis
 
 */
class core_kernel_users_Cache
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute SERIAL_PREFIX_INCLUDED_ROLES
     *
     * @access public
     * @var string
     */
    const SERIAL_PREFIX_INCLUDED_ROLES = 'roles-ir';

    // --- OPERATIONS ---

    /**
     * Retrieve roles included in a given Generis Role from the Cache memory.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     *
     * @param  core_kernel_classes_Resource $role A Generis Role Resource.
     *
     * @return array
     * @throws \oat\oatbox\service\ServiceNotFoundException
     * @throws common_Exception
     * @throws core_kernel_users_CacheException
     */
    public static function retrieveIncludedRoles( core_kernel_classes_Resource $role)
    {
        $returnValue = array();

        
        try{
        	$serial = self::buildIncludedRolesSerial($role);
        	$fromCache = ServiceManager::getServiceManager()->get('generis/cache')->get($serial); // array of URIs.

            foreach ($fromCache as $uri){
                $returnValue[$uri] = new core_kernel_classes_Resource($uri);
            }
        }
        catch (common_cache_Exception $e){
        	$roleUri = $role->getUri();
        	$msg = "Includes roles related to Role with URI '${roleUri}' is not in the Cache memory.";
        	throw new core_kernel_users_CacheException($msg);
        }
        

        return (array) $returnValue;
    }

    /**
     * Put roles included in a Generis Role in the Cache memory.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     *
     * @param  core_kernel_classes_Resource $role
     * @param  array $includedRoles
     *
     * @return bool
     * @throws \oat\oatbox\service\ServiceNotFoundException
     * @throws common_Exception
     * @throws core_kernel_users_CacheException
     */
    public static function cacheIncludedRoles( core_kernel_classes_Resource $role, $includedRoles)
    {
        // Make a simple array of URIs with the included roles.
        $toCache = array();
        foreach ($includedRoles as $resource){
        	$toCache[] = $resource->getUri();
        }
        
        $serial = self::buildIncludedRolesSerial($role);
        $cache = ServiceManager::getServiceManager()->get('generis/cache');
        try{
        	$cache->put($toCache, $serial);
        	$returnValue = true;
        }
        catch (common_Exception $e){
        	$roleUri = $role->getUri();
        	$msg = "An error occurred while writing included roles in the cache memory for Role '${roleUri}': ";
        	$msg.= $e->getMessage();
        	throw new core_kernel_users_CacheException($msg);
        }
        

        return (bool) $returnValue;
    }

    /**
     * Remove roles included in a Generis Role from the Cache memory.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  core_kernel_classes_Resource $role A Generis Role as a Resource.
     * @return boolean
     */
    public static function removeIncludedRoles( core_kernel_classes_Resource $role)
    {
        $serial = self::buildIncludedRolesSerial($role);
        ServiceManager::getServiceManager()->get('generis/cache')->remove($serial);;

        // -- note: the cache might exist even if it was successfully
        // removed due to race conditions.
        // $returnValue = (file_exists(GENERIS_CACHE_PATH . $serial)) ? false : true;
        return true;
    }

    /**
     * Returns true if the roles included in a given Generis Role are in the
     * memory.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  core_kernel_classes_Resource $role The Generis Role you want to check if its included roles are in the cache memory.
     * @return boolean
     */
    public static function areIncludedRolesInCache( core_kernel_classes_Resource $role)
    {

        $serial = self::buildIncludedRolesSerial($role);

        return ServiceManager::getServiceManager()->get('generis/cache')->has($serial);
    }

    /**
     * Build a serial aiming at identifying the includes roles of a given
     * Role in the Cache memory.
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  core_kernel_classes_Resource $role The role you want to create a serial for.
     * @return string
     */
    private static function buildIncludedRolesSerial(core_kernel_classes_Resource $role)
    {
        return self::SERIAL_PREFIX_INCLUDED_ROLES . urlencode($role->getUri());
    }

    /**
     * Removes all entries related to included roles from the Cache memory.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return void
     */
    public static function flush()
    {
        ServiceManager::getServiceManager()->get('generis/cache')->purge();
    }

}