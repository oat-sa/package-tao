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
 * An implementation of TranslationFileWriter aiming at writing PO files.
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
 * A Writing class for TranslationFiles. Must be implemented by a concrete class
 * a given Translation Format such as XLIFF, PO, ... The write method must be
 * by subclasses.
 *
 * @author Jerome Bogaerts
 * @since 2.2
 * @version 1.0
 */
require_once('tao/helpers/translation/class.TranslationFileWriter.php');

/* user defined includes */
// section 10-13-1-85-72d0ca97:1331b62f595:-8000:00000000000034E1-includes begin
// section 10-13-1-85-72d0ca97:1331b62f595:-8000:00000000000034E1-includes end

/* user defined constants */
// section 10-13-1-85-72d0ca97:1331b62f595:-8000:00000000000034E1-constants begin
// section 10-13-1-85-72d0ca97:1331b62f595:-8000:00000000000034E1-constants end

/**
 * An implementation of TranslationFileWriter aiming at writing PO files.
 *
 * @access public
 * @author Jerome Bogaerts
 * @package tao
 * @since 2.2
 * @subpackage helpers_translation
 * @version 1.0
 */
class tao_helpers_translation_POFileWriter
    extends tao_helpers_translation_TranslationFileWriter
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method write
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    public function write()
    {
        // section 10-13-1-85-72d0ca97:1331b62f595:-8000:00000000000034E3 begin
        $buffer = '';
        $file = $this->getTranslationFile();
        
        // Add PO Headers.
        $buffer .= 'msgid ""' . "\n";
        $buffer .= 'msgstr ""' . "\n";
        
        // If the TranslationFile is a specific POFile instance, we add PO Headers
        // to the output.
        if (get_class($this->getTranslationFile()) == 'tao_helpers_translation_POFile') {
        	foreach ($file->getHeaders() as $name => $value) {
        		$buffer .= '"' . $name . ': ' . $value . '\n"' . "\n";
        	}
        }
        
        // Write all Translation Units.
        $buffer .= "\n";
		foreach($this->getTranslationFile()->getTranslationUnits() as $tu) {
            
			$s = tao_helpers_translation_POUtils::sanitize($tu->getSource(), true);
			$t = tao_helpers_translation_POUtils::sanitize($tu->getTarget(), true);
            $a = tao_helpers_translation_POUtils::serializeAnnotations($tu->getAnnotations());
            
            if (!empty($a)){
                $buffer .= "${a}\n";
            }
            
			$buffer .= "msgid \"{$s}\"\n";
			$buffer .= "msgstr \"{$t}\"\n";
			$buffer .= "\n";
		}
		
		return file_put_contents($this->getFilePath(), $buffer);
        // section 10-13-1-85-72d0ca97:1331b62f595:-8000:00000000000034E3 end
    }

} /* end of class tao_helpers_translation_POFileWriter */

?>