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
use qtism\common\datatypes\Boolean;
use qtism\data\expressions\operators\ToleranceMode;
use qtism\data\expressions\operators\Equal;
use qtism\data\expressions\Expression;
use qtism\runtime\expressions\Utils as ProcessingUtils;
use \InvalidArgumentException;

/**
 * The EqualProcessor class aims at processing Equal operators.
 * 
 * From IMS QTI:
 * 
 * The equal operator takes two sub-expressions which must both have single 
 * cardinality and have a numerical base-type. The result is a single boolean 
 * with a value of true if the two expressions are numerically equal and false 
 * if they are not. If either sub-expression is NULL then the operator results 
 * in NULL.
 * 
 * When comparing two floating point numbers for equality it is often desirable 
 * to have a tolerance to ensure that spurious errors in scoring are not 
 * introduced by rounding errors. The tolerance mode determines whether 
 * the comparison is done exactly, using an absolute range or a relative range.
 * 
 * If the tolerance mode is absolute or relative then the tolerance must be specified.
 * The tolerance consists of two positive numbers, t0 and t1, that define the lower 
 * and upper bounds. If only one value is given it is used for both.
 *
 * In absolute mode the result of the comparison is true if the value of the 
 * second expression, y is within the following range defined by the first value, x.
 *
 * x-t0,x+t1 
 *
 * In relative mode, t0 and t1 are treated as percentages and the following 
 * range is used instead.
 * 
 * x*(1-t0/100),x*(1+t1/100)
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class EqualProcessor extends OperatorProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof Equal) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The EqualProcessor class only processes Equal QTI Data Model objects.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Process the Equal operator.
	 * 
	 * @return boolean|null Whether the two expressions are numerically equal and false if they are not or NULL if either sub-expression is NULL.
	 * @throws OperatorProcessingException
	 */
	public function process() {
		$operands = $this->getOperands();
		
		if ($operands->containsNull() === true) {
			return null;
		}
		
		if ($operands->exclusivelySingle() === false) {
			$msg = "The Equal operator only accepts operands with a single cardinality.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_CARDINALITY);
		}
		
		if ($operands->exclusivelyNumeric() === false) {
			$msg = "The Equal operator only accepts operands with an integer or float baseType";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_BASETYPE);
		}
		
		$operand1 = $operands[0];
		$operand2 = $operands[1];
		$expression = $this->getExpression();
		
		if ($expression->getToleranceMode() === ToleranceMode::EXACT) {
			return new Boolean($operand1->getValue() == $operand2->getValue());
		}
		else {
			$tolerance = $expression->getTolerance();
			
			if (gettype($tolerance[0]) === 'string') {
				$strTolerance = $tolerance;
				$tolerance = array();
				
				// variableRef to handle.
				$state = $this->getState();
				$tolerance0Name = ProcessingUtils::sanitizeVariableRef($strTolerance[0]);
				$varValue = $state[$tolerance0Name];
				
				if (is_null($varValue)) {
					$msg = "The variable with name '${tolerance0Name}' could not be resolved.";
					throw new OperatorProcessingException($msg, $this, OperatorProcessingException::NONEXISTENT_VARIABLE);
				}
				else if (!$varValue instanceof Float) {
					$msg = "The variable with name '${tolerance0Name}' is not a float.";
					throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_VARIABLE_BASETYPE);
				}
				
				$tolerance[] = $varValue->getValue();
				
				if (isset($strTolerance[1]) && gettype($strTolerance[1]) === 'string') {
					// A second variableRef to handle.
					$tolerance1Name = ProcessingUtils::sanitizeVariableRef($strTolerance[1]);
					
					if (($varValue = $state[$tolerance1Name]) !== null && $varValue instanceof Float) {
						$tolerance[] = $varValue->getValue();
					}
				}
			}
			
			if ($expression->getToleranceMode() === ToleranceMode::ABSOLUTE) {
				
				$t0 = $operand1->getValue() - $tolerance[0];
				$t1 = $operand1->getValue() + ((isset($tolerance[1])) ? $tolerance[1] : $tolerance[0]);
					
				$moreThanLower = ($expression->doesIncludeLowerBound()) ? $operand2->getValue() >= $t0 : $operand2->getValue() > $t0;
				$lessThanUpper = ($expression->doesIncludeUpperBound()) ? $operand2->getValue() <= $t1 : $operand2->getValue() < $t1;
					
				return new Boolean($moreThanLower && $lessThanUpper);
			}
			else {
				// Tolerance mode RELATIVE
				$tolerance = $expression->getTolerance();
				$t0 = $operand1->getValue() * (1 - $tolerance[0] / 100);
				$t1 = $operand1->getValue() * (1 + ((isset($tolerance[1])) ? $tolerance[1] : $tolerance[0]) / 100);
					
				$moreThanLower = ($expression->doesIncludeLowerBound()) ? $operand2->getValue() >= $t0 : $operand2->getValue() > $t0;
				$lessThanUpper = ($expression->doesIncludeUpperBound()) ? $operand2->getValue() <= $t1 : $operand2->getValue() < $t1;
					
				return new Boolean($moreThanLower && $lessThanUpper);
			}
		}
	}
}
