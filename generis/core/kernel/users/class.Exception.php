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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Short description of class core_kernel_users_Exception
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package generis
 
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

}