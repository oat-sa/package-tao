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
 *               2013      (update and modification) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * Generis Object Oriented API - helpers/class.File.php
 *
 * Utilities on files
 *
 * @access public
 * @author Lionel Lecaque, <lionel@taotesting.com>
 * @package generis
 * @subpackage helpers
 */
class helpers_File
{
    // --- ATTRIBUTES ---
    
    /**
     * Directory Mode
     *
     * @access public
     * @var int
     */
    public static $DIR = 2;

    /**
     * File Mode
     *
     * @access public
     * @var int
     */
    public static $FILE = 1;
    
    // --- OPERATIONS ---
    
    /**
     * Check if Resource exists
     *
     * @access public
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @param
     *            string path
     * @return boolean
     */
    public static function resourceExists($path = "")
    {
        $returnValue = (bool) false;
        
        $instances = self::searchResourcesFromPath($path);
        if (! empty($instances)) {
            $returnValue = true;
        }
        
        return (bool) $returnValue;
    }

    /**
     * Retrieve Resource
     *
     * @access public
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @param
     *            string path
     * @return core_kernel_file_File
     */
    public static function getResource($path = "")
    {
        $returnValue = null;
        
        $instances = self::searchResourcesFromPath($path);
        if (! empty($instances)) {
            $returnValue = current($instances);
        }
        
        return $returnValue;
    }

    /**
     * Search Resources from specific path
     *
     * @access public
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @param string path
     * @return array
     */
    public static function searchResourcesFromPath($path = "")
    {
        $returnValue = array();
		
        $fileSource = null;
		foreach (helpers_FileSource::getFileSources() as $fs) {
            $fsPath = $fs->getPath();
        	if (substr($path, 0, strlen($fsPath)) == $fsPath) {
        		$fileSource = $fs;
        		break;
        	}
		}
		
        if (!is_null($fileSource)) {
            $fsPath = core_kernel_fileSystem_Cache::getFileSystemPath($fileSource);
            $relPath = substr($path, strlen($fsPath));
            $lastDirSep = strrpos($relPath, DIRECTORY_SEPARATOR);
            if ($lastDirSep === false) {
                $filePath = '';
                $fileName = $relPath;
            } else {
                $filePath = trim(substr($relPath, 0, $lastDirSep + 1), DIRECTORY_SEPARATOR);
                $fileName = substr($relPath, $lastDirSep + 1);
            }
            
            $clazz = new core_kernel_classes_Class(CLASS_GENERIS_FILE);
            $propertyFilters = array(
                PROPERTY_FILE_FILEPATH => $filePath,
                PROPERTY_FILE_FILENAME => $fileName,
                PROPERTY_FILE_FILESYSTEM => $fileSource
            );
            $resources = $clazz->searchInstances($propertyFilters, array(
                'recursive' => true,
                'like' => false
            ));
            foreach ($resources as $resource) {
                $returnValue[$resource->getUri()] = new core_kernel_versioning_File($resource);
            }
        }
        
        return (array) $returnValue;
    }

    /**
     * returns the relative path from the file/directory 'from'
     * to the file/directory 'to'
     *
     * @access public
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @param string from
     * @param string to
     * @return string
     */
    public static function getRelPath($from, $to)
    {
        $returnValue = (string) '';
        
        $from = is_dir($from) ? $from : dirname($from);
        $arrFrom = explode(DIRECTORY_SEPARATOR, rtrim($from, DIRECTORY_SEPARATOR));
        $arrTo = explode(DIRECTORY_SEPARATOR, rtrim($to, DIRECTORY_SEPARATOR));
        
        while (count($arrFrom) && count($arrTo) && ($arrFrom[0] == $arrTo[0])) {
            array_shift($arrFrom);
            array_shift($arrTo);
        }
        foreach ($arrFrom as $dir) {
            $returnValue .= '..' . DIRECTORY_SEPARATOR;
        }
        $returnValue .= implode(DIRECTORY_SEPARATOR, $arrTo);
        
        return (string) $returnValue;
    }

    /**
     * deletes a file or a directory recursively
     *
     * @access public
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @param string path
     * @return boolean
     */
    public static function remove($path)
    {
        $returnValue = (bool) false;
        
        if (is_file($path)) {
            $returnValue = unlink($path);
        } elseif (is_dir($path)) {
            /*
             * //It seems to raise problemes on windows, depending on the php version the resource handler is not freed with DirectoryIterator preventing from further deletions // $iterator = new DirectoryIterator($path); foreach ($iterator as $fileinfo) { if (!$fileinfo->isDot()) { } }
             */
            $handle = opendir($path);
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    self::remove($path . DIRECTORY_SEPARATOR . $entry);
                }
            }
            closedir($handle);
            
            $returnValue = rmdir($path);
        } else {
            throw new common_exception_Error('"' . $path . '" cannot be removed since it\'s neither a file nor directory');
        }
        
        return (bool) $returnValue;
    }

    public static function emptyDirectory($path)
    {
        $returnValue = (bool) false;
        $handle = opendir($path);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                self::remove($path . DIRECTORY_SEPARATOR . $entry);
            }
        }
        closedir($handle);
    }

    /**
     * Copy a file from source to destination, may be done recursively and may ignore system files
     *
     * @access public
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @param string source
     * @param string destination
     * @param boolean recursive
     * @param boolean ignoreSystemFiles
     * @return boolean
     */
    public static function copy($source, $destination, $recursive = true, $ignoreSystemFiles = true)
    {
        $returnValue = (bool) false;
        
        // Check for System File
        $basename = basename($source);
        if ($basename[0] == '.' && $ignoreSystemFiles == true) {
            return false;
        }
        
        // Check for symlinks
        if (is_link($source)) {
            return symlink(readlink($source), $destination);
        }
        
        // Simple copy for a file
        if (is_file($source)) {
            // get path info of destination.
            $destInfo = pathinfo($destination);
            if (isset($destInfo['dirname']) && ! is_dir($destInfo['dirname'])) {
                if (! mkdir($destInfo['dirname'], 0777, true)) {
                    return false;
                }
            }
            
            return copy($source, $destination);
        }
        
        // Make destination directory
        if ($recursive == true) {
            if (! is_dir($destination)) {
                // 0777 is default. See mkdir PHP Official documentation.
                mkdir($destination, 0777, true);
            }
            
            // Loop through the folder
            $dir = dir($source);
            while (false !== $entry = $dir->read()) {
                // Skip pointers
                if ($entry == '.' || $entry == '..') {
                    continue;
                }
                
                // Deep copy directories
                self::copy("${source}/${entry}", "${destination}/${entry}", $recursive, $ignoreSystemFiles);
            }
            
            // Clean up
            $dir->close();
            return true;
        } else {
            return false;
        }
        
        return (bool) $returnValue;
    }

    /**
     * Scan directory depending on option
     *
     * @access public
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @param string path
     * @param array options
     * @return array
     */
    public static function scandir($path, $options = array())
    {
        $returnValue = array();
        
        $recursive = isset($options['recursive']) ? $options['recursive'] : false;
        $only = isset($options['only']) ? $options['only'] : null;
        
        if (is_dir($path)) {
            $iterator = new DirectoryIterator($path);
            foreach ($iterator as $fileinfo) {
                if (! $fileinfo->isDot()) {
                    if (! is_null($only)) {
                        if ($only == self::$DIR && $fileinfo->isDir()) {
                            array_push($returnValue, $fileinfo->getFilename());
                        } else {
                            if ($only == self::$FILE && $fileinfo->isFile()) {
                                array_push($returnValue, $fileinfo->getFilename());
                            }
                        }
                    } else {
                        array_push($returnValue, $fileinfo->getFilename());
                    }
                    
                    if ($fileinfo->isDir() && $recursive) {
                        $returnValue = array_merge($returnValue, self::scandir(realpath($fileinfo->getPathname()), $options));
                    }
                }
            }
        } else {
            throw new common_Exception("An error occured : The function (" . __METHOD__ . ") of the class (" . __CLASS__ . ") is expecting a directory path as first parameter : " . $path);
        }
        
        return (array) $returnValue;
    }
}