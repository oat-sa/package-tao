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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */


/**
 * Short description of class wfAuthoring_helpers_Monitoring_ProcessMonitoringGrid
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 
 */
class wfAuthoring_helpers_Monitoring_TranslationProcessMonitoringGrid
    extends wfAuthoring_helpers_Monitoring_ProcessMonitoringGrid
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
		
		$this->grid->addColumn('unit', __('Unit'));
		$this->grid->addColumn('country', __('Country'));
		$this->grid->addColumn('language', __('Language'));
		
		$returnValue = parent::initColumns();
		
		$returnValue = $this->grid->setColumnsAdapter(
			array('unit', 'country', 'language'),
			new wfAuthoring_helpers_Monitoring_TranslationMetaAdapter()
		);	
		
        return (bool) $returnValue;
    }
	
	/**
     * Can be easily extended to adapt the current activity executions column
     *
     * @access protected
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return boolean
     */
    protected function initCurrentActivityColumn()
    {
        $returnValue = (bool) false;

        
		$this->grid->addColumn(PROPERTY_PROCESSINSTANCES_CURRENTACTIVITYEXECUTIONS, __('Current Activities'));
		$returnValue = $this->grid->setColumnsAdapter(
			PROPERTY_PROCESSINSTANCES_CURRENTACTIVITYEXECUTIONS,
			new wfAuthoring_helpers_Monitoring_CurrentActivitiesAdapter(
				array('excludedProperties' => $this->excludedProperties),
				'wfAuthoring_helpers_Monitoring_TranslationActivityMonitoringGrid'
			)
		);	
        

        return (bool) $returnValue;
    }
}
?>
