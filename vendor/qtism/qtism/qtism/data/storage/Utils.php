<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *   
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * 
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package 
 */


namespace qtism\data\storage;

use qtism\common\datatypes\Point;
use qtism\data\expressions\BaseValue;
use qtism\data\state\Value;
use qtism\common\enums\BaseType;
use qtism\common\utils\Format;
use qtism\common\datatypes\Pair;
use qtism\common\datatypes\DirectedPair;
use qtism\common\datatypes\Duration;
use qtism\common\datatypes\Coords;
use \InvalidArgumentException;
use \UnexpectedValueException;

/**
 * XML Storage Utility class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Utils {
	
	/**
	 * Transform a string representing a QTI valueType value in a
	 * the correct datatype.
	 * 
	 * @param string $string The QTI valueType value as a string.
	 * @param integer $baseType The QTI baseType that defines the datatype of $string.
	 * @return mixed A converted object/primitive type.
	 * @throws InvalidArgumentException If $baseType is not a value from the BaseType enumeration.
	 * @throws UnexpectedValueException If $string cannot be transformed in a Value expression with the given $baseType.
	 */
	public static function stringToDatatype($string, $baseType) {
	if (in_array($baseType, BaseType::asArray())) {
			$value = null;
			
			switch ($baseType) {
				case BaseType::BOOLEAN:
					if (Format::isBoolean($string)) {
						$value = (Format::toLowerTrim($string) == 'true') ? true : false;
						return $value;
					}
					else {
						$msg = "'${string}' cannot be transformed into boolean.";
						throw new UnexpectedValueException($msg);
					}
				break;
				
				case BaseType::INTEGER:
					if (Format::isInteger($string)) {
						$value = intval($string);
						return $value;
					}
					else {
						$msg = "'${string}' cannot be transformed into integer.";
						throw new UnexpectedValueException($msg);
					}
				break;
				
				case BaseType::FLOAT:
					if (Format::isFloat($string)) {
						$value = floatval($string);
						return $value;
					}
					else {
						$msg = "'${string}' cannot be transformed into float.";
						throw new UnexpectedValueException($msg);
					}
				break;
				
				case BaseType::URI:
					if (Format::isUri($string)) {
						return $string;
					}
					else {
						$msg = "'${string}' is not a valid URI.";
						throw new UnexpectedValueException($msg);
					}
				break;
				
				case BaseType::IDENTIFIER:
					if (Format::isIdentifier($string)) {
						return $string;
					}
					else {
						$msg = "'${string}' is not a valid QTI Identifier.";
						throw new UnexpectedValueException($msg);
					}
				break;
				
				case BaseType::INT_OR_IDENTIFIER:
					if (Format::isIdentifier($string)) {
						return $string;
					}
					else if (Format::isInteger($string)) {
						return intval($string);
					}
					else {
						$msg = "'${string}' is not a valid QTI Identifier nor a valid integer.";
						throw new UnexpectedValueException($msg);
					}
				break;
				
				case BaseType::PAIR:
					if (Format::isPair($string)) {
						$pair = explode("\x20", $string);
						return new Pair($pair[0], $pair[1]);
					}
					else {
						$msg = "'${string}' is not a valid pair.";
						throw new UnexpectedValueException($msg);
					}
				break;
				
				case BaseType::DIRECTED_PAIR:
					if (Format::isDirectedPair($string)) {
						$pair = explode("\x20", $string);
						return new DirectedPair($pair[0], $pair[1]);
					}
					else {
						$msg = "'${string}' is not a valid directed pair.";
						throw new UnexpectedValueException($msg);
					}
				break;
				
				case BaseType::DURATION:
					if (Format::isDuration($string)) {
						return new Duration($string);
					}
					else {
						$msg = "'${string}' is not a valid duration.";
						throw new UnexpectedValueException($msg);
					}
				break;
				
				case BaseType::FILE:
					throw new \RuntimeException("Unsupported baseType: file.");
				break;
				
				case BaseType::STRING:
					return '' . $string;
				break;
				
				case BaseType::POINT:
					if (Format::isPoint($string)) {
						$parts = explode("\x20", $string);
						return new Point(intval($parts[0]), intval($parts[1]));
					}
					else {
						$msg = "'${string}' is not valid point.";
						throw new UnexpectedValueException($msg);
					}
				break;
				
				default:
					throw new \RuntimeException("Unknown baseType.");
				break;
			}
		}
		else {
			$msg = "BaseType must be a value from the BaseType enumeration.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Transforms a string to a Coord object according to a given shape.
	 * 
	 * @param string $string Coordinates as a string.
	 * @param int $shape A value from the Shape enumeration.
	 * @throws InvalidArgumentException If $string is are not valid coordinates or $shape is not a value from the Shape enumeration.
	 * @throws UnexpectedValueException If $string cannot be converted to a Coords object.
	 * @return Coords A Coords object.
	 */
	public static function stringToCoords($string, $shape) {
		if (Format::isCoords($string)) {
			
			$stringCoords = explode(",", $string);
			$intCoords = array();
			
			foreach ($stringCoords as $sC) {
				$intCoords[] = intval($sC);
			}
			
			// Maybe it was accepted has coords, but is it buildable with
			// the given shape?
			return new Coords($shape, $intCoords);
		}
		else {
			throw new UnexpectedValueException("'${string}' cannot be converted to Coords.");
		}
	}
	
	/**
	 * Sanitize a URI (Uniform Resource Identifier).
	 * 
	 * The following processings will be applied:
	 * 
	 * * If there is/are trailing slashe(s), they will be removed.
	 * 
	 * @param string $uri A Uniform Resource Identifier.
	 * @throws InvalidArgumentException If $uri is not a string.
	 * @return string A sanitized Uniform Resource Identifier.
	 */
	public static function sanitizeUri($uri) {
		if (gettype($uri) === 'string') {
			return rtrim($uri, '/');
		}
		
		$msg = "The uri argument must be a string, '" . gettype($uri) . "' given.";
		throw new InvalidArgumentException($msg);
	}
}
