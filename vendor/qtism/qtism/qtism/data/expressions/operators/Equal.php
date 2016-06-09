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


namespace qtism\data\expressions\operators;

use qtism\data\expressions\ExpressionCollection;
use qtism\common\utils\Format;
use \InvalidArgumentException;
use \UnexpectedValueException;

/**
 * From IMS QTI:
 * 
 * The equal operator takes two sub-expressions which must both have single cardinality 
 * and have a numerical base-type. The result is a single boolean with a value of true 
 * if the two expressions are numerically equal and false if they are not. If either 
 * sub-expression is NULL then the operator results in NULL.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Equal extends Operator {
	
	/**
	 * From IMS QTI:
	 * 
	 * When comparing two floating point numbers for equality it is often desirable to 
	 * have a tolerance to ensure that spurious errors in scoring are not introduced by 
	 * rounding errors. The tolerance mode determines whether the comparison is done 
	 * exactly, using an absolute range or a relative range.
	 * 
	 * @var integer
	 * @qtism-bean-property
	 */
	private $toleranceMode = ToleranceMode::EXACT;
	
	/**
	 * Index [0] is t0 and index [1] is t1.
	 * 
	 * From IMS QTI:
	 * 
	 * If the tolerance mode is absolute or relative then the tolerance must be specified.
	 * The tolerance consists of two positive numbers, t0 and t1, that define the lower and 
	 * upper bounds. If only one value is given it is used for both.
	 * 
	 * In absolute mode the result of the comparison is true if the value of the second 
	 * expression, y is within the following range defined by the first value, x.
	 * 
	 * x-t0,x+t1
	 * 
	 * In relative mode, t0 and t1 are treated as percentages and the following range is used instead.
	 * 
	 * x*(1-t0/100),x*(1+t1/100)
	 * 
	 * @var array
	 * @qtism-bean-property
	 */
	private $tolerance = array();
	
	/**
	 * From IMS QTI:
	 * 
	 * Controls whether or not the lower bound is included in the comparison.
	 * 
	 * @var boolean
	 * @qtism-bean-property
	 */
	private $includeLowerBound = true;
	
	/**
	 * FROM IMS QTI:
	 * 
	 * @var boolean
	 * @qtism-bean-property
	 */
	private $includeUpperBound = true;
	
	/**
	 * Create a new Equal object.
	 * 
	 * @param ExpressionCollection $expressions A collection of Expression objects.
	 * @param integer $toleranceMode The tolerance mode, a value from the ToleranceMode enumeration.
	 * @param array $tolerance An array of 1 or 2 elements which are float or variableRef values.
	 * @param boolean $includeLowerBound Whether or not to include the lower bound in the comparison. 
	 * @param boolean $includeUpperBound Whether or not to include the upper bound in the comparison.
	 * @throws UnexpectedValueException If The tolerance argument is ABSOLUTE or RELATIVE but no $tolerance array is given.
	 * @throws InvalidArgumentException If $toleranceMode is not a value from the ToleranceMode, if $tolerance is not a valid tolerance array, if $includeLowerBound/$includeUpperBound is not a boolean.
	 */
	public function __construct(ExpressionCollection $expressions, $toleranceMode = ToleranceMode::EXACT, $tolerance = array(), $includeLowerBound = true, $includeUpperBound = true) {
		parent::__construct($expressions, 2, 2, array(OperatorCardinality::SINGLE), array(OperatorBaseType::INTEGER, OperatorBaseType::FLOAT));
		$this->setToleranceMode($toleranceMode);
		
		if (($this->getToleranceMode() == ToleranceMode::ABSOLUTE || $this->getToleranceMode() == ToleranceMode::RELATIVE) && empty($tolerance)) {
			$msg = "The tolerance argument must be specified when ToleranceMode = ABSOLUTE or EXACT.";
			throw new UnexpectedValueException($msg);
		}
		
		$this->setTolerance($tolerance);
		$this->setIncludeLowerBound($includeLowerBound);
		$this->setIncludeUpperBound($includeUpperBound);
	}
	
	/**
	 * Set the tolerance mode.
	 * 
	 * @param integer $toleranceMode A value from the ToleranceMode enumeration.
	 * @throws InvalidArgumentException If $toleranceMode is not a value from the ToleranceMode enumeration.
	 */
	public function setToleranceMode($toleranceMode) {
		if (in_array($toleranceMode, ToleranceMode::asArray())) {
			$this->toleranceMode = $toleranceMode;
		}
		else {
			$msg = "The toleranceMode argument must be a value from the ToleranceMode enumeration, '" . $toleranceMode . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the tolerance mode.
	 * 
	 * @return integer A value from the ToleranceMode enumeration.
	 */
	public function getToleranceMode() {
		return $this->toleranceMode;
	}
	
	/**
	 * Set the tolerance array where index[0] is t0 and index[1] is t1.
	 * 
	 * Please note that if there is no t1, t0 is used as t0 AND t1.
	 * 
	 * @param array $tolerance An array of float|variableRef.
	 * @throws InvalidArgumentException If the $tolerance count is less than 1 or greather than 2.
	 */
	public function setTolerance(array $tolerance) {
		if (($this->getToleranceMode() == ToleranceMode::ABSOLUTE || $this->getToleranceMode() == ToleranceMode::RELATIVE) && count($tolerance) < 1) {
			$msg = "The tolerance array must contain at least t0.";
			throw new InvalidArgumentException($msg);
		}
		else if (($this->getToleranceMode() == ToleranceMode::ABSOLUTE || $this->getToleranceMode() == ToleranceMode::RELATIVE) && count($tolerance) > 2) {
			$msg = "The tolerance array must contain at most t0 and t1";
			throw new InvalidArgumentException($msg);
		}
		
		$this->tolerance = $tolerance;
	}
	
	/**
	 * Get the tolerance array where index[0] is t0 and index[1] is t1.
	 * 
	 * Please note that if there is no t1, t0 is used as t0 AND t1.
	 * 
	 * @return array An array of float|variableRef.
	 */
	public function getTolerance() {
		return $this->tolerance;
	}
	
	/**
	 * Set whether or not the lower bound must be included in the comparison.
	 * 
	 * @param boolean $includeLowerBound
	 * @throws InvalidArgumentException If $includedLowerBound is not a boolean value.
	 */
	public function setIncludeLowerBound($includeLowerBound) {
		if (is_bool($includeLowerBound)) {
			$this->includeLowerBound = $includeLowerBound;
		}
		else {
			$msg = "The includeLowerBound argument must be a boolean, '" . gettype($includelowerBound) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Whether or not the lower bound must be included in the comparison.
	 * 
	 * @return boolean
	 */
	public function doesIncludeLowerBound() {
		return $this->includeLowerBound;
	}
	
	/**
	 * Set whether or not the upper bound must be included in the comparison.
	 * 
	 * @param boolean $includeUpperBound
	 * @throws InvalidArgumentException If $includeUpperBound is not a boolean.
	 */
	public function setIncludeUpperBound($includeUpperBound) {
		if (is_bool($includeUpperBound)) {
			$this->includeUpperBound = $includeUpperBound;
		}
		else {
			$msg = "The includeUpperBound argument must be a boolean, '" . gettype($includeUpperBound) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Whether or not the upper bound must be included in the comparison.
	 * 
	 * @return boolean
	 */
	public function doesIncludeUpperBound() {
		return $this->includeUpperBound;
	}
	
	public function getQtiClassName() {
		return 'equal';
	}
}
