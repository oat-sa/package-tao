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
 * A PO Translation Unit.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 * @subpackage helpers_translation
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * A Translation Unit represents a single unit of translation of a software,
 * file, ... It has a source text in the original language and a target in which
 * text has to be translated.
 *
 * Example:
 * Source (English): The end is far away
 * Target (Yoda English): Far away the end is
 *
 * @author Jerome Bogaerts
 * @since 2.2
 * @version 1.0
 */
require_once('tao/helpers/translation/class.TranslationUnit.php');

/* user defined includes */
// section -64--88-56-1--6ccfbacb:137c11aa2dd:-8000:0000000000003AC5-includes begin
// section -64--88-56-1--6ccfbacb:137c11aa2dd:-8000:0000000000003AC5-includes end

/* user defined constants */
// section -64--88-56-1--6ccfbacb:137c11aa2dd:-8000:0000000000003AC5-constants begin
// section -64--88-56-1--6ccfbacb:137c11aa2dd:-8000:0000000000003AC5-constants end

/**
 * A PO Translation Unit.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 * @subpackage helpers_translation
 */
class tao_helpers_translation_POTranslationUnit
    extends tao_helpers_translation_TranslationUnit
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Annotation identifier for PO translator comments.
     *
     * @access public
     * @var string
     */
    const TRANSLATOR_COMMENTS = 'po-translator-comments';

    /**
     * Annotation identifier for PO extracted comments.
     *
     * @access public
     * @var string
     */
    const EXTRACTED_COMMENTS = 'po-extracted-comments';

    /**
     * Annotation identifier for PO message flags.
     *
     * @access public
     * @var string
     */
    const FLAGS = 'po-flags';

    /**
     * Annotation identifier for PO reference flag.
     *
     * @access public
     * @var string
     */
    const REFERENCE = 'po-reference';

    /**
     * Annotation identifier for the PO previous untranslated string (singular)
     *
     * @access public
     * @var string
     */
    const PREVIOUS_MSGID = 'po-previous-msgid';

    /**
     * Annotation identifier for the PO previous untranslated string (plural)
     *
     * @access public
     * @var string
     */
    const PREVIOUS_MSGID_PLURAL = 'po-previous-msgid-plural';

    /**
     * Annotation identifier for the message context comment.
     *
     * @access public
     * @var string
     */
    const PREVIOUS_MSGCTXT = 'po-previous-msgctxt';

    // --- OPERATIONS ---

    /**
     * Add a PO compliant flag to the TranslationUnit. The FLAGS annotation will
     * created if no flags were added before.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string flag A flag string.
     * @return void
     */
    public function addFlag($flag)
    {
        // section -64--88-56-1--3f1036:137c6806719:-8000:0000000000003AF3 begin
        $currentAnnotations = $this->getAnnotations();
        if (!isset($currentAnnotations[self::FLAGS])){
            $currentAnnotations[self::FLAGS] = $flag;
        }
        else if (!(mb_strpos($currentAnnotations[self::FLAGS], $flag, 0, TAO_DEFAULT_ENCODING) !== false)){
            $currentAnnotations[self::FLAGS] .= " ${flag}";
        }
    
        $this->setAnnotations($currentAnnotations);
        // section -64--88-56-1--3f1036:137c6806719:-8000:0000000000003AF3 end
    }

    /**
     * Remove a given PO compliant flag from the TranslationUnit. The FLAGS
     * will be removed from the TranslationUnit if it was the last one of the
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string flag A flag string.
     * @return void
     */
    public function removeFlag($flag)
    {
        // section -64--88-56-1--3f1036:137c6806719:-8000:0000000000003AF8 begin
        $currentFlags = $this->getFlags();
        for ($i = 0; $i < count($currentFlags); $i++){
            if ($currentFlags[$i] == $flag){
                break;
            }
        }
        
        if ($i <= count($currentFlags)){
            // The flag is found.
            unset($currentFlags[$i]);
            $this->setFlags($currentFlags);
        }
        // section -64--88-56-1--3f1036:137c6806719:-8000:0000000000003AF8 end
    }

    /**
     * Short description of method hasFlag
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string flag A PO flag string.
     * @return boolean
     */
    public function hasFlag($flag)
    {
        $returnValue = (bool) false;

        // section -64--88-56-1--3f1036:137c6806719:-8000:0000000000003AFD begin
        foreach ($this->getFlags() as $f){
            if ($f == $flag){
                $returnValue = true;
                break;
            }
        }
        // section -64--88-56-1--3f1036:137c6806719:-8000:0000000000003AFD end

        return (bool) $returnValue;
    }

    /**
     * Get the flags associated to the TranslationUnit. If there are no flags,
     * empty array is returned. Otherwise, a collection of strings is returned.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getFlags()
    {
        $returnValue = array();

        // section -64--88-56-1--3f1036:137c6806719:-8000:0000000000003B02 begin
        $currentAnnotations = $this->getAnnotations();
        if (isset($currentAnnotations[self::FLAGS])){
            $returnValue = explode(" ", $currentAnnotations[self::FLAGS]);
        }
        // section -64--88-56-1--3f1036:137c6806719:-8000:0000000000003B02 end

        return (array) $returnValue;
    }

    /**
     * Associate a collection of PO flags to the TranslationUnit. A FLAGS
     * will be added to the TranslationUnit will be added consequently to the
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array flags An array of PO string flags.
     * @return core_kernel_classes_Session_void
     */
    public function setFlags($flags)
    {
        // section -64--88-56-1--3f1036:137c6806719:-8000:0000000000003B06 begin
        $currentAnnotations = $this->getAnnotations();
        $currentAnnotations[self::FLAGS] = implode(" ", $flags);
        $this->setAnnotations($currentAnnotations);
        // section -64--88-56-1--3f1036:137c6806719:-8000:0000000000003B06 end
    }

} /* end of class tao_helpers_translation_POTranslationUnit */

?>