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
namespace qtism\runtime\expressions\operators;

use qtism\common\datatypes\Float;
use qtism\data\expressions\operators\Power;
use qtism\data\expressions\Expression;
use \InvalidArgumentException;

/**
 * The PowerProcessor class aims at processing PowerValue expressions.
 * 
 * From IMS QTI:
 * 
 * The power operator takes 2 sub-expression which both have single cardinality and 
 * numerical base-types. The result is a single float that corresponds to the first 
 * expression raised to the power of the second. If either or the sub-expressions is 
 * NULL then the operator results in NULL.
 * 
 * If the resulting value is outside the value set defined by float (not including 
 * positive and negative infinity) then the operator shall result in NULL.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class PowerProcessor extends OperatorProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof Power) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The PowerProcessor class only processes Power QTI Data Model objects.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Process the Power operator.
	 * 
	 * @return float|null A float value that corresponds to the first expression raised to the power of the second or NULL if the either sub-expression is NULL.
	 * @throws OperatorProcessingException
	 */
	public function process() {
		$operands = $this->getOperands();
		
		if ($operands->containsNull() === true) {
			return null;
		}
		
		if ($operands->exclusivelySingle() === false) {
			$msg = "The Power operator only accepts operands with a single cardinality.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_CARDINALITY);
		}
		
		if ($operands->exclusivelyNumeric() === false) {
			$msg = "The Power operator only accepts operands with a baseType of integer or float.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_BASETYPE);
		}
		
		$operand1 = $operands[0];
		$operand2 = $operands[1];
		$raised = pow($operand1->getValue(), $operand2->getValue());
		
		if (is_nan($raised)) {
			return null;
		}
		
		// If the first operand was not 0 but the result is 0, it means
		// we are subject to a lower overflow.
		if ($operand1->getValue() != 0 && $raised == 0) {
			return null;
		}
		
		// If the first and the second operands are not infinite but the result is infinite
		// it means we are subject to an upper overflow.
		if (!is_infinite($operand1->getValue()) && !is_infinite($operand2->getValue()) && is_infinite($raised)) {
			return null;
		}
		
		// pow() returns integers as much as it can, so we must cast.
		// If the casted value cannot be contained in a float, we are
		// subject to an overflow/underflow.
		$floatval = floatval($raised);
		if ($raised != 0 && $floatval == 0) {
			// underflow
			return null;
		}
		else if (!is_infinite($raised) && is_infinite($floatval)) {
			// overflow
			return null;
		}
		else {
			return new Float($floatval);
		}
	}
}