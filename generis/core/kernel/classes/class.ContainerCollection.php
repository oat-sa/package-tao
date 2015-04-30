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
 * should inherit from standard collection provided in php
 *
 * @access public
 * @author patrick.plichart@tudor.lu
 * @package generis
 
 */
class core_kernel_classes_ContainerCollection
    extends common_Collection
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method add
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Object element
     * @return void
     */
    public function add( common_Object $element)
    {
        
        parent::add($element);
        
    }


    /**
     * Short description of method union
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Collection collection
     * @return core_kernel_classes_ContainerCollection
     */
    public function union( common_Collection $collection)
    {
        $returnValue = null;

        
        $returnValue = new core_kernel_classes_ContainerCollection($this);     
        $returnValue->sequence = array_merge($this->sequence, $collection->sequence );      
        

        return $returnValue;
    }

    /**
     * Short description of method intersect
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Collection collection
     * @return core_kernel_classes_ContainerCollection
     */
    public function intersect( common_Collection $collection)
    {
        $returnValue = null;

        
         $returnValue = new core_kernel_classes_ContainerCollection(new common_Object(__METHOD__));
         $returnValue->sequence = array_uintersect($this->sequence, $collection->sequence, 'core_kernel_classes_ContainerComparator::compare');
        

        return $returnValue;
    }

    /**
     * Short description of method indexOf
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Object resource
     * @return Integer
     */
    public function indexOf( common_Object $resource)
    {
        $returnValue = null;

        
        $returnValue = -1;
        foreach($this->sequence as $index => $_resource){
        	if ($_resource instanceof  core_kernel_classes_Resource){
				if($resource->equals($_resource)){
					return $index;
				}
        	}
		}
        

        return $returnValue;
    }

    /**
     * Short description of method __toString
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function __toString()
    {
        $returnValue = (string) '';

        
        $returnValue = 'Collection containning ' . $this->count() . ' elements' ;
        

        return (string) $returnValue;
    }

}