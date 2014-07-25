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

use qtism\common\Comparable;
use qtism\data\state\CorrectResponse;
use qtism\data\state\Mapping;
use qtism\data\state\AreaMapping;
use qtism\data\state\ResponseDeclaration;
use qtism\data\state\VariableDeclaration;
use \InvalidArgumentException;

/**
 * This class represents a Response Variable in the QTI Runtime Model.
 * 
 * A note from IMS about response variables initialization:
 * 
 * At runtime, response variables are instantiated as part of an item session. Their values are always initialized to 
 * NULL (no value) regardless of whether or not a default value is given in the declaration. A response variable with a NULL
 * value indicates that the candidate has not offered a response, either because they have not attempted the item at all or 
 * because they have attempted it and chosen not to provide a response.
 * 
 * If a default value has been provided for a response variable then the variable is set to this value at the start of the 
 * first attempt. If the candidate never attempts the item, in other words, the item session passes straight from the initial 
 * state to the closed state without going through the interacting state, then the response variable remains NULL and the 
 * default value is never used.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class ResponseVariable extends Variable {
	
	/**
	 * The correct response from the QTI Data Model as QTI Runtime value (primitive or container).
	 * 
	 * @var mixed
	 */
	private $correctResponse = null;
	
	/**
	 * The mapping from the QTI Data Model.
	 * 
	 * @var Mapping
	 */
	private $mapping = null;
	
	/**
	 * The AreaMapping from the QTI Data Model.
	 * 
	 * @var AreaMapping
	 */
	private $areaMapping = null;
	
	/**
	 * Create a new ResponseVariable object. If the cardinality is multiple, ordered or record,
	 * the appropriate container will be instantiated interally as the $value argument.
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
	
	/**
	 * Set the correct response.
	 * 
	 * @param mixed $correctResponse A QTI Runtime compliant object.
	 */
	public function setCorrectResponse($correctResponse) {
		if (Utils::isBaseTypeCompliant($this->getBaseType(), $correctResponse) === true) {
			$this->correctResponse = $correctResponse;
			return;
		}
		else if ($correctResponse instanceof Container) {
			if ($correctResponse->getCardinality() === $this->getCardinality()) {
		
				if (get_class($correctResponse) === 'qtism\\runtime\\common\\Container' ||
						$correctResponse->getBaseType() === $this->getBaseType()) {
					// This is a simple container with no baseType restriction
					// or a Multiple|Record|Ordered container with a compliant
					// baseType.
					$this->correctResponse = $correctResponse;
					return;
				}
				else {
					$msg = "The baseType of the given container ('" . BaseType::getNameByConstant($correctResponse->getBaseType()) . "') ";
					$msg.= "is not compliant with ";
					$msg.= "the baseType of the variable ('" . BaseType::getNameByConstant($this->getBaseType()) . "').";
					throw new InvalidArgumentException($msg);
				}
			}
			else {
				$msg = "The cardinality of the given container ('" . Cardinality::getNameByConstant($value->getCardinality()) . "') ";
				$msg.= "is not compliant with ";
				$msg.= "the cardinality of the variable ('" . Cardinality::getNameByConstant($this->getCardinality()) . "').";
				throw new InvalidArgumentException($msg);
			}
		}
		
		$msg = "The provided value is not compliant with the baseType of the ResponseVariable.";
		throw new InvalidArgumentException($msg);
	}
	
	/**
	 * Get the correct response.
	 * 
	 * @return mixed A QTI Runtime value (primitive or container).
	 */
	public function getCorrectResponse() {
		return $this->correctResponse;
	}
	
	/**
	 * Whether the ResponseVariable holds a CorrectResponse object.
	 * 
	 * @return boolean
	 */
	public function hasCorrectResponse() {
	    return is_null($this->getCorrectResponse()) === false;
	}
	
	/**
	 * Set the mapping.
	 * 
	 * @param Mapping $mapping A Mapping object from the QTI Data Model.
	 */
	public function setMapping(Mapping $mapping = null) {
		$this->mapping = $mapping;
	}
	
	/**
	 * Get the mapping.
	 * 
	 * @return Mapping A mapping object from the QTI Data Model.
	 */
	public function getMapping() {
		return $this->mapping;
	}
	
	/**
	 * Set the area mapping.
	 * 
	 * @param AreaMapping $areaMapping An AreaMapping object from the QTI Data Model.
	 */
	public function setAreaMapping(AreaMapping $areaMapping = null) {
		$this->areaMapping = $areaMapping;
	}
	
	/**
	 * Get the area mapping.
	 * 
	 * @return AreaMapping An AreaMapping object from the QTI Data Model.
	 */
	public function getAreaMapping() {
		return $this->areaMapping;
	}
	
	/**
	 * Whether the value of the ResponseVariable matches its
	 * correct response.
	 * 
	 * @return boolean
	 */
	public function isCorrect() {
	    if ($this->hasCorrectResponse() === true) {
	        $correctResponse = $this->getCorrectResponse();
	        
	        if ($correctResponse instanceof Comparable) {
	            return $correctResponse->equals($this->getValue());
	        }
	        else {
	            return $correctResponse === $this->getValue();
	        }
	    }
	    
	    return false;
	}
	
	public static function createFromDataModel(VariableDeclaration $variableDeclaration) {
		$variable = parent::createFromDataModel($variableDeclaration);
	
		if ($variableDeclaration instanceof ResponseDeclaration) {
			
			$variable->setMapping($variableDeclaration->getMapping());
			$variable->setAreaMapping($variableDeclaration->getAreaMapping());
			
			$dataModelCorrectResponse = $variableDeclaration->getCorrectResponse();
			if (!empty($dataModelCorrectResponse)) {
				$baseType = $variable->getBaseType();
				$cardinality = $variable->getCardinality();
				$dataModelValues = $dataModelCorrectResponse->getValues();
				$correctResponse = static::dataModelValuesToRuntime($dataModelValues, $baseType, $cardinality);
				
				$variable->setCorrectResponse($correctResponse);
			}
				
			return $variable;
		}
		else {
			$msg = "ResponseVariable::createFromDataModel only accept 'qtism\\data\\state\\ResponseDeclaration' objects, '" . get_class($variableDeclaration) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	public function __clone() {
	    parent::__clone();
	}
}