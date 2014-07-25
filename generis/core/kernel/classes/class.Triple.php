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
 * Generis Object Oriented API - core/kernel/classes/class.Triple.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 21.02.2012, 16:41:35 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author patrick.plichart@tudor.lu
 * @package core
 * @subpackage kernel_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_classes_Container
 *
 * @author patrick.plichart@tudor.lu
 */
require_once('core/kernel/classes/class.Container.php');

/* user defined includes */
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000812-includes begin
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000812-includes end

/* user defined constants */
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000812-constants begin
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000812-constants end

/**
 * Short description of class core_kernel_classes_Triple
 *
 * @access public
 * @author patrick.plichart@tudor.lu
 * @package core
 * @subpackage kernel_classes
 */
class core_kernel_classes_Triple
    extends core_kernel_classes_Container
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute modelID
     *
     * @access public
     * @var int
     */
    public $modelID = 0;

    /**
     * Short description of attribute subject
     *
     * @access public
     * @var string
     */
    public $subject = '';

    /**
     * Short description of attribute predicate
     *
     * @access public
     * @var string
     */
    public $predicate = '';

    /**
     * Short description of attribute object
     *
     * @access public
     * @var string
     */
    public $object = '';

    /**
     * Short description of attribute id
     *
     * @access public
     * @var int
     */
    public $id = 0;

    /**
     * Short description of attribute lg
     *
     * @access public
     * @var string
     */
    public $lg = '';

    /**
     * Short description of attribute readPrivileges
     *
     * @access public
     * @var string
     */
    public $readPrivileges = '';

    /**
     * Short description of attribute editPrivileges
     *
     * @access public
     * @var string
     */
    public $editPrivileges = '';

    /**
     * Short description of attribute deletePrivileges
     *
     * @access public
     * @var string
     */
    public $deletePrivileges = '';

    // --- OPERATIONS ---

} /* end of class core_kernel_classes_Triple */

?>