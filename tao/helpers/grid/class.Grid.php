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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - tao/helpers/grid/class.Grid.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 15.11.2011, 14:54:48 with ArgoUML PHP module
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package tao
 * @subpackage helpers_grid
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1--17d909f0:1336f22bf6e:-8000:0000000000003290-includes begin
// section 127-0-1-1--17d909f0:1336f22bf6e:-8000:0000000000003290-includes end

/* user defined constants */
// section 127-0-1-1--17d909f0:1336f22bf6e:-8000:0000000000003290-constants begin
// section 127-0-1-1--17d909f0:1336f22bf6e:-8000:0000000000003290-constants end

/**
 * Short description of class tao_helpers_grid_Grid
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package tao
 * @subpackage helpers_grid
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

        // section 127-0-1-1--509f1d4b:133a6f9e0dc:-8000:0000000000003401 begin

        // section 127-0-1-1--509f1d4b:133a6f9e0dc:-8000:0000000000003401 end

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
        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032A5 begin
		$this->options = $options;
        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032A5 end
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

        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032A7 begin
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
        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032A7 end

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

        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032AB begin
		unset($this->columns[$id]);
		$returnValue = true;
        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032AB end

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

        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032B2 begin
		if(!$replace && isset($this->rows[$id])){
			throw new common_Exception('the row with the id '.$id.' already exists');
		}else{
			$this->rows[$id] = $cells;
			//@TODO: implement a sort funciton?

			$returnValue = true;
		}
        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032B2 end

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

        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032B5 begin
		unset($this->rows[$id]);
		$returnValue = true;
        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032B5 end

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

        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032CD begin
		//TODO: for creating row and column if not exists?
		if(isset($this->columns[$columnId])){
			if(isset($this->rows[$rowId])){
				$this->rows[$rowId][$columnId] = $content;
				$returnValue = true;
			}
		}
        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032CD end

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

        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032D2 begin
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
        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032D2 end

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

        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032E0 begin

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
        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032E0 end

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

        // section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003316 begin
        // section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003316 end

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

        // section 127-0-1-1--3aed8f55:13388eba496:-8000:0000000000003366 begin
		$returnValue = $this->columns;
        // section 127-0-1-1--3aed8f55:13388eba496:-8000:0000000000003366 end

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

        // section 127-0-1-1-3d16f06:13388f94a40:-8000:000000000000336B begin
		if(isset($this->columns[$id]) && $this->columns[$id] instanceof tao_helpers_grid_Column){
			$returnValue = $this->columns[$id];
		}
        // section 127-0-1-1-3d16f06:13388f94a40:-8000:000000000000336B end

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

        // section 127-0-1-1-72bb438:1338cba5f73:-8000:0000000000003370 begin
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

        // section 127-0-1-1-72bb438:1338cba5f73:-8000:0000000000003370 end

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
        // section 127-0-1-1--509f1d4b:133a6f9e0dc:-8000:00000000000033F7 begin

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

        // section 127-0-1-1--509f1d4b:133a6f9e0dc:-8000:00000000000033F7 end
    }

} /* end of class tao_helpers_grid_Grid */

?>