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
 * A Translation File Writer that produces compiled PHP files.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 * @subpackage helpers_translation
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
// section -64--88-56-1--3f1036:137c6806719:-8000:0000000000003B11-includes begin
// section -64--88-56-1--3f1036:137c6806719:-8000:0000000000003B11-includes end

/* user defined constants */
// section -64--88-56-1--3f1036:137c6806719:-8000:0000000000003B11-constants begin
// section -64--88-56-1--3f1036:137c6806719:-8000:0000000000003B11-constants end

/**
 * A Translation File Writer that produces compiled PHP files.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 * @subpackage helpers_translation
 */
class tao_helpers_translation_PHPFileWriter
    extends tao_helpers_translation_TranslationFileWriter
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Writes the TranslationFile as a PHP compiled file.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    public function write()
    {
        // section -64--88-56-1--3f1036:137c6806719:-8000:0000000000003B13 begin
        $tf = $this->getTranslationFile();
        $buffer = "<?php\n";
        foreach ($tf->getTranslationUnits() as $tu){
            // Prevent empty messages.
            if ($tu->getSource() != '' && $tu->getTarget() != ''){
                $escapes = array("\\", '$', '"', "\n", "\t", "\v", "\r", "\f");
                $replace = array("\\\\", '\\$', '\\"', "\\n", "\\t", "\\v", "\\r", "\\f");
                $source = str_replace($escapes, $replace, $tu->getSource());
                $target = str_replace($escapes, $replace, $tu->getTarget());
                $buffer .= '$GLOBALS[\'__l10n\']["' . $source . '"]="' . $target . '";' . "\n";
            }
        }
        $buffer .= "\n?>";
        
        file_put_contents($this->getFilePath(), $buffer);
        // section -64--88-56-1--3f1036:137c6806719:-8000:0000000000003B13 end
    }

} /* end of class tao_helpers_translation_PHPFileWriter */

?>