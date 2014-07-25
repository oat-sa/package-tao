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
 * Extend the default users grid model here
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package tao
 * @subpackage models_grids
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_models_grids_Users
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('tao/models/grids/class.Users.php');

/* user defined includes */
// section 127-0-1-1--2e12219e:1360c8283db:-8000:000000000000387B-includes begin
// section 127-0-1-1--2e12219e:1360c8283db:-8000:000000000000387B-includes end

/* user defined constants */
// section 127-0-1-1--2e12219e:1360c8283db:-8000:000000000000387B-constants begin
// section 127-0-1-1--2e12219e:1360c8283db:-8000:000000000000387B-constants end

/**
 * Extend the default users grid model here
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package tao
 * @subpackage models_grids
 */
class tao_models_grids_CustomUsers
    extends tao_models_grids_Users
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method initColumns
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return boolean
     */
    public function initColumns()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--2e12219e:1360c8283db:-8000:000000000000387C begin
		$returnValue = parent::initColumns();
		/*if (!in_array('country', $this->excludedProperties)) {*/
		foreach ($this->options['customProps'] as $uri => $opts) {
			$name = explode('#', $uri);
			//$userProperties[$uri] = __($name[1]);
			$this->grid->addColumn($uri, __($name[1]));
			$returnValue &= $this->grid->setColumnsAdapter(
				$uri,
				new tao_models_grids_adaptors_UserAdditionalProperties()
			);
		}
		/*}*/
        // section 127-0-1-1--2e12219e:1360c8283db:-8000:000000000000387C end

        return (bool) $returnValue;
    }

} /* end of class tao_models_grids_CustomUsers */

?>