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
 * TAO - tao/helpers/grid/class.Column.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 15.11.2011, 12:15:38 with ArgoUML PHP module 
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
// section 127-0-1-1--17d909f0:1336f22bf6e:-8000:0000000000003294-includes begin
// section 127-0-1-1--17d909f0:1336f22bf6e:-8000:0000000000003294-includes end

/* user defined constants */
// section 127-0-1-1--17d909f0:1336f22bf6e:-8000:0000000000003294-constants begin
// section 127-0-1-1--17d909f0:1336f22bf6e:-8000:0000000000003294-constants end

/**
 * Short description of class tao_helpers_grid_Column
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package tao
 * @subpackage helpers_grid
 */
class tao_helpers_grid_Column
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute id
     *
     * @access protected
     * @var string
     */
    protected $id = '';

    /**
     * Short description of attribute title
     *
     * @access protected
     * @var string
     */
    protected $title = '';

    /**
     * Short description of attribute type
     *
     * @access protected
     * @var string
     */
    protected $type = '';

    /**
     * Short description of attribute order
     *
     * @access protected
     * @var int
     */
    protected $order = 0;

    /**
     * Short description of attribute adapters
     *
     * @access protected
     * @var array
     */
    protected $adapters = array();

    /**
     * Short description of attribute options
     *
     * @access public
     * @var array
     */
    public $options = array();

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string id
     * @param  string title
     * @param  array options
     * @return mixed
     */
    public function __construct($id, $title, $options = array())
    {
        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:0000000000003295 begin
		$this->id = $id;
		$this->title = $title;
		$this->options = $options;
        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:0000000000003295 end
    }

    /**
     * Short description of method setType
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string type
     * @return boolean
     */
    public function setType($type)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032C0 begin
		$this->type = $type;
		$returnValue = true;
        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032C0 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getType
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return string
     */
    public function getType()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032C4 begin
		$returnValue = $this->type;
        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032C4 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setTitle
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string title
     * @return boolean
     */
    public function setTitle($title)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032C6 begin
		$this->title = $title;
		$returnValue = true;
        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032C6 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getTitle
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return string
     */
    public function getTitle()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032C9 begin
		$returnValue = $this->title;
        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032C9 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getId
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return string
     */
    public function getId()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032CB begin
		$returnValue = $this->id;
        // section 127-0-1-1--17d909f0:1336f22bf6e:-8000:00000000000032CB end

        return (string) $returnValue;
    }

    /**
     * Short description of method setAdapter
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Adapter adapter
     * @return boolean
     */
    public function setAdapter( tao_helpers_grid_Cell_Adapter $adapter)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003312 begin
		if(!is_null($adapter)){
			$this->adapters[] = $adapter;
			$returnValue = true;
		}
        // section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003312 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method hasAdapter
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string type (to check if the adaptor is of a certain type)
     * @return boolean
     */
    public function hasAdapter($type = '')
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003318 begin
		$adapterClass = empty($type)?'tao_helpers_grid_Cell_Adapter':$type;
		foreach($this->adapters as $adapter){
			if($adapter instanceof $adapterClass){
				$returnValue = true;
				break;
			}
		}
		
        // section 127-0-1-1-6c609706:1337d294662:-8000:0000000000003318 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getAdaptersData
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string rowId
     * @param  string cellValue (tao_helpers_grid_Grid, tao_helpers_grid_GridContainer or string)
     * @param  bool evaluateData
     * @return mixed
     */
    public function getAdaptersData($rowId, $cellValue = null, $evaluateData = true)
    {
        $returnValue = null;

        // section 127-0-1-1-6c609706:1337d294662:-8000:000000000000331A begin
		
		if($this->hasAdapter()){
			foreach($this->adapters as $adapter){
				if($adapter instanceof tao_helpers_grid_Cell_Adapter){
					$cellValue = $adapter->getValue($rowId, $this->id, $cellValue);
				}
			}
			$returnValue = $cellValue;
		}
		
		if($evaluateData){
			//allow returning to type "string" or "Grid" only
			if ($returnValue instanceof tao_helpers_grid_Grid) {
				$returnValue = $returnValue->toArray();
			} else if ($returnValue instanceof tao_helpers_grid_GridContainer) {
				$returnValue = $returnValue->toArray();
			} else if(is_array($returnValue)){
				//ok; authorize array type
			}else{
				$returnValue = (string) $returnValue;
			}
		}
		
        // section 127-0-1-1-6c609706:1337d294662:-8000:000000000000331A end

        return $returnValue;
    }

    /**
     * Short description of method getAdapter
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  type
     * @return tao_helpers_transfert_Adapter
     */
    public function getAdapter($type)
    {
        $returnValue = null;

        // section 127-0-1-1-72bb438:1338cba5f73:-8000:00000000000033D6 begin
		foreach($this->adapters as $adapter){
			if($adapter instanceof $type){
				$returnValue = $adapter;
				break;
			}
		}
        // section 127-0-1-1-72bb438:1338cba5f73:-8000:00000000000033D6 end

        return $returnValue;
    }

    /**
     * Short description of method getOptions
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return array
     */
    public function getOptions()
    {
        $returnValue = array();

        // section 127-0-1-1--17a51322:133a2840e6a:-8000:00000000000033BD begin
        $returnValue = $this->options;
        // section 127-0-1-1--17a51322:133a2840e6a:-8000:00000000000033BD end

        return (array) $returnValue;
    }

    /**
     * Short description of method setOptions
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  array options
     */
    public function setOptions($options)
    {
        // section 127-0-1-1--17a51322:133a2840e6a:-8000:00000000000033BF begin
		$this->options = array_merge($this->options, $options);
        // section 127-0-1-1--17a51322:133a2840e6a:-8000:00000000000033BF end
    }

    /**
     * Short description of method getOption
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  name
     * @return core_kernel_classes_object
     */
    public function getOption($name)
    {
        $returnValue = null;

        // section 127-0-1-1--17a51322:133a2840e6a:-8000:00000000000033C3 begin
        $returnValue = $this->options[$name];
        // section 127-0-1-1--17a51322:133a2840e6a:-8000:00000000000033C3 end

        return $returnValue;
    }

    /**
     * Short description of method setOption
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  name
     * @param  value
     */
    public function setOption($name, $value)
    {
        // section 127-0-1-1--17a51322:133a2840e6a:-8000:00000000000033C6 begin
        $this->options[$name] = $value;
        // section 127-0-1-1--17a51322:133a2840e6a:-8000:00000000000033C6 end
    }

    /**
     * Short description of method removeAdapter
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string type
     * @return boolean
     */
    public function removeAdapter($type = '')
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-648bde17:133a6d54b9f:-8000:00000000000033ED begin
        // section 127-0-1-1-648bde17:133a6d54b9f:-8000:00000000000033ED end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getAdapters
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return array
     */
    public function getAdapters()
    {
        $returnValue = array();

        // section 127-0-1-1-648bde17:133a6d54b9f:-8000:00000000000033F6 begin
		$returnValue = $this->adapters;
        // section 127-0-1-1-648bde17:133a6d54b9f:-8000:00000000000033F6 end

        return (array) $returnValue;
    }

} /* end of class tao_helpers_grid_Column */

?>