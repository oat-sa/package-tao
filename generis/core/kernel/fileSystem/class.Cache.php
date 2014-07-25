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
 * FileSystem helper functions
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package helpers
 */
class core_kernel_fileSystem_Cache
{
	const SERIAL = 'fileSystemMap';
	
	private static $cache = null;
	
    private static function getFSInfo(core_kernel_versioning_Repository $fileSystem) {
        if (is_null(self::$cache)) {
            try {
                self::$cache = common_cache_FileCache::singleton()->get(self::SERIAL);
            } catch (common_cache_NotFoundException $e) {
                self::$cache = array();
            }
        }
        if (!isset(self::$cache[$fileSystem->getUri()])) {
            $props = $fileSystem->getPropertiesValues(array(
                PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH, PROPERTY_GENERIS_VERSIONEDREPOSITORY_TYPE
            ));
            if (count($props[PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH]) == 1 && count($props[PROPERTY_GENERIS_VERSIONEDREPOSITORY_TYPE]) == 1) {
                self::$cache[$fileSystem->getUri()] = array(
                    PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH => current($props[PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH]),
                    PROPERTY_GENERIS_VERSIONEDREPOSITORY_TYPE => current($props[PROPERTY_GENERIS_VERSIONEDREPOSITORY_TYPE])
                );
    			common_cache_FileCache::singleton()->put(self::$cache, self::SERIAL);
            } else {
                throw new common_exception_InconsistentData('Filesystem '.$fileSystem->getUri().' has '.
                    count($props[PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH]).' path entries and '.
                    count($props[PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH]).' type entries');
            }
        }
    	return self::$cache[$fileSystem->getUri()];
    }

    public static function getFileSystemPath(core_kernel_versioning_Repository $fileSystem) {
    	$info = self::getFSInfo($fileSystem);
    	return $info[PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH];
    }
    
    public static function getFileSystemType(core_kernel_versioning_Repository $fileSystem) {
    	$info = self::getFSInfo($fileSystem);
    	return $info[PROPERTY_GENERIS_VERSIONEDREPOSITORY_TYPE];
    }
    
    public static function flushCache() {
    	common_cache_FileCache::singleton()->remove(self::SERIAL);
    	self::$cache = array();
    }

}