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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * This exception depicts an error while accessing the FileSystem.
 *
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package common
 * @subpackage exception
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include common_exception_Error
 *
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 */
require_once('common/exception/class.Error.php');

/* user defined includes */
// section 10-30-1--82--5bc03e16:13cb918086b:-8000:0000000000001FAC-includes begin
// section 10-30-1--82--5bc03e16:13cb918086b:-8000:0000000000001FAC-includes end

/* user defined constants */
// section 10-30-1--82--5bc03e16:13cb918086b:-8000:0000000000001FAC-constants begin
// section 10-30-1--82--5bc03e16:13cb918086b:-8000:0000000000001FAC-constants end

/**
 * This exception depicts an error while accessing the FileSystem.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package common
 * @subpackage exception
 */
class common_exception_FileSystemError
    extends common_exception_Error
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

} /* end of class common_exception_FileSystemError */

?>