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

use qtism\common\enums\Cardinality;
use qtism\data\expressions\ExpressionCollection;
use qtism\common\utils\Format;
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * The roundTo operator takes one sub-expression which must have single cardinality 
 * and a numerical base-type. The result is a single float with the value nearest 
 * to that of the expression's value such that when converted to a decimal string 
 * it represents the expression rounded by the specified rounding method to the 
 * specified precision. If the sub-expression is NULL, then the result is NULL. 
 * If the sub-expression is INF, then the result is INF. If the sub-expression 
 * is -INF, then the result is -INF. If the argument is NaN, then the result is NULL.
 * 
 * When rounding to n significant figures, the deciding digit is the (n+1)th digit 
 * counting from the first non-zero digit from the left in the number. If the deciding 
 * digit is 5 or greater, the nth digit is increased by 1 and all digits to its right 
 * are discarded; if the deciding digit is less than 5, all digits to the right of 
 * the nth digit are discarded.
 * 
 * When rounding to n decimal places, the deciding digit is the (n+1)th digit counting 
 * to the right from the decimal point. If the deciding digit is 5 or greater, the nth 
 * digit is increased by 1 and all digits to its right are discarded; if the deciding 
 * digit is less than 5, all digits to the right of the nth digit are discarded.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class RoundTo extends Operator {
	
	/**
	 * From IMS QTI:
	 * 
	 * The number of figures to round to. If roundingMode= "significantFigures", 
	 * the value of figures must be a non-zero positive integer. 
	 * If roundingMode="decimalPlaces", the value of figures must be an integer 
	 * greater than or equal to zero.
	 * 
	 * @var integer|string
	 * @qtism-bean-property
	 */
	private $figures;
	
	/**
	 * From IMS QTI:
	 * 
	 * Numbers are rounded to a given number of significantFigures or decimalPlaces.
	 * 
	 * Default value is RoundingMode::SIGNIFICANT_FIGURES.
	 * 
	 * @var integer
	 * @qtism-bean-property
	 */
	private $roundingMode = RoundingMode::SIGNIFICANT_FIGURES;
	
	/**
	 * Create a new instance of RoundTo.
	 * 
	 * @param ExpressionCollection $expressions A collection of Expression objects.
	 * @param integer|string $figures An integer or a variable reference. 
	 * @param integer $roundingMode A value from the RoundingMode enumeration.
	 */
	public function __construct(ExpressionCollection $expressions, $figures, $roundingMode = RoundingMode::SIGNIFICANT_FIGURES) {
		parent::__construct($expressions, 1, 1, array(Cardinality::SINGLE), array(OperatorBaseType::INTEGER, OperatorBaseType::FLOAT));
		
		$this->setFigures($figures);
		$this->setRoundingMode($roundingMode);
	}
	
	/**
	 * Set the figures attribute.
	 * 
	 * @param integer|string $figures An integer or a variable reference.
	 * @throws InvalidArgumentException If $figures is not an integer nor a variable reference.
	 */
	public function setFigures($figures) {
		if (is_int($figures) || (gettype($figures) === 'string' && Format::isVariableRef($figures))) {
			$this->figures = $figures;
		}
		else {
			$msg = "The figures argument must be an integer or a QTI variable reference, '" . $figures . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the figures attribute.
	 * 
	 * @return integer|string An integer or a variable reference.
	 */
	public function getFigures() {
		return $this->figures;
	}
	
	/**
	 * Set the roundingMode attribute.
	 * 
	 * @param integer $roundingMode A value from the RoundingMode enumeration.
	 * @throws InvalidArgumentException If $rounding mode is not a value from the RoundingMode enumeration.
	 */
	public function setRoundingMode($roundingMode) {
		if (in_array($roundingMode, RoundingMode::asArray())) {
			$this->roundingMode = $roundingMode;
		}
		else {
			$msg = "The roudingMode attribute must be a value from the RoundingMode enumeration, '" . $roundingMode . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the roundingMode attribute.
	 * 
	 * @return integer A value from the RoundingMode enumeration.
	 */
	public function getRoundingMode() {
		return $this->roundingMode;
	}
	
	public function getQtiClassName() {
		return 'roundTo';
	}
}
