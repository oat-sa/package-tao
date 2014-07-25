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
 * Generis Object Oriented API - core\kernel\api\interface.Api.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 23.03.2010, 15:58:21 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_api
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009AD-includes begin
// section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009AD-includes end

/* user defined constants */
// section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009AD-constants begin
// section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009AD-constants end

/**
 * Short description of class core_kernel_api_Api
 *
 * @abstract
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_api
 */
interface core_kernel_api_Api
{


    // --- OPERATIONS ---

    /**
     * Short description of method logIn
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string login
     * @param  string password
     * @param  string module
     * @param  boolean role
     * @return boolean
     */
    public function logIn($login, $password, $module, $role);

    /**
     * Short description of method logOut
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return boolean
     */
    public function logOut();

} /* end of interface core_kernel_api_Api */

?>