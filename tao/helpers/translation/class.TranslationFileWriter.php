<?php
/**  
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

/**
 * A Writing class for TranslationFiles. Must be implemented by a concrete class
 * a given Translation Format such as XLIFF, PO, ... The write method must be
 * by subclasses.
 *
 * @abstract
 * @access public
 * @author Jerome Bogaerts
 * @package tao
 * @since 2.2
 
 * @version 1.0
 */
abstract class tao_helpers_translation_TranslationFileWriter
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute filePath
     *
     * @access private
     * @var string
     */
    private $filePath = '';

    /**
     * Short description of attribute translationFile
     *
     * @access private
     * @var TranslationFile
     */
    private $translationFile = null;

    // --- OPERATIONS ---

    /**
     * Creates a new instance of TranslationFileWriter.
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string filePath
     * @param  TranslationFile translationFile
     * @return mixed
     */
    public function __construct($filePath,  tao_helpers_translation_TranslationFile $translationFile)
    {
        
        $this->filePath = $filePath;
        $this->translationFile = $translationFile;
        
    }

    /**
     * Reads a translation file to persist a TranslationFile in a specific
     * Subclasses must implement this method to meet the requirement of the
     * they support such as XLIFF or PO files.
     *
     * @abstract
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    public abstract function write();

    /**
     * Sets the TranslationFile that has to be serialized.
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  TranslationFile translationFile
     * @return mixed
     */
    public function setTranslationFile( tao_helpers_translation_TranslationFile $translationFile)
    {
        
        $this->translationFile = $translationFile;
        
    }

    /**
     * Gets the location where the file must be written.
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    public function getFilePath()
    {
        $returnValue = (string) '';

        
        return $this->filePath;
        

        return (string) $returnValue;
    }

    /**
     * Sets the location where the file has to be written.
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string filePath
     * @return mixed
     */
    public function setFilePath($filePath)
    {
        
        $this->filePath = $filePath;
        
    }

    /**
     * Short description of method getTranslationFile
     *
     * @access protected
     * @author firstname and lastname of author, <author@example.org>
     * @return tao_helpers_translation_TranslationFile
     */
    protected function getTranslationFile()
    {
        $returnValue = null;

        
        $returnValue = $this->translationFile;
        

        return $returnValue;
    }

} /* end of abstract class tao_helpers_translation_TranslationFileWriter */

?>