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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * This class provides the services on the list management
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every servcie instances.
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/models/classes/class.GenerisService.php');

/* user defined includes */
// section 127-0-1-1-68d242d4:127aa5da21a:-8000:000000000000234A-includes begin
// section 127-0-1-1-68d242d4:127aa5da21a:-8000:000000000000234A-includes end

/* user defined constants */
// section 127-0-1-1-68d242d4:127aa5da21a:-8000:000000000000234A-constants begin
// section 127-0-1-1-68d242d4:127aa5da21a:-8000:000000000000234A-constants end

/**
 * This class provides the services on the list management
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 * @subpackage models_classes
 */
class tao_models_classes_ListService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * the list parent class
     *
     * @access protected
     * @var Class
     */
    protected $parentListClass = null;

    // --- OPERATIONS ---

    /**
     * initialize the service
     *
     * @access protected
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    protected function __construct()
    {
        // section 127-0-1-1-3fbbe8f5:127aa7fc0e0:-8000:0000000000002364 begin
        
    	$this->parentListClass = new core_kernel_classes_Class(TAO_LIST_CLASS);
        
        // section 127-0-1-1-3fbbe8f5:127aa7fc0e0:-8000:0000000000002364 end
    }

    /**
     * get all the lists class
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getLists()
    {
        $returnValue = array();

        // section 127-0-1-1-3fbbe8f5:127aa7fc0e0:-8000:000000000000234C begin
        
        $returnValue[] = new core_kernel_classes_Class(GENERIS_BOOLEAN); 
        
        foreach($this->parentListClass->getSubClasses(false) as $list){
        	$returnValue[] = $list;
        }
        
        
        
        // section 127-0-1-1-3fbbe8f5:127aa7fc0e0:-8000:000000000000234C end

        return (array) $returnValue;
    }

    /**
     * Get a list class from the uri in parameter
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string uri
     * @return core_kernel_classes_Class
     */
    public function getList($uri)
    {
        $returnValue = null;

        // section 127-0-1-1-7add8745:127b99f9642:-8000:0000000000002388 begin
        
        foreach($this->getLists() as $list){
        	if($list->getUri() == $uri){
        		 $returnValue = $list;
        		 break;
        	}
        }
        
        // section 127-0-1-1-7add8745:127b99f9642:-8000:0000000000002388 end

        return $returnValue;
    }

    /**
     * get the element of the list defined by listClass and identified by the
     * in parameter
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class listClass
     * @param  string uri
     * @return core_kernel_classes_Resource
     */
    public function getListElement( core_kernel_classes_Class $listClass, $uri)
    {
        $returnValue = null;

        // section 127-0-1-1--4fd18cbc:1281f7e7c81:-8000:00000000000023CA begin
        
        if(!empty($uri)){
	        foreach($this->getListElements($listClass, false) as $element){   	
	           	if($element->getUri() == $uri){
	           		$returnValue = $element;
	           		break;
	           	}
			}
        }
        
        // section 127-0-1-1--4fd18cbc:1281f7e7c81:-8000:00000000000023CA end

        return $returnValue;
    }

    /**
     * get all the elements of the list
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class listClass
     * @param  boolean sort
     * @return array
     */
    public function getListElements( core_kernel_classes_Class $listClass, $sort = true)
    {
        $returnValue = array();

        // section 127-0-1-1-3fbbe8f5:127aa7fc0e0:-8000:0000000000002359 begin
        
        if(!is_null($listClass)){

        	if($sort){
	        	$levelProperty = new core_kernel_classes_Property(TAO_LIST_LEVEL_PROP);
	        	$elements = $listClass->getInstances(false);
	        	$secureMax = (count($elements) + 1);
	        	foreach($elements as $listElement){
	        		$level = '';
	        		try{
	        		 	$level = trim($listElement->getUniquePropertyValue($levelProperty));
	        		}
	        		catch(common_Exception $ce){
	        		}
	        		$i = 0;
	        	 	while( (array_key_exists($level, $returnValue) || empty($level)) && $i < $secureMax){
						if(count($returnValue) == 0){
							$level = "1";
							break;
						}
	        	 		$keys = array_keys($returnValue);
	        	 		$level = max($keys) + 1;
	        	 		$i++;
	        	 	}
	        	 	$returnValue[(string)$level] = $listElement;
	        		
	        	}
				uksort($returnValue, 'strnatcasecmp');
        	}
        	else{
        		$returnValue = $listClass->getInstances(false);
        	}
        }
        
        // section 127-0-1-1-3fbbe8f5:127aa7fc0e0:-8000:0000000000002359 end

        return (array) $returnValue;
    }

    /**
     * remove a list and it's elements
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class listClass
     * @return boolean
     */
    public function removeList( core_kernel_classes_Class $listClass)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-3fbbe8f5:127aa7fc0e0:-8000:0000000000002366 begin
        
        if(!is_null($listClass)){
        	foreach($this->getListElements($listClass) as $element){
        		$this->removeListElement($element);
        	}
        	$returnValue = $listClass->delete();
        }
        
        // section 127-0-1-1-3fbbe8f5:127aa7fc0e0:-8000:0000000000002366 end

        return (bool) $returnValue;
    }

    /**
     * remove a list element
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource element
     * @return boolean
     */
    public function removeListElement( core_kernel_classes_Resource $element)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-3fbbe8f5:127aa7fc0e0:-8000:0000000000002369 begin
        
		if(!is_null($element)){
			$returnValue = $element->delete();
        }
        
        // section 127-0-1-1-3fbbe8f5:127aa7fc0e0:-8000:0000000000002369 end

        return (bool) $returnValue;
    }

    /**
     * create a new list class
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string label
     * @return core_kernel_classes_Class
     */
    public function createList($label = '')
    {
        $returnValue = null;

        // section 127-0-1-1-3fbbe8f5:127aa7fc0e0:-8000:000000000000236C begin
        
        if(empty($label)) {
        	$label = __('List') . ' ' . (count($this->getLists()) + 1);
        }
        $returnValue = $this->createSubClass($this->parentListClass, $label);
        
        // section 127-0-1-1-3fbbe8f5:127aa7fc0e0:-8000:000000000000236C end

        return $returnValue;
    }

    /**
     * add a new element to a list
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class listClass
     * @param  string label
     * @return core_kernel_classes_Resource
     */
    public function createListElement( core_kernel_classes_Class $listClass, $label = '')
    {
        $returnValue = null;

        // section 127-0-1-1-3fbbe8f5:127aa7fc0e0:-8000:0000000000002374 begin
        
        if(!is_null($listClass)){
			$level = count($this->getListElements($listClass)) + 1;
        	if(empty($label)) {
	        	$label = __('Element') . ' ' . $level;
	        }
	        $returnValue = $this->createInstance($listClass, $label);
	        $this->bindProperties($returnValue, array(TAO_LIST_LEVEL_PROP => count($this->getListElements($listClass, false))));
        }
        // section 127-0-1-1-3fbbe8f5:127aa7fc0e0:-8000:0000000000002374 end

        return $returnValue;
    }

} /* end of class tao_models_classes_ListService */

?>