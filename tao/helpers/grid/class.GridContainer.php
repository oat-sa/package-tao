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
 * Short description of class tao_helpers_grid_GridContainer
 *
 * @abstract
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package tao
 
 */
abstract class tao_helpers_grid_GridContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute grid
     *
     * @access protected
     * @var Grid
     */
    protected $grid = null;

    /**
     * Short description of attribute grids
     *
     * @access protected
     * @var array
     */
    protected static $grids = array();

    /**
     * Short description of attribute options
     *
     * @access protected
     * @var array
     */
    protected $options = array();

    /**
     * Short description of attribute data
     *
     * @access protected
     * @var array
     */
    protected $data = array();

    /**
     * Short description of attribute excludedProperties
     *
     * @access protected
     * @var array
     */
    protected $excludedProperties = array();

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  array data
     * @param  array options
     * @return mixed
     */
    public function __construct($data = array(), $options = array())
    {
        
		
		$this->data = $data;
		$this->options = $options;
		$this->excludedProperties = (is_array($this->options) && isset($this->options['excludedProperties'])) ? $this->options['excludedProperties'] : array();
		$this->grid = new tao_helpers_grid_Grid($options);
		
		//init columns ...
		$this->initGrid();
		$this->initColumns();
		$this->initOptions($options);
		
        
    }

    /**
     * Short description of method __destruct
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return mixed
     */
    public function __destruct()
    {
        
		if(!is_null($this->grid)){
			//remove the refs of the contained grid
			
		}
        
    }

    /**
     * Short description of method getGrid
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return tao_helpers_grid_Grid
     */
    public function getGrid()
    {
        $returnValue = null;

        
		$returnValue = $this->grid;
        

        return $returnValue;
    }

    /**
     * Short description of method initGrid
     *
     * @access protected
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return boolean
     */
    protected function initGrid()
    {
        $returnValue = (bool) false;

        
		
		//set data if data given
		$returnValue = $this->grid->setData($this->data);
		
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method initColumns
     *
     * @abstract
     * @access protected
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return boolean
     */
    protected abstract function initColumns();

    /**
     * Short description of method toArray
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return array
     */
    public function toArray()
    {
        $returnValue = array();

        
		$returnValue = $this->grid->toArray();
        

        return (array) $returnValue;
    }

    /**
     * Short description of method initOptions
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return boolean
     */
    public function initOptions($options = array())
    {
        $returnValue = (bool) false;

        
        
        $columns = $this->grid->getColumns();
        if(isset($options['columns'])){
        	foreach($options['columns'] as $columnId=>$columnOptions){
				
				if(isset($columns[$columnId])){
					foreach($columnOptions as $optionsName=>$optionsValue){
						if($optionsName=='columns'){
							//if the options is columns, the options will be used to augment the subgrid model
							$columns = $this->grid->getColumns();
							$subGridAdapter = null;
							//get the last subgrid adapter which defines the column
							$adapters = $columns[$columnId]->getAdapters();
							$adaptersLength = count($adapters);
							for($i=$adaptersLength-1; $i>=0; $i--){
								if($adapters[$i] instanceof tao_helpers_grid_Cell_SubgridAdapter){
									$subGridAdapter = $adapters[$i];
									break;
								}
							}
							if(is_null($subGridAdapter)){
								throw new Exception(__('The column ').$columnId.__(' requires a subgrid adapter'));
							}
							//init options of the subgrid
							$subGridAdapter->getGridContainer()->initOptions($columnOptions);

							continue;
						}
						$columns[$columnId]->setOption($optionsName, $optionsValue);
					}
				}
				
        	}
        }
        

        return (bool) $returnValue;
    }

    /**
     * Short description of method __clone
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return mixed
     */
    public function __clone()
    {
        
		$this->grid = clone $this->grid;
        
    }

} /* end of abstract class tao_helpers_grid_GridContainer */

?>