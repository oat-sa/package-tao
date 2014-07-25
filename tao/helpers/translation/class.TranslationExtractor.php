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
 * A TranslationExtractor instance extracts TranslationUnits from a given source
 * as an Item, source code, ...
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

/* user defined includes */
// section -64--88-1-7-3ec47102:13332ada7cb:-8000:00000000000031E0-includes begin
// section -64--88-1-7-3ec47102:13332ada7cb:-8000:00000000000031E0-includes end

/* user defined constants */
// section -64--88-1-7-3ec47102:13332ada7cb:-8000:00000000000031E0-constants begin
// section -64--88-1-7-3ec47102:13332ada7cb:-8000:00000000000031E0-constants end

/**
 * A TranslationExtractor instance extracts TranslationUnits from a given source
 * as an Item, source code, ...
 *
 * @abstract
 * @access public
 * @author Jerome Bogaerts
 * @package tao
 * @since 2.2
 * @subpackage helpers_translation
 * @version 1.0
 */
abstract class tao_helpers_translation_TranslationExtractor
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute paths
     *
     * @access private
     * @var array
     */
    private $paths = array();

    /**
     * Short description of attribute translationUnits
     *
     * @access private
     * @var array
     */
    private $translationUnits = array();

    // --- OPERATIONS ---

    /**
     * Creates a new Instance of TranslationExtractor.
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array paths
     * @return mixed
     */
    public function __construct($paths)
    {
        // section -64--88-1-7-3ec47102:13332ada7cb:-8000:00000000000031E7 begin
        $this->setPaths($paths);
        // section -64--88-1-7-3ec47102:13332ada7cb:-8000:00000000000031E7 end
    }

    /**
     * Any subclass of TranslationExtractor must implement this method aiming at
     * TranslationUnits from a given source and set the translationUnit member
     * the class.
     *
     * @abstract
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    public abstract function extract();

    /**
     * Sets an array of paths where the translations have to be extracted.
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array paths
     * @return mixed
     */
    public function setPaths($paths)
    {
        // section -64--88-1-7-3ec47102:13332ada7cb:-8000:00000000000031F1 begin
        $this->paths = $paths;
        // section -64--88-1-7-3ec47102:13332ada7cb:-8000:00000000000031F1 end
    }

    /**
     * Gets an array of paths where the translations have to be extracted
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return array
     */
    public function getPaths()
    {
        $returnValue = array();

        // section -64--88-1-7-3ec47102:13332ada7cb:-8000:00000000000031F4 begin
        $returnValue = $this->paths;
        // section -64--88-1-7-3ec47102:13332ada7cb:-8000:00000000000031F4 end

        return (array) $returnValue;
    }

    /**
     * Gets an array of TranslationUnit instances that were generated during the
     * of the TranslationExtractor::extract method.
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return array
     */
    public function getTranslationUnits()
    {
        $returnValue = array();

        // section -64--88-1-7-3ec47102:13332ada7cb:-8000:00000000000031F6 begin
        $returnValue = $this->translationUnits;
        // section -64--88-1-7-3ec47102:13332ada7cb:-8000:00000000000031F6 end

        return (array) $returnValue;
    }

    /**
     * Sets an array of TranslationUnit instances that will be generated during
     * invokation of the TranslationExtractor::extract method.
     *
     * @access protected
     * @author firstname and lastname of author, <author@example.org>
     * @param  array translationUnits
     * @return mixed
     */
    protected function setTranslationUnits($translationUnits)
    {
        // section -64--88-1-7-3ec47102:13332ada7cb:-8000:00000000000031FE begin
        $this->translationUnits = $translationUnits;
        // section -64--88-1-7-3ec47102:13332ada7cb:-8000:00000000000031FE end
    }

} /* end of abstract class tao_helpers_translation_TranslationExtractor */

?>