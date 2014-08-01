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
namespace qtism\runtime\common;

use qtism\common\datatypes\File;

use qtism\common\datatypes\Boolean;

use qtism\common\datatypes\String;
use qtism\data\state\ValueCollection;
use qtism\common\enums\Cardinality;
use qtism\common\datatypes\Point;
use qtism\common\datatypes\DirectedPair;
use qtism\common\datatypes\Pair;
use qtism\common\datatypes\Duration;
use qtism\common\collections\AbstractCollection;
use qtism\common\utils\Format;
use qtism\common\Comparable;
use qtism\runtime\common\Utils as RuntimeUtils;
use \InvalidArgumentException;

/**
 * A Collection which is able to contain any PHP datatypes compliant
 * with the QTI Specification + QTIStateMachine equivalents which are:
 * 
 * * Duration (qti:duration)
 * * Pair (qti:pair)
 * * DirectedPair (qti:directedPair)
 * * Point (qti:point)
 * 
 * From IMS QTI:
 * 
 * A container is an aggregate data type that can contain multiple values
 * of the primitive Base-types. Containers may be empty.
 * 
 * A container contains a list of values, this list may be empty in which
 * case it is treated as NULL. All the values in a multiple or ordered 
 * container are drawn from the same value set, however, containers may 
 * contain multiple occurrences of the same value. In other words, [A,B,B,C] 
 * is an acceptable value for a container. A container with cardinality 
 * multiple and value [A,B,C] is equivalent to a similar one with 
 * value [C,B,A] whereas these two values would be considered distinct 
 * for containers with cardinality ordered. When used as the value of 
 * a response variable this distinction is typified by the difference 
 * between selecting choices in a multi-response multi-choice task and 
 * ranking choices in an order objects task. In the language of [ISO11404] 
 * a container with multiple cardinality is a "bag-type", a container with 
 * ordered cardinality is a "sequence-type" and a container with record 
 * cardinality is a "record-type".
 * 
 * The record container type is a special container that contains a set 
 * of independent values each identified by its own identifier and 
 * having its own base-type. This specification does not make use of 
 * the record type directly however it is provided to enable 
 * customInteractions to manipulate more complex responses and 
 * customOperators to return more complex values, in addition 
 * to the use for detailed information about numeric responses 
 * described in the stringInteraction abstract class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Container extends AbstractCollection implements Comparable {
	
	/**
	 * Create a new Container object.
	 * 
	 * @param array $array An array of values to be set in the container.
	 */
	public function __construct(array $array = array()) {
		parent::__construct($array);
	}
	
	protected function checkType($value) {
		if (!Utils::isRuntimeCompliant($value)) {
			Utils::throwTypingError($value);
		}
	}
	
	/**
	 * In the QTI runtime model, an empty container is considered to
	 * have the NULL value. This method helps you to know whether or not
	 * the container has the NULL value.
	 * 
	 * @return boolean Whether the container has to be considered as NULL.
	 */
	public function isNull() {
		$data = $this->getDataPlaceHolder();
		return empty($data);
	}
	
	/**
	 * Get the QTI cardinality of the container.
	 * 
	 * @return int A value from the Cardinality enumeration.
	 */
	public function getCardinality() {
		return Cardinality::MULTIPLE;
	}
	
	/**
	 * Wheter the container is equal to $obj.
	 * 
	 * * If $obj is not an instance of Container, false is returned.
	 * * If $obj is [A,B,C] and the container is [C,A,B], true is returned because the order does not matter.
	 * * If $obj is [A,B,C] and the container is [B,C,D], false is returned.
	 * * If $obj is [] and the container is [], false is returned.
	 * 
	 * @param mixed $obj A value to compare to this one.
	 * @return boolean Whether the container is equal to $obj.
	 */
	public function equals($obj) {
		$countA = count($obj);
		$countB = count($this);
		
		if (gettype($obj) === 'object' && $obj instanceof static && $countA === $countB) {
			foreach (array_keys($this->getDataPlaceHolder()) as $key) {
				$t = $this[$key];
				$occurencesA = $this->occurences($t);
				$occurencesB = $obj->occurences($t);
				
				if ($occurencesA !== $occurencesB) {
					return false;
				}
			}
			return true;
		}
		else {
			// Not the same type or different item count.
			return false;
		}
	}
	
	/**
	 * Get the number of occurences of a given $obj in the container.
	 * 
	 * * If $obj is an instance of Comparable, an equality check will be performed using the Comparable::equals method of $obj.
	 * * If $obj is a primitive type, a strict comparison (===) will be applied.
	 * 
	 * @param mixed $obj The object you want to find the number of occurences in the container.
	 * @return int A number of occurences.
	 */
	public function occurences($obj) {
		$occurences = 0;
		
		foreach (array_keys($this->getDataPlaceHolder()) as $key) {
			$t = $this[$key];
			if (gettype($obj) === 'object' && $obj instanceof Comparable) {
				// try to use Comparable.
				if ($obj->equals($t)) {
					$occurences++;
				}
			}
			else if (gettype($t) === 'object' && $t instanceof Comparable) {
				// Again, use Comparable.
				if ($t->equals($obj)) {
					$occurences++;
				}
			}
			else {
				// Both primitive.
				if ($obj === $t) {
					$occurences++;
				}
			}
		}
		
		return $occurences;
	}
	
	/**
	 * Create a Container object from a Data Model ValueCollection object.
	 * 
	 * @param ValueCollection $valueCollection A collection of qtism\data\state\Value objects.
	 * @return Container A Container object populated with the values found in $valueCollection.
	 * @throws InvalidArgumentException If a value from $valueCollection is not compliant with the QTI Runtime Model or the container type.
	 */
	public static function createFromDataModel(ValueCollection $valueCollection) {
		$container = new static();
		foreach ($valueCollection as $value) {
			$container[] = RuntimeUtils::valueToRuntime($value->getValue(), $value->getBaseType());
		}
		return $container;
	}
	
	/**
	 * Get the character bounds of the container while output in
	 * a __toString context. For instance, this method returns 
	 * array('[', ']'). Thus, the result of the __toString method will
	 * be '[10, 20, 30]' if the values held by the container are integer
	 * 10, 20 and 30.
	 * 
	 * @return array An array with two entries which are respectively to character lower and upper bounds.
	 */
	protected function getToStringBounds() {
		return array('[', ']');
	}
	
	public function __toString() {
		$bounds = $this->getToStringBounds();
		$data = &$this->getDataPlaceHolder();
		
		if (count($data) === 0) {
			// Empty container.
			return $bounds[0] . $bounds[1];
		}
		$strings = array();
		
		foreach (array_keys($data) as $k) {
			$d = $data[$k];
			
			if (is_null($d) === true) {
			    $strings[] = 'NULL';
			}
			else if ($d instanceof String) {
				$strings[] = "'${d}'";
			}
			else if ($d instanceof Boolean) {
				// PHP boolean primitive type.
				$strings[] = ($d->getValue() === true) ? 'true' : 'false';
			}
			else if ($d instanceof File) {
			    $strings[] = $d->getFilename();
			}
			else{
				// Other PHP primitive/object type.
				$strings[] = '' . $d;
			}
		}
		
		return $bounds[0] . implode('; ', $strings) . $bounds[1];
	}
}