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
 * session has been set public because when implementing an interface, the son
 * this class may not read this attribute otherwise in php 5.2
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package core
 * @subpackage kernel_impl
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_api_Api
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('core/kernel/api/interface.Api.php');

/* user defined includes */
// section 10-13-1--31--64270bf:11918ad765e:-8000:000000000000098C-includes begin
// section 10-13-1--31--64270bf:11918ad765e:-8000:000000000000098C-includes end

/* user defined constants */
// section 10-13-1--31--64270bf:11918ad765e:-8000:000000000000098C-constants begin
// section 10-13-1--31--64270bf:11918ad765e:-8000:000000000000098C-constants end

/**
 * session has been set public because when implementing an interface, the son
 * this class may not read this attribute otherwise in php 5.2
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package core
 * @subpackage kernel_impl
 */
class core_kernel_impl_Api
        implements core_kernel_api_Api
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method logIn
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string login
     * @param  string password
     * @param  string module
     * @param  boolean role
     * @return boolean
     */
    public function logIn($login, $password, $module, $role)
    {
        $returnValue = (bool) false;

        // section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009AF begin
        if($role === true) {
        	$role = new core_kernel_classes_Resource(INSTANCE_ROLE_GLOBALMANAGER);
        }
       
        core_kernel_users_Service::singleton()->login($login, $password, $role);
        // section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009AF end

        return (bool) $returnValue;
    }

    /**
     * Short description of method logOut
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return boolean
     */
    public function logOut()
    {
        $returnValue = (bool) false;

        // section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009B5 begin
        core_kernel_users_Service::singleton()->logout();
        // section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009B5 end

        return (bool) $returnValue;
    }

} /* end of class core_kernel_impl_ApiI */

?>