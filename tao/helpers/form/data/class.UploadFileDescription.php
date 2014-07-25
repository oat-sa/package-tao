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
 * The description of a file at upload time.
 *
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package tao
 * @subpackage helpers_form_data
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The FileDescription data type contains all the data that a form collects or
 * about a file.
 *
 * @author Jerome Bogaerts <jerome@taotesting.com>
 */
require_once('tao/helpers/form/data/class.FileDescription.php');

/* user defined includes */
// section 127-0-1-1--2c821474:13d11698078:-8000:0000000000003CD9-includes begin
// section 127-0-1-1--2c821474:13d11698078:-8000:0000000000003CD9-includes end

/* user defined constants */
// section 127-0-1-1--2c821474:13d11698078:-8000:0000000000003CD9-constants begin
// section 127-0-1-1--2c821474:13d11698078:-8000:0000000000003CD9-constants end

/**
 * The description of a file at upload time.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package tao
 * @subpackage helpers_form_data
 */
class tao_helpers_form_data_UploadFileDescription
    extends tao_helpers_form_data_FileDescription
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The mime/type of the file e.g. text/plain.
     *
     * @access private
     * @var string
     */
    private $type = '';

    /**
     * The temporary file path where the file is uploaded, waiting to be moved
     * the right place on the file system.
     *
     * @access private
     * @var string
     */
    private $tmpPath = '';

    // --- OPERATIONS ---

    /**
     * The temporary file path where the file is stored before being moved to
     * right place.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  string name The name of the file e.g. tmpImage.tmp
     * @param  int size The size of the file in bytes
     * @param  string type The mime-type of the file e.g. text/plain.
     * @param  string tmpPath
     * @return mixed
     */
    public function __construct($name, $size, $type, $tmpPath)
    {
        // section 127-0-1-1--2c821474:13d11698078:-8000:0000000000003CE6 begin
        parent::__construct($name, $size);
        $this->type = $type;
        $this->tmpPath = $tmpPath;
        // section 127-0-1-1--2c821474:13d11698078:-8000:0000000000003CE6 end
    }

    /**
     * Returns the mime-type of the file.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return string
     */
    public function getType()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--2c821474:13d11698078:-8000:0000000000003CF8 begin
        $returnValue = $this->type;
        // section 127-0-1-1--2c821474:13d11698078:-8000:0000000000003CF8 end

        return (string) $returnValue;
    }

    /**
     * Returns the temporary path of the file.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return string
     */
    public function getTmpPath()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--2c821474:13d11698078:-8000:0000000000003CFB begin
        $returnValue = $this->tmpPath;
        // section 127-0-1-1--2c821474:13d11698078:-8000:0000000000003CFB end

        return (string) $returnValue;
    }

} /* end of class tao_helpers_form_data_UploadFileDescription */

?>