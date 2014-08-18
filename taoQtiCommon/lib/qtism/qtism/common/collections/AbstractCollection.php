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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package qtism
 *  
 *
 */
namespace qtism\common\collections;

use qtism\common\Comparable;
use \InvalidArgumentException;
use \UnexpectedValueException;

/**
 * The AbstractCollection class is the base class of all collections.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
abstract class AbstractCollection implements \Countable, \Iterator, \ArrayAccess {
	
	/**
	 * A data place holder to actually store data for the collection.
	 * 
	 * @var array
	 */
	protected $dataPlaceHolder = array();
	
	/**
	 * Create a new instance of AbstractCollection.
	 * 
	 * @param array $array An array of data to be stored by the collection.
	 * @throws \InvalidArgumentException If the $array argument contains invalid datatypes, checked with the AbstractCollection::checkType() method.
	 */
	public function __construct(array $array = array()) {
		
		foreach ($array as $a) {
			$this->checkType($a);
			array_push($this->dataPlaceHolder, $a);
		}
		
		reset($this->dataPlaceHolder);
	}
	
	/**
	 * Set the data placeholder.
	 * 
	 * @param array $dataPlaceHolder An array.
	 */
	protected function setDataPlaceHolder(array &$dataPlaceHolder) {
		$this->dataPlaceHolder = $dataPlaceHolder;
	}
	
	/**
	 * Get the data placeholder.
	 * 
	 * @return array The data placeholder of the collection.
	 */
	protected function &getDataPlaceHolder() {
		return $this->dataPlaceHolder;
	}
	
	/**
	 * Check the type of a given $value that has to be stored by the collection.
	 * 
	 * @param mixed $value A given value.
	 * @throws \InvalidArgumentException If the datatype of $value is incorrect.
	 */
	abstract protected function checkType($value);
	
	/**
	 * Return the amout of values currently stored by the collection.
	 * 
	 * @return int The amount of values stored by the collection.
	 */
	public function count() {
		return count($this->dataPlaceHolder);
	}
	
	/**
	 * Return the current element of the collection while iterating.
	 * 
	 * @return mixed The current element.
	 */
	public function current() {
		// @todo find why sometimes, the current value is the placeholder itself!
		return current($this->dataPlaceHolder);
	}
	
	/**
	 * Move forward to the next element of the collection while iterating.
	 */
	public function next() {
		next($this->dataPlaceHolder);
	}
	
	/**
	 * Return the key of the current element of the collection while iterating.
	 * 
	 * @return mixed Depends on the implementation.
	 */
	public function key() {
		return key($this->dataPlaceHolder);
	}
	
	/**
	 * Check if the current position of the iterator is valid.
	 * 
	 * @return boolean true on success or false on failure.
	 */
	public function valid() {
		return key($this->dataPlaceHolder) !== null;
	}
	
	/**
	 * Rewind the iterator to the first element of the collection;
	 */
	public function rewind() {
		reset($this->dataPlaceHolder);
	}
	
	/**
	 * Wether a offset exists.
	 * 
	 * @param mixed $offset An offset to check for.
	 * @return Wether the offset exist.
	 */
	public function offsetExists($offset) {
		return isset($this->dataPlaceHolder[$offset]);
	}
	
	/**
	 * Offset to retrieve. If the $offset does not reference any value, a null
	 * value is returned.
	 * 
	 * @param mixed $offset The offset to retrieve.
	 * @return mixex The value at specified offset.
	 */
	public function offsetGet($offset) {
		return isset($this->dataPlaceHolder[$offset]) ? $this->dataPlaceHolder[$offset] : null;
	}
	
	/**
	 * Offset to set. If a value is already set for the given $offset, its value
	 * will be overriden with $value.
	 * 
	 * @param mixed $offset The offset to assign the value to.
	 * @param mixed $value The value to set.
	 * @throws InvalidArgumentException If $value has not a valid type regarding the implementation.
	 */
	public function offsetSet($offset, $value) {
		$this->checkType($value);
		
		if (is_null($offset)) {
			array_push($this->dataPlaceHolder, $value);
		}
		else {
			$this->dataPlaceHolder[$offset] = $value;
		}
	}
	
	public function offsetUnset($offset) {
		unset($this->dataPlaceHolder[$offset]);
	}
	
	/**
	 * Get a copy of the collection as an array. This method is implemented in order
	 * to implement ArrayObject in a near future.
	 * 
	 * @return array The collection as an array of data.
	 */
	public function getArrayCopy($preserveKeys = false) {
		return ($preserveKeys === true) ? $this->dataPlaceHolder : array_values($this->dataPlaceHolder);
	}
	
	/**
	 * Whether the collection contains a given $value. The comparison 
	 * is strict, using the === operator.
	 * 
	 * @param mixed $value A value.
	 * @return boolean Whether the collection contains $value.
	 */
	public function contains($value) {
		foreach (array_keys($this->dataPlaceHolder) as $key) {
			$data = $this->dataPlaceHolder[$key];
			if ($value === $data || ($data instanceof Comparable && $data->equals($value))) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Attach a given $object to the collection.
	 * 
	 * @param mixed $object An object.
	 * @throws InvalidArgumentException If $object is not an 'object' type or not compliant with the typing of the collection.
	 */
	public function attach($object) {
		$this->checkType($object);
		
		if (gettype($object) !== 'object') {
			$msg = "You can only attach 'objects' into an AbstractCollection, '" . gettype($object) . "' given";
			throw new InvalidArgumentException($msg);
		}
		else if (!$this->contains($object)) {
			$this->offsetSet(null, $object);
		}
	}
	
	/**
	 * Detach a given $object from the collection.
	 * 
	 * @param mixed $object An object.
	 * @throws InvalidArgumentException If $object is not an 'object' type or not compliant with the typing of the collection.
	 * @throws UnexpectedValueException If $object cannot be found in the collection.
	 */
	public function detach($object) {
		$this->checkType($object);
		
		if (gettype($object) !== 'object') {
			$msg = "You can only attach 'objects' into an AbstractCollection, '" . gettype($object) . "' given.";
			throw new InvalidArgumentException($msg);
		}
		
		foreach (array_keys($this->dataPlaceHolder) as $k) {
			if ($this->dataPlaceHolder[$k] === $object) {
				$this->offsetUnset($k);
				return;
			}
		}
		
		$msg = "The object you want to detach could not be found in the collection.";
		throw new UnexpectedValueException($msg);
	}
	
	/**
	 * Replace an $object in the collection by another $replacement $object.
	 * 
	 * @param mixed $object An object to be replaced.
	 * @param mixed $replacement An object to be used as a replacement.
	 * @throws InvalidArgumentException If $object or $replacement are not compliant with the current collection typing.
	 * @throws UnexpectedValueException If $object is not contained in the collection.
	 */
	public function replace($object, $replacement) {
		$this->checkType($object);
		$this->checkType($replacement);
		
		if (gettype($object) !== 'object') {
			$msg = "You can only attach 'objects' into an AbstractCollection, '" . gettype($object) . "' given.";
			throw new InvalidArgumentException($msg);
		}
		
		if (gettype($replacement) !== 'object') {
			$msg = "You can only attach 'objects' into an AbstractCollection, '" . gettype($replacement) . "' given.";
			throw new InvalidArgumentException($msg);
		}
		
		foreach (array_keys($this->dataPlaceHolder) as $k) {
			if ($this->dataPlaceHolder[$k] === $object) {
				$this->dataPlaceHolder[$k] = $replacement;
				return;
			}
		}
		
		$msg = "The object you want to replace could not be found.";
		throw new UnexpectedValueException($msg);
	}
	
	/**
	 * Remove $object from the collection. If $object is not in the collection,
	 * it remains as it is.
	 * 
	 * @param mixed $object The object to remove from the collection.
	 */
	public function remove($object) {
	    
	    foreach (array_keys($this->dataPlaceHolder) as $k) {
	        if ($this->dataPlaceHolder[$k] === $object) {
	            unset($this->dataPlaceHolder[$k]);
	            return;
	        }
	    }
	}
	
	/**
	 * Reset the collection to an empty one.
	 */
	public function reset() {
		$a = array();
		$this->dataPlaceHolder = $a;
	}
	
	/**
	 * Get the keys of the collection.
	 * 
	 * @return array An array of values which are the keys of the collection.
	 */
	public function getKeys() {
		return array_keys($this->dataPlaceHolder);
	}
	
	/**
	 * Merge the collection with another one.
	 * 
	 * @param AbstractCollection $collection
	 * @throws InvalidArgumentException If $collection is not a subclass of the target of the call.
	 */
	public function merge(AbstractCollection $collection) {
	    if (is_subclass_of($collection, get_class($this)) === true || get_class($collection) === get_class($this)) {
	        $newData = array_merge($this->dataPlaceHolder, $collection->getDataPlaceHolder());
	        $this->dataPlaceHolder = $newData;
	    }
	    else {
	        $msg = "Only collections with compliant types can be merged ";
	        $msg.= "('" . get_class($this) . "' vs '" . get_class($collection) . "').";
	        throw new InvalidArgumentException($msg);
	    }
	}
	
	/**
	 * Get the difference between this collection and another one.
	 * 
	 * @param AbstractCollection $collection
	 * @return AbstractCollection
	 */
	public function diff(AbstractCollection $collection) {
	    if (get_class($this) === get_class($collection)) {
	        $newData = array_diff($this->dataPlaceHolder, $collection->getDataPlaceHolder());
	        return new static($newData);
	    }
	    else {
	        $msg = "Difference may apply only on two collection of the same type.";
	        throw new InvalidArgumentException($msg);
	    }
	}
	
	/**
	 * Get the intersection between this collection and another one.
	 * 
	 * @param AbstractCollection $collection
	 * @return AbstractCollection
	 */
	public function intersect(AbstractCollection $collection) {
	    if (get_class($this) === get_class($collection)) {
	        $newData = array_intersect($this->dataPlaceHolder, $collection->getDataPlaceHolder());
	        return new static($newData);
	    }
	    else {
	        $msg = "Intersection may apply only on two collections of the same type.";
	        throw new InvalidArgumentException($msg);
	    }
	}
	
	/**
	 * Reset the keys of the collection. This method is similar
	 * in behaviour with PHP's array_values.
	 * 
	 */
	public function resetKeys() {
	    $newData = array_values($this->dataPlaceHolder);
	    $this->setDataPlaceHolder($newData);
	}
	
	public function __clone() {
		foreach ($this->dataPlaceHolder as $key => $value) {
			if (gettype($value) === 'object') {
				$this[$key] = clone $value;
			}
		}
	}
}