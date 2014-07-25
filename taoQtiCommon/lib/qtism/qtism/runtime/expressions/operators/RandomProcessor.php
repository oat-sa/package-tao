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

use qtism\data\expressions\operators\Random;
use qtism\data\expressions\Expression;
use \InvalidArgumentException;

/**
 * The RandomProcessor class aims at processing Random operators.
 * 
 * From IMS QTI:
 * 
 * The random operator takes a sub-expression with a multiple or ordered container
 * value and any base-type. The result is a single value randomly selected from the
 * container. The result has the same base-type as the sub-expression but single
 * cardinality. If the sub-expression is NULL then the result is also NULL.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class RandomProcessor extends OperatorProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof Random) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The RandomProcessor class only processes Random QTI Data Model objects.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Process the Random operator.
	 * 
	 * @return mixed|null A single cardinality QTI runtime compliant value or NULL if the operand is considered to be NULL.
	 * @throws OperatorProcessingException
	 */
	public function process() {
		$operands = $this->getOperands();
		
		if ($operands->containsNull() === true) {
			return null;
		}
		
		if ($operands->exclusivelyMultipleOrOrdered() === false) {
			$msg = "The Random operator only accepts values with multiple or ordered cardinality.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_CARDINALITY);
		}
		
		$operand = $operands[0];
		$maxIndex = count($operand) - 1;
		return $operand[mt_rand(0, $maxIndex)];
	}
}