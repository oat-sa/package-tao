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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * This class provides the services on the list management
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package tao
 
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
        
        
    	$this->parentListClass = new core_kernel_classes_Class(TAO_LIST_CLASS);
        
        
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

        
        
        $returnValue[] = new core_kernel_classes_Class(GENERIS_BOOLEAN); 
        
        foreach($this->parentListClass->getSubClasses(false) as $list){
        	$returnValue[] = $list;
        }
        
        
        
        

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

        
        
        foreach($this->getLists() as $list){
        	if($list->getUri() == $uri){
        		 $returnValue = $list;
        		 break;
        	}
        }
        
        

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

        
        
        if(!empty($uri)){
	        foreach($this->getListElements($listClass, false) as $element){   	
	           	if($element->getUri() == $uri){
	           		$returnValue = $element;
	           		break;
	           	}
			}
        }
        
        

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

    	if($sort){
        	$levelProperty = new core_kernel_classes_Property(TAO_LIST_LEVEL_PROP);
        	foreach ($listClass->getInstances(false) as $element) {
        	    $literal = $element->getOnePropertyValue($levelProperty);
        	    $level = is_null($literal) ? 0 : (string) $literal;
        	    while (isset($returnValue[$level])) {
        	        $level++;
        	    }
        	    $returnValue[$level] = $element;
        	}
			uksort($returnValue, 'strnatcasecmp');
    	}
    	else{
    		$returnValue = $listClass->getInstances(false);
    	}

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

        
        
        if(!is_null($listClass)){
        	foreach($this->getListElements($listClass) as $element){
        		$this->removeListElement($element);
        	}
        	$returnValue = $listClass->delete();
        }
        
        

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

        
        
		if(!is_null($element)){
			$returnValue = $element->delete();
        }
        
        

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

        
        
        if(empty($label)) {
        	$label = __('List') . ' ' . (count($this->getLists()) + 1);
        }
        $returnValue = $this->createSubClass($this->parentListClass, $label);
        
        

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

        
        
        if(!is_null($listClass)){
			$level = count($this->getListElements($listClass)) + 1;
        	if(empty($label)) {
	        	$label = __('Element') . ' ' . $level;
	        }
	        $returnValue = $this->createInstance($listClass, $label);
	        $this->bindProperties($returnValue, array(TAO_LIST_LEVEL_PROP => count($this->getListElements($listClass, false))));
        }
        

        return $returnValue;
    }

}

?>