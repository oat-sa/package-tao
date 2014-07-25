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
 * TAO - tao/helpers/form/data/class.StoredFileDescription.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 26.02.2013, 08:50:58 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
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
// section 127-0-1-1--2c821474:13d11698078:-8000:0000000000003CF0-includes begin
// section 127-0-1-1--2c821474:13d11698078:-8000:0000000000003CF0-includes end

/* user defined constants */
// section 127-0-1-1--2c821474:13d11698078:-8000:0000000000003CF0-constants begin
// section 127-0-1-1--2c821474:13d11698078:-8000:0000000000003CF0-constants end

/**
 * Short description of class tao_helpers_form_data_StoredFileDescription
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package tao
 * @subpackage helpers_form_data
 */
class tao_helpers_form_data_StoredFileDescription
    extends tao_helpers_form_data_FileDescription
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  File file The URI of the file stored by Generis.
     * @return mixed
     */
    public function __construct( core_kernel_file_File $file)
    {
        // section 127-0-1-1--2c821474:13d11698078:-8000:0000000000003D05 begin
        $this->setFile($file);
        // section 127-0-1-1--2c821474:13d11698078:-8000:0000000000003D05 end
    }

} /* end of class tao_helpers_form_data_StoredFileDescription */

?>