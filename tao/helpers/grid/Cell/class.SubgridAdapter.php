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

/**
 * Short description of class tao_helpers_grid_Cell_SubgridAdapter
 *
 * @abstract
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package tao
 
 */
abstract class tao_helpers_grid_Cell_SubgridAdapter
    extends tao_helpers_grid_Cell_Adapter
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute subgridClass
     *
     * @access public
     * @var string
     */
    public $subgridClass = '';

    /**
     * Instance of gridContainer used to format the cell content.
     * This instance is a prototype and will be cloned for each cells.
     *
     * @access public
     * @var GridContainer
     */
    public $subGridContainer = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array options
     * @param  string subgridClass
     * @return mixed
     */
    public function __construct($options = array(), $subgridClass = '')
    {
        
		parent::__construct($options);
		
		$this->initSubgridClass($subgridClass);
		//the class exists
		if(!class_exists($this->subgridClass)){
			throw new Exception('the subgrid class does not exist : '.$this->subgridClass);
		}
		$this->subGridContainer = new $this->subgridClass(array());
		//the instance is an instance of the good class
		if(is_a($this->subGridContainer, $this->subgridClass) && is_a($this->subGridContainer, 'tao_helpers_grid_GridContainer')){
			$returnValue = $this->subGridContainer->getGrid()->getColumnsModel();
		}else{
			throw new common_Exception('invalid subgrid class : '.$this->subgridClass);
		}
        
    }

    /**
     * Short description of method getValue
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string rowId
     * @param  string columnId
     * @param  string data
     * @return mixed
     */
    public function getValue($rowId, $columnId, $data = null)
    {
        $returnValue = null;

        
		if(isset($this->data[$rowId]) && is_a($this->data[$rowId], 'wfEngine_helpers_Monitoring_ActivityMonitoringGrid')){
			
			$returnValue = $this->data[$rowId];
		}
		else{
			
			$subgridData = $this->getSubgridRows($rowId);
			$cloneSubGridCtn = clone $this->subGridContainer;
			$cloneSubGridCtn->getGrid()->setData($subgridData);
			$returnValue = $cloneSubGridCtn;
			$this->data[$rowId] = $cloneSubGridCtn;
		}
        

        return $returnValue;
    }

    /**
     * Short description of method getSubgridRows
     *
     * @abstract
     * @access protected
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string rowId
     * @return array
     */
    protected abstract function getSubgridRows($rowId);

    /**
     * Short description of method initSubgridClass
     *
     * @abstract
     * @access protected
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  subgridClass
     * @return mixed
     */
    protected abstract function initSubgridClass($subgridClass = '');

    /**
     * Short description of method getGridContainer
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return tao_helpers_grid_GridContainer
     */
    public function getGridContainer()
    {
        $returnValue = null;

        
        $returnValue = $this->subGridContainer;
        

        return $returnValue;
    }

} /* end of abstract class tao_helpers_grid_Cell_SubgridAdapter */

?>