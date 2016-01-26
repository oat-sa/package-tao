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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */




namespace oat\taoOutcomeUi\helper;

use \tao_helpers_grid_Cell_ResourceLabelAdapter;
use \tao_helpers_grid_GridContainer;

/**
 * Short description of class oat\taoOutcomeUi\helper\DeliveryResultGrid
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package wfEngine
 
 */
class DeliveryResultGrid
    extends tao_helpers_grid_GridContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute processExecutions
     *
     * @access protected
     * @var array
     */
    protected $processExecutions = array();

    // --- OPERATIONS ---

    /**
     * Short description of method initColumns
     *
     * @access protected
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return boolean
     */
    protected function initColumns()
    {
        $returnValue = (bool) false;

		$excludedProperties = (is_array($this->options) && isset($this->options['excludedProperties']))?$this->options['excludedProperties']:array();
		$columnNames = (is_array($this->options) && isset($this->options['columnNames']))?$this->options['columnNames']:array();
		
		
		$processProperties = array(
			RDFS_LABEL					=> __('Label'),
			PROPERTY_RESULT_OF_DELIVERY	=> __('Delivery'),
			PROPERTY_RESULT_OF_SUBJECT	=> __('Test taker'),
            RDF_TYPE                	=> __('Class')
		);
		
		foreach($processProperties as $processPropertyUri => $label){
			if(!isset($excludedProperties[$processPropertyUri])){
				$column = $this->grid->addColumn($processPropertyUri, $label);
               
			}
		}

		$this->grid->setColumnsAdapter(
			array_keys($processProperties),
			new tao_helpers_grid_Cell_ResourceLabelAdapter()
		);

        return (bool) $returnValue;
    }


} /* end of class oat\taoOutcomeUi\helper\DeliveryResultGrid*/

?>