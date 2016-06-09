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
 * The FileDescription data type contains all the data that a form collects or
 * about a file.
 *
 * @abstract
 * @access public
 * @author Jerome Bogaerts <jerome@taotesting.com>
 * @package tao
 
 */
abstract class tao_helpers_form_data_FileDescription
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The name of the file e.g. thumbnail.png.
     *
     * @access private
     * @var string
     */
    private $name = '';

    /**
     * The size of the file in bytes.
     *
     * @access private
     * @var int
     */
    private $size = 0;

    /**
     * The filed stored in persistent memory (if already stored).
     *
     * @access private
     * @var File
     */
    private $file = null;

    // --- OPERATIONS ---

    /**
     * Creates a new instance of FileDescription.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  string name The name of the file such as thumbnail.svg
     * @param  int size The size of the file in bytes.
     * @return mixed
     */
    public function __construct($name, $size)
    {
        
        $this->name = $name;
        $this->size = $size;
        
    }

    /**
     * Returns the name of the file e.g. test.xml.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return string
     */
    public function getName()
    {
        $returnValue = (string) '';

        
        if (!empty($this->file) && empty($this->name)){
        	// collect information about the file instance itself.
        	$this->name = $this->file->getLabel();
        }
        
        $returnValue = $this->name;
        

        return (string) $returnValue;
    }

    /**
     * Returns the size of the file in bytes.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return int
     */
    public function getSize()
    {
        $returnValue = (int) 0;

        
        if (!empty($this->file) && empty($this->size)){
        	// collect from the file instance itself.
        	$fileInfo = $this->file->getFileInfo();
        	$this->size = $fileInfo->getSize();
        }
        
        $returnValue = $this->size;
        

        return (int) $returnValue;
    }

    /**
     * Gets the file bound to the FileDescription (returns null if not file
     * in persistent memory).
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return core_kernel_file_File
     */
    public function getFile()
    {
        $returnValue = null;

        
        $returnValue = $this->file;
        

        return $returnValue;
    }

    /**
     * Set the File corresponding to the FileDescription in persistent memory.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  File file
     * @return void
     */
    public function setFile( core_kernel_file_File $file)
    {
        
        $this->file = $file;
        
        // Reset data about the file to make them computed from
        // the Generis File instance.
        $this->name = '';
        $this->size = 0;
        
    }

} /* end of abstract class tao_helpers_form_data_FileDescription */

?>