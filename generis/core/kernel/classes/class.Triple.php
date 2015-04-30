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
 * Short description of class core_kernel_classes_Triple
 *
 * @access public
 * @author patrick.plichart@tudor.lu
 * @package generis
 
 */
class core_kernel_classes_Triple
    extends core_kernel_classes_Container
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute modelid
     *
     * @access public
     * @var int
     */
    public $modelid = 0;

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



}