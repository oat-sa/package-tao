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
use qtism\data\expressions\operators\Index;
use qtism\data\expressions\Expression;
use qtism\runtime\expressions\Utils as ProcessingUtils;
use \InvalidArgumentException;

/**
 * The IndexProcessor class aims at processing Index operators.
 * 
 * From IMS QTI:
 * 
 * The index operator takes a sub-expression with an ordered container value and any
 * base-type. The result is the nth value of the container. The result has the same
 * base-type as the sub-expression but single cardinality. The first value of a container
 * has index 1, the second 2 and so on. n must be a positive integer. If n exceeds the
 * number of values in the container (or the sub-expression is NULL) then the result
 * of the index operator is NULL. If n is an identifier, it is the value of n at
 * runtime that is used.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class IndexProcessor extends OperatorProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof Index) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The IndexProcessor class only processes Index QTI Data Model objects.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Process the Index operator.
	 * 
	 * @return mixed|null A QTIRuntime compliant scalar value. NULL is returned if expression->n exceeds the number of values in the container or the sub-expression is NULL.
	 * @throws OperatorProcessingException
	 */
	public function process() {
		$operands = $this->getOperands();
		
		if ($operands->containsNull()) {
			return null;
		}
		
		if ($operands->exclusivelyOrdered() === false) {
			$msg = "The Index operator only accepts values with a cardinality of ordered.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_CARDINALITY);
		}
		
		$n = $this->getExpression()->getN();
		if (gettype($n) === 'string') {
			// The value of $n comes from the state.
			$state = $this->getState();
			if (($index = $state[ProcessingUtils::sanitizeVariableRef($n)]) !== null) {
				if ($index instanceof Integer) {
					$n = $index->getValue();
				}
				else {
					$msg = "The value '${index}' is not an integer. Ordered containers can be only accessed by integers.";
					throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_VARIABLE_BASETYPE);
				}
			}
			else {
				$msg = "Unknown variable reference '${n}'.";
				throw new OperatorProcessingException($msg, $this, OperatorProcessingException::NONEXISTENT_VARIABLE);
			}
		}
		
		if ($n < 1) {
			$msg = "The value of 'n' must be a non-zero postive integer, '${n}' given.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::LOGIC_ERROR);
		}
		
		$n = $n - 1; // QTI indexes begin at 1...
		if ($n > count($operands[0]) - 1) {
			// As per specs, if n exceeds the number of values in the container,
			// the result of the index operator is NULL.
			return null;
		}
		
		return $operands[0][$n];
	}
}