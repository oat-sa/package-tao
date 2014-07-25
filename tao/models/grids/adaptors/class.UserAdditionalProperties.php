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
 * Add more data to users grid here
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package tao
 * @subpackage models_grids_adaptors
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_helpers_grid_Cell_Adapter
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('tao/helpers/grid/Cell/class.Adapter.php');

/* user defined includes */
// section 127-0-1-1--2e12219e:1360c8283db:-8000:0000000000003880-includes begin
// section 127-0-1-1--2e12219e:1360c8283db:-8000:0000000000003880-includes end

/* user defined constants */
// section 127-0-1-1--2e12219e:1360c8283db:-8000:0000000000003880-constants begin
// section 127-0-1-1--2e12219e:1360c8283db:-8000:0000000000003880-constants end

/**
 * Add more data to users grid here
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package tao
 * @subpackage models_grids_adaptors
 */
class tao_models_grids_adaptors_UserAdditionalProperties
    extends tao_helpers_grid_Cell_Adapter
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getValue
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string rowId
     * @param  string columnId
     * @param  string data
     * @return mixed
     */
    public function getValue($rowId, $columnId, $data = null)
    {
        $returnValue = null;

        // section 127-0-1-1--2e12219e:1360c8283db:-8000:0000000000003883 begin
		$user = new core_kernel_classes_Resource($rowId);
		$prop = new core_kernel_classes_Property($columnId);
		$res = $user->getOnePropertyValue($prop);
		if (is_null($res)) {
			$returnValue = '';
		} elseif ($res instanceof core_kernel_classes_Resource) {
			$returnValue = $res->getLabel();
		} else {
			$returnValue = (string)$res;
		}
        // section 127-0-1-1--2e12219e:1360c8283db:-8000:0000000000003883 end

        return $returnValue;
    }

} /* end of class tao_models_grids_adaptors_UserAdditionalProperties */

?>