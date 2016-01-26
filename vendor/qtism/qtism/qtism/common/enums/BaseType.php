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
namespace qtism\common\enums;

/**
 * The BaseType enumeration.
 * 
 * From IMS QTI:
 * 
 * A base-type is simply a description of a set of atomic values (atomic to this specification). 
 * Note that several of the baseTypes used to define the runtime data model have identical 
 * definitions to those of the basic data types used to define the values for attributes 
 * in the specification itself. The use of an enumeration to define the set of baseTypes 
 * used in the runtime model, as opposed to the use of classes with similar names, is 
 * designed to help distinguish between these two distinct levels of modelling.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class BaseType implements Enumeration {
	
	/**
	 * From IMS QTI:
	 * 
	 * The set of identifier values is the same as the set of values 
	 * defined by the identifier class.
	 * 
	 * @var int
	 */
	const IDENTIFIER = 0;
	
	/**
	 * From IMS QTI:
	 * 
	 * The set of boolean values is the same as the set of values defined 
	 * by the boolean class.
	 * 
	 * @var int
	 */
	const BOOLEAN = 1;
	
	/**
	 * From IMS QTI:
	 * 
	 * The set of integer values is the same as the set of values defined 
	 * by the integer class.
	 * 
	 * @var int
	 */
	const INTEGER = 2;
	
	/**
	 * From IMS QTI:
	 * 
	 * The set of float values is the same as the set of values defined by the 
	 * float class.
	 * 
	 * @var int
	 */
	const FLOAT = 3;
	
	/**
	 * From IMS QTI:
	 * 
	 * The set of string values is the same as the set of values defined by the 
	 * string class.
	 * 
	 * @var int
	 */
	const STRING = 4;
	
	/**
	 * From IMS QTI:
	 * 
	 * A point value represents an integer tuple corresponding to a graphic point. 
	 * The two integers correspond to the horizontal (x-axis) and vertical (y-axis) 
	 * positions respectively. The up/down and left/right senses of the axes are 
	 * context dependent.
	 * 
	 * @var int
	 */
	const POINT = 5;
	
	/**
	 * From IMS QTI:
	 * 
	 * A pair value represents a pair of identifiers corresponding to an association 
	 * between two objects. The association is undirected so (A,B) and (B,A) are equivalent.
	 * 
	 * @var int
	 */
	const PAIR = 6;
	
	/**
	 * From IMS QTI:
	 * 
	 * A directedPair value represents a pair of identifiers corresponding to a directed 
	 * association between two objects. The two identifiers correspond to the source 
	 * and destination objects.
	 * 
	 * @var int
	 */
	const DIRECTED_PAIR = 7;
	
	/**
	 * From IMS QTI:
	 * 
	 * A duration value specifies a distance (in time) between two time points. 
	 * In other words, a time period as defined by [ISO8601], but represented as
	 * a single float that records time in seconds. Durations may have a fractional
	 * part. Durations are represented using the xsd:double datatype rather than 
	 * xsd:dateTime for convenience and backward compatibility.
	 * 
	 * @var int
	 */
	const DURATION = 8;
	
	/**
	 * From IMS QTI:
	 * 
	 * A file value is any sequence of octets (bytes) qualified by a content-type and an 
	 * optional filename given to the file (for example, by the candidate when uploading 
	 * it as part of an interaction). The content type of the file is one of the MIME 
	 * types defined by [RFC2045].
	 * 
	 * @var int
	 */
	const FILE = 9;
	
	/**
	 * From IMS QTI:
	 * 
	 * A URI value is a Uniform Resource Identifier as defined by [URI].
	 * 
	 * @var int
	 */
	const URI = 10;
	
	/**
	 * From IMS QTI:
	 * 
	 * An intOrIdentifier value is the union of the integer baseType and 
	 * the identifier baseType.
	 * 
	 * @var int
	 */
	const INT_OR_IDENTIFIER = 11;
	
	/**
	 * In qtism, we consider an extra 'coords' baseType.
	 * 
	 * @var integer
	 */
	const COORDS = 12;
	
	public static function asArray() {
		return array(
			'IDENTIFIER' => self::IDENTIFIER,
			'BOOLEAN' => self::BOOLEAN,
			'INTEGER' => self::INTEGER,
			'FLOAT' => self::FLOAT,
			'STRING' => self::STRING,
			'POINT' => self::POINT,
			'PAIR' => self::PAIR,
			'DIRECTED_PAIR' => self::DIRECTED_PAIR,
			'DURATION' => self::DURATION,
			'FILE' => self::FILE,
			'URI' => self::URI,
			'INT_OR_IDENTIFIER' => self::INT_OR_IDENTIFIER,
		    'COORDS' => self::COORDS
		);
	}
	
	/**
	 * Get a constant value from the BaseType enumeration by baseType name.
	 * 
	 * * 'identifier' -> BaseType::IDENTIFIER
	 * * 'boolean' -> BaseType::BOOLEAN
	 * * 'integer' -> BaseType::INTEGER
	 * * 'float' -> BaseType::FLOAT
	 * * 'string' -> BaseType::STRING
	 * * 'point' -> BaseType::POINT
	 * * 'pair' -> BaseType::PAIR
	 * * 'directedPair' -> BaseType::DIRECTED_PAIR
	 * * 'duration' -> BaseType::DURATION
	 * * 'file' -> BaseType::FILE
	 * * 'uri' -> BaseType::URI
	 * * 'intOrIdentifier' -> BaseType::INT_OR_IDENTIFIER
	 * * extra 'coords' -> BaseType::COORDS
	 * 
	 * @param string $name The baseType name.
	 * @return integer|boolean The related enumeration value or false if the name could not be resolved.
	 */
	public static function getConstantByName($name) {
		switch (trim(strtolower($name))) {
			case 'identifier':
				return self::IDENTIFIER;
			break;
			
			case 'boolean':
				return self::BOOLEAN;
			break;
			
			case 'integer':
				return self::INTEGER;
			break;
			
			case 'float':
				return self::FLOAT;
			break;
			
			case 'string':
				return self::STRING;
			break;
			
			case 'point':
				return self::POINT;
			break;
			
			case 'pair':
				return self::PAIR; 
			break;
			
			case 'directedpair':
				return self::DIRECTED_PAIR;
			break;
			
			case 'duration':
				return self::DURATION;
			break;
			
			case 'file':
				return self::FILE;
			break;
			
			case 'uri':
				return self::URI;
			break;
			
			case 'intoridentifier':
				return self::INT_OR_IDENTIFIER;
			break;
			
			case 'coords':
			    return self::COORDS;
			break;
			
			default:
				return false;
			break;
		}
	}
	
	/**
	 * Get the QTI name of a BaseType.
	 * 
	 * @param int $constant A constant value from the BaseType enumeration.
	 * @return string|boolean The QTI name or false if not match.
	 */
	public static function getNameByConstant($constant) {
		switch ($constant) {
			case self::IDENTIFIER:
				return 'identifier';
			break;
					
			case self::BOOLEAN:
				return 'boolean';
			break;
					
			case self::INTEGER:
				return 'integer';
			break;
					
			case self::FLOAT:
				return 'float';
			break;
					
			case self::STRING:
				return 'string';
			break;
					
			case self::POINT:
				return 'point';
			break;
					
			case self::PAIR:
				return 'pair';
			break;
					
			case self::DIRECTED_PAIR:
				return 'directedPair';
			break;
					
			case self::DURATION:
				return 'duration';
			break;
					
			case self::FILE:
				return 'file';
			break;
					
			case self::URI:
				return 'uri';
			break;
			
			case self::INT_OR_IDENTIFIER:
				return 'intOrIdentifier';
			break;
			
			case self::COORDS:
			    return 'coords';
			break;
					
			default:
				return false;
			break;
		}
	} 
}