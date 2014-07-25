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
namespace qtism\runtime\expressions\operators;

use qtism\common\datatypes\Boolean;

use qtism\common\datatypes\String;

use qtism\common\datatypes\Float;
use qtism\common\datatypes\Integer;
use qtism\common\collections\Stack;
use qtism\common\datatypes\Point;
use qtism\common\datatypes\Duration;
use qtism\common\enums\Cardinality;
use qtism\common\enums\BaseType;
use qtism\runtime\common\Container;
use qtism\common\collections\AbstractCollection;
use qtism\runtime\common\RecordContainer;
use qtism\runtime\common\OrderedContainer;
use qtism\runtime\common\MultipleContainer;
use qtism\runtime\common\Utils as RuntimeUtils;
use InvalidArgumentException as InvalidArgumentException;

/**
 * A collection that aims at storing operands (QTI Runtime compliant values).
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class OperandsCollection extends AbstractCollection implements Stack {

	/**
	 * Check if $value is a QTI Runtime compliant value.
	 * 
	 * @throws InvalidArgumentException If $value is not a QTI Runtime compliant value.
	 */
	protected function checkType($value) {
		if (RuntimeUtils::isRuntimeCompliant($value)) {
			return;
		}
		else if ($value instanceof MultipleContainer || $value instanceof OrderedContainer || $value instanceof RecordContainer) {
			return;
		}
		else {
			$value = (gettype($value) === 'object') ? get_class($value) : $value;
			$msg = "The OperandsCollection only accepts QTI Runtime compliant values, '" . $value . "' given.";
			throw new InvalidArgumentException($msg); 
		}
	}
	
	/**
	 * Wether the collection contains a QTI Runtime compliant value which is
	 * considered to be NULL.
	 * 
	 * * If the collection of operands is empty, true is returned.
	 * * If the collection of operands contains null, an empty container, or an empty string, true is returned.
	 * * In any other case, false is returned.
	 * 
	 * @return boolean
	 */
	public function containsNull() {
		foreach (array_keys($this->getDataPlaceHolder()) as $key) {
			$v = $this[$key];
			if ($v instanceof Container && $v->isNull()) {
				return true;
			}
			else if (($v instanceof String && $v->getValue() === '') || is_null($v)) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Whether the collection is exclusively composed of numeric values: primitive
	 * or Containers. Please note that:
	 * 
	 * * A primitive with the NULL value is not considered numeric.
	 * * Only float and integer primitive are considered numeric.
	 * * An empty Multiple/OrderedContainer with baseType integer or float is not considered numeric.
	 * * If the collection contains a container with cardinality RECORD, it is not considered exclusively numeric.
	 * * If the the current OperandsCollection is empty, false is returned.
	 * 
	 * @return boolean.
	 */
	public function exclusivelyNumeric() {
		
		if (count($this) === 0) {
			return false;
		}
		
		foreach (array_keys($this->getDataPlaceHolder()) as $key) {
			$v = $this[$key];
			if (($v instanceof MultipleContainer || $v instanceof OrderedContainer) && ($v->isNull() || ($v->getBaseType() !== BaseType::FLOAT && $v->getBaseType() !== BaseType::INTEGER))) {
				return false;
			}
			else if (!$v instanceof Integer && !$v instanceof Float && !$v instanceof MultipleContainer && !$v instanceof OrderedContainer) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Wether the collection contains exclusively boolean values or containers.
	 * 
	 * * If the collection of operands is empty, false is returned.
	 * * If the collection of operands contains a NULL value or a NULL container, false is returned.
	 * * If the collection of operands contains a value or container which is not boolean, false is returned.
	 * * If the collection of operands contains a RECORD container, false is returned, because records are not typed.
	 * 
	 * @return boolean
	 */
	public function exclusivelyBoolean() {
		
		if (count($this) === 0) {
			return false;
		}
		
		foreach (array_keys($this->getDataPlaceHolder()) as $key) {
			$v = $this[$key];
			if (($v instanceof MultipleContainer || $v instanceof OrderedContainer) && ($v->isNull() || $v->getBaseType() !== BaseType::BOOLEAN)) {
				return false;
			}
			else if (!$v instanceof Boolean && !$v instanceof MultipleContainer && !$v instanceof OrderedContainer) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Wether the collection contains exclusively single cardinality values. If the container
	 * is empty or contains a null value, false is returned.
	 * 
	 * @return boolean
	 */
	public function exclusivelySingle() {
		
		if (count($this) === 0) {
			return false;
		}
		
		foreach (array_keys($this->getDataPlaceHolder()) as $key) {
			$v = $this[$key];
			if (is_null($v) || $v instanceof Container) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Whether the collection is exclusively composed of string values: primitive or Containers.
	 * Please note that:
	 * 
	 * * A primitive with the NULL value is not considered as a string.
	 * * An empty string is considered to be NULL and then not considered a valid string as per QTI 2.1 specification.
	 * * An empty Multiple/OrderedContainer with baseType string is not considered to contain strings.
	 * * If the collection contains a container with cardinality RECORD, it is not considered exclusively string.
	 * * If the the current OperandsCollection is empty, false is returned.
	 * 
	 * @return boolean
	 */
	public function exclusivelyString() {
		if (count($this) === 0) {
			return false;
		}
		
		foreach (array_keys($this->getDataPlaceHolder()) as $key) {
			$v = $this[$key];
			if (($v instanceof MultipleContainer || $v instanceof OrderedContainer) && ($v->isNull() || $v->getBaseType() !== BaseType::STRING)) {
				return false;
			}
			else if (!$v instanceof MultipleContainer && !$v instanceof OrderedContainer && (!$v instanceof String || $v->getValue() === '')) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Whether the collection contains only MultipleContainer OR OrderedContainer.
	 * 
	 * @return boolean
	 */
	public function exclusivelyMultipleOrOrdered() {
		
		if (count($this) === 0) {
			return false;
		}
		
		foreach (array_keys($this->getDataPlaceHolder()) as $key) {
			$v = $this[$key];
			if (!$v instanceof MultipleContainer && !$v instanceof OrderedContainer) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Whether the collection is exclusively composed of integer values: primitive
	 * or Containers. Please note that:
	 * 
	 * * A primitive with the NULL value is not considered as an integer.
	 * * Only integer primitives and non-NULL Multiple/OrderedContainer objects are considered valid integers.
	 * * If the the current OperandsCollection is empty, false is returned.
	 * 
	 * @return boolean.
	 */
	public function exclusivelyInteger() {
		if (count($this) === 0) {
			return false;
		}
		
		foreach (array_keys($this->getDataPlaceHolder()) as $key) {
			$v = $this[$key];
			if (($v instanceof MultipleContainer || $v instanceof OrderedContainer) && ($v->isNull() || $v->getBaseType() !== BaseType::INTEGER)) {
				return false;
			}
			else if (!$v instanceof Integer && !$v instanceof MultipleContainer && !$v instanceof OrderedContainer) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Wether the collection contains only Single primitive values or MultipleContainer objects.
	 * 
	 * * If the collection of operands is empty, false is returned.
	 * * If the collection of operands contains a RecordContainer object, false is returned.
	 * * If the collection of operands contains an OrderedContainer object, false is returned.
	 * * In any other case, true is returned.
	 * 
	 * @return boolean
	 */
	public function exclusivelySingleOrMultiple() {
		if (count($this) === 0) {
			return false;
		}
		
		foreach (array_keys($this->getDataPlaceHolder()) as $key) {
			$v = $this[$key];
			
			if ($v instanceof RecordContainer || $v instanceof OrderedContainer) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Wether the collection contains only Single primitive values or OrderedContainer objects.
	 *
	 * * If the collection of operands is empty, false is returned.
	 * * If the collection of operands contains a RecordContainer object, false is returned.
	 * * If the collection of operands contains a MultipleContainer object, false is returned.
	 * * In any other case, true is returned.
	 *
	 * @return boolean
	 */
	public function exclusivelySingleOrOrdered() {
		if (count($this) === 0) {
			return false;
		}
	
		foreach (array_keys($this->getDataPlaceHolder()) as $key) {
			$v = $this[$key];
				
			if ($v instanceof RecordContainer || ($v instanceof MultipleContainer && $v->getCardinality() === Cardinality::MULTIPLE)) {
				return false;
			}
		}
	
		return true;
	}
	
	/**
	 * Whether the collection contains exclusively RecordContainer objects.
	 * 
	 * * Returns false if the collection of operands is empty.
	 * * Returns false if any of the value contained in the collection of operands is not a RecordContainer object.
	 * * In any other case, returns true;
	 * 
	 * @return boolean
	 */
	public function exclusivelyRecord() {
		
		if (count($this) === 0) {
			return false;
		}
		
		foreach (array_keys($this->getDataPlaceHolder()) as $key) {
			$v = $this[$key];
			if (!$v instanceof RecordContainer) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Wether the collection contains exclusively OrderedContainer objects.
	 * 
	 * * Returns false if the collection of operands is empty.
	 * * Returns false if any of the value contained in the collection of operands is not an OrderedContainer object.
	 * * Returns true in any other case.
	 * 
	 * @return boolean
	 */
	public function exclusivelyOrdered() {
		if (count($this) === 0) {
			return false;
		}
		
		foreach (array_keys($this->getDataPlaceHolder()) as $key) {
			$v = $this[$key];
			if (!$v instanceof OrderedContainer) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Whether the collection contains anything but a RecordContainer object.
	 * 
	 * @return boolean
	 */
	public function anythingButRecord() {
		
		foreach (array_keys($this->getDataPlaceHolder()) as $key) {
			$v = $this[$key];
			if ($v instanceof RecordContainer) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Whether the collection is composed of values with the same baseType.
	 * 
	 * * If any of the values has not the same baseType than other values in the collection, false is returned.
	 * * If the OperandsCollection is an empty collection, false is returned.
	 * * If the OperandsCollection contains a value considered to be null, false is returned.
	 * * If the OperandsCollection is composed exclusively by non-null RecordContainer objects, true is returned.
	 * 
	 * @return boolean
	 */
	public function sameBaseType() {
		$operandsCount = count($this);
		if ($operandsCount > 0 && !$this->containsNull()) {
			
			// take the first value of the collection as a referer.
			$refValue = $this[0];
			$refType = RuntimeUtils::inferBaseType($refValue);

			for ($i = 1; $i < $operandsCount; $i++) {
				$value = $this[$i];
				$testType = RuntimeUtils::inferBaseType($value);
				
				if ($testType !== $refType) {
				    return false;
				}
			}
			
			// In any other case, return true.
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Wether the collection is composed of values with the same cardinality. Please
	 * note that:
	 * 
	 * * If the OperandsCollection is empty, false is returned.
	 * * If the OperandsCollection contains a NULL value or a NULL container (empty), false is returned
	 * 
	 * @return boolean
	 */
	public function sameCardinality() {
		$operandsCount = count($this);
		if ($operandsCount > 0 && !$this->containsNull()) {
			$refType = RuntimeUtils::inferCardinality($this[0]);
			
			for ($i = 1; $i < $operandsCount; $i++) {
				if ($refType !== RuntimeUtils::inferCardinality($this[$i])) {
					return false;
				}
			}
			
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Wheter the collection of operands is composed exclusively of Point objects or Container objects
	 * with a point baseType.
	 * 
	 * If the collection of operands contains something other than a Point object or a null Container object
	 * with baseType point, false is returned.
	 * 
	 * @return boolean
	 */
	public function exclusivelyPoint() {
		if (count($this) === 0) {
			return false;
		}
		
		foreach (array_keys($this->getDataPlaceHolder()) as $key) {
			$v = $this[$key];
			if (($v instanceof MultipleContainer || $v instanceof OrderedContainer) && ($v->isNull() || $v->getBaseType() !== BaseType::POINT)) {
				return false;
			}
			else if (!$v instanceof Point && !$v instanceof MultipleContainer && !$v instanceof OrderedContainer) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Wheter the collection of operands is composed exclusively of Duration objects or Container objects
	 * with a duration baseType.
	 *
	 * If the collection of operands contains something other than a Duration object or a null Container object
	 * with baseType duration, false is returned.
	 *
	 * @return boolean
	 */
	public function exclusivelyDuration() {
		if (count($this) === 0) {
			return false;
		}
	
		foreach (array_keys($this->getDataPlaceHolder()) as $key) {
			$v = $this[$key];
			if (($v instanceof MultipleContainer || $v instanceof OrderedContainer) && ($v->isNull() || $v->getBaseType() !== BaseType::DURATION)) {
				return false;
			}
			else if (!$v instanceof Duration && !$v instanceof MultipleContainer && !$v instanceof OrderedContainer) {
				return false;
			}
		}
	
		return true;
	}
	
	public function push($value) {
		$this->checkType($value);
		
		$data = &$this->getDataPlaceHolder();
		array_push($data, $value);
	}
	
	public function pop($count = 1) {
		
		$data = &$this->getDataPlaceHolder();
		if ($count === 1) {
			return new OperandsCollection(array(array_pop($data)));
		}
		
		$returnValue = new OperandsCollection();
		$opCount = count($data);
		$i = $opCount - $count;
		while ($i < $opCount) {
			$returnValue[] = $data[$i];
			unset($data[$i]);
			$i++;
		}
		
		$newData = array_values($data);
		$this->setDataPlaceHolder($newData);
		
		return $returnValue;
	}
}