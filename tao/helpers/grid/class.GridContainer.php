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
 * TAO - tao/helpers/grid/class.GridContainer.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 16.11.2011, 10:43:11 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package tao
 * @subpackage helpers_grid
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003370-includes begin
// section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003370-includes end

/* user defined constants */
// section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003370-constants begin
// section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003370-constants end

/**
 * Short description of class tao_helpers_grid_GridContainer
 *
 * @abstract
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package tao
 * @subpackage helpers_grid
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
        // section 127-0-1-1-6c609706:1337d294662:-8000:000000000000337D begin
		
		$this->data = $data;
		$this->options = $options;
		$this->excludedProperties = (is_array($this->options) && isset($this->options['excludedProperties'])) ? $this->options['excludedProperties'] : array();
		$this->grid = new tao_helpers_grid_Grid($options);
		
		//init columns ...
		$this->initGrid();
		$this->initColumns();
		$this->initOptions($options);
		
        // section 127-0-1-1-6c609706:1337d294662:-8000:000000000000337D end
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
        // section 127-0-1-1-6c609706:1337d294662:-8000:000000000000338F begin
		if(!is_null($this->grid)){
			//remove the refs of the contained grid
			
		}
        // section 127-0-1-1-6c609706:1337d294662:-8000:000000000000338F end
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

        // section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003392 begin
		$returnValue = $this->grid;
        // section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003392 end

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

        // section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003395 begin
		
		//set data if data given
		$returnValue = $this->grid->setData($this->data);
		
        // section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003395 end

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

        // section 127-0-1-1-6c609706:1337d294662:-8000:000000000000339E begin
		$returnValue = $this->grid->toArray();
        // section 127-0-1-1-6c609706:1337d294662:-8000:000000000000339E end

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

        // section 127-0-1-1--17a51322:133a2840e6a:-8000:00000000000033C1 begin
        
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
        // section 127-0-1-1--17a51322:133a2840e6a:-8000:00000000000033C1 end

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
        // section 127-0-1-1-1e2308e2:133abaa95c2:-8000:0000000000003408 begin
		$this->grid = clone $this->grid;
        // section 127-0-1-1-1e2308e2:133abaa95c2:-8000:0000000000003408 end
    }

} /* end of abstract class tao_helpers_grid_GridContainer */

?>