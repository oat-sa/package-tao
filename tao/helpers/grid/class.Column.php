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
 * Short description of class tao_helpers_grid_Column
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package tao
 
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
        
		$this->id = $id;
		$this->title = $title;
		$this->options = $options;
        
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

        
		$this->type = $type;
		$returnValue = true;
        

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

        
		$returnValue = $this->type;
        

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

        
		$this->title = $title;
		$returnValue = true;
        

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

        
		$returnValue = $this->title;
        

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

        
		$returnValue = $this->id;
        

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

        
		if(!is_null($adapter)){
			$this->adapters[] = $adapter;
			$returnValue = true;
		}
        

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

        
		$adapterClass = empty($type)?'tao_helpers_grid_Cell_Adapter':$type;
		foreach($this->adapters as $adapter){
			if($adapter instanceof $adapterClass){
				$returnValue = true;
				break;
			}
		}
		
        

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

        
		foreach($this->adapters as $adapter){
			if($adapter instanceof $type){
				$returnValue = $adapter;
				break;
			}
		}
        

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

        
        $returnValue = $this->options;
        

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
        
		$this->options = array_merge($this->options, $options);
        
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

        
        $returnValue = $this->options[$name];
        

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
        
        $this->options[$name] = $value;
        
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

        
		$returnValue = $this->adapters;
        

        return (array) $returnValue;
    }

}

?>