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
 * Exception to be thrown when an errors occurs at Data Binding time.
 *
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package tao
 * @subpackage models_classes_dataBinding
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include common_Exception
 *
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 */
require_once('common/class.Exception.php');

/* user defined includes */
// section 127-0-1-1-2d2ef7de:13d10c8a117:-8000:0000000000003C9F-includes begin
// section 127-0-1-1-2d2ef7de:13d10c8a117:-8000:0000000000003C9F-includes end

/* user defined constants */
// section 127-0-1-1-2d2ef7de:13d10c8a117:-8000:0000000000003C9F-constants begin
// section 127-0-1-1-2d2ef7de:13d10c8a117:-8000:0000000000003C9F-constants end

/**
 * Exception to be thrown when an errors occurs at Data Binding time.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package tao
 * @subpackage models_classes_dataBinding
 */
class tao_models_classes_dataBinding_DataBindingException
    extends common_Exception
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

} /* end of class tao_models_classes_dataBinding_DataBindingException */

?>