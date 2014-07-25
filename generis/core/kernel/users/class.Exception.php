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
 * Generis Object Oriented API - core/kernel/users/class.Exception.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 18.02.2013, 16:43:05 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package core
 * @subpackage kernel_users
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
// section 127-0-1-1--55ee3a0d:13cedda118c:-8000:0000000000001FBC-includes begin
// section 127-0-1-1--55ee3a0d:13cedda118c:-8000:0000000000001FBC-includes end

/* user defined constants */
// section 127-0-1-1--55ee3a0d:13cedda118c:-8000:0000000000001FBC-constants begin
// section 127-0-1-1--55ee3a0d:13cedda118c:-8000:0000000000001FBC-constants end

/**
 * Short description of class core_kernel_users_Exception
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package core
 * @subpackage kernel_users
 */
class core_kernel_users_Exception
    extends common_Exception
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute BAD_PASSWORD
     *
     * @access public
     * @var int
     */
    const BAD_PASSWORD = 0;

    /**
     * Short description of attribute BAD_LOGIN
     *
     * @access public
     * @var int
     */
    const BAD_LOGIN = 1;

    /**
     * Short description of attribute BAD_ROLE
     *
     * @access public
     * @var int
     */
    const BAD_ROLE = 2;

    /**
     * Short description of attribute LOGIN_EXITS
     *
     * @access public
     * @var int
     */
    const LOGIN_EXITS = 3;

    // --- OPERATIONS ---

} /* end of class core_kernel_users_Exception */

?>