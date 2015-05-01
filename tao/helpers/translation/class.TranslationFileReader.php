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
 * A Reading class for TranslationFiles. Must be implemented by a concrete class
 * a given Translation Format such as XLIFF, PO, ... The read method must be
 * by subclasses.
 *
 * @abstract
 * @access public
 * @author Jerome Bogaerts
 * @package tao
 * @since 2.2
 
 * @version 1.0
 */
abstract class tao_helpers_translation_TranslationFileReader
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
     * Creates a new instance of TranslationFileReader.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string filePath
     * @return mixed
     */
    public function __construct($filePath)
    {
        
        $this->filePath = $filePath;
        
    }

    /**
     * Reads a translation file to put TranslationUnits of the TranslationFile
     * memory. Retrieved strings must be unescaped to avoid any misunderstanding
     * the client code. This method must be implemented by subclasses.
     *
     * @abstract
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    public abstract function read();

    /**
     * Gets the TranslationFile instance resulting of the reading of the file.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return tao_helpers_translation_TranslationFile
     */
    public function getTranslationFile()
    {
        $returnValue = null;

        
    	if ($this->translationFile != null) {
        	return $this->translationFile;
        }
        else {
        	throw new tao_helpers_translation_TranslationException('No TranslationFile to retrieve.');
        }
        

        return $returnValue;
    }

    /**
     * Gets the location where the file has to be read.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getFilePath()
    {
        $returnValue = (string) '';

        
        return $this->filePath;
        

        return (string) $returnValue;
    }

    /**
     * Sets the location where the file has to be read.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string filePath
     * @return mixed
     */
    public function setFilePath($filePath)
    {
        
        $this->filePath = $filePath;
        
    }

    /**
     * Short description of method setTranslationFile
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  TranslationFile translationFile
     * @return mixed
     */
    public function setTranslationFile( tao_helpers_translation_TranslationFile $translationFile)
    {
        
        $this->translationFile = $translationFile;
        
    }

} /* end of abstract class tao_helpers_translation_TranslationFileReader */

?>