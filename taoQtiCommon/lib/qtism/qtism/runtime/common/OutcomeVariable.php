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

use qtism\common\datatypes\Float;
use qtism\common\datatypes\Integer;
use qtism\common\enums\BaseType;
use qtism\data\state\OutcomeDeclaration;
use qtism\data\state\VariableDeclaration;
use qtism\data\state\LookupTable;
use qtism\data\ViewCollection;
use qtism\common\enums\Cardinality;
use \InvalidArgumentException;
use \UnexpectedValueException;

/**
 * This class represents an Outcome Variable in a QTI Runtime context.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class OutcomeVariable extends Variable {
	
	/**
	 * The intended audiance of the OutcomeVariable.
	 * 
	 * @var ViewCollection
	 */
	private $views;
	
	/**
	 * The normal maximum.
	 * 
	 * @var boolean|float
	 */
	private $normalMaximum = false;
	
	/**
	 * The normal minimum.
	 * 
	 * @var boolean|float
	 */
	private $normalMinimum = false;
	
	/**
	 * The mastery value.
	 * 
	 * @var boolean|float
	 */
	private $masteryValue = false;
	
	/**
	 * The QTI Data Model lookupTable bound to the OutcomeVariable.
	 * 
	 * @var LookupTable
	 */
	private $lookupTable = null;
	
	/**
	 * Create a new OutcomeVariable object. If the cardinality is multiple, ordered or record,
	 * the appropriate container will be instantiated internally as the $value argument.
	 * 
	 * @param string $identifier An identifier for the variable.
	 * @param integer $cardinality A value from the Cardinality enumeration.
	 * @param integer $baseType A value from the BaseType enumeration. -1 can be given to state there is no particular baseType if $cardinality is Cardinality::RECORD.
	 * @param int|float|double|boolean|string|Duration|Point|Pair|DirectedPair $value A value which is compliant with the QTI Runtime Model.
	 * @throws InvalidArgumentException If $identifier is not a string, if $baseType is not a value from the BaseType enumeration, if $cardinality is not a value from the Cardinality enumeration, if $value is not compliant with the QTI Runtime Model.
	 */
	public function __construct($identifier, $cardinality, $baseType = -1, $value = null) {
		parent::__construct($identifier, $cardinality, $baseType, $value);
	}
	
	public function getViews() {
		return $this->views;
	}
	
	public function setViews(ViewCollection $views) {
		$this->views = $views;
	}
	
	/**
	 * Set the normal maximum.
	 * 
	 * @param float|double|boolean $normalMaximum The normal maximum or false if not defined.
	 * @throws InvalidArgumentException If $normalMaximum is not false nor a floating point value.
	 */
	public function setNormalMaximum($normalMaximum) {
		if ((is_bool($normalMaximum) && $normalMaximum === false) || is_float($normalMaximum) || is_double($normalMaximum)) {
			$this->normalMaximum = $normalMaximum;
		}
		else {
			$msg = "The normalMaximum argument must be a floating point value or false, '" . gettype($normalMaximum) . "' given.";
			throw new InvalidArgumentException($msg); 
		}
	}
	
	/**
	 * Get the normal maximum.
	 * 
	 * @return boolean|float|double False if not defined, otherwise a floating point value.
	 */
	public function getNormalMaximum() {
		return $this->normalMaximum;
	}
	
	/**
	 * Set the normal minimum.
	 *
	 * @param float|double|boolean $normalMinimum The normal minimum or false if not defined.
	 * @throws InvalidArgumentException If $normalMinimum is not false nor a floating point value.
	 */
	public function setNormalMinimum($normalMinimum) {
		if ((is_bool($normalMinimum) && $normalMinimum === false) || is_float($normalMinimum) || is_double($normalMinimum)) {
			$this->normalMinimum = $normalMinimum;
		}
		else {
			$msg = "The normalMinimum argument must be a floating point value or false, '" . gettype($normalMinimum) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the normal minimum.
	 *
	 * @return boolean|float|double False if not defined, otherwise a floating point value.
	 */
	public function getNormalMinimum() {
		return $this->normalMinimum;
	}
	
	/**
	 * Set the mastery value.
	 * 
	 * @param float|double|boolean $masteryValue A floating point value or false if not defined.
	 * @throws InvalidArgumentException If $masteryValue is not a floating point value nor false.
	 */
	public function setMasteryValue($masteryValue) {
		if ((is_bool($masteryValue) && $masteryValue === false) || is_float($masteryValue) || is_double($masteryValue)) {
			$this->masteryValue = $masteryValue;
		}
		else {
			$msg = "The masteryValue argument must be a floating point value or false, '" . gettype($masteryValue) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the mastery value.
	 * 
	 * @return float|double|boolean False if not defined, otherwise a floating point value.
	 */
	public function getMasteryValue() {
		return $this->masteryValue;
	}
	
	/**
	 * Set the lookup table.
	 * 
	 * @param LookupTable $lookupTable A QTI Data Model LookupTable object or null if not specified.
	 */
	public function setLookupTable(LookupTable $lookupTable = null) {
		$this->lookupTable = $lookupTable;
	}
	
	/**
	 * Get the lookup table.
	 * 
	 * @return LookupTable A QTI Data Model LookupTable object or null if not defined.
	 */
	public function getLookupTable() {
		return $this->lookupTable;
	}
	
	public static function createFromDataModel(VariableDeclaration $variableDeclaration) {
		$variable = parent::createFromDataModel($variableDeclaration);
		
		if ($variableDeclaration instanceof OutcomeDeclaration) {
			
			$variable->setViews($variableDeclaration->getViews());
			$variable->setNormalMaximum($variableDeclaration->getNormalMaximum());
			$variable->setNormalMinimum($variableDeclaration->getNormalMinimum());
			$variable->setMasteryValue($variableDeclaration->getMasteryValue());
			$variable->setLookupTable($variableDeclaration->getLookupTable());
			
			return $variable;
		}
		else {
			$msg = "OutcomeVariable::createFromDataModel only accept 'qtism\\data\\state\\OutcomeDeclaration' objects, '" . get_class($variableDeclaration) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Apply the default value to the OutcomeVariable.
	 * 
	 * If no default value is described, and the cardinality is single and the baseType
	 * is integer or float, the value of the variable becomes 0.
	 * 
	 */
	public function applyDefaultValue() {
		parent::applyDefaultValue();
		
		if (is_null($this->getDefaultValue()) === true && $this->getCardinality() === Cardinality::SINGLE) {
			if ($this->getBaseType() === BaseType::INTEGER) {
				$this->setValue(new Integer(0));
			}
			else if ($this->getBaseType() === BaseType::FLOAT) {
				$this->setValue(new Float(0.0));
			}
		}
	}
	
	public function __clone() {
	    parent::__clone();
	}
}