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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *               
 * 
 */

namespace oat\taoQtiItem\helpers;

use \ZipArchive;
use \tao_helpers_File;
use \common_Exception;

/**
 * @access public
 * @package tao

 */
class QtiPackage
{

    public static function isValidZip($source){

        $returnValue = false;

        if(!file_exists($source)){
            throw new common_Exception("File {$source} not found.");
        }
        if(!is_readable($source)){
            throw new common_Exception("Unable to read file {$source}.");
        }
        if(!tao_helpers_File::securityCheck($source)){
            throw new common_Exception("{$source} seems to contain some security issues");
        }

        $zip = new ZipArchive();
        //check the archive opening and the consistency
        $res = $zip->open($source, ZIPARCHIVE::CHECKCONS);
        if($res !== true){

            switch($res){
                case ZipArchive::ER_NOZIP :
                    $msg = 'not a zip archive';
                    break;
                case ZipArchive::ER_INCONS :
                    $msg = 'consistency check failed';
                    break;
                case ZipArchive::ER_CRC :
                    $msg = 'checksum failed';
                    break;
                default:
                    $msg = 'Bad Zip file';
            }
            throw new common_Exception($msg);
            
        }else{

            $returnValue = true;
        }
        $zip->close();

        return $returnValue;
    }

    public static function isValidQtiZip($source){

        $returnValue = false;

        if(self::isValidZip($source)){

            $zip = new ZipArchive();
            $zip->open($source, ZIPARCHIVE::CHECKCONS);
            if($zip->locateName("imsmanifest.xml") === false){
                throw new common_Exception("A QTI package must contains a imsmanifest.xml file  at the root of the archive");
            }else{
                $returnValue = true;
            }

            $zip->close();
        }

        return $returnValue;
    }

}