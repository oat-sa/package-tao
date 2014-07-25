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
use qtism\data\state\Value;
use \InvalidArgumentException;

class MatchTableEntry extends QtiComponent {
	
	/**
	 * From IMS QTI:
	 * 
	 * The source integer that must be matched exactly.
	 * 
	 * @var int
	 * @qtism-bean-property
	 */
	private $sourceValue;
	
	/**
	 * From IMS QTI:
	 * 
	 * The target value that is used to set the outcome when a match is found.
	 *
	 * 
	 * @var mixed
	 * @qtism-bean-property
	 */
	private $targetValue;
	
	/**
	 * Create a new instance of MatchTableEntry.
	 * 
	 * @param int $sourceValue The source integer that must be matched exactly.
	 * @param mixed $targetValue The target value compliant with the baseType datatype.
	 */
	public function __construct($sourceValue, $targetValue) {
		$this->setSourceValue($sourceValue);
		$this->setTargetValue($targetValue);
	}
	
	/**
	 * Get the source integer that must be matched exactlty.
	 * 
	 * @return int An integer value.
	 */
	public function getSourceValue() {
		return $this->sourceValue;
	}
	
	/**
	 * Set the source integer that must be matched exactly.
	 * 
	 * @param integer $sourceValue An integer value.
	 * @throws InvalidArgumentException If $sourceValue is not an integer.
	 */
	public function setSourceValue($sourceValue) {
		if (is_int($sourceValue)) {
			$this->sourceValue = $sourceValue;
		}
		else {
			$msg = "SourceValue must be an integer, '" . gettype($sourceValue) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the target value.
	 * 
	 * @return mixed A value compliant with the QTI baseType datatype.
	 */
	public function getTargetValue() {
		return $this->targetValue;
	}
	
	/**
	 * Set the target value.
	 * 
	 * @param mixed $targetValue A Value object.
	 */
	public function setTargetValue($targetValue) {
		$this->targetValue = $targetValue;
	}
	
	public function getQtiClassName() {
		return 'matchTableEntry';
	}
	
	public function getComponents() {
		return new QtiComponentCollection();
	}
}
