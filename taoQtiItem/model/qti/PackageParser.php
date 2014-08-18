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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

namespace oat\taoQtiItem\model\qti;

use oat\taoQtiItem\model\qti\PackageParser;
use \tao_models_classes_Parser;
use \Exception;
use \tao_helpers_File;
use \ZipArchive;
use \common_exception_Error;

/**
 * Enables you to parse and validate a QTI Package.
 * The Package is formated as a zip archive containing the manifest and the
 * (item files and media files)
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package taoQTI
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_intgv2p0.html#section10003
 
 */
class PackageParser
    extends tao_models_classes_Parser
{
    /**
     * Short description of method validate
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string schema
     * @return boolean
     */
    public function validate($schema = '')
    {
        
        $forced = $this->valid;
        $this->valid = true;
        
        try{
        	switch($this->sourceType){
        		case self::SOURCE_FILE:
	        		//check file
			   		if(!file_exists($this->source)){
			    		throw new Exception("File {$this->source} not found.");
			    	}
			    	if(!is_readable($this->source)){
			    		throw new Exception("Unable to read file {$this->source}.");
			    	}
			   		if(!preg_match("/\.zip$/", basename($this->source))){
			    		throw new Exception("Wrong file extension in {$this->source}, zip extension is expected");
			    	}
			   		if(!tao_helpers_File::securityCheck($this->source)){
			    		throw new Exception("{$this->source} seems to contain some security issues");
			    	}
			    	break;
        		default:
	        		throw new Exception("Only regular files are allowed as package source");
	        		break;
        	}
        }
        catch(Exception $e){
        	if($forced){
        		throw $e;
        	}
        	else{
        		$this->addError($e);
        	}
        }   
             
        if($this->valid && !$forced){	//valida can be true if forceValidation has been called
        	
        	$this->valid = false;
        	
			try{
	    		$zip = new ZipArchive();
	    		//check the archive opening and the consistency
	    		$res = $zip->open($this->source, ZIPARCHIVE::CHECKCONS);
				if( $res !== true){
				   
				    switch($res) {
				        case ZipArchive::ER_NOZIP :
				             $msg = 'not a zip archive';
				             break;
				        case ZipArchive::ER_INCONS :
				            $msg ='consistency check failed';
				            break;
				        case ZipArchive::ER_CRC :
				            $msg = 'checksum failed';
				            break;
				        default:
				            $msg ='Bad Zip file';
				    }
					throw new Exception($msg);
				}
				else{
					//check if the manifest is there
					if($zip->locateName("imsmanifest.xml") === false){
						throw new Exception("A QTI package must contains a imsmanifest.xml file  at the root of the archive");
					}
					
					$this->valid = true;
				}
				$zip->close();
			}
			catch(Exception $e){
				$this->addError($e);
			}
        }
        
    	$returnValue = $this->valid;
        
        return (bool) $returnValue;
    }

    /**
     * Short description of method extract
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function extract()
    {
        
    	if(!is_file($this->source)){	//ultimate verification
        	throw new common_exception_Error("source ".$this->source." not a file");
        }
        
        $sourceFile = basename($this->source);
        $folder = tao_helpers_File::createTempDir();
		
		if(!is_dir($folder)){
        	mkdir($folder);
        }
        
	    $zip = new ZipArchive();
		if ($zip->open($this->source) === true) {
		    if($zip->extractTo($folder)){
		    	$returnValue = $folder;
		    }
		    $zip->close();
		}
        
        return (string) $returnValue;
    }

}