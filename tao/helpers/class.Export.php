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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

/**
 * Utility class that focuses on export.
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package tao
 
 */
class tao_helpers_Export
{

    /**
     * get the path to save and retrieve the exported files regarding the current extension
     * @return string the path
     */
    public static function getExportPath(){
        $path = sys_get_temp_dir().DIRECTORY_SEPARATOR.'tao_export';
        if (!file_exists($path)) {
            mkdir($path);
        }
        return $path;
    }
    
    public static function getRelativPath($file){
        return ltrim(substr($file, strlen(self::getExportPath())), DIRECTORY_SEPARATOR);
    }
    
    public static function getExportFile($filename = null) {
        if (is_null($filename)) {
            $path = tempnam(self::getExportPath(), 'tao_export_');
        } else {
            $path = self::getExportPath().DIRECTORY_SEPARATOR.$filename;
        }
        return $path;
    }

    public static function outputFile($relPath, $filename = null) {
        
        $fullpath = self::getExportPath().DIRECTORY_SEPARATOR.$relPath;
        if(tao_helpers_File::securityCheck($fullpath, true) && file_exists($fullpath)){
            Context::getInstance()->getResponse()->setContentHeader(tao_helpers_File::getMimeType($fullpath));
            $fileName = empty($filename) ? basename($fullpath) : $filename;
            header('Content-Disposition: attachment; fileName="'.$fileName.'"');
            header("Content-Length: " . filesize($fullpath));
            
            //Clean all levels of output buffering
            while (ob_get_level() > 0) { 
                ob_end_clean();
            }
            
            flush();
            $fp = fopen($fullpath, "r");
            if ($fp !== false) {
                while (!feof($fp))
                {
                    echo fread($fp, 65536);
                    flush();
                }
                fclose($fp);
                @unlink($fullpath);
            } else {
                common_Logger::e('Unable to open File to export' . $fullpath);
            }
        }
        else{
            common_Logger::e('Could not find File to export: ' . $fullpath);
        }
    }
    
}