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
 * Copyright (c) 2009-2012 (original work) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * tao - taoResults/models/classes/table/class.GradeColumn.php
 *
 * $Id$
 *
 * This file is part of tao.
 *
 * Automatically generated on 31.08.2012, 16:46:46 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoResults
 * @subpackage models_classes_table
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include taoResults_models_classes_table_VariableColumn
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoResults/models/classes/table/class.VariableColumn.php');

/* user defined includes */
// section 127-0-1-1--228e2cb4:13971ca3814:-8000:0000000000000C42-includes begin
// section 127-0-1-1--228e2cb4:13971ca3814:-8000:0000000000000C42-includes end

/* user defined constants */
// section 127-0-1-1--228e2cb4:13971ca3814:-8000:0000000000000C42-constants begin
// section 127-0-1-1--228e2cb4:13971ca3814:-8000:0000000000000C42-constants end

/**
 * Short description of class taoResults_models_classes_table_GradeColumn
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoResults
 * @subpackage models_classes_table
 */
class taoResults_models_classes_table_GradeColumn
    extends taoResults_models_classes_table_VariableColumn
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getVariableClass
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_classes_Class
     */
    public function getVariableClass()
    {
        $returnValue = null;

        // section 127-0-1-1--1b42b935:1397d0ae818:-8000:0000000000000C7E begin
        $returnValue = new core_kernel_classes_Class(CLASS_OUTCOME_VARIABLE);
        // section 127-0-1-1--1b42b935:1397d0ae818:-8000:0000000000000C7E end

        return $returnValue;
    }

} /* end of class taoResults_models_classes_table_GradeColumn */

?>