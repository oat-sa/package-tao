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

use qtism\common\datatypes\Boolean;
use qtism\common\Comparable;
use qtism\common\enums\Cardinality;
use qtism\data\expressions\Expression;
use qtism\data\expressions\operators\Contains;

/**
 * The ContainsProcessor class aims at processing Contains QTI DataModel operators.
 * 
 * From IMS QTI:
 * 
 * The not operator takes a single sub-expression with a base-type of boolean and 
 * single cardinality. The result is a single boolean with a value obtained by the 
 * logical negation of the sub-expression's value. If the sub-expression is NULL 
 * then the not operator also results in NULL.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class ContainsProcessor extends OperatorProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof Contains) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The ContainsProcessor class only processes Contains QTI Data Model objects.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Returns the logical negation of the sub-expressions.
	 * 
	 * @return boolean
	 * @throws OperatorProcessingException
	 */
	public function process() {
		$operands = $this->getOperands();
		
		if ($operands->containsNull()) {
			return null;
		}
		
		if ($operands->exclusivelyMultipleOrOrdered() === false) {
			$msg = "The Contains Expression only accept operands with multiple or ordered cardinality.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_CARDINALITY);
		}
		
		if ($operands->sameCardinality() === false) {
			$msg = "The Contains Expression only accept operands with the same cardinality.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_CARDINALITY);
		}
		
		if ($operands->sameBaseType() === false) {
			$msg = "The Contains Expression only accept operands with the same baseType.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_BASETYPE);
		}
		
		$operand1 = $operands[0];
		$operand2 = $operands[1];
		
		if ($operand1->getCardinality() === Cardinality::MULTIPLE) {
			foreach ($operand2 as $value) {
				if ($operand1->contains($value) === false || $operand1->occurences($value) !== $operand2->occurences($value)) {
					return new Boolean(false);
				}
			}
			
			return new Boolean(true);
		}
		else {
			// $operand1->getCardinality() === Cardinality::ORDERED
			$op1Index = 0;
			$op2Index = 0;
			$lastFoundIndex = -1;
			$foundCount = 0;
			
			while ($op1Index < count($operand1)) {
				$op1Val = $operand1[$op1Index];
				$op2Val = $operand2[$op2Index];
				
				if ($op2Val === $op1Val || ($op2Val instanceof Comparable && $op2Val->equals($op1Val))) {
					$op2Index++;
					
					if ($lastFoundIndex >= 0 && ($op1Index - $lastFoundIndex) > 1) {
						// Sequence not respected.
						return new Boolean(false);
					}
					else {
						$lastFoundIndex = $op1Index;
						$foundCount++;
					}
				}
				
				$op1Index++;
			}
			
			if ($foundCount > 0 && $foundCount === count($operand2)) {
				return new Boolean(true);
			}
			else {
				return new Boolean(false);
			}
		}
	}
}