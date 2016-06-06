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


namespace qtism\data\state;

use qtism\data\QtiComponentCollection;
use qtism\data\QtiComponent;
use \InvalidArgumentException;
use qtism\common\utils\Format;
use qtism\data\storage\Utils;
use qtism\common\enums\BaseType;

/**
 * A class that can represent a single value of any baseType in variable declarations 
 * and result reports. The base-type is defined by the baseType attribute of the 
 * declaration except in the case of variables with record cardinality.
 * 
 * It is the responsibility of the client-code to set an intrinsic value which is 
 * representative of the baseType of the variable the Value is affected to.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Value extends QtiComponent {
	
	/**
	 * From IMS QTI:
	 * 
	 * This attribute is only used for specifying the field identifier for a value 
	 * that forms part of a record.
	 * 
	 * An empty string means there is no fieldIdentifier.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $fieldIdentifier = '';
	
	/**
	 * From IMS QTI:
	 * 
	 * This attribute is only used for specifying the base-type of a value that forms 
	 * part of a record.
	 * 
	 * A negative value means there is not baseType.
	 * 
	 * @var int
	 * @qtism-bean-property
	 */
	private $baseType = -1;
	
	/**
	 * From IMS QTI:
	 * 
	 * The actual value.
	 * 
	 * @var mixed
	 * @qtism-bean-property
	 */
	private $value;
	
	/**
	 * Declare if the value is part of a record.
	 * 
	 * @var boolean
	 * @qtism-bean-property
	 */
	private $isPartOfRecord = false;
	
	/**
	 * Create a new instance of Value. Please note that it is the responsability of the client-code (you) to set up an intrinsic
	 * value which is representative of $baseType.
	 * 
	 * @param mixed $value An intrinsic value. Must be cast to the correct datatype, regarding to the baseType of the value.
	 * @param int $baseType A value of the BaseType enumeration or -1 if no baseType (default).
	 * @param string $fieldIdentifier A field identifier if the value is part of a record.
	 * @throws InvalidArgumentException If $baseType is not a value from the BaseType enumeration or $fieldIdentifier is not a valid QTI Identifier.
	 */
	public function __construct($value, $baseType = -1, $fieldIdentifier = '') {
		$this->setBaseType($baseType);
		$this->setValue($value);
		$this->setFieldIdentifier($fieldIdentifier);
	}
	
	/**
	 * Set wether or not the value is part of a record.
	 * 
	 * @param boolean $partOfRecord
	 * @throws InvalidArgumentException If $partOfRecord is not a boolean.
	 */
	public function setPartOfRecord($partOfRecord) {
		if (is_bool($partOfRecord)) {
			$this->isPartOfRecord = $partOfRecord;
		}
		else {
			$msg = "The argument must be a boolean, '" . gettype($partOfRecord) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Wheter the value is being part of a record. Default is false.
	 * 
	 * @return boolean
	 */
	public function isPartOfRecord() {
		return $this->isPartOfRecord;
	}
	
	/**
	 * Get the field identifier. An empty string means it is not specified.
	 * 
	 * @return string A QTI identifier.
	 */
	public function getFieldIdentifier() {
		return $this->fieldIdentifier;
	}
	
	/**
	 * Set the field identifier. An empty string means it is not specified.
	 * 
	 * @param string $fieldIdentifier A QTI identifier.
	 * @throws InvalidArgumentException If $fieldIdentifier is not a valid QTI Identifier.
	 */
	public function setFieldIdentifier($fieldIdentifier) {
		if ($fieldIdentifier == '' || Format::isIdentifier($fieldIdentifier)) {
			$this->fieldIdentifier = $fieldIdentifier;
		}
		else {
			$msg = "'${fieldIdentifier}' is not a valid QTI identifier.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Whether or not a field identifier is defined.
	 * 
	 * @return boolean
	 */
	public function hasFieldIdentifier() {
	    return $this->getFieldIdentifier() !== '';
	}
	
	/**
	 * Get the BaseType of the value.
	 * 
	 * @return int A value of the BaseType enumeration or a negative value (< 0) if there is no baseType.
	 */
	public function getBaseType() {
		return $this->baseType;
	}
	
	/**
	 * Set the BaseType of the value.
	 * 
	 * @param int $baseType A value of the BaseType enumeration. A negative value (< 0) means there is no baseType. 
	 * @throws InvalidArgumentException If $baseType is not a value from the BaseType operation.
	 */
	public function setBaseType($baseType) {
		if (in_array($baseType, BaseType::asArray()) || $baseType === -1) {
			$this->baseType = $baseType;
		}
		else {
			$msg = "'${baseType}' is not a value from the BaseType enumeration.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Whether the value has a defined baseType.
	 * 
	 * @return boolean
	 */
	public function hasBaseType() {
	    return $this->getBaseType() !== -1;
	}
	
	/**
	 * Get the intrinsic value.
	 * 
	 * @return mixed A value with a datatype depending on the baseType of the Value.
	 */
	public function getValue() {
		return $this->value;
	}
	
	/**
	 * Set the intrinsic value. Please note that it is the responsability of the client-code (you) to set up an intrinsic
	 * value which is representative of $baseType.
	 * 
	 * @param mixed $value A value with a correct datatype regarding the baseType.
	 */
	public function setValue($value) {
		$this->value = $value;
	}
	
	public function getQtiClassName() {
		return 'value';
	}
	
	public function getComponents() {
		return new QtiComponentCollection();
	}
}
