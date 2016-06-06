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
 * Short description of class tao_helpers_grid_Grid
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package tao
 
 */
class tao_helpers_grid_Grid
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute columns
     *
     * @access protected
     * @var array
     */
    protected $columns = array();

    /**
     * Short description of attribute rows
     *
     * @access protected
     * @var array
     */
    protected $rows = array();

    /**
     * Short description of attribute options
     *
     * @access protected
     * @var array
     */
    protected $options = array();

    /**
     * Short description of attribute columnsModel
     *
     * @access protected
     * @var array
     */
    protected $columnsModel = array();

    // --- OPERATIONS ---

    /**
     * Short description of method __clone
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return tao_helpers_grid_Grid
     */
    public function __clone()
    {
        $returnValue = null;

        

        

        return $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array options
     * @return mixed
     */
    public function __construct($options = array())
    {
        
		$this->options = $options;
        
    }

    /**
     * Short description of method addColumn
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string id
     * @param  string title
     * @param  array options
     * @return tao_helpers_grid_Column
     */
    public function addColumn($id, $title, $options = array())
    {
        $returnValue = null;

        
		$replace = false;
		if(isset($options['replace'])){
			$replace = $options['replace'];
			unset($options['replace']);
		}
		if(!$replace && isset($this->columns[$id])){
			throw new common_Exception('the column with the id '.$id.' already exists');
		}else{
			$this->columns[$id] = new tao_helpers_grid_Column($id, $title, $options);
			//set order as well:
			$returnValue = true;
		}
        

        return $returnValue;
    }

    /**
     * Short description of method removeColumn
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string id
     * @return boolean
     */
    public function removeColumn($id)
    {
        $returnValue = (bool) false;

        
		unset($this->columns[$id]);
		$returnValue = true;
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method addRow
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string id
     * @param  array cells
     * @param  boolean replace
     * @return boolean
     */
    public function addRow($id, $cells = array(), $replace = false)
    {
        $returnValue = (bool) false;

        
		if(!$replace && isset($this->rows[$id])){
			throw new common_Exception('the row with the id '.$id.' already exists');
		}else{
			$this->rows[$id] = $cells;
			//@TODO: implement a sort funciton?

			$returnValue = true;
		}
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method removeRow
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string id
     * @return boolean
     */
    public function removeRow($id)
    {
        $returnValue = (bool) false;

        
		unset($this->rows[$id]);
		$returnValue = true;
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method setCellValue
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string columnId
     * @param  string rowId
     * @param  string content string or Grid
     * @param  boolean forceCreation
     * @return boolean
     */
    public function setCellValue($columnId, $rowId, $content, $forceCreation = false)
    {
        $returnValue = (bool) false;

        
		//TODO: for creating row and column if not exists?
		if(isset($this->columns[$columnId])){
			if(isset($this->rows[$rowId])){
				$this->rows[$rowId][$columnId] = $content;
				$returnValue = true;
			}
		}
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method setColumnsAdapter
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array columnIds
     * @param  Adapter adapter
     * @return boolean
     */
    public function setColumnsAdapter($columnIds,  tao_helpers_grid_Cell_Adapter $adapter)
    {
        $returnValue = (bool) false;

        
		if(is_string($columnIds)){
			$columnIds = array($columnIds);
		}
		if(is_array($columnIds)){
			foreach($columnIds as $colId){
				if (!isset($this->columns[$colId]) || !$this->columns[$colId] instanceof tao_helpers_grid_Column) {
					throw new common_Exception('cannot set the column\'s adapter : the column with the id ' . $colId . ' does not exist');
				} else {
					$returnValue = $this->columns[$colId]->setAdapter($adapter);
				}
			}
		}
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method toArray
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return array
     */
    public function toArray()
    {
        $returnValue = array();

        

		//sort columns:
		$this->sortColumns();

		foreach($this->rows as $rowId => $cells){
			$returnValue[$rowId] = array();

			foreach($this->columns as $columnId => $column){
				if($column->hasAdapter()){

					//fill content with adapter:
					$data = null;
					if(isset($returnValue[$rowId][$columnId])){
						$data = $returnValue[$rowId][$columnId];
					}else if(isset($cells[$columnId])){
						$data = $cells[$columnId];
					}
					$returnValue[$rowId][$columnId] = $column->getAdaptersData($rowId, $data);

				}else if(isset($cells[$columnId])){

					if($cells[$columnId] instanceof tao_helpers_grid_Grid){
						$returnValue[$rowId][$columnId] = $cells[$columnId]->toArray();
					}else{
						$returnValue[$rowId][$columnId] = $cells[$columnId];
					}

				}else{
					$returnValue[$rowId][$columnId] = null;//empty cell
				}
			}

		}
        

        return (array) $returnValue;
    }

    /**
     * Short description of method sortColumns
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return boolean
     */
    public function sortColumns()
    {
        $returnValue = (bool) false;

        
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method getColumns
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return array
     */
    public function getColumns()
    {
        $returnValue = array();

        
		$returnValue = $this->columns;
        

        return (array) $returnValue;
    }

    /**
     * Short description of method getColumn
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string id
     * @return tao_helpers_grid_Column
     */
    public function getColumn($id)
    {
        $returnValue = null;

        
		if(isset($this->columns[$id]) && $this->columns[$id] instanceof tao_helpers_grid_Column){
			$returnValue = $this->columns[$id];
		}
        

        return $returnValue;
    }

    /**
     * Short description of method getColumnsModel
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  boolean rebuild
     * @return array
     */
    public function getColumnsModel($rebuild = false)
    {
        $returnValue = array();

        
		foreach($this->columns as $column){

			if($column instanceof tao_helpers_grid_Column){

				$returnValue[$column->getId()] = array(
					'id' => $column->getId(),
					'title' => $column->getTitle(),
					'type' => $column->getType()
				);

				foreach($column->getOptions() as $optionsName=>$optionValue){
					$returnValue[$column->getId()][$optionsName] = $optionValue;
				}

				if($column->hasAdapter('tao_helpers_grid_Cell_SubgridAdapter')){
        			$subGridAdapter = null;
					$adapters = $column->getAdapters();
        			$adaptersLength = count($adapters);
        			for($i=$adaptersLength-1; $i>=0; $i--){
        				if($adapters[$i] instanceof tao_helpers_grid_Cell_SubgridAdapter){
        					$subGridAdapter = $adapters[$i];
        					break;
        				}
        			}
					$returnValue[$column->getId()]['subgrids'] = $subGridAdapter->getGridContainer()->getGrid()->getColumnsModel();
				}
			}

		}

        

        return (array) $returnValue;
    }

    /**
     * Short description of method setData
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array data
     * @return mixed
     */
    public function setData($data = array())
    {
        

    	//empty local data
    	$this->rows = array();
    	//fill the local data
		foreach($data as $rowId => $cells){
			if(is_array($cells)){
				$this->addRow($rowId, $cells);
			}else if(is_string($cells)){
				$this->addRow($cells);
			}
		}

        
    }

}

?>