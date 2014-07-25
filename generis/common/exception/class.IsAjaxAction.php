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
 * This exception allow developers to generate expected
 * errors when clients try to acces to an ajax service
 * through an other way than the ajax mechanism
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package common
 * @subpackage exception
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include common_Exception
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('common/class.Exception.php');

/* user defined includes */
// section 127-0-1-1-641a0ff0:1359a3da29e:-8000:0000000000001B23-includes begin
// section 127-0-1-1-641a0ff0:1359a3da29e:-8000:0000000000001B23-includes end

/* user defined constants */
// section 127-0-1-1-641a0ff0:1359a3da29e:-8000:0000000000001B23-constants begin
// section 127-0-1-1-641a0ff0:1359a3da29e:-8000:0000000000001B23-constants end

/**
 * This exception allow developers to generate expected
 * errors when clients try to acces to an ajax service
 * through an other way than the ajax mechanism
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package common
 * @subpackage exception
 */
class common_exception_IsAjaxAction
    extends common_Exception
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string service
     * @return mixed
     */
    public function __construct($service = "")
    {
        // section 127-0-1-1-641a0ff0:1359a3da29e:-8000:0000000000001B24 begin
        
        $message = 'The following service ('.$path.') is an Ajax service';
        parent::__construct($message);
        
        // section 127-0-1-1-641a0ff0:1359a3da29e:-8000:0000000000001B24 end
    }

} /* end of class common_exception_IsAjaxAction */

?>