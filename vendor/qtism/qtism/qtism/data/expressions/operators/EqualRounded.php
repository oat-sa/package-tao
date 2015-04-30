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

use qtism\common\utils\Format;
use qtism\data\expressions\ExpressionCollection;
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * The equalRounded operator takes two sub-expressions which must both have single 
 * cardinality and have a numerical base-type. The result is a single boolean with 
 * a value of true if the two expressions are numerically equal after rounding and 
 * false if they are not. If either sub-expression is NULL then the operator results 
 * in NULL.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class EqualRounded extends Operator {
	
	/**
	 * From IMS QTI:
	 * 
	 * Numbers are rounded to a given number of significantFigures or decimalPlaces.
	 * 
	 * @var integer
	 * @qtism-bean-property
	 */
	private $roundingMode = RoundingMode::SIGNIFICANT_FIGURES;
	
	/**
	 * From IMS QTI:
	 * 
	 * The number of figures to round to. 
	 * If roundingMode= "significantFigures", the value of figures must be a non-zero positive integer.
	 * If roundingMode= "decimalPlaces", the value of figures must be an integer greater than or equal to zero.
	 * 
	 * @var string|integer
	 * @qtism-bean-property
	 */
	private $figures;
	
	/**
	 * Create a new EqualRounded object.
	 * 
	 * @param ExpressionCollection $expressions A collection of Expression objects.
	 * @param string|integer $figures The number of figures to round to. It must be an integer or a variable reference. 
	 * @param integer $roundingMode A value from the RoundingMode enumeration.
	 * @throws InvalidArgumentException If $figures is not an integer nor a variable reference, if $roundingMode is not a value from the RoundingMode enumeration, or if the $expressions count exceeds 2.
	 */
	public function __construct(ExpressionCollection $expressions, $figures, $roundingMode = RoundingMode::SIGNIFICANT_FIGURES) {
		parent::__construct($expressions, 2, 2, array(OperatorCardinality::SINGLE), array(OperatorBaseType::INTEGER, OperatorBaseType::FLOAT));
		$this->setFigures($figures);
		$this->setRoundingMode($roundingMode);
	}
	
	/**
	 * Set the rounding mode.
	 * 
	 * @param integer $roundingMode A value from the RoundingMode enumeration.
	 * @throws InvalidArgumentException If $roundingMode is not a value from the RoundingMode enumeration.
	 */
	public function setRoundingMode($roundingMode) {
		if (in_array($roundingMode, RoundingMode::asArray())) {
			$this->roundingMode = $roundingMode;
		}
		else {
			$msg = "The roundingMode argument must be a value from the RoundingMode enumeration, '" . $roundingMode . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the rounding mode.
	 * 
	 * @return integer A value from the RoundingMode enumeration.
	 */
	public function getRoundingMode() {
		return $this->roundingMode;
	}
	
	/**
	 * Set the number of figures to round to.
	 * 
	 * @param integer|string $figures An integer value or a variable reference.
	 * @throws InvalidArgumentException If $figures is not an integer nor a variable reference.
	 */
	public function setFigures($figures) {
		if (is_int($figures) || (gettype($figures) === 'string' && Format::isVariableRef($figures))) {
			$this->figures = $figures;
		}
		else {
			$msg = "The figures argument must be an integer or a variable reference, '" . $figures . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the number of figures to round to.
	 * 
	 * @return integer|string An integer value or a variable reference.
	 */
	public function getFigures() {
		return $this->figures;
	}
	
	public function getQtiClassName() {
		return 'equalRounded';
	}
}
