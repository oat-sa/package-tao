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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * Utility class focusing on files stored by the filemanager.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package filemanager
 * @subpackage helpers
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1-85--1a7dc942:13b229d9435:-8000:0000000000003C3C-includes begin
// section 10-13-1-85--1a7dc942:13b229d9435:-8000:0000000000003C3C-includes end

/* user defined constants */
// section 10-13-1-85--1a7dc942:13b229d9435:-8000:0000000000003C3C-constants begin
// section 10-13-1-85--1a7dc942:13b229d9435:-8000:0000000000003C3C-constants end

/**
 * Utility class focusing on files stored by the filemanager.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package filemanager
 * @subpackage helpers
 */
class filemanager_helpers_FileUtils
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method deleteFolder
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string dir The full path to the directory.
     * @param  boolean recursive Recursive deletion or not. Default value is false.
     * @return boolean
     */
    public static function deleteFolder($dir, $recursive = false)
    {
        $returnValue = (bool) false;

        // section 10-13-1-85--1a7dc942:13b229d9435:-8000:0000000000003C3E begin
        $files = glob($dir. "*", GLOB_MARK);
	    foreach($files as $file){
	        if(substr($file, -1) == '/'){
	        	if($recursive){
					 $this->deleteFolder($file, true);
				}
			}
	        else{
	        	 unlink($file);
	        }
	    }
	    if (is_dir($dir)){
	    	rmdir($dir);
		}
		return !file_exists($dir);
        // section 10-13-1-85--1a7dc942:13b229d9435:-8000:0000000000003C3E end

        return (bool) $returnValue;
    }

    /**
     * Check if the path is a folder of refPath.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string refPath
     * @param  string path
     * @return boolean
     */
    public static function isFolder($refPath, $path)
    {
        $returnValue = (bool) false;

        // section 10-13-1-85--1a7dc942:13b229d9435:-8000:0000000000003C50 begin
        if(!empty($refPath) && !empty($path) && is_string($refPath)){
			do{
				if($refPath == $path || $refPath == basename($path)){
					return true;
				}
				$refPath = dirname($refPath);
			
			} while(($refPath != '/'  && $refPath != '\\' )&& $refPath != '' && $refPath != '.');
		}
		return false;
        // section 10-13-1-85--1a7dc942:13b229d9435:-8000:0000000000003C50 end

        return (bool) $returnValue;
    }

    /**
     * Clean up a file name by replacing non alphanumeric characters by a joker.
     * default joker character is '_' (underscore).
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string fileName
     * @param  string joker Non alphanumeric characters placeholder.
     * @return string
     */
    public static function cleanName($fileName, $joker = '_')
    {
        $returnValue = (string) '';

        // section 10-13-1-85--1a7dc942:13b229d9435:-8000:0000000000003C55 begin
		$i = 0;
		while ($i < strlen($fileName)){
			if(preg_match("/^[a-zA-Z0-9.-]{1}$/", $fileName[$i])){
				$returnValue .= $fileName[$i];
			}
			else if ($fileName[$i] != $joker){
				$returnValue .= $joker;
			}
			$i++;
		}
		return $returnValue;
        // section 10-13-1-85--1a7dc942:13b229d9435:-8000:0000000000003C55 end

        return (string) $returnValue;
    }

    /**
     * Makes a path with an array of partial paths.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array files
     * @return string
     */
    public static function cleanConcat($files)
    {
        $returnValue = (string) '';

        // section 10-13-1-85--1a7dc942:13b229d9435:-8000:0000000000003C5F begin
        $path = '';
		foreach($files as $file){
			if(!preg_match("/^\//", $file) && !preg_match("/\/$/", $path) && !empty($path)){
				$path .= '/';
			}
			$path .= $file;
		}
		return $path;
        // section 10-13-1-85--1a7dc942:13b229d9435:-8000:0000000000003C5F end

        return (string) $returnValue;
    }

    /**
     * Get the mime-type of a file. If the mime-type cannot be found,
     * is returned.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string fileName The path to the file.
     * @return string
     */
    public static function getMimeType($fileName)
    {
        $returnValue = (string) '';

        // section 10-13-1-85--1a7dc942:13b229d9435:-8000:0000000000003C64 begin
    if (empty($fileName)) {
			common_Logger::e('getMimeType called without filename');
		}

        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

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
			'mp4' => 'video/mp4',
			'wmv' => 'video/x-ms-wmv',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
        	'ogv' => 'video/ogg',
        	'oga' => 'audio/ogg',

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
        		
        	// srt
            'srt' => 'application/x-subrip'
        );

        
		
		if (function_exists('finfo_open')) {			
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $fileName);
            finfo_close($finfo);
            
        }
        else if (function_exists('mime_content_type')) {
        	
			$mimetype = mime_content_type($fileName);
		}
		if(!empty($mimetype)){
			if(preg_match("/; charset/", $mimetype)){
				$mimetypeInfos = explode(';', $mimetype);
				$mimetype = $mimetypeInfos[0];
			}
			if(!preg_match("/^application/",$mimetype)){
				return $mimetype;
			}
		}
		
		$explosion = explode('.',$fileName);
		$ext = strtolower(array_pop($explosion));
		
		if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        else {
            return (empty($mimetype)) ? 'application/octet-stream' : $mimetype;
        }
        // section 10-13-1-85--1a7dc942:13b229d9435:-8000:0000000000003C64 end

        return (string) $returnValue;
    }

    /**
     * Obtains the path of the folder that contains the file described by
     * If no path to the file can be determined, null is returned.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string filePath A path to a file.
     * @param  string dataPath Set a specific media data path instead of letting the implementation of the method use the value in the BASE_DATA constant.
     * @param  boolean strict Default value is true. If set to false, the file does not necessary need to exist.
     * @return string
     */
    public static function getFolderPath($filePath, $dataPath = null, $strict = true)
    {
        $returnValue = (string) '';

        // section 10-13-1-85--34176ece:13b2750645e:-8000:0000000000003C63 begin
        // Get the BASE_DATA constant properly.
        if (is_readable($filePath) || false == $strict){
        	$base = '';
        	if (null == $dataPath){
        		$man = common_ext_ExtensionsManager::singleton();
        		$ext = $man->getExtensionById('filemanager');
        		$base = $ext->getConstant('BASE_DATA');
        	}
        	else{
        		$base = $dataPath;
        	}
        	
        	$fileName = pathinfo($filePath, PATHINFO_BASENAME);
        	
        	// Remove $base and $fileName from $filePath
        	$filePath = str_replace(array($base, $fileName), '', $filePath);
        	$filePath = preg_replace(array('!^/*!', '!/*$!'), '', $filePath);
        	// Add a leading/trailing slash to the final result.
        	// We consider the containing folder in $base as the root folder.
        	$filePath = '/' . $filePath . '/';
        	
        	if ($filePath == '//'){
        		$returnValue = '/';
        	}
        	else{
        		$returnValue = $filePath;
        	}
        }
        else{
        	return null;
        }
        // section 10-13-1-85--34176ece:13b2750645e:-8000:0000000000003C63 end

        return (string) $returnValue;
    }

} /* end of class filemanager_helpers_FileUtils */

?>