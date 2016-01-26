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
 *               2013      (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
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
     * returns the relative path from the file/directory 'from'
     * to the file/directory 'to'
     *
     * @access public
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @param string from
     * @param string to
     * @return string
     */
    static public function getRelPath($from, $to)
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
    static public function remove($path)
    {
        $returnValue = (bool) false;
        
        if (is_file($path)) {
            $returnValue = unlink($path);
        } elseif (is_dir($path)) {
            /*
             * //It seems to raise problemes on windows, depending on the php version the resource handler is not freed with DirectoryIterator preventing from further deletions // $iterator = new DirectoryIterator($path); foreach ($iterator as $fileinfo) { if (!$fileinfo->isDot()) { } }
             */
            $handle = opendir($path);
            if ($handle !== false) {
                while (false !== ($entry = readdir($handle))) {
                    if ($entry != "." && $entry != "..") {
                        self::remove($path . DIRECTORY_SEPARATOR . $entry);
                    }
                }
                closedir($handle);
            } else {
                throw new common_exception_Error('"' . $path . '" cannot be opened for removal');
            }
            
            $returnValue = rmdir($path);
        } else {
            throw new common_exception_Error('"' . $path . '" cannot be removed since it\'s neither a file nor directory');
        }
        
        return (bool) $returnValue;
    }

    /**
     * Empty a directory without deleting it
     * 
     * @param string $path
     * @param boolean $ignoreSystemFiles 
     * @return boolean
     */
    static public function emptyDirectory($path, $ignoreSystemFiles = false)
    {
        $success = true;
        $handle = opendir($path);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                if ($entry[0] === '.' && $ignoreSystemFiles === true) {
                    continue;
                }
                
                $success = self::remove($path . DIRECTORY_SEPARATOR . $entry) ? $success : false;
            }
        }
        closedir($handle);
        return $success;
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
    static public function copy($source, $destination, $recursive = true, $ignoreSystemFiles = true)
    {
        if(!is_readable($source)){
            return false;
        }

        $returnValue = (bool) false;

        
        // Check for System File
        $basename = basename($source);
        if ($basename[0] === '.' && $ignoreSystemFiles === true) {
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
                if ($entry === '.' || $entry === '..') {
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
     * Scan directory located at $path depending on given $options array.
     * 
     * Options are the following:
     * 
     * * 'recursive' -> boolean
     * * 'only' -> boolean ($FILE or $DIR)
     * * 'absolute' -> boolean (returns absolute path or file name)
     *
     * @access public
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @param string path
     * @param array options
     * @return array An array of paths.
     */
    static public function scandir($path, $options = array())
    {
        $returnValue = array();
        
        $recursive = isset($options['recursive']) ? $options['recursive'] : false;
        $only = isset($options['only']) ? $options['only'] : null;
        $absolute = isset($options['absolute']) ? $options['absolute'] : false;
        
        if (is_dir($path)) {
            $iterator = new DirectoryIterator($path);
            foreach ($iterator as $fileinfo) {
                $fileName = $fileinfo->getFilename();
                if ($absolute === true) {
                    $fileName = $fileinfo->getPathname();
                }
                
                if (! $fileinfo->isDot()) {
                    if (! is_null($only)) {
                        if ($only == self::$DIR && $fileinfo->isDir()) {
                            array_push($returnValue, $fileName);
                        } else {
                            if ($only == self::$FILE && $fileinfo->isFile()) {
                                array_push($returnValue, $fileName);
                            }
                        }
                    } else {
                        array_push($returnValue, $fileName);
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
    
    /**
     * Convert url to path
     * @param string $path
     * @return string
     */
    static public function urlToPath($path)
    {
        $path = parse_url($path);
        return $path === null ? null : str_replace('/', DIRECTORY_SEPARATOR, $path['path']);
    }


    /**
     * An alternative way to resolve the real path of a path. The resulting
     * path might point to something non-existant on the file system contrary to
     * PHP's realpath function.
     * 
     * @param string $path The original path.
     * @return string The resolved path.
     */
    static public function truePath($path) 
    {
        // From Magento Mass Import utils (MIT)
        // http://sourceforge.net/p/magmi/git/ci/master/tree/magmi-0.8/inc/magmi_utils.php
        
        // whether $path is unix or not
        $unipath = strlen( $path ) == 0 || $path{0} != '/';
        // attempts to detect if path is relative in which case, add cwd
        if ( strpos( $path, ':' ) === false && $unipath ){
            $path = getcwd() . DIRECTORY_SEPARATOR . $path;
        }
        // resolve path parts (single dot, double dot and double delimiters)
        $path      = str_replace( array( '/', '\\' ), DIRECTORY_SEPARATOR, $path );
        $parts     = array_filter( explode( DIRECTORY_SEPARATOR, $path ), 'strlen' );
        $absolutes = array();
        foreach ( $parts as $part ) {
            if ( '.' == $part ) {
                continue;
            }
            if ( '..' == $part ) {
                array_pop( $absolutes );
            } else {
                $absolutes[ ] = $part;
            }
        }
        $path = implode( DIRECTORY_SEPARATOR, $absolutes );
        // resolve any symlinks
        if ( file_exists( $path ) && linkinfo( $path ) > 0 ) {
            $path = readlink( $path );
        }
        // put initial separator that could have been lost
        $path = !$unipath ? '/' . $path : $path;
        return $path;
    }
    
    /**
     * Whether or not a given directory located at $path
     * contains one or more files with extension $types.
     * 
     * If $path is not readable or not a directory, false is returned.
     * 
     * @param string $path
     * @param string|array $types Types to look for with no dot. e.g. 'php', 'js', ...
     * @param boolean $recursive Whether or not scan the directory recursively.
     * @return boolean
     */
    static public function containsFileType($path, $types = array(), $recursive = true)
    {
        if (!is_array($types)) {
            $types = array($types);
        }
        
        if (!is_dir($path)) {
            return false;
        }
        
        foreach (self::scandir($path, array('absolute' => true, 'recursive' => $recursive)) as $item) {
            if (is_file($item)) {
                $pathParts = pathinfo($item);
                
                // if .inc.php, returns 'php' as extension, so no worries with composed types
                // if you are looking for some php code.
                if (isset($pathParts['extension']) && in_array($pathParts['extension'], $types)) {
                    return true;
                }
            }
        }
        
        return false;
    }
}