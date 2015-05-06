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

/**
 * From IMS QTI:
 * 
 * A special class used to create a mapping from a source set of any 
 * baseType (except file and duration) to a single float. Note that 
 * mappings from values of base type float should be avoided due to the 
 * difficulty of matching floating point values, see the match operator 
 * for more details. When mapping containers the result is the sum of 
 * the mapped values from the target set. See mapResponse for details.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Mapping extends QtiComponent {
	
	/**
	 * From IMS QTI:
	 * 
	 * The lower bound for the result of mapping a container. If unspecified
	 * there is no lower-bound.
	 * 
	 * @var float|boolean
	 * @qtism-bean-property
	 */
	private $lowerBound = false;
	
	/**
	 * From IMS QTI:
	 * 
	 * The upper bound for the result of mapping a container. If unspecified
	 * there is no upper-bound.
	 * 
	 * @var float|boolean
	 * @qtism-bean-property
	 */
	private $upperBound = false;
	
	/**
	 * From IMS QTI:
	 * 
	 * The default value from the target set to be used when no explicit
	 * mapping for a source value is given.
	 * 
	 * @var float
	 * @qtism-bean-property
	 */
	private $defaultValue = 0.0;

	/**
	 * From IMS QTI:
	 * 
	 * The map is defined by a set of mapEntries, each of which maps a
	 * single value from the source set onto a single float.
	 * 
	 * @var MapEntryCollection
	 * @qtism-bean-property
	 */
	private $mapEntries;
	
	/**
	 * Create a new Mapping object.
	 * 
	 * @param MapEntryCollection $mapEntries A collection of MapEntry which compose the Mapping object to be created.
	 * @param float|boolean $lowerBound A lower bound. Give false if not specified.
	 * @param float|boolean $upperBound An upper bound. Give false if not specified.
	 * @param integer|float $defaultValue A default value. Default is 0.
	 * @throws InvalidArgumentException If $defaultValue is not a float, if $lowerBound or $upperBound are not floats nor false, If $mapEntries is an empty collection.
	 */
	public function __construct(MapEntryCollection $mapEntries, $defaultValue = 0.0, $lowerBound = false, $upperBound = false) {
		$this->setLowerBound($lowerBound);
		$this->setUpperBound($upperBound);
		$this->setDefaultValue($defaultValue);
		$this->setMapEntries($mapEntries);
	}
	
	/**
	 * Set the lower bound.
	 * 
	 * @param boolean|float $lowerBound A float or false if not lower bound.
	 * @throws InvalidArgumentException If $lowerBound is not a float nor false.
	 */
	public function setLowerBound($lowerBound) {
		if (is_float($lowerBound) || is_double($lowerBound) || (is_bool($lowerBound) && $lowerBound === false)) {
			$this->lowerBound = $lowerBound;
		}
		else {
			$msg = "The 'lowerBound' attribute must be a float or false, '" . gettype($lowerBound) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the lower bound.
	 * 
	 * @return boolean|float A float value or false if not specified.
	 */
	public function getLowerBound() {
		return $this->lowerBound;
	}
	
	/**
	 * Whether the Mapping has a lower bound.
	 * 
	 * @return boolean
	 */
	public function hasLowerBound() {
		return $this->getLowerBound() !== false;
	}
	
	/**
	 * Set the upper bound.
	 * 
	 * @param boolean|float $upperBound A float value or false if not specified.
	 * @throws InvalidArgumentException If $upperBound is not a float nor false.
	 */
	public function setUpperBound($upperBound) {
		if (is_float($upperBound) || is_double($upperBound) || (is_bool($upperBound) && $upperBound === false)) {
			$this->upperBound = $upperBound;
		}
		else {
			$msg = "The 'upperBound' argument must be a float or false, '" . gettype($lowerBound) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the upper bound.
	 * 
	 * @return float|boolean A float value or false if not specified.
	 */
	public function getUpperBound() {
		return $this->upperBound;
	}
	
	/**
	 * Whether the Mapping has an upper bound.
	 * 
	 * @return boolean
	 */
	public function hasUpperBound() {
		return $this->getUpperBound() !== false;
	}
	
	/**
	 * Set the default value of the Mapping.
	 * 
	 * @param float $defaultValue A float value.
	 * @throws InvalidArgumentException If $defaultValue is not a float value.
	 */
	public function setDefaultValue($defaultValue) {
		if (is_numeric($defaultValue)) {
			$this->defaultValue = $defaultValue;
		}
		else {
			$msg = "The 'defaultValue' argument must be a numeric value, '" . gettype($defaultValue) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the default value of the Mapping.
	 * 
	 * @return float A default value as a float.
	 */
	public function getDefaultValue() {
		return $this->defaultValue;
	}
	
	/**
	 * Set the collection of MapEntry objects which compose the Mapping.
	 * 
	 * @param MapEntryCollection $mapEntries A collection of MapEntry objects with at least one item.
	 * @throws InvalidArgumentException If $mapEnties is an empty collection.
	 */
	public function setMapEntries(MapEntryCollection $mapEntries) {
		if (count($mapEntries) > 0) {
			$this->mapEntries = $mapEntries;
		}
		else {
			$msg = "A Mapping object must contain at least one MapEntry object, none given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the collection of MapEntry objects which compose the Mapping.
	 * 
	 * @return MapEntryCollection A collection of MapEntry objects.
	 */
	public function getMapEntries() {
		return $this->mapEntries;
	}
	
	public function getQtiClassName() {
		return 'mapping';
	}
	
	public function getComponents() {
		return new QtiComponentCollection($this->getMapEntries()->getArrayCopy());
	}
}
