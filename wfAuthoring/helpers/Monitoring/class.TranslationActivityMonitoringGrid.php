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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *
 *
 */

/**
 * TAO - wfEngine/helpers/Monitoring/class.TranslationProcessMonitoringGrid.php
 *
 * This file is part of TAO.
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage helpers_Monitoring
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}


/**
 * Short description of class wfAuthoring_helpers_Monitoring_ProcessMonitoringGrid
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 * @subpackage helpers_Monitoring
 */
class wfAuthoring_helpers_Monitoring_TranslationActivityMonitoringGrid
    extends wfAuthoring_helpers_Monitoring_ActivityMonitoringGrid
{

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
		
		parent::initColumns();
		
		$this->grid->addColumn('xliff_version', __('XLIFF version'));
		$this->grid->addColumn('xliff', __('XLIFF'));
		
		$this->grid->addColumn('vff_version', __('VFF version'));
		$this->grid->addColumn('vff', __('VFF'));
		
		$returnValue = $this->grid->setColumnsAdapter(
			array(
				'xliff',
				'xliff_version',
				'vff',
				'vff_version'
			),
			new wfAuthoring_helpers_Monitoring_VersionedFileAdapter()
		);
		
        return (bool) $returnValue;
    }

}
?>
