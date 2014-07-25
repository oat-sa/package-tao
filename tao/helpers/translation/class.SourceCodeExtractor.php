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
?>
<?php

error_reporting(E_ALL);

/**
 * A TranslationExtractor that focuses on the extraction of Translation Units
 * source code. It searches for calls to the __() function. The generated
 * units will get the first parameter of the __() function as their source.
 *
 * @author Jerome Bogaerts
 * @package tao
 * @since 2.2
 * @subpackage helpers_translation
 * @version 1.0
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * A TranslationExtractor instance extracts TranslationUnits from a given source
 * as an Item, source code, ...
 *
 * @author Jerome Bogaerts
 * @since 2.2
 * @version 1.0
 */
require_once('tao/helpers/translation/class.TranslationExtractor.php');

/* user defined includes */
// section -64--88-1-7-3ec47102:13332ada7cb:-8000:0000000000003201-includes begin
// section -64--88-1-7-3ec47102:13332ada7cb:-8000:0000000000003201-includes end

/* user defined constants */
// section -64--88-1-7-3ec47102:13332ada7cb:-8000:0000000000003201-constants begin
// section -64--88-1-7-3ec47102:13332ada7cb:-8000:0000000000003201-constants end

/**
 * A TranslationExtractor that focuses on the extraction of Translation Units
 * source code. It searches for calls to the __() function. The generated
 * units will get the first parameter of the __() function as their source.
 *
 * @access public
 * @author Jerome Bogaerts
 * @package tao
 * @since 2.2
 * @subpackage helpers_translation
 * @version 1.0
 */
class tao_helpers_translation_SourceCodeExtractor
    extends tao_helpers_translation_TranslationExtractor
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute filesTypes
     *
     * @access private
     * @var array
     */
    private $filesTypes = array();

    // --- OPERATIONS ---

    /**
     * Short description of method extract
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    public function extract()
    {
        // section -64--88-1-7-3ec47102:13332ada7cb:-8000:0000000000003209 begin
        $this->setTranslationUnits(array());
        foreach ($this->getPaths() as $dir) {
        	// Directories should come with a trailing slash.
        	$d = strrev($dir);
        	if ($d[0] !== '/') {
        		$dir = $dir . '/';	
        	}
        	
        	$this->recursiveSearch($dir);
        }
        // section -64--88-1-7-3ec47102:13332ada7cb:-8000:0000000000003209 end
    }

    /**
     * Short description of method recursiveSearch
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @param  string directory
     */
    private function recursiveSearch($directory)
    {
        // section -64--88-1-7-23b3662f:133330291f8:-8000:000000000000322E begin
        $pExtension = $this->getFileTypes();
	
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
		
        // section -64--88-1-7-23b3662f:133330291f8:-8000:000000000000322E end
    }

    /**
     * Creates a SourceCodeExtractor for a given set of paths. Only file
     * that matches an entry in the $fileTypes array will be processed.
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array paths
     * @param  array fileTypes
     * @return mixed
     */
    public function __construct($paths, $fileTypes)
    {
        // section -64--88-1-7-23b3662f:133330291f8:-8000:0000000000003234 begin
        parent::__construct($paths);
        $this->setFileTypes($fileTypes);
        // section -64--88-1-7-23b3662f:133330291f8:-8000:0000000000003234 end
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
        $returnValue = array();

        // section -64--88-1-7-23b3662f:133330291f8:-8000:000000000000323E begin
        $returnValue = $this->filesTypes;
        // section -64--88-1-7-23b3662f:133330291f8:-8000:000000000000323E end

        return (array) $returnValue;
    }

    /**
     * Sets an array that contains the extensions of files that have to be
     * during the invokation of the SourceCodeExtractor::extract method.
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array fileTypes
     * @return mixed
     */
    public function setFileTypes($fileTypes)
    {
        // section -64--88-1-7-23b3662f:133330291f8:-8000:0000000000003240 begin
        $this->filesTypes = $fileTypes;
        // section -64--88-1-7-23b3662f:133330291f8:-8000:0000000000003240 end
    }

    /**
     * Short description of method getTranslationsInFile
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @param  string filePath
     * @return mixed
     */
    private function getTranslationsInFile($filePath)
    {
        // section -64--88-1-7-576a6b36:1333bcb6e9d:-8000:000000000000323B begin
	 	// File extension ?
		$extOk = false;
		foreach ($this->getFileTypes() as $exp) {
			if (@preg_match("/\.${exp}$/", $filePath)) {
				$extOk = true;
				break;
			}
		}
		
		if ($extOk) {
		 	// We read the file.
		 	$lines = file($filePath);
		 	foreach ($lines as $line_num => $line) {
		 		$string	= array();
		 		preg_match_all("/__\(['\"](.*?)['\"]\)/u", $line, $string);
				
		 		if (!empty($string[1])) {
		 			foreach($string[1] as $s) {
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
		 					array_push($tus, $tu);
		 					$this->setTranslationUnits($tus);
		 				}
		 			}
		 		}
		 	}
		}
        // section -64--88-1-7-576a6b36:1333bcb6e9d:-8000:000000000000323B end
    }

} /* end of class tao_helpers_translation_SourceCodeExtractor */

?>