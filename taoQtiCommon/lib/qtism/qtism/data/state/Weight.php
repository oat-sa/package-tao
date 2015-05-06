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

use qtism\data\QtiIdentifiable;
use qtism\data\QtiComponentCollection;
use qtism\data\QtiComponent;
use \SplObserver;
use \SplObjectStorage;
use \InvalidArgumentException as InvalidArgumentException;
use qtism\common\utils\Format as Format;

/**
 * From IMS QTI:
 * 
 * The contribution of an individual item score to an overall test score typically 
 * varies from test to test. The score of the item is said to be weighted. Weights 
 * are defined as part of each reference to an item (assessmentItemRef) within a test.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Weight extends QtiComponent implements QtiIdentifiable {
	
	/**
	 * A QTI identifier.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $identifier;
	
	/**
	 * A floating point value corresponding to the wheight to be applied on outcome
	 * variables.
	 * 
	 * @var int|float
	 * @qtism-bean-property
	 */
	private $value;
	
	/**
	 * The observers of this object.
	 * 
	 * @var SplObjectStorage
	 */
	private $observers;
	
	/**
	 * Create a new instance of Weight.
	 * 
	 * @param string $identifier A QTI identifier.
	 * @param int|float $value An integer/float value.
	 * @throws InvalidArgumentException If $identifier is not a valid QTI identifier or if $value is not a float nor an integer.
	 */
	public function __construct($identifier, $value) {
		$this->setObservers(new SplObjectStorage());
		
		$this->setIdentifier($identifier);
		$this->setValue($value);
	}
	
	/**
	 * Get the identifier of the Weight.
	 * 
	 * @return string A QTI identifier.
	 */
	public function getIdentifier() {
		return $this->identifier;
	}
	
	/**
	 * Set the identifier of the Weight.
	 * 
	 * @param string $identifier A QTI Identifier.
	 * @throws InvalidArgumentException If $identifier is not a valid QTI identifier.
	 */
	public function setIdentifier($identifier) {
		if (Format::isIdentifier($identifier, false)) {
			
			$this->identifier = $identifier;
			$this->notify();
		}
		else {
			$msg = "'${identifier}' is not a valid QTI Identifier.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the value of the Weight.
	 * 
	 * @return int|float An integer/float value.
	 */
	public function getValue() {
		return $this->value;
	}
	
	/**
	 * Set the value of the Weight.
	 * 
	 * @param int|float $value A in integer/float value.
	 * @throws InvalidArgumentException If $value is not an integer nor a float.
	 */
	public function setValue($value) {
		if (is_int($value) || is_float($value)) {
			$this->value = $value;
		}
		else {
			$msg = "The value of a Weight must be a valid integer or float value, '" . gettype($value) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	public function getQtiClassName() {
		return 'weight';
	}
	
	public function getComponents() {
		return new QtiComponentCollection();
	}
	
	/**
	 * Get the observers of the object.
	 *
	 * @return SplObjectStorage An SplObjectStorage object.
	 */
	protected function getObservers() {
		return $this->observers;
	}
	
	/**
	 * Set the observers of the object.
	 *
	 * @param SplObjectStorage $observers An SplObjectStorage object.
	 */
	protected function setObservers(SplObjectStorage $observers) {
		$this->observers = $observers;
	}
	
	/**
	 * SplSubject::attach implementation.
	 *
	 * @param SplObserver An SplObserver object.
	 */
	public function attach(SplObserver $observer) {
		$this->getObservers()->attach($observer);
	}
	
	/**
	 * SplSubject::detach implementation.
	 *
	 * @param SplObserver $observer An SplObserver object.
	 */
	public function detach(SplObserver $observer) {
		$this->getObservers()->detach($observer);
	}
	
	/**
	 * SplSubject::notify implementation.
	 */
	public function notify() {
		foreach ($this->getObservers() as $observer) {
			$observer->update($this);
		}
	}
}
