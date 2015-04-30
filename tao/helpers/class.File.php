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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Utility class that focuses on files.
 *
 * @author Lionel Lecaque, <lionel@taotesting.com>
 * @package tao
 
 */
class tao_helpers_File
    extends helpers_File
{

    /**
     * Check if the path in parameter can be securly used into the application.
     * (check the cross directory injection, the null byte injection, etc.)
     * Use it when the path may be build from a user variable
     *
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @param  string path The path to check.
     * @param  boolean traversalSafe (optional, default is false) Check if the path is traversal safe.
     * @return boolean States if the path is secure or not.
     */
    public static function securityCheck($path, $traversalSafe = false)
    {
   		$returnValue = true;

        //security check: detect directory traversal (deny the ../)
		if($traversalSafe){
	   		if(preg_match("/\.\.\//", $path)){
				$returnValue = false;
				common_Logger::w('directory traversal detected in ' . $path);
			}
		}

		//security check:  detect the null byte poison by finding the null char injection
		if($returnValue){
			for($i = 0; $i < strlen($path); $i++){
				if(ord($path[$i]) === 0){
					$returnValue = false;
					common_Logger::w('null char injection detected in ' . $path);
					break;
				}
			}
		}

        return (bool) $returnValue;
    }

    /**
     * Use this method to cleanly concat components of a path. It will remove extra slashes/backslashes.
     *
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @param  array paths The path components to concatenate.
     * @return string The concatenated path.
     */
    public static function concat($paths)
    {
        $returnValue = (string) '';

        foreach ($paths as $path){
        	if (!preg_match("/\/$/", $returnValue) && !preg_match("/^\//", $path) && !empty($returnValue)){
        		$returnValue .= '/';
        	}
        	$returnValue .= $path;
        }
        $returnValue = str_replace('//', '/', $returnValue);

        return (string) $returnValue;
    }

    /**
     * Remove a file. If the recursive parameter is set to true, the target file
     * can be a directory that contains data.
     *
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @param  string path The path to the file you want to remove.
     * @param  boolean recursive (optional, default is false) Remove file content recursively (only if the path points to a directory).
     * @return boolean Return true if the file is correctly removed, false otherwise.
     */
    public static function remove($path, $recursive = false)
    {
        $returnValue = (bool) false;

		if ($recursive) {
			$returnValue = helpers_File::remove($path);
		} elseif (is_file($path)) {
        	$returnValue = @unlink($path);
        }
        // else fail silently

        return (bool) $returnValue;
    }

    /**
     * Move file from source to destination.
     *
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @param  string source A path to the source file.
     * @param  string destination A path to the destination file.
     * @return boolean Returns true if the file was successfully moved, false otherwise.
     */
    public static function move($source, $destination)
    {
        $returnValue = (bool) false;

        if(is_dir($source)){
			if(!file_exists($destination)){
				mkdir($destination, 0777, true);
			}
			$error = false;
			foreach(scandir($source) as $file){
				if($file != '.' && $file != '..'){
					if(is_dir($source.'/'.$file)){
						if(!self::move($source.'/'.$file, $destination.'/'.$file, true)){
							$error = true;
						}
					}
					else{
						if(!self::copy($source.'/'.$file, $destination.'/'.$file, true)){
							$error = true;
						}
					}
				}
			}
			if(!$error){
				$returnValue = true;
			}
			self::remove($source, true);
		}
		else{
	        if(file_exists($source) && file_exists($destination)){
	        	$returnValue = rename($source, $destination);
	        }
	        else{
	        	if(self::copy($source, $destination, true)){
	        		$returnValue = self::remove($source);
	        	}
	        }
		}

        return (bool) $returnValue;
    }

    /**
     * Retrieve mime-types that are recognized by the TAO platform.
     *
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @return array An associative array of mime-types where keys are the extension related to the mime-type. Values of the array are mime-types.
     */
    protected static function getMimeTypes()
    {
        $returnValue = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'text/xml',
            'rdf' => 'text/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',
            'csv' => 'text/csv',
            'rtx' => 'text/richtext',
            'rtf' => 'text/rtf',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'oga' => 'audio/ogg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            'mp4' => 'video/mp4',//(H.264 + AAC) for ie8, etc.
            'webm' => 'video/webm',//(VP8 + Vorbis) for ie9, ff, chrome, android, opera
            'ogv' => 'video/ogg',//ff, chrome, opera

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
                        
            // fonts
            'woff' => 'application/x-font-woff',
            'eot'  => 'application/vnd.ms-fontobject',
            'ttf'  => 'application/x-font-ttf'
        );

        return (array) $returnValue;
    }

    /**
     * Retrieve file extensions usually associated to a given mime-type.
     *
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @param  string mimeType A mime-type which is recognized by the platform.
     * @return string The extension usually associated to the mime-type. If it could not be retrieved, an empty string is returned.
     */
    public static function getExtention($mimeType)
    {
        $returnValue = (string) '';

        $mime_types = self::getMimeTypes();

        foreach($mime_types as $key => $value){
        	if($value == trim($mimeType)){
        		$returnValue = $key;
        		break;
        	}
        }

        return (string) $returnValue;
    }

    /**
     * Get the mime-type of the file in parameter.
     * different methods are used regarding the configuration of the server.
     *
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @param  string path
     * @param  boolean ext If set to true, the extension of the file will be used to retrieve the mime-type. If now extension can be found, 'text/plain' is returned by the method.
     * @return string The associated mime-type.
     */
    public static function getMimeType($path, $ext = false)
    {
        $mime_types = self::getMimeTypes();
        
        if (false == $ext){
        	$ext = pathinfo($path, PATHINFO_EXTENSION);
        	
        	if (array_key_exists($ext, $mime_types)) {
        		$mimetype =  $mime_types[$ext];
        	} 
        	else {
        		$mimetype = '';
        	}
        	
        	if (!in_array($ext, array('css'))) {
        		if  (file_exists($path)) {
        			if (function_exists('finfo_open')) {
        				$finfo = finfo_open(FILEINFO_MIME);
        				$mimetype = finfo_file($finfo, $path);
        				finfo_close($finfo);
        			}
        			else if (function_exists('mime_content_type')) {
        				$mimetype = mime_content_type($path);
        			}
        			if (!empty($mimetype)) {
        				if (preg_match("/; charset/", $mimetype)) {
        					$mimetypeInfos = explode(';', $mimetype);
        					$mimetype = $mimetypeInfos[0];
        				}
        			}
        		}
        	}
        }
        else{
        	// find out the mime-type from the extension of the file.
        	$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        	if (array_key_exists($ext, $mime_types)){
        		$mimetype = $mime_types[$ext];
        	}
        }
		
        // If no mime-type found ...
        if (empty($mimetype)) {
        	$mimetype =  'application/octet-stream';
        }

		return (string) $mimetype;
    }

    /**
     * creates a directory in the system's temp dir.
     *
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @return string The path to the created folder.
     */
    public static function createTempDir()
    {
        do {
			$folder = sys_get_temp_dir().DIRECTORY_SEPARATOR."tmp".mt_rand().DIRECTORY_SEPARATOR;
		} while (file_exists($folder));
		mkdir($folder);
		return $folder;
    }

    /**
     * deletes a directory and its content.
     *
     * @author Lionel Lecaque, <lionel@taotesting.com>
     * @param  string directory absolute path of the directory
     * @return boolean true if the directory and its content were deleted, false otherwise.
     */
    public static function delTree($directory)
    {

        $files = array_diff(scandir($directory), array('.','..'));
		foreach ($files as $file) {
			$abspath = $directory.DIRECTORY_SEPARATOR.$file;
			if (is_dir($abspath)) {
				self::delTree($abspath);
			} else {
				unlink($abspath);
			}
		}
		return rmdir($directory);

    }
    
    public static function isIdentical($path1, $path2) {
        return self::md5_dir($path1) == self::md5_dir($path2);
    }
    
    public static function md5_dir($path) {
        if (is_file($path)) {
            $md5 = md5_file($path);
        } elseif (is_dir($path)) {
            $filemd5s = array();
            // using scandir to get files in a fixed order
            $files = scandir($path);
            sort($files);
            foreach ($files as $basename) {
                if($basename != '.' && $basename != '..') {
                    //$fileInfo->getFilename()
                    $filemd5s[] = $basename.self::md5_dir(self::concat(array($path, $basename)));
                }
            }
            $md5 = md5(implode('', $filemd5s));
        } else {
            throw new common_Exception(__FUNCTION__.' called on non file or directory "'.$path.'"');
        }
        return $md5;
    }
    
    /**
     * Create a zip of a directory or file
     * 
     * @param string $src path to the files to zip
     * @throws common_Exception if unable to create the zip
     * @return string path to the zip file
     */
    public static function createZip($src) {
        $zipArchive = new \ZipArchive();
        $path = self::createTempDir().'file.zip';
        if ($zipArchive->open($path, \ZipArchive::CREATE)!==TRUE) {
            throw new common_Exception('Unable to create zipfile '.$path);
        }
        self::addFilesToZip($zipArchive, $src, DIRECTORY_SEPARATOR);
        $zipArchive->close();
        return $path;
    }
    
    /**
     * Add files or folders (and their content) to the Zip Archive that will contain all the files to the current export session.
     * For instance, if you want to copy the file 'taoItems/data/i123/item.xml' as 'myitem.xml' to your archive call addFile('path_to_item_location/item.xml', 'myitem.xml').
     * As a result, you will get a file entry in the final ZIP archive at '/i123/myitem.xml'.
     *
     * @param ZipArchive $zipArchive the archive to add to
     * @param string $src The path to the source file or folder to copy into the ZIP Archive.
     * @param string *dest The <u>relative</u> to the destination within the ZIP archive.
     * @return integer The amount of files that were transfered from TAO to the ZIP archive within the method call.
     */
    public static function addFilesToZip(ZipArchive $zipArchive, $src, $dest) {
        $returnValue = null;
    
        $done = 0;
    
        if (is_dir($src)) {
            // Go deeper in folder hierarchy !
            $src = rtrim($src, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            $dest = rtrim($dest, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;;
            // Recursively copy.
            $content = scandir($src);
             
            foreach ($content as $file) {
                // avoid . , .. , .svn etc ...
                if(!preg_match("/^\./", $file)) {
                    $done += self::addFilesToZip($zipArchive, $src.$file, $dest.$file);
                }
            }
        }
        else {
            // Simply copy the file. Beware of leading slashes
            if($zipArchive->addFile($src, ltrim($dest, DIRECTORY_SEPARATOR))){
                $done++;
            }
        }
    
        $returnValue = $done;
    
        return $returnValue;
    }
    
    /**
     * Gets the local path to a publicly available resource
     * no verification if the file should be accessible
     * 
     * @param string $url
     * @throws common_Exception
     * @return string
     */
    public static function getPathFromUrl($url) {
        if (substr($url, 0, strlen(ROOT_URL)) != ROOT_URL) {
            throw new common_Exception($url.' does not lie within the tao instalation path');
        }
        $subUrl = substr($url, strlen(ROOT_URL));
        $parts = array();
        foreach (explode('/', $subUrl) as $directory) {
            $parts[] = urldecode($directory);
        }
        $path = ROOT_PATH.implode(DIRECTORY_SEPARATOR, $parts);
        if (self::securityCheck($path)) {
            return $path;
        } else {
            throw new common_Exception($url.' is not secure');
        }
    }
    
    /**
     * Get a safe filename for a proposed filename.
     * 
     * If directory is specified it will return a filename which is
     * safe to not overwritte an existing file. This function is not injective.
     * 
     * @param string $fileName
     * @param string $directory
     */
    public static function getSafeFileName($fileName, $directory = null) {
        $lastDot = strrpos($fileName, '.');
        $file = $lastDot ? substr($fileName, 0, $lastDot) : $fileName;
        $ending = $lastDot ? substr($fileName, $lastDot+1) : '';
        $safeName = self::removeSpecChars($file);
        $safeEnding = empty($ending)
            ? ''
            : '.'.self::removeSpecChars($ending);
        
        if ($directory != null && file_exists($directory.$safeName.$safeEnding)) {
            $count = 1;
            while (file_exists($directory.$safeName.'_'.$count.$safeEnding)) {
                $count++;
            }
            $safeName = $safeName.'_'.$count;
        } 
        
        return $safeName.$safeEnding;
    }
    
    /**
     * Remove special characters for safe filenames
     * 
     * @author Dieter Raber
     * 
     * @param string $string
     * @param string $repl
     * @param string $lower
     */
    private static function removeSpecChars($string, $repl='-', $lower=true) {
        $spec_chars = array (
            'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'Ae', 'Å' => 'A','Æ' => 'A', 'Ç' => 'C',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I',
            'Ï' => 'I', 'Ð' => 'E', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O',
            'Ö' => 'Oe', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U','Û' => 'U', 'Ü' => 'Ue', 'Ý' => 'Y',
            'Þ' => 'T', 'ß' => 'ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'ae',
            'å' => 'a', 'æ' => 'ae', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i',  'ï' => 'i', 'ð' => 'e', 'ñ' => 'n', 'ò' => 'o',
            'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'oe', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u',
            'û' => 'u', 'ü' => 'ue', 'ý' => 'y', 'þ' => 't', 'ÿ' => 'y', ' ' => $repl, '?' => $repl,
            '\'' => $repl, '.' => $repl, '/' => $repl, '&' => $repl, ')' => $repl, '(' => $repl,
            '[' => $repl, ']' => $repl, '_' => $repl, ',' => $repl, ':' => $repl, '-' => $repl,
            '!' => $repl, '"' => $repl, '`' => $repl, '°' => $repl, '%' => $repl, ' ' => $repl,
            '  ' => $repl, '{' => $repl, '}' => $repl, '#' => $repl, '’' => $repl
        );
        $string = strtr($string, $spec_chars);
        $string = trim(preg_replace("~[^a-z0-9]+~i", $repl, $string), $repl);
        return $lower ? strtolower($string) : $string;
    }
    
    /**
     * Check if the directory is empty
     * 
     * @param string $directory
     * @return boolean
     */
    public static function isDirEmpty($directory){
        $path = self::concat(array($directory, '*'));
        return (count(glob($path, GLOB_NOSORT)) === 0 );
    }
}