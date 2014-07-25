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
 * A TranslationFile aiming at translating a TAO Component
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 * @subpackage helpers_translation
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * A translation file represents the translation of a file, software, item, ...
 * contains a list of Translation Units a source language and a target language.
 * File can be read and written by TranslationFileReader & TranslationFileWriter
 *
 * @author Jerome Bogaerts
 * @see tao_model_classes_TranslationUnit
tao_model_classes_TranslationFileReader
tao_model_classes_TranslationFileWriter
 * @since 2.2
 * @version 1.0
 */
require_once('tao/helpers/translation/class.TranslationFile.php');

/* user defined includes */
// section 10-13-1-85--345dcc7e:13543c6ca3a:-8000:0000000000003A74-includes begin
// section 10-13-1-85--345dcc7e:13543c6ca3a:-8000:0000000000003A74-includes end

/* user defined constants */
// section 10-13-1-85--345dcc7e:13543c6ca3a:-8000:0000000000003A74-constants begin
// section 10-13-1-85--345dcc7e:13543c6ca3a:-8000:0000000000003A74-constants end

/**
 * A TranslationFile aiming at translating a TAO Component
 *
 * @abstract
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 * @subpackage helpers_translation
 */
abstract class tao_helpers_translation_TaoTranslationFile
    extends tao_helpers_translation_TranslationFile
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Id of the extension the translations belongs to.
     *
     * @access public
     * @var Integer
     */
    public $extensionId = null;

    // --- OPERATIONS ---

    /**
     * Gets the extensionId of the extension the translations belong to.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getExtensionId()
    {
        $returnValue = (string) '';

        // section 10-13-1-85--345dcc7e:13543c6ca3a:-8000:0000000000003A7A begin
        // section 10-13-1-85--345dcc7e:13543c6ca3a:-8000:0000000000003A7A end

        return (string) $returnValue;
    }

    /**
     * Sets the extensionId of the extension the translations belong to.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string extensionId
     * @return mixed
     */
    public function setExtensionId($extensionId)
    {
        // section 10-13-1-85--345dcc7e:13543c6ca3a:-8000:0000000000003A7D begin
        // section 10-13-1-85--345dcc7e:13543c6ca3a:-8000:0000000000003A7D end
    }

} /* end of abstract class tao_helpers_translation_TaoTranslationFile */

?>