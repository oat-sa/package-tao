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
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * The mathOperator operator takes 1 or more sub-expressions which all have single 
 * cardinality and have numerical base-types. The trigonometric functions, sin, 
 * cos and tan, take one argument in radians, which evaluates to a single float. 
 * Other functions take one numerical argument. Further functions might take more 
 * than one numerical argument, e.g. atan2 (two argument arc tan). The result is a 
 * single float, except for the functions signum, floor and ceil, which return a 
 * single integer. If any of the sub-expressions is NULL, the result is NULL. If 
 * any of the sub-expressions falls outside the natural domain of the function 
 * called by mathOperator, e.g. log(0) or asin(2), then the result is NULL.
 * 
 * The reciprocal trigonometric functions also follow these rules:
 * 
 * * If the argument is NaN, then the result is NULL
 * * If the value of tan for the argument is INF, then the value of cot is 0
 * * If the value of tan for the argument is -INF, then the value of cot is 0
 * * If the value of a trigonometric function is 0, then the value of the corresponding reciprocal trigonometric function is NULL
 * 
 * The reciprocal trigonometric and hyperbolic functions also follow these rules:
 * 
 * * If the argument is NaN, then the result is NULL
 * * If the value of a trigonometric or hyperbolic function for the argument is INF, then the value of the corresponding reciprocal trigonometric or hyperbolic function is 0
 * * If the value of a trigonometric or hyperbolic function for the argument is -INF, then the value of the corresponding reciprocal trigonometric or hyperbolic function is 0
 * * If the value of a trigonometric or hyperbolic function for the argument is 0, then the value of the corresponding reciprocal trigonometric or hyperbolic function is NULL.
 * * If the value of a trigonometric or hyperbolic function for the argument is -0, then the value of the corresponding reciprocal trigonometric or hyperbolic function is NULL.
 * 
 * The function atan2 also follows these rules:
 * 
 * * If either argument is NaN, then the result is NULL
 * * If the first argument is positive zero and the second argument is positive, or the first argument is positive and finite and the second argument is positive infinity, then the result is 0.
 * * If the first argument is negative zero and the second argument is positive, or the first argument is negative and finite and the second argument is positive infinity, then the result is 0.
 * * If the first argument is positive zero and the second argument is negative, or the first argument is positive and finite and the second argument is negative infinity, then the result is the double value closest to π.
 * * If the first argument is negative zero and the second argument is negative, or the first argument is negative and finite and the second argument is negative infinity, then the result is the double value closest to -π.
 * * If the first argument is positive and the second argument is positive zero or negative zero, or the first argument is positive infinity and the second argument is finite, then the result is the double value closest to π/2.
 * * If the first argument is negative and the second argument is positive zero or negative zero, or the first argument is negative infinity and the second argument is finite, then the result is the double value closest to -π/2.
 * * If both arguments are positive infinity, then the result is the double value closest to π/4.
 * * If the first argument is positive infinity and the second argument is negative infinity, then the result is the double value closest to 3*π/4.
 * * If the first argument is negative infinity and the second argument is positive infinity, then the result is the double value closest to -π/4.
 * * If both arguments are negative infinity, then the result is the double value closest to -3*π/4.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class MathOperator extends Operator {
	
	/**
	 * The name of the mathematical function.
	 * 
	 * @var integer
	 * @qtism-bean-property
	 */
	private $name;
	
	/**
	 * Create a new instance of MathOperator.
	 * 
	 * @param ExpressionCollection $expressions A collection of Expression objects.
	 * @param integer $name The math functions to use as a value from the MathFunctions enumeration.
	 */
	public function __construct(ExpressionCollection $expressions, $name) {
		parent::__construct($expressions, 1, -1, array(Cardinality::SINGLE), array(OperatorBaseType::INTEGER, OperatorBaseType::FLOAT));
		$this->setName($name);
	}
	
	/**
	 * Get the name of the math function to use.
	 * 
	 * @return integer A value from the MathFunctions enumeration.
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Set the name of the math function to use.
	 * 
	 * @param integer $name A value from the MathFunctions enumeration.
	 * @throws InvalidArgumentException If $name is not a value from the MathFunctions enumeration.
	 */
	public function setName($name) {
		if (in_array($name, MathFunctions::asArray())) {
			$this->name = $name;
		}
		else {
			$msg = "The name attribute must be a value from the MathFunctions enumeration, '" . $name . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	public function getQtiClassName() {
		return 'mathOperator';
	}
}
