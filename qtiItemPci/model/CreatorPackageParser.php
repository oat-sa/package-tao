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
 */

namespace oat\qtiItemPci\model;

use oat\taoQtiItem\model\qti\PackageParser;
use oat\taoQtiItem\helpers\QtiPackage;
use common_Exception;
use \ZipArchive;

/**
 * Parser of a QTI PCI Creator package
 *
 * @package qtiItemPci
 */
class CreatorPackageParser extends PackageParser
{

    /**
     * Validate the zip package
     *
     * @access public
     * @param  string schema
     * @return boolean
     */
    public function validate($schema = ''){

        $this->valid = false;

        try{

            if(QtiPackage::isValidZip($this->source)){

                $zip = new ZipArchive();
                $zip->open($this->source, ZIPARCHIVE::CHECKCONS);
                if($zip->locateName("pciCreator.json") === false){
                    throw new common_Exception("A PCI creator package must contains a pciCreator.json file at the root of the archive");
                }else if($zip->locateName("pciCreator.js") === false){
                    throw new common_Exception("A PCI creator package must contains a pciCreator.js file at the root of the archive");
                }else{
                    //check manifest format :
                    $manifest = $this->getManifest();
                    $this->valid = $this->validateManifest($manifest);
                }

                $zip->close();
            }
            
        }catch(common_Exception $e){
            $this->addError($e);
        }
    }
    
    /**
     * Validate the manifest pciCreator.json
     * 
     * @param array $manifest
     * @return boolean
     * @throws common_Exception
     */
    protected function validateManifest($manifest){

        $returnValue = true;

        $requiredEntries = array(
            'typeIdentifier' => 'identifier',
            'label' => 'string',
            'short' => 'string',
            'description' => 'string',
            'version' => 'string',
            'author' => 'string',
            'email' => 'string',
            'tags' => 'array',
            'icon' => 'file',
            'entryPoint' => 'file',
            'response' => 'array'
        );

        $zip = new ZipArchive();
        $zip->open($this->source, ZIPARCHIVE::CHECKCONS);

        foreach($requiredEntries as $entry => $type){
            //@todo : implement more generic data validation ?
            if(isset($manifest[$entry])){
                $value = $manifest[$entry];
                switch($type){
                    case 'identifier':
                    case 'string':
                        if(!is_string($value)){
                            $returnValue = false;
                            throw new common_Exception('invalid attribute format in the manifest pciCreator.json : "'.$entry.'" (expected a string)');
                        }
                        break;
                    case 'array':
                        if(!is_array($value)){
                            $returnValue = false;
                            throw new common_Exception('invalid attribute format in the manifest pciCreator.json : "'.$entry.'" (expected an array)');
                        }
                        break;
                    case 'file':
                        if($zip->locateName(preg_replace('/^\.\//', '', $value)) === false){
                            $returnValue = false;
                            throw new common_Exception('cannot locate "'.$entry.'" file : "'.$value.'"');
                        }
                        break;
                }
            }else{
                throw new common_Exception('missing required attribute in the manifest pciCreator.json : "'.$entry.'"');
            }
        }

        $zip->close();

        return $returnValue;
    }
    
    /**
     * Get the manifest as an associative array from the source zip package
     * 
     * @return array
     */
    public function getManifest(){

        $str = '';
        $handle = fopen('zip://'.$this->source.'#pciCreator.json', 'r');
        while(!feof($handle)){
            $str .= fread($handle, 8192);
        }
        fclose($handle);

        $returnValue = json_decode($str, true);

        return $returnValue;
    }

}