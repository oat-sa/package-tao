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
use oat\oatbox\service\ServiceManager;
use oat\oatbox\filesystem\FileSystemService;

/**
 * Factory to create new FileSystems
 *
 * @access public
 * @author Joel bout, <joel@taotesting.com>
 * @package generis
 
 */
class core_kernel_fileSystem_FileSystemFactory
{
    /**
     * creates a new FileSystem
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource type
     * @param  string url
     * @param  string login
     * @param  string password
     * @param  string path
     * @param  string label
     * @param  boolean enabled
     * @return core_kernel_versioning_Repository
     */
    public static function createFileSystem( core_kernel_classes_Resource $type, $url, $login, $password, $path, $label, $enabled = false)
    {
        $path = rtrim($path, "\\/") . DIRECTORY_SEPARATOR;
        if (!file_exists($path)) {
            if (! mkdir($path, 0700, true)) {
                throw new common_exception_Error("Could not create path '".$path."' for filesystem '".$label."'");
            }
        }
        
        $versioningRepositoryClass = new core_kernel_classes_Class(CLASS_GENERIS_VERSIONEDREPOSITORY);
        $resource = $versioningRepositoryClass->createInstanceWithProperties(array(
        	RDFS_LABEL										=> $label,
        	PROPERTY_GENERIS_VERSIONEDREPOSITORY_URL		=> $url,
        	PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH		=> $path,
        	PROPERTY_GENERIS_VERSIONEDREPOSITORY_TYPE		=> $type,
        	PROPERTY_GENERIS_VERSIONEDREPOSITORY_LOGIN		=> $login,
        	PROPERTY_GENERIS_VERSIONEDREPOSITORY_PASSWORD	=> $password,
        	PROPERTY_GENERIS_VERSIONEDREPOSITORY_ENABLED	=> ($enabled ? GENERIS_TRUE : GENERIS_FALSE)
        ));
        core_kernel_fileSystem_Cache::flushCache();
        
        $serviceManager = ServiceManager::getServiceManager();
        $fsm = $serviceManager->get(FileSystemService::SERVICE_ID);
        $fsm->registerLocalFileSystem($resource->getUri(), $path);
        $serviceManager->register(FileSystemService::SERVICE_ID, $fsm);
        
        return new core_kernel_fileSystem_FileSystem($resource);
    }

}

?>