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

/**
 * Factory to create new FileSystems
 *
 * @access public
 * @author Joel bout, <joel@taotesting.com>
 * @package core
 * @subpackage kernel_filesystem
 */
class core_kernel_fileSystem_FileSystemFactory
{
    const DENY_ACCESS_HTACCESS = "Deny from All";
    
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
        
        $filePath = $path . '.htaccess';
        if (false !== ($fp = @fopen($filePath, 'c')) && true === flock($fp, LOCK_EX)){
        
            // We first need to truncate.
            ftruncate($fp, 0);
        
            fwrite($fp, self::DENY_ACCESS_HTACCESS);
            @flock($fp, LOCK_UN);
            @fclose($fp);
        } else {
            throw new common_exception_Error('Could not secure newly created filesource \''.$resource->getUri().'\' for folder \''.$path.'\'');
        }
        return new core_kernel_fileSystem_FileSystem($resource);
    }

}

?>