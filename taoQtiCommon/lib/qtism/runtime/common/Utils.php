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
 * @subpackage 
 *
 */
namespace qtism\runtime\common;

use qtism\common\enums\Cardinality;
use qtism\common\datatypes\Duration;
use qtism\common\datatypes\Pair;
use qtism\common\datatypes\DirectedPair;
use qtism\common\datatypes\Point;
use qtism\common\enums\BaseType;
use qtism\common\utils\Format;
use \InvalidArgumentException;
use \RuntimeException;

// @todo write an isNull method that applies on both scalar and container values.

class Utils {
	
	/**
	 * Whether a given primitive $value is compliant with the QTI runtime model.
	 *
	 * Compliant primitive values are:
	 *
	 * * string (qti:string, qti:identifier)
	 * * integer
	 * * float
	 * * double (qti:float)
	 * * boolean
	 * * Duration
	 * * Point
	 * * Pair
	 * * DirectedPair
	 * * NULL
	 *
	 * @param mixed $value A value you want to check the compatibility with the QTI runtime model.
	 * @return boolean
	 */
	public static function isRuntimeCompliant($value) {
		$primitiveTypes = array('integer', 'float', 'double', 'string', 'boolean');
	
		if ($value === null || in_array(gettype($value), $primitiveTypes)) {
			return true;
		}
		else if (gettype($value) == 'object') {
			if ($value instanceof Duration ||
					$value instanceof Pair ||
					$value instanceof Point) {
				return true;
			}
		}
	
		return false;
	}
	
	/**
	 * Whether a given $value is compliant with a given $baseType.
	 * 
	 * @param int $baseType A value from the BaseType enumeration.
	 * @param mixed $value A value.
	 * @throws InvalidArgumentException If $baseType is not a value from the BaseType enumeration.
	 * @return boolean
	 */
	public static function isBaseTypeCompliant($baseType, $value) {
		
		if ($value === null) {
			return true; // A value can always be null.
		}
		
		switch ($baseType) {
			case BaseType::BOOLEAN:
				return is_bool($value);
			break;
					
			case BaseType::DIRECTED_PAIR:
				return $value instanceof DirectedPair;
			break;
					
			case BaseType::DURATION:
				return $value instanceof Duration;
			break;
					
			case BaseType::FILE:
				return Format::isFile($value);
			break;
					
			case BaseType::FLOAT:
				return is_float($value) || is_double($value);
			break;
					
			case BaseType::IDENTIFIER:
				return Format::isIdentifier($value);
			break;
					
			case BaseType::INT_OR_IDENTIFIER:
				return Format::isIdentifier($value) || is_int($value);
			break;
					
			case BaseType::INTEGER:
				return is_int($value);
			break;
					
			case BaseType::PAIR:
				return $value instanceof Pair;
			break;
					
			case BaseType::POINT:
				return $value instanceof Point;
			break;
					
			case BaseType::STRING:
				return gettype($value) === 'string';
			break;
					
			case BaseType::URI:
				return Format::isUri($value);
			break;
			
			default:
				$msg = "Unknown baseType '" . $baseType . "'.";
				throw new InvalidArgumentException($msg);
			break;
		}
	}
	
	/**
	 * Throw an InvalidArgumentException depending on a PHP in-memory value.
	 *
	 * @param mixed $value A given PHP primitive value.
	 * @throws InvalidArgumentException In any case.
	 */
	public static function throwTypingError($value) {
		$givenValue = (gettype($value) == 'object') ? get_class($value) : gettype($value);
		$acceptedTypes = array('boolean', 'integer', 'float', 'double', 'string', 'Duration', 'Pair', 'DirectedPair', 'Point');
		$acceptedTypes = implode(", ", $acceptedTypes);
		$msg = "A value is not compliant with the QTI runtime model datatypes: ${acceptedTypes} . '${givenValue}' given.";
		throw new InvalidArgumentException($msg);
	}
	
	/**
	 * Throw an InvalidArgumentException depending on a given qti:baseType
	 * and an in-memory PHP value.
	 *
	 * @param int $baseType A value from the BaseType enumeration.
	 * @param mixed $value A given PHP primitive value.
	 * @throws InvalidArgumentException In any case.
	 */
	public static function throwBaseTypeTypingError($baseType, $value) {
		$givenValue = (gettype($value) == 'object') ? get_class($value) : gettype($value) . ':' . $value;
		$acceptedTypes = BaseType::getNameByConstant($baseType);
		$msg = "The value '${givenValue}' is not compliant with the '${acceptedTypes}' baseType.";
		throw new InvalidArgumentException($msg);
	}
	
	/**
	 * Infer the QTI baseType of a given $value.
	 * 
	 * @param mixed $value A value you want to know the QTI baseType.
	 * @return integer|false A value from the BaseType enumeration or false if the baseType could not be infered.
	 */
	public static function inferBaseType($value) {
		if (is_scalar($value)) {
			switch (gettype($value)) {
				case 'boolean':
					return BaseType::BOOLEAN;
				break;
			
				case 'integer':
					return BaseType::INTEGER;
				break;
			
				case 'double':
					return BaseType::FLOAT;
				break;
			
				case 'string':
					return BaseType::STRING;
				break;
			}
		}
		else if ($value instanceof MultipleContainer || $value instanceof OrderedContainer) {
			return $value->getBaseType();
		}
		else if ($value instanceof Point) {
			return BaseType::POINT;
		}
		else if ($value instanceof DirectedPair) {
			return BaseType::DIRECTED_PAIR;
		}
		else if ($value instanceof Pair) {
			return BaseType::PAIR;
		}
		else if ($value instanceof Duration) {
			return BaseType::DURATION;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Infer the cardinality of a given $value.
	 * 
	 * Please note that:
	 * 
	 * * A RecordContainer has no cardinality, thus it always returns false for such a container.
	 * * The null value has no cardinality, this it always returns false for such a value. 
	 * 
	 * @param mixed $value A value you want to infer the cardinality.
	 * @return integer|boolean A value from the Cardinality enumeration or false if it could not be infered.
	 */
	public static function inferCardinality($value) {
		if (is_scalar($value)) {
			return Cardinality::SINGLE;
		}
		else if ($value instanceof Point || $value instanceof Pair || $value instanceof Duration) {
			return Cardinality::SINGLE;
		}
		else if ($value instanceof Container) {
			return $value->getCardinality();
		}
		else {
			return false;
		}
	}
	
	/**
	 * Whether a given $string is a valid variable identifier.
	 * 
	 * Q01			-> Valid
	 * Q_01			-> Valid
	 * 1_Q01		-> Invalid
	 * Q01.SCORE	-> Valid
	 * Q-01.1.Score	-> Valid
	 * Q*01.2.Score	-> Invalid
	 * 
	 * @param string $string A string value.
	 * @return boolean Whether the given $string is a valid variable identifier.
	 */
	public static function isValidVariableIdentifier($string) {
		
		if (gettype($string) !== 'string' || empty($string)) {
			return false;
		}
		
		$pattern = '/^[a-z][a-z0-9_\-]*(?:(?:\.[1-9][0-9]*){0,1}\.[a-z][a-z0-9_\-]*){0,1}$/iu';
		return preg_match($pattern, $string) === 1;
	}
}