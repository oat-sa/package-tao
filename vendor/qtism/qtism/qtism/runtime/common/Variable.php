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

use qtism\data\expressions\operators\IsNull;
use qtism\data\state\ValueCollection;
use qtism\data\state\VariableDeclaration;
use qtism\common\enums\Cardinality;
use qtism\common\enums\BaseType;
use qtism\runtime\common\Utils as RuntimeUtils;
use \InvalidArgumentException;
use \UnexpectedValueException;

/**
 * This class represents a QTI Variable at runtime.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
abstract class Variable {
	
	/**
	 * The identifier of the variable.
	 * 
	 * @var string
	 */
	private $identifier;
	
	/**
	 * The cardinality of the variable.
	 * 
	 * @var int
	 */
	private $cardinality;
	
	/**
	 * The baseType of the variable.
	 * 
	 * @var int
	 */
	private $baseType;
	
	/**
	 * The value of the variable.
	 * 
	 * @var int|float|double|boolean|string|Duration|Point|Pair|DirectedPair|Container
	 */
	private $value;
	
	/**
	 * The default value of the variable.
	 * 
	 * @var int|float|double|boolean|string|Duration|Point|Pair|DirectedPair|Container
	 */
	private $defaultValue = null;
	
	/**
	 * Create a new Variable object. If the cardinality is multiple, ordered or record,
	 * the appropriate container will be instantiated as the $value argument.
	 * 
	 * @param string $identifier An identifier.
	 * @param integer $cardinality A value from the Cardinality enumeration.
	 * @param integer $baseType A value from the BaseType enumeration. -1 can be given to state there is no particular baseType if $cardinality is Cardinality::RECORD.
	 * @param int|float|double|boolean|string|Duration|Point|Pair|DirectedPair $value A value compliant with the QTI Runtime Model.
	 * @throws InvalidArgumentException If the cardinality is record but -1 is not given as a $baseType (Records have no baseType) or If the given $value is not compliant with the given $baseType.
	 */
	public function __construct($identifier, $cardinality, $baseType = -1, $value = null) {
		$this->setIdentifier($identifier);
		$this->setCardinality($cardinality);
		$this->setBaseType($baseType);
		
		// Initialize the variable with the appropriate default value.
		$this->initialize();
		
		// If provided, set the value of the variable.
		if ($value !== null) {
			$this->setValue($value);
		}
	}
	
	/**
	 * Initialize the variable with the appropriate default value.
	 * 
	 * * If the variable is supposed to contain a Container (Multiple, Ordered or Record cardinality), the variable's value becomes an empty container.
	 * * If the variable is scalar (Cardinality single), the value becomes NULL.
	 *
	 */
	public function initialize() {
		if ($this->cardinality === Cardinality::MULTIPLE) {
			$value = new MultipleContainer($this->baseType);
		}
		else if ($this->cardinality === Cardinality::ORDERED) {
			$value = new OrderedContainer($this->baseType);
		}
		else if ($this->cardinality === Cardinality::RECORD) {
			$value = new RecordContainer();
		}
		else {
			$value = null;
		}
		
		$this->value = $value;
	}
	
	/**
	 * Get the identifier of the Variable.
	 * 
	 * @return string An identifier.
	 */
	public function getIdentifier() {
		return $this->identifier;
	}
	
	/**
	 * Get the identifier of the Variable.
	 * 
	 * @param string $identifier An identifier.
	 */
	public function setIdentifier($identifier) {
	    $this->identifier = $identifier;
	}
	
	/**
	 * Get the cardinality of the Variable.
	 * 
	 * @return integer A value from the Cardinality enumeration.
	 */
	public function getCardinality() {
		return $this->cardinality;
	}
	
	/**
	 * Set the cardinality of the Variable.
	 * 
	 * @param integer $cardinality A value from the Cardinality enumeration.
	 */
	public function setCardinality($cardinality) {
		$this->cardinality = $cardinality;
	}
	
	/**
	 * Get the baseType of the Variable.
	 * 
	 * @return integer A value from the Cardinality enumeration.
	 */
	public function getBaseType() {
		return $this->baseType;
	}
	
	/**
	 * Set the baseType of the Variable.
	 * 
	 * @param integer $baseType A value from the Cardinality enumeration or -1 if there is no baseType in a Cardinality::RECORD context.
	 * @throws InvalidArgumentException If -1 is passed but Cardinality::RECORD is not set.
	 */
	public function setBaseType($baseType) {
		if ($baseType === -1 && $this->isRecord() === false) {
			$msg = "You are forced to specify a baseType if cardinality is not RECORD.";
			throw new InvalidArgumentException($msg);
		}
		
		$this->baseType = $baseType;
	}
	
	/**
	 * Get the value of the Variable.
	 * 
	 * @return int|float|double|boolean|string|Duration|Point|Pair|DirectedPair|Container A value compliant with the QTI Runtime Model.
	 */
	public function getValue() {
		return $this->value;
	}
	
	/**
	 * Set the value of the Variable.
	 * 
	 * @param int|float|double|boolean|string|Duration|Point|Pair|DirectedPair|Container $value A value compliant with the QTI Runtime Model.
	 * @throws InvalidArgumentException If the baseType and cardinality of $value are not compliant with the Variable.
	 */
	public function setValue($value) {
		
		if (Utils::isBaseTypeCompliant($this->getBaseType(), $value) && Utils::isCardinalityCompliant($this->getCardinality(), $value)) {
		    $this->value = $value;
		}
		else {
		    Utils::throwBaseTypeTypingError($this->baseType, $value);
		}
	}
	
	/**
	 * Get the default value of the Variable.
	 * 
	 * @return int|float|double|boolean|string|Duration|Point|Pair|DirectedPair $value A value compliant with the QTI Runtime Model.
	 */
	public function getDefaultValue() {
		return $this->defaultValue;
	}
	
	/**
	 * Set the default value of the Variable.
	 * 
	 * @param int|float|double|boolean|string|Duration|Point|Pair|DirectedPair|Container $defaultValue A value compliant with the QTI Runtime Model.
	 * @throws InvalidArgumentException If $defaultValue's type is not compliant with the qti:baseType of the Variable.
	 */
	public function setDefaultValue($defaultValue) {
		if (Utils::isBaseTypeCompliant($this->getBaseType(), $defaultValue) && Utils::isCardinalityCompliant($this->getCardinality(), $defaultValue)) {
			$this->defaultValue = $defaultValue;
			return;
		}
		else {
		    Utils::throwBaseTypeTypingError($this->getBaseType(), $defaultValue);
		}
	}
	
	/**
	 * Create a runtime Variable object from its Data Model representation.
	 * 
	 * @param VariableDeclaration $variableDeclaration A VariableDeclaration object from the QTI Data Model.
	 * @return Variable A Variable object.
	 * @throws UnexpectedValueException If $variableDeclaration is not consistent.
	 */
	static public function createFromDataModel(VariableDeclaration $variableDeclaration) {
		$identifier = $variableDeclaration->getIdentifier();
		$baseType = $variableDeclaration->getBaseType();
		$cardinality = $variableDeclaration->getCardinality();
			
		$variable = new static($identifier, $cardinality, $baseType);
			
		// Default value?
		$dataModelDefaultValue = $variableDeclaration->getDefaultValue();
		
		if (!empty($dataModelDefaultValue)) {
			$dataModelValues = $dataModelDefaultValue->getValues();
			$defaultValue = static::dataModelValuesToRuntime($dataModelValues, $baseType, $cardinality);
			$variable->setDefaultValue($defaultValue);
		}
			
		return $variable;
	}
	
	/**
	 * Create a QTI Runtime value from Data Model ValueCollection
	 * 
	 * @param ValueCollection $valueCollection A collection of qtism\data\state\Value objects.
	 * @param integer $baseType The baseType the Value objects in the ValueCollection must respect.
	 * @param integer $cardinality The cardinality the Value objects in the ValueCollection must respect.
	 * @throws UnexpectedValueException If $baseType or/and $cardinality are not respected by the Value objects in the ValueCollection.
	 * @return mixed The resulting QTI Runtime value (primitive or container depending on baseType/cardinality).
	 */
	protected static function dataModelValuesToRuntime(ValueCollection $valueCollection, $baseType, $cardinality) {
	    
		// Cardinality?
		// -> Single? Multiple? Ordered? Record?
		if ($cardinality === Cardinality::SINGLE) {
			// We should find a single value in the DefaultValue's values.
			if (count($valueCollection) == 1) {
				$dataModelValue = RuntimeUtils::valueToRuntime($valueCollection[0]->getValue(), $baseType);
				return $dataModelValue;
			}
			else {
				// The Data Model is in an inconsistent state.
				// This should be handled by the Data Model but
				// I prefer to be defensive.
				$msg = "A Data Model VariableDeclaration with 'single' cardinality must contain a single value, ";
				$msg .= count($valueCollection) . " value(s) found.";
				throw new UnexpectedValueException($msg);
			}
		}
		else {
			// Multiple|Ordered|Record, use a container.
			$container = null;
		
			try {
				// Create the appropriate Container object.
				$className = ucfirst(Cardinality::getNameByConstant($cardinality)) . 'Container';
				$nsClassName = 'qtism\\runtime\\common\\' . $className;
				$callback = array($nsClassName , 'createFromDataModel');
				$container = call_user_func_array($callback, array($valueCollection, $baseType));
					
				return $container; // return container.
			}
			catch (InvalidArgumentException $e) {
				$msg = "The default value found in the Data Model Variable Declaration is not consistent. ";
				$msg.= "The values must have a baseType compliant with the baseType of the VariableDeclaration.";
				$msg.= "If the VariableDeclaration's cardinality is 'record', make sure the values it contains have ";
				$msg.= "fieldIdentifiers.";
					
				throw new UnexpectedValueException($msg, 0, $e);
			}
		}
	}
	
	/**
	 * Convenience method.
	 * 
	 * Whether the variable stores a single value.
	 * 
	 * @return boolean
	 */
	public function isSingle() {
		return $this->cardinality === Cardinality::SINGLE;
	}
	
	/**
	 * Convenience method.
	 * 
	 * Whether the variable stores multiple values.
	 * 
	 * @return boolean
	 */
	public function isMultiple() {
		return $this->cardinality === Cardinality::MULTIPLE || $this->cardinality === Cardinality::ORDERED;
	}
	
	/**
	 * Convenience method.
	 * 
	 * Whether the variable stores orered values.
	 * 
	 * @return boolean
	 */
	public function isOrdered() {
		return $this->cardinality === Cardinality::ORDERED;
	}
	
	/**
	 * Convenience method.
	 * 
	 * whether the variable stores values as in a record.
	 * 
	 * @return boolean
	 */
	public function isRecord() {
		return $this->cardinality === Cardinality::RECORD;
	}
	
	/**
	 * Convenience method.
	 * 
	 * Whether the variable's value is numeric. If the variable value
	 * contains NULL, this method return false.
	 * 
	 * @return boolean
	 */
	public function isNumeric() {
		return ($this->IsNull()) ? false : ($this->baseType === BaseType::INTEGER || $this->baseType === BaseType::FLOAT);
	}
	
	/**
	 * Convenience method.
	 * 
	 * Whether the variable's value has the NULL value.
	 * 
	 * Be carefull, the following values as per QTI specification will be considered NULL:
	 * 
	 * * An empty MultipleContainer, OrderedContainer or RecordContainer.
	 * * An empty string.
	 * 
	 * @return boolean
	 */
	public function isNull() {
		$value = $this->getValue();
		// Containers as per QTI Spec, are considered to be NULL if empty.
		if ($value instanceof Container && $value->isNull() === true) {
			return true;
		}
		else if (!$value instanceof Container && !is_null($value) && $this->getBaseType() === BaseType::STRING) {
			return $value->getValue() === '';
		}
		else {
			return is_null($value);
		}
	}
	
	/**
	 * Convenience method.
	 * 
	 * Whether the variable's value is boolean. If the variable's value is NULL, the
	 * method returns false.
	 * 
	 * @return boolean
	 */
	public function isBool() {
		return (!$this->isNull() && $this->getBaseType() === BaseType::BOOLEAN);
	}
	
	/**
	 * Convenience method.
	 * 
	 * Whether the variable's value is float. If the variable's value is NULL, the method
	 * returns false.
	 * 
	 * @return boolean
	 */
	public function isInteger() {
		return (!$this->isNull() && $this->getBaseType() === BaseType::INTEGER);
	}
	
	/**
	 * Convenience method.
	 * 
	 * Whether the variable's value is float. If the variable's value is NULL, the method
	 * returns false.
	 * 
	 * @return boolean
	 */
	public function isFloat() {
		return (!$this->isNull() && $this->getBaseType() === BaseType::FLOAT);
	}
	
	/**
	 * Convenience method.
	 * 
	 * Whether the variable's value is a point. If the variable's value is NULL, the method
	 * returns false.
	 * 
	 * @return boolean
	 */
	public function isPoint() {
		return (!$this->isNull() && $this->getBaseType() === BaseType::POINT);
	}
	
	/**
	 * Convenience method.
	 * 
	 * Whether the variable's value is a pair. If the variable's value is NULL, the method
	 * returns false.
	 * 
	 * Be carefull! This method considers that a directedPair is also a pair.
	 * 
	 * @return boolean
	 */
	public function isPair() {
		return (!$this->isNull() && ($this->getBaseType() === BaseType::PAIR) || $this->getBaseType() === BaseType::DIRECTED_PAIR);
	}
	
	/**
	 * Convenience method.
	 * 
	 * Whether the variable's value is a directedPair. If the variable's value is NULL, the method
	 * returns false.
	 * 
	 * @return boolean
	 */
	public function isDirectedPair() {
		return (!$this->isNull() && $this->getBaseType() === BaseType::DIRECTED_PAIR);
	}
	
	/**
	 * Convenience method.
	 * 
	 * Whether the variable's value is a duration. If the variable's value is NULL, the method
	 * returns false.
	 * 
	 * @return boolean
	 */
	public function isDuration() {
		return (!$this->isNull() && $this->getBaseType() === BaseType::DURATION);
	}
	
	/**
	 * Convenience method.
	 * 
	 * Whether the variable's value is a string. If the variable's value is NULL, the method
	 * returns false.
	 * 
	 * @return boolean
	 */
	public function isString() {
		return (!$this->isNull() && $this->getBaseType() === BaseType::STRING);
	}
	
	/**
	 * Set the value of the Variable with its default value. If no default
	 * value was given, the value of the variable becomes NULL.
	 * 
	 */
	public function applyDefaultValue() {
		$this->setValue($this->getDefaultValue());
	}
	
	public function __clone() {
	    if (($v = $this->value) !== null) {
	        $this->value = clone $v;
	    }
	    
	    if (($dv = $this->defaultValue) !== null) {
	        $this->defaultValue = clone $dv;
	    }
	}
}