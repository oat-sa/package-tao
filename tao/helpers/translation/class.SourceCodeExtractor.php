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
 *               2013-2014 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

/**
 * A TranslationExtractor that focuses on the extraction of Translation Units
 * source code. It searches for calls to the __() function. The generated
 * units will get the first parameter of the __() function as their source.
 *
 * @access public
 * @author Jerome Bogaerts
 * @package tao
 * @since 2.2
 
 * @version 1.0
 */
class tao_helpers_translation_SourceCodeExtractor extends tao_helpers_translation_TranslationExtractor
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute filesTypes
     *
     * @var array
     */
    private $filesTypes = array();

    /**
     *
     * @var array
     */
    private $bannedFileType = array( '.min.js' );
    // --- OPERATIONS ---

    /**
     * Short description of method extract
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     */
    public function extract()
    {
        
        $this->setTranslationUnits(array());
        foreach ($this->getPaths() as $dir) {
        	// Directories should come with a trailing slash.
        	$d = strrev($dir);
        	if ($d[0] !== '/') {
        		$dir = $dir . '/';	
        	}
        	
        	$this->recursiveSearch($dir);
        }
        
    }

    /**
     * Short description of method recursiveSearch
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @param  string $directory
     */
    private function recursiveSearch($directory)
    {
        
		if (is_dir($directory)) {
	    	// We get the list of files and directories.
	    	if (($files = scandir($directory)) !== false) {
	    		
	    		foreach($files as $fd) {
	
	   		 		if (!preg_match("/^\./", $fd) &&  $fd != "ext") {
	   		 			// If it is a directory ...
	   		 			if (is_dir($directory . $fd. "/")) {
	   		 				$this->recursiveSearch($directory . $fd . "/");
		    			// If it is a file ...
	    				} else {
	    					// Retrieve from the file ...
	    					$this->getTranslationsInFile($directory . $fd);
	    				}
	   		 		}
	    			
	    		}
	    	}   	
		}
		
        
    }

    /**
     * Creates a SourceCodeExtractor for a given set of paths. Only file
     * that matches an entry in the $fileTypes array will be processed.
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array $paths
     * @param  array $fileTypes
     * @return mixed
     */
    public function __construct($paths, array $fileTypes)
    {
        parent::__construct($paths);
        $this->setFileTypes($fileTypes);
    }

    /**
     * Gets an array of file extensions that will be processed. It acts as a
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return array
     */
    public function getFileTypes()
    {
        return $this->filesTypes;
    }

    /**
     * Sets an array that contains the extensions of files that have to be
     * during the invokation of the SourceCodeExtractor::extract method.
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array $fileTypes
     */
    public function setFileTypes(array $fileTypes)
    {
        $this->filesTypes = $fileTypes;
    }

    /**
     * Short description of method getTranslationsInFile
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @param  string $filePath
     */
    private function getTranslationsInFile($filePath)
    {
        
	 	// File extension ?
        $extOk = in_array(\Jig\Utils\FsUtils::getFileExtension($filePath), $this->getFileTypes());

        if ($extOk){
            foreach ($this->getBannedFileType() as $bannedExt){
                $extOk &= substr_compare($filePath, $bannedExt, strlen($filePath)-strlen($bannedExt), strlen($bannedExt)) !== 0;
            }
        }

        if ($extOk) {
            
		 	// We read the file.
		 	$lines = file($filePath);
		 	foreach ($lines as $line) {
                $strings = $this->getTranslationPhrases( $line );
                //preg_match_all("/__(\(| )+['\"](.*?)['\"](\))?/u", $line, $string);
                
                //lookup for __('to translate') or __ 'to translate'
            //    preg_match_all("/__(\(| )+(('(.*?)')|(\"(.*?)\"))(\))?/u", $line, $string);
            
                foreach($strings as $s) {
                    $tu = new tao_helpers_translation_TranslationUnit();
                    $tu->setSource(tao_helpers_translation_POUtils::sanitize($s));
                    $tus = $this->getTranslationUnits();
                    $found = false;
                    
                    // We must add the string as a new TranslationUnit only
                    // if a similiar source does not exist.
                    foreach ($tus as $t) {
                        if ($tu->getSource() == $t->getSource()) {
                            $found = true;
                            break;
                        }
                    }
                    
                    if (!$found) {
                        $tus[] = $tu;
                        $this->setTranslationUnits($tus);
                    }
                }
		 	}
		}
        
    }

    /**
     * @return array
     */
    public function getBannedFileType()
    {
        return $this->bannedFileType;
    }

    /**
     * @param array $bannedFileType
     */
    public function setBannedFileType( $bannedFileType )
    {
        $this->bannedFileType = $bannedFileType;
    }

    /**
     * @param $line
     *
     * @return array
     */
    protected function getTranslationPhrases( $line )
    {
        $strings = array();
        $patternMatch1 = array();
        $patternMatch2 = array();

        preg_match_all( "/__\\(([\\\"'])(?:(?=(\\\\?))\\2.)*?\\1/u", $line, $patternMatch1); //for php and JS helper function
        preg_match_all( "/\{\{__ ['\"](.*?)['\"]\}\}/u", $line, $patternMatch2 ); //used for parsing templates

        if ( ! empty( $patternMatch1[0] )) {
            $strings = array_reduce(
                $patternMatch1[0],
                function ( $m, $str ) use ( $patternMatch1 ) {
                    $found = preg_match(
                        "/([\"'])(?:(?=(\\\\?))\\2.)*?\\1/u",
                        $str,
                        $matches
                    ); //matches first passed argument only
                    $m[]   = $found ? trim( $matches[0], '"\'' ) : $patternMatch1[1];

                    return $m;
                },
                array()
            );
        }
        if ( ! empty( $patternMatch2[1] )) {
            $strings = array_merge( $strings, $patternMatch2[1] );

            return $strings;
        }

        return $strings;
    }

}