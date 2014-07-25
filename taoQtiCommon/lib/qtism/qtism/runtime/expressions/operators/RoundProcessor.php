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

use qtism\common\datatypes\Integer;
use qtism\data\expressions\operators\Round;
use qtism\data\expressions\Expression;
use \InvalidArgumentException;

/**
 * The RoundProcessor class aims at processing Round operators.
 * 
 * From IMS QTI:
 * 
 * The round operator takes a single sub-expression which must have single cardinality
 * and a numerical base-type. The result is a value of base-type integer formed by 
 * rounding the value of the sub-expression. The result is the integer n for all input
 * values in the range [n-0.5,n+0.5). In other words, 6.8 and 6.5 both round up to 
 * 7, 6.49 rounds down to 6 and -6.5 rounds up to -6. If the sub-expression is NULL 
 * then the operator results in NULL. If the sub-expression is NaN, then the result 
 * is NULL. If the sub-expression is INF, then the result is INF. If the 
 * sub-expression is -INF, then the result is -INF.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class RoundProcessor extends OperatorProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof Round) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The RoundProcessor class only processes Round QTI Data Model objects.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Process the Round operator.
	 * 
	 * @return integer|null An integer value formed by rounding the value of the sub-expression or NULL if the sub-expression is NULL.
	 * @throws OperatorProcessingException
	 */
	public function process() {
		$operands = $this->getOperands();
		
		if ($operands->containsNull() === true) {
			return null;
		}
		
		if ($operands->exclusivelySingle() === false) {
			$msg = "The Round operator only accepts operands with a single cardinality.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_CARDINALITY);
		}
		
		if ($operands->exclusivelyNumeric() === false) {
			$msg = "The Round operator only accepts operands with baseType integer or float.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_BASETYPE);
		}
		
		$operand = $operands[0];
		$mode = ($operand->getValue() >= 0) ? PHP_ROUND_HALF_UP : PHP_ROUND_HALF_DOWN;
		return new Integer(intval(round($operand->getValue(), 0, $mode)));
	}
}