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
 * Generis Object Oriented API - common\class.Collection.php
 *
 * Object Collection
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 24.03.2010, 14:38:36 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Lionel Lecaque <lionel.lecaque@tudor.lu>
 * @package generis
 * @subpacakge common
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 */

class common_Collection
    extends common_Object implements IteratorAggregate
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute sequence
     *
     * @access public
     * @var array
     */
    public $sequence = array();

    /**
     * Short description of attribute container
     *
     * @access public
     * @var Object
     */
    public $container = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Lionel Lecaque <lionel.lecaque@tudor.lu>
     * @param  Object container
     * @param  string debug
     * @return void
     */
    public function __construct( common_Object $container, $debug = '')
    {
        // section 10-13-1--99--4fdec042:11a2a3b44dc:-8000:00000000000010A2 begin
        $this->sequence = array();
		$this->container = $container;	
        // section 10-13-1--99--4fdec042:11a2a3b44dc:-8000:00000000000010A2 end
    }

    /**
     * return the number of node of the collection (only this level)
     *
     * @access public
     * @author Lionel Lecaque <lionel.lecaque@tudor.lu>
     * @return int
     */
    public function count()
    {
        $returnValue = (int) 0;

        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BB7 begin
        $returnValue = count($this->sequence);
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BB7 end

        return (int) $returnValue;
    }

    /**
     * return the index of the node array at which the given node resides
     *
     * @access public
     * @author Lionel Lecaque <lionel.lecaque@tudor.lu>
     * @param  Object object
     * @return int
     */
    public function indexOf( common_Object $object)
    {
        $returnValue = (int) 0;

        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BB9 begin
    	$returnValue = -1;
        foreach($this->sequence as $index => $_object){
			if($object === $_object){
				return $index;
			}
		}
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BB9 end

        return (int) $returnValue;
    }

    /**
     * Retrun the node at the given index
     *
     * @access public
     * @author Lionel Lecaque <lionel.lecaque@tudor.lu>
     * @param  int index
     * @return common_Object
     */
    public function get($index)
    {
        $returnValue = null;

        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BBC begin
			
		$returnValue = isset($this->sequence[$index]) ? $this->sequence[$index] : null;
		if($returnValue == null) {
			throw new common_Exception('index is out of range');
		}
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BBC end

        return $returnValue;
    }

    /**
     * Short description of method isEmpty
     *
     * @access public
     * @author Lionel Lecaque <lionel.lecaque@tudor.lu>
     * @return boolean
     */
    public function isEmpty()
    {
        $returnValue = (bool) false;

        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BC8 begin
        $returnValue = (count($this->sequence) == 0);
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BC8 end

        return (bool) $returnValue;
    }

    /**
     * Implementation of ArrayAccess:offsetSet()
     *
     * @access public
     * @author Lionel Lecaque <lionel.lecaque@tudor.lu>
     * @param  Object key
     * @param  Object value
     * @return void
     */
    public function offsetSet( common_Object $key,  common_Object $value)
    {
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BCA begin
        $this->sequence[$key] = $value;
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BCA end
    }

    /**
     * Implementation of ArrayAccess:offsetGet()
     *
     * @access public
     * @author Lionel Lecaque <lionel.lecaque@tudor.lu>
     * @param  Object key
     * @return common_Object
     */
    public function offsetGet( common_Object $key)
    {
        $returnValue = null;

        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BCE begin
        $returnValue = $this->sequence[$key];
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BCE end

        return $returnValue;
    }

    /**
     * Implementation of ArrayAccess:offsetUnset()
     *
     * @access public
     * @author Lionel Lecaque <lionel.lecaque@tudor.lu>
     * @param  Object key
     * @return void
     */
    public function offsetUnset( common_Object $key)
    {
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BD1 begin
        unset($this->sequence[$key]);
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BD1 end
    }

    /**
     * Implementation of ArrayAccess:offsetExists()
     *
     * @access public
     * @author Lionel Lecaque <lionel.lecaque@tudor.lu>
     * @param  Object key
     * @return boolean
     */
    public function offsetExists( common_Object $key)
    {
        $returnValue = (bool) false;

        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BD4 begin
        $returnValue = isset($this->sequence[$key]);
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BD4 end

        return (bool) $returnValue;
    }

    /**
     * Implementation of IteratorAggregate::getIterator()
     *
     * @access public
     * @author Lionel Lecaque <lionel.lecaque@tudor.lu>
     * @return mixed
     */
    public function getIterator()
    {
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BD6 begin
         return new ArrayIterator($this->sequence);
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BD6 end
    }

    /**
     * Add a node to the collection
     *
     * @access public
     * @author Lionel Lecaque <lionel.lecaque@tudor.lu>
     * @param  Object node
     * @return mixed
     */
    public function add( common_Object $node)
    {
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BF6 begin
		$this->sequence[] = $node;
		$returnValue = $node;
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BF6 end
    }

    /**
     * Remove the node from the collection
     *
     * @access public
     * @author Lionel Lecaque <lionel.lecaque@tudor.lu>
     * @param  Object object
     * @return boolean
     */
    public function remove( common_Object $object)
    {
        $returnValue = (bool) false;

        // section 10-13-1--99-2f1559da:11a15934f36:-8000:00000000000013CC begin
        foreach($this->sequence as $index => $_node){
			if($_node === $object){
				unset($this->sequence[$index]);
				$this->sequence = array_values($this->sequence);
				return true;
			}		
		}
		return false;
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:00000000000013CC end

        return (bool) $returnValue;
    }

    /**
     * Short description of method union
     *
     * @access public
     * @author Lionel Lecaque <lionel.lecaque@tudor.lu>
     * @param  Collection collection
     * @return common_Collection
     */
    public function union( common_Collection $collection)
    {
        $returnValue = null;

        // section 10-13-1--99--1201ed7f:11c6b266eba:-8000:0000000000000EA4 begin
        $returnValue = new common_Collection($this);     
        $returnValue->sequence = array_merge($this->sequence, $collection->sequence );      
        // section 10-13-1--99--1201ed7f:11c6b266eba:-8000:0000000000000EA4 end

        return $returnValue;
    }

    /**
     * Short description of method intersect
     *
     * @access public
     * @author Lionel Lecaque <lionel.lecaque@tudor.lu>
     * @param  Collection collection
     * @return common_Collection
     */
    public function intersect( common_Collection $collection)
    {
        $returnValue = null;

        // section 10-13-1--99--1201ed7f:11c6b266eba:-8000:0000000000000EA7 begin
         $returnValue = new common_Collection(new common_Object(__METHOD__));
         $returnValue->sequence = array_uintersect($this->sequence, $collection->sequence, 'core_kernel_classes_ContainerComparator::compare');
        // section 10-13-1--99--1201ed7f:11c6b266eba:-8000:0000000000000EA7 end

        return $returnValue;
    }

    /**
     * Short description of method toArray
     *
     * @access public
     * @author Lionel Lecaque <lionel.lecaque@tudor.lu>
     * @return array
     */
    public function toArray()
    {
        $returnValue = array();

        // section -87--2--3--76--8049d3c:12424ebfe97:-8000:00000000000017D1 begin
        foreach ($this->getIterator() as $it){
        	$returnValue[] = $it;
        }
        // section -87--2--3--76--8049d3c:12424ebfe97:-8000:00000000000017D1 end

        return (array) $returnValue;
    }

} /* end of class common_Collection */

?>