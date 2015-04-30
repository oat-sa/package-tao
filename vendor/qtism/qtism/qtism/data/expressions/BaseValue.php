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


namespace qtism\data\expressions;

use \InvalidArgumentException;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;

/**
 * Express a constant value with a given base type.
 * 
 * From IMS QTI:
 * 
 * The simplest expression returns a single value from the set defined by the given baseType.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class BaseValue extends Expression {
	
	/**
	 * The baseType of the value.
	 * 
	 * @var int
	 * @qtism-bean-property
	 */
	private $baseType;
	
	/**
	 * The actual value.
	 * 
	 * @var mixed
	 * @qtism-bean-property
	 */
	private $value;
	
	/**
	 * Create a new instance of BaseValue.
	 * 
	 * @param int $baseType The base type of the value.
	 * @param mixed $value The actual value.
	 * @throws InvalidArgumentException If $baseType is not a value from the BaseType enumeration.
	 */
	public function __construct($baseType, $value) {
		$this->setBaseType($baseType);
		$this->setValue($value);
	}
	
	/**
	 * Get the base type.
	 * 
	 * @return int A value from the BaseType enumeration.
	 */
	public function getBaseType() {
		return $this->baseType;
	}
	
	/**
	 * Set the base type.
	 * 
	 * @param int $baseType A value from the BaseType enumeration.
	 * @throws InvalidArgumentException If $baseType is not a value from the BaseType enumeration.
	 */
	public function setBaseType($baseType) {
		if (in_array($baseType, BaseType::asArray())) {
			$this->baseType = $baseType;
		}
		else {
			$msg = "BaseType must be a value from the BaseType enumeration.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the actual value.
	 * 
	 * @return mixed A value.
	 */
	public function getValue() {
		return $this->value;
	}
	
	/**
	 * Set the actual value.
	 * 
	 * @param mixed $value The actual value.
	 */
	public function setValue($value) {
		$this->value = $value;
	}
	
	public function getQtiClassName() {
		return 'baseValue';
	}
}
