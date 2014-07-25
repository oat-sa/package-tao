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

use qtism\common\datatypes\QtiDatatype;
use qtism\common\enums\Cardinality;
use qtism\data\state\ValueCollection;
use qtism\common\utils\Arrays;
use qtism\common\enums\BaseType;
use qtism\runtime\common\Utils as RuntimeUtils;
use \InvalidArgumentException;
use \RuntimeException;

/**
 * Implementation of the qti:record cardinality. It behaves as an associative
 * array. There is no information in the QTI standard about how the equality of 
 * two records can be established. In this implementation, it is implemented as
 * if it was a bag, and the keys are not taken into account.
 * 
 * From IMS QTI:
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class RecordContainer extends Container implements QtiDatatype {
	
	/**
	 * Create a new RecordContainer object.
	 * 
	 * @param array $array An associative array.
	 * @throws InvalidArgumentException If the given $array is not associative.
	 */
	public function __construct(array $array = array()) {
		if (Arrays::isAssoc($array)) {
			$dataPlaceHolder = &$this->getDataPlaceHolder();
		
			foreach ($array as $k => $v) {
				$this->checkType($v);
				$dataPlaceHolder[$k] = $v;
			}
		
			reset($dataPlaceHolder);
		}
		else {
			$msg = "The array argument must be an associative array.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	public function getCardinality() {
		return Cardinality::RECORD;
	}
	
	/**
	 * Overloading of offsetSet that makes sure that the $offset
	 * is a string.
	 *
	 * @param string $offset A string offset.
	 * @param mixed $value A value.
	 *
	 * @throws RuntimeException If $offset is not a string.
	 */
	public function offsetSet($offset, $value) {
		if (gettype($offset) === 'string') {
			$this->checkType($value);
			$placeholder = &$this->getDataPlaceHolder();
			$placeholder[$offset] = $value;
		}
		else {
			$msg = "The offset of a value in a RecordContainer must be a string.";
			throw new RuntimeException($msg);
		}
	}
	
	/**
	 * Create a RecordContainer object from a Data Model ValueCollection object.
	 *
	 * @param ValueCollection $valueCollection A collection of qtism\data\state\Value objects.
	 * @return RecordContainer A Container object populated with the values found in $valueCollection.
	 * @throws InvalidArgumentException If a value from $valueCollection is not compliant with the QTI Runtime Model or the container type or if a value has no fieldIdentifier.
	 */
	public static function createFromDataModel(ValueCollection $valueCollection) {
		$container = new static();
		foreach ($valueCollection as $value) {
			$fieldIdentifier = $value->getFieldIdentifier();
			
			if (!empty($fieldIdentifier)) {
				$container[$value->getFieldIdentifier()] = RuntimeUtils::valueToRuntime($value->getValue(), $value->getBaseType());
			}
			else {
				$msg = "Cannot include qtism\\data\\state\\Value '" . $value->getValue() . "' in the RecordContainer ";
				$msg .= "because it has no fieldIdentifier specified.";
				throw new InvalidArgumentException($msg);
			}
		}
		return $container;
	}
	
	protected function getToStringBounds() {
		return array('{', '}');
	}
	
	public function getBaseType() {
	    return -1;
	}
}