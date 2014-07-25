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
 * @subpackage 
 *
 */
namespace qtism\runtime\expressions\operators;

use qtism\data\expressions\operators\MathFunctions;
use qtism\data\expressions\operators\MathOperator;
use qtism\data\expressions\Expression;
use \InvalidArgumentException;

/**
 * The MathOperatorProcessor class aims at processing MathOperator operators.
 * 
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
class MathOperatorProcessor extends OperatorProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof MathOperator) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The MathOperatorProcessor class only processes MathOperator QTI Data Model objects.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Process the MathOperator operator.
	 * 
	 * @return float|integer|null The result of the MathOperator call or NULL if any of the sub-expressions is NULL. See the class documentation for special cases.
	 */
	public function process() {
		
		$operands = $this->getOperands();
		
		if ($operands->containsNull() === true) {
			return null;
		}
		
		if ($operands->exclusivelySingle() === false) {
			$msg = "The MathOperator operator only accepts operands with a single cardinality.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_CARDINALITY);
		}
		
		if ($operands->exclusivelyNumeric() === false) {
			$msg = "The MathOperator operator only accepts operands with an integer or float baseType.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_BASETYPE);
		}
		
		$qtiFuncName = MathFunctions::getNameByConstant($this->getExpression()->getName());
		$methodName = 'process' . ucfirst($qtiFuncName);
		$result = call_user_func_array(array($this, $methodName), array());
		
		if (is_nan($result) === true) {
			// outside the domain of the function.
			return null;
		}
		else {
			return $result;
		}
	}
	
	protected function processSin() {
		$operands = $this->getOperands();
		return sin($operands[0]);
	}
	
	protected function processCos() {
		$operands = $this->getOperands();
		return cos($operands[0]);
	}
	
	protected function processTan() {
		$operands = $this->getOperands();
		return tan($operands[0]);
	}
	
	protected function processSec() {
		$operands = $this->getOperands();
		$cos = cos($operands[0]);
		if ($cos == 0) {
			return null;
		}
		else {
			return 1 / $cos;
		}
	}
	
	protected function processCsc() {
		$operands = $this->getOperands();
		$sin = sin($operands[0]);
		if ($sin == 0) {
			return null;
		}
		else {
			return 1 / $sin;
		}
	}
	
	protected function processCot() {
		$operands = $this->getOperands();
		$tan = tan($operands[0]);
		if (is_infinite($tan)) {
			return 0.0;
		}
		else if ($tan == 0) {
			return null;
		}
		else {
			return 1 / $tan;
		}
	}
	
	protected function processAsin() {
		$operands = $this->getOperands();
		return asin($operands[0]);
	}
	
	protected function processAcos() {
		$operands = $this->getOperands();
		return acos($operands[0]);
	}
	
	protected function processAtan() {
		$operands = $this->getOperands();
		return atan($operands[0]);
	}
	
	protected function processAtan2() {
		$operands = $this->getOperands();
		
		if (!isset($operands[1])) {
			$msg = "The atan2 math function of the MathOperator requires 2 operands, 1 operand given.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::NOT_ENOUGH_OPERANDS);
		}
		else if (count($operands) > 2) {
			$msg = "The atan2 math function of the MathOperator requires 2 operands, more than 2 operands given.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::TOO_MUCH_OPERANDS);
		}
		
		$operand1 = $operands[0];
		$operand2 = $operands[1];
		
		return atan2($operand1, $operand2);
	}
	
	protected function processAsec() {
		$operands = $this->getOperands();
		$operand = $operands[0];
		
		if (abs($operand) < 1) {
			return null;
		}
		
		return acos(1 / $operand);
	}
	
	protected function processAcsc() {
		$operands = $this->getOperands();
		$operand = $operands[0];
		
		if (abs($operand) < 1) {
			return null;
		}
		
		return asin(1 / $operand);
	}
	
	protected function processAcot() {
		$operands = $this->getOperands();
		$operand = $operands[0];
		
		if ($operand === 0) {
			return M_PI_2;
		}
		
		return atan(1 / $operand);
	}
	
	protected function processSinh() {
		$operands = $this->getOperands();
		return sinh($operands[0]);
	}
	
	protected function processCosh() {
		$operands = $this->getOperands();
		return cosh($operands[0]);
	}
	
	protected function processTanh() {
		$operands = $this->getOperands();
		return tanh($operands[0]);
	}
	
	protected function processSech() {
		$operands = $this->getOperands();
		$operand = $operands[0];
		
		if ($operand == 0) {
			return null;
		}
		
		return 1 / cosh($operand);
	}
	
	protected function processCsch() {
		$operands = $this->getOperands();
		$operand = $operands[0];
		
		if ($operand == 0) {
			return null;
		}
		
		return 1 / sinh($operand);
	}
	
	protected function processCoth() {
		$operands = $this->getOperands();
		$operand = $operands[0];
		
		if ($operand == 0) {
			return null;
		}
		else if (is_infinite($operand)) {
			return 0.0;
		}
		
		return 1 / tanh($operand);
	}
	
	protected function processLog() {
		$operands = $this->getOperands();
		$operand = $operands[0];
		
		if ($operand < 0) {
			return null;
		}
		else if ($operand == 0) {
			return -INF;
		}
		
		return log($operand, 10);
	}
	
	protected function processLn() {
		$operands = $this->getOperands();
		$operand = $operands[0];
		
		if ($operand < 0) {
			return null;
		}
		else if ($operand == 0) {
			return -INF;
		}
		
		return log($operand);
	}
	
	protected function processExp() {
		$operands = $this->getOperands();
		$operand = $operands[0];
		
		if (is_nan($operand) === true) {
			return null;
		}
		else if (is_infinite($operand) === true && $operand > 0) {
			return INF;
		}
		else if (is_infinite($operand) === true && $operand < 0) {
			return 0.0;
		}
		
		return exp($operand);
	}
	
	protected function processAbs() {
		$operands = $this->getOperands();
		$operand = $operands[0];
		
		if (is_nan($operand) === true) {
			return null;
		}
		
		return floatval(abs($operand));
	}
	
	/**
	 * Process the signum (a.k.a. sign) function.
	 * 
	 * @link https://en.wikipedia.org/wiki/Sign_function
	 */
	protected function processSignum() {
		$operands = $this->getOperands();
		$operand = $operands[0];
		
		if (is_nan($operand)) {
			return null;
		}
		else if ($operand < 0) {
			return -1;
		}
		else if ($operand > 0) {
			return 1;
		}
		else {
			return 0;
		}
	}
	
	protected function processFloor() {
		$operands = $this->getOperands();
		$operand = $operands[0];
		
		if (is_nan($operand)) {
			return null;
		}
		else if (is_infinite($operand) === true && $operand > 0) {
			return INF;
		}
		else if (is_infinite($operand) === true && $operand < 0) {
			return -INF;
		}
		
		return intval(floor($operand));
	}
	
	protected function processCeil() {
		$operands = $this->getOperands();
		$operand = $operands[0];
		
		if (is_nan($operand)) {
			return null;
		}
		else if (is_infinite($operand) === true && $operand > 0) {
			return INF;
		}
		else if (is_infinite($operand) === true && $operand < 0) {
			return -INF;
		}
		
		return intval(ceil($operand));
	}
	
	protected function processToDegrees() {
		$operands = $this->getOperands();
		$operand = $operands[0];
		
		if (is_nan($operand)) {
			return null;
		}
		else if (is_infinite($operand) === true && $operand > 0) {
			return INF;
		}
		else if (is_infinite($operand) === true && $operand < 0) {
			return -INF;
		}
		
		return floatval(rad2deg($operand));
	}
	
	protected function processToRadians() {
		$operands = $this->getOperands();
		$operand = $operands[0];
		
		if (is_nan($operand)) {
			return null;
		}
		else if (is_infinite($operand) === true && $operand > 0) {
			return INF;
		}
		else if (is_infinite($operand) === true && $operand < 0) {
			return -INF;
		}
		
		return floatval(deg2rad($operand));
	}
}