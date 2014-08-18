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
use qtism\common\datatypes\Integer;
use qtism\data\expressions\operators\Subtract;
use qtism\data\expressions\Expression;
use \InvalidArgumentException;

/**
 * The SubtractProcessor class aims at processing Subtract expressions.
 * 
 * From IMS QTI:
 * 
 * The subtract operator takes 2 sub-expressions which all have single cardinality 
 * and numerical base-types. The result is a single float or, if both sub-expressions 
 * are of integer type, a single integer that corresponds to the first value minus 
 * the second. If either of the sub-expressions is NULL then the operator results in 
 * NULL.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class SubtractProcessor extends OperatorProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof Subtract) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The SubtractProcessor class only processes Subtract QTI Data Model objects.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Process the Subtract operator.
	 * 
	 * @return float|integer|null A single float or if both sub-expressions are integers, a single integer or NULL if either of the sub-expressions is NULL.
	 * @throws OperatorProcessingException
	 */
	public function process() {
		$operands = $this->getOperands();
		
		if ($operands->containsNull() === true) {
			return null;
		}
		
		if ($operands->exclusivelySingle() === false) {
			$msg = "The Subtract operator only accepts operands with a single cardinality";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_CARDINALITY);
		}
		
		if ($operands->exclusivelyNumeric() === false) {
			$msg = "The Subtract operator only accepts operands with a baseType of integer or float";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_BASETYPE);
		}
		
		$operand1 = $operands[0];
		$operand2 = $operands[1];
		
	 	$subtract = $operand1->getValue() - $operand2->getValue();
	 	return (is_int($subtract) === true) ? new Integer($subtract) : new Float($subtract);
	}
}