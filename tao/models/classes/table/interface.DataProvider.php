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
 * TAO - tao/models/classes/table/interface.DataProvider.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 31.08.2012, 09:51:22 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage models_classes_table
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BD7-includes begin
// section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BD7-includes end

/* user defined constants */
// section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BD7-constants begin
// section 127-0-1-1--8febfab:13977a059a7:-8000:0000000000003BD7-constants end

/**
 * Short description of class tao_models_classes_table_DataProvider
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage models_classes_table
 */
interface tao_models_classes_table_DataProvider
{


    // --- OPERATIONS ---

    /**
     * Short description of method prepare
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array resources
     * @param  array columns
     * @return mixed
     */
    public function prepare($resources, $columns);

    /**
     * Short description of method getValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Column column
     * @return string
     */
    public function getValue( core_kernel_classes_Resource $resource,  tao_models_classes_table_Column $column);

} /* end of interface tao_models_classes_table_DataProvider */

?>