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
use \InvalidArgumentException;
use qtism\data\QtiComponent;
use qtism\data\state\Value;

/**
 * The MatchTableEntry class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class InterpolationTableEntry extends QtiComponent {
	
	/**
	 * From IMS QTI:
	 * 
	 * The lower bound for the source value to match this entry.
	 * 
	 * @var float
	 * @qtism-bean-property
	 */
	private $sourceValue;
	
	/**
	 * From IMS QTI:
	 * 
	 * The target value that is used to set the outcome when a match is found.
	 * 
	 * @var mixed
	 * @qtism-bean-property
	 */
	private $targetValue;
	
	/**
	 * From IMS QTI:
	 * 
	 * Determines if an exact match of sourceValue matches this entry. 
	 * If true, the default, then an exact match of the value is considered a
	 * match of this entry.
	 * 
	 * @var boolean
	 * @qtism-bean-property
	 */
	private $includeBoundary = true;
	
	/**
	 * Create a new instance of InterpolationTableEntry.
	 * 
	 * @param float $sourceValue The lower bound for the source value to match this entry.
	 * @param mixed $targetValue The target value that is used to set the outcome when a match is found.
	 * @param boolean $includeBoundary Determines if an exact match of $sourceValue matches this entry.
	 * @throws InvalidArgumentException If $sourceValue is not a float or $includeBoundary is not a boolean.
	 */
	public function __construct($sourceValue, $targetValue, $includeBoundary = true) {
		$this->setSourceValue($sourceValue);
		$this->setTargetValue($targetValue);
		$this->setIncludeBoundary($includeBoundary);
	}
	
	/**
	 * Get the lower bound for the source value to match this entry.
	 * 
	 * @return float A float value.
	 */
	public function getSourceValue() {
		return $this->sourceValue;
	}
	
	/**
	 * Set the lower bound for the source value to match this entry.
	 * 
	 * @param float $sourceValue A float value.
	 * @throws InvalidArgumentException If $sourceValue is not a float.
	 */
	public function setSourceValue($sourceValue) {
		if (is_float($sourceValue)) {
			$this->sourceValue = $sourceValue;
		}
		else {
			$msg = "SourceValue must be a float value, '" . gettype($sourceValue) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the value that is used to set the ouctome when a match is found.
	 * 
	 * @return mixed A value that satisfies the QTI baseType datatype.
	 */
	public function getTargetValue() {
		return $this->targetValue;
	}
	
	/**
	 * Set the value that is used to set the ouctome when a match is found.
	 * 
	 * @param mixed $targetValue A value that satisfies the QTI baseType datatype.
	 */
	public function setTargetValue($targetValue) {
		$this->targetValue = $targetValue;
	}
	
	/**
	 * Set if an exact match of the sourceValue attribute matches this entry.
	 * 
	 * @param boolean $includeBoundary A boolean value.
	 * @throws InvalidArgumentException If $includeBoundary is not a boolean.
	 */
	public function setIncludeBoundary($includeBoundary) {
		if (is_bool($includeBoundary)) {
			$this->includeBoundary = $includeBoundary;
		}
		else {
			$msg = "IncludeBoudary must be a boolean value, '" . gettype($includeBoundary) . "' given";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * State if an exact match of the sourceValue attribute matches this entry. 
	 * 
	 * @return boolean A boolean value.
	 */
	public function doesIncludeBoundary() {
		return $this->includeBoundary;
	}
	
	public function getQtiClassName() {
		return 'interpolationTableEntry';
	}
	
	public function getComponents() {
		return new QtiComponentCollection();
	}
}
