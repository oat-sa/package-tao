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
 * should inherit from standard collection provided in php
 *
 * @author patrick.plichart@tudor.lu
 * @package core
 * @subpackage kernel_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include common_Collection
 *
 * @author lionel.lecaque@tudor.lu
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 */
require_once('common/class.Collection.php');

/**
 * include core_kernel_classes_Container
 *
 * @author patrick.plichart@tudor.lu
 */
require_once('core/kernel/classes/class.Container.php');

/* user defined includes */
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:000000000000078B-includes begin
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:000000000000078B-includes end

/* user defined constants */
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:000000000000078B-constants begin
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:000000000000078B-constants end

/**
 * should inherit from standard collection provided in php
 *
 * @access public
 * @author patrick.plichart@tudor.lu
 * @package core
 * @subpackage kernel_classes
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
        // section 10-13-1--31-7253183:11993fa4132:-8000:0000000000000ACF begin
        parent::add($element);
        // section 10-13-1--31-7253183:11993fa4132:-8000:0000000000000ACF end
    }

    /**
     * Short description of method toHtml
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function toHtml()
    {
        $returnValue = (string) '';

        // section 10-13-1--31--3bf74db1:119c3d777ef:-8000:0000000000000B41 begin
        // section 10-13-1--31--3bf74db1:119c3d777ef:-8000:0000000000000B41 end

        return (string) $returnValue;
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

        // section 10-13-1--99-666b4abd:1205836518e:-8000:00000000000016DC begin
        $returnValue = new core_kernel_classes_ContainerCollection($this);     
        $returnValue->sequence = array_merge($this->sequence, $collection->sequence );      
        // section 10-13-1--99-666b4abd:1205836518e:-8000:00000000000016DC end

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

        // section 10-13-1--99-666b4abd:1205836518e:-8000:00000000000020E1 begin
         $returnValue = new core_kernel_classes_ContainerCollection(new common_Object(__METHOD__));
         $returnValue->sequence = array_uintersect($this->sequence, $collection->sequence, 'core_kernel_classes_ContainerComparator::compare');
        // section 10-13-1--99-666b4abd:1205836518e:-8000:00000000000020E1 end

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

        // section 10-13-1--99-666b4abd:1205836518e:-8000:00000000000020E4 begin
        $returnValue = -1;
        foreach($this->sequence as $index => $_resource){
        	if ($_resource instanceof  core_kernel_classes_Resource){
				if($resource->equals($_resource)){
					return $index;
				}
        	}
		}
        // section 10-13-1--99-666b4abd:1205836518e:-8000:00000000000020E4 end

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

        // section 10-13-1--99--150f74b9:12066be2698:-8000:000000000000172C begin
        $returnValue = 'Collection containning ' . $this->count() . ' elements' ;
        // section 10-13-1--99--150f74b9:12066be2698:-8000:000000000000172C end

        return (string) $returnValue;
    }

} /* end of class core_kernel_classes_ContainerCollection */

?>