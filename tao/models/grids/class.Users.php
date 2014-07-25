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
 * TAO - tao/models/grids/class.Users.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 12.03.2012, 17:18:46 with ArgoUML PHP module
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package tao
 * @subpackage models_grids
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_helpers_grid_GridContainer
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('tao/helpers/grid/class.GridContainer.php');

/* user defined includes */
// section 127-0-1-1--3130d5b7:13607a37283:-8000:000000000000386D-includes begin
// section 127-0-1-1--3130d5b7:13607a37283:-8000:000000000000386D-includes end

/* user defined constants */
// section 127-0-1-1--3130d5b7:13607a37283:-8000:000000000000386D-constants begin
// section 127-0-1-1--3130d5b7:13607a37283:-8000:000000000000386D-constants end

/**
 * Short description of class tao_models_grids_Users
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package tao
 * @subpackage models_grids
 */
class tao_models_grids_Users
    extends tao_helpers_grid_GridContainer
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

        // section 127-0-1-1--3130d5b7:13607a37283:-8000:000000000000386F begin

		$columnNames = (is_array($this->options) && isset($this->options['columnNames']))?$this->options['columnNames']:array();
		$adapterOptions = array();
		$excludedProperties = array();
		if(is_array($this->options) && isset($this->options['excludedProperties']) && is_array($this->options['excludedProperties'])){
			$excludedProperties = $this->options['excludedProperties'];
			$adapterOptions['excludedProperties'] = $excludedProperties;
		}

		$userProperties = array(
			RDFS_LABEL => __('Label'),
			PROPERTY_USER_LOGIN => __('Login'),
			PROPERTY_USER_FIRSTNAME => __('First Name'),
			PROPERTY_USER_LASTNAME => __('Last Name'),
			PROPERTY_USER_MAIL => __('e-mail'),
			PROPERTY_USER_UILG => __('UI Lang.'),
			PROPERTY_USER_DEFLG => __('Data Lang.'),
			'roles' => __('Roles')
		);
		/*foreach ($this->options['customProps'] as $uri => $opts) {
			$name = explode('#', $uri);
			$userProperties[$uri] = __($name[1]);
		}*/

		$propertyUris = array();

		foreach($userProperties as $userPropertyUri => $label){
			if(!in_array($userPropertyUri, $excludedProperties)){
				if(isset($columnNames[$userPropertyUri]) && !empty($columnNames[$userPropertyUri])){
					$label = $columnNames[$userPropertyUri];
				}
				$this->grid->addColumn($userPropertyUri, $label);
				$propertyUris[] = $userPropertyUri;
			}
		}

		$returnValue = $this->grid->setColumnsAdapter(
			$propertyUris,
			new tao_models_grids_adaptors_UserProperty($adapterOptions)
		);

        // section 127-0-1-1--3130d5b7:13607a37283:-8000:000000000000386F end

        return (bool) $returnValue;
    }

} /* end of class tao_models_grids_Users */

?>