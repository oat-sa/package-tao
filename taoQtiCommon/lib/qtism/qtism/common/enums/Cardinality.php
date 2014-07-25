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
 * From IMS QTI:
 * 
 * An expression or itemVariable can either be single-valued or multi-valued. A multi-valued 
 * expression (or variable) is called a container. A container contains a list of values, 
 * this list may be empty in which case it is treated as NULL. All the values in a multiple 
 * or ordered container are drawn from the same value set, however, containers may contain 
 * multiple occurrences of the same value. In other words, [A,B,B,C] is an acceptable value 
 * for a container. A container with cardinality multiple and value [A,B,C] is equivalent 
 * to a similar one with value [C,B,A] whereas these two values would be considered distinct 
 * for containers with cardinality ordered. When used as the value of a response variable 
 * this distinction is typified by the difference between selecting choices in a multi-response 
 * multi-choice task and ranking choices in an order objects task. In the language of [ISO11404] 
 * a container with multiple cardinality is a "bag-type", a container with ordered cardinality 
 * is a "sequence-type" and a container with record cardinality is a "record-type".
 * 
 * The record container type is a special container that contains a set of independent values 
 * each identified by its own identifier and having its own base-type. This specification 
 * does not make use of the record type directly however it is provided to enable 
 * customInteractions to manipulate more complex responses and customOperators to 
 * return more complex values, in addition to the use for detailed information about 
 * numeric responses described in the stringInteraction abstract class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Cardinality implements Enumeration {
	
	const SINGLE = 0;
	
	const MULTIPLE = 1;
	
	const ORDERED = 2;
	
	const RECORD = 3;
	
	public static function asArray() {
		return array(
			'SINGLE' => self::SINGLE,
			'MULTIPLE' => self::MULTIPLE,
			'ORDERED' => self::ORDERED,
			'RECORD' => self::RECORD		
		);
	}
	
	/**
	 * Get a constant value from its name.
	 * 
	 * @param unknown_type $name The name of the constant, as per QTI spec.
	 * @return integer|boolean The constant value or false if not found.
	 */
	public static function getConstantByName($name) {
		switch (strtolower($name)) {
			case 'single':
				return self::SINGLE;
			break;
			
			case 'multiple':
				return self::MULTIPLE;
			break;
			
			case 'ordered':
				return self::ORDERED;	
			break;
			
			case 'record':
				return self::RECORD;
			break;
			
			default:
				return false;
			break;
		}
	}
	
	/**
	 * Get the name of a constant from its value.
	 * 
	 * @param string $constant The constant value to search the name for.
	 * @return string|boolean The name of the constant or false if not found.
	 */
	public static function getNameByConstant($constant) {
		switch ($constant) {
			case self::SINGLE:
				return 'single';
			break;
			
			case self::MULTIPLE:
				return 'multiple';
			break;
			
			case self::ORDERED:
				return 'ordered';
			break;
			
			case self::RECORD:
				return 'record';
			break;
			
			default:
				return false;
			break;
		}
	}
}