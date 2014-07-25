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
 * An implementation of TranslationFileReader aiming at reading PO files.
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
 * A Reading class for TranslationFiles. Must be implemented by a concrete class
 * a given Translation Format such as XLIFF, PO, ... The read method must be
 * by subclasses.
 *
 * @author Jerome Bogaerts
 * @since 2.2
 * @version 1.0
 */
require_once('tao/helpers/translation/class.TranslationFileReader.php');

/* user defined includes */
// section 10-13-1-85-72d0ca97:1331b62f595:-8000:00000000000034C8-includes begin
// section 10-13-1-85-72d0ca97:1331b62f595:-8000:00000000000034C8-includes end

/* user defined constants */
// section 10-13-1-85-72d0ca97:1331b62f595:-8000:00000000000034C8-constants begin
// section 10-13-1-85-72d0ca97:1331b62f595:-8000:00000000000034C8-constants end

/**
 * An implementation of TranslationFileReader aiming at reading PO files.
 *
 * @access public
 * @author Jerome Bogaerts
 * @package tao
 * @since 2.2
 * @subpackage helpers_translation
 * @version 1.0
 */
class tao_helpers_translation_POFileReader
    extends tao_helpers_translation_TranslationFileReader
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method read
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    public function read()
    {
        // section 10-13-1-85-72d0ca97:1331b62f595:-8000:00000000000034CA begin
        $file = $this->getFilePath();
        if (!file_exists($file)) {
			throw new tao_helpers_translation_TranslationException("The translation file '${file}' does not exist.");
		}
    
        // Create the translation file.
        $tf = new tao_helpers_translation_POFile();
		
		$fc = implode('',file($file));
		
		$matched = preg_match_all('/((?:#[\.:,\|]{0,1}\s+(?:.*?)\\n)*)(msgid\s+(?:"(?:[^"]|\\\\")*?"\s*)+)\s+' .
								  '(msgstr\s+(?:"(?:[^"]|\\\\")*?(?<!\\\)"\s*)+)/',
								  $fc, $matches);

		if (!$matched) {
			$res = array();
		}
		else {
			$res = array();
			
			for ($i = 0; $i < $matched; $i++) {
                
                $annotations = $matches[1][$i];
				$msgid = preg_replace('/\s*msgid\s*"(.*)"\s*/s','\\1',$matches[2][$i]);
				$msgstr = preg_replace('/\s*msgstr\s*"(.*)"\s*/s','\\1',$matches[3][$i]);
                
                // Do not include meta data as a translation unit..
                if ($msgid !== ''){
                    
                    // Sanitze the strings.
                    $msgid = tao_helpers_translation_POUtils::sanitize($msgid);
    				$msgstr = tao_helpers_translation_POUtils::sanitize($msgstr);
                    $tu = new tao_helpers_translation_POTranslationUnit();
                    
                    // Set up source & target.
                    $tu->setSource($msgid);
    				if ($msgstr !== '') {
    					$tu->setTarget($msgstr);
    				}
                    
                    // Deal with annotations
                    $annotations = tao_helpers_translation_POUtils::unserializeAnnotations($annotations);  
                    foreach ($annotations as $name => $value){
                        $tu->addAnnotation($name, $value);
                    }  
                    
                    $tf->addTranslationUnit($tu);
                }
			}
		}
		
		$this->setTranslationFile($tf);
        // section 10-13-1-85-72d0ca97:1331b62f595:-8000:00000000000034CA end
    }

} /* end of class tao_helpers_translation_POFileReader */

?>