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
use qtism\common\enums\BaseType;
use qtism\runtime\common\MultipleContainer;
use qtism\data\expressions\operators\Sum;
use qtism\data\expressions\Expression;
use \InvalidArgumentException;

/**
 * The SumProcessor class aims at processing Sum QTI Data Model Expressions.
 * 
 * From IMS QTI:
 * 
 * The sum operator takes 1 or more sub-expressions which all have numerical base-types 
 * and may have single, multiple or ordered cardinality. The result is a single float or,
 * if all sub-expressions are of integer type, a single integer that corresponds to the
 * sum of the numerical values of the sub-expressions. If any of the sub-expressions are 
 * NULL then the operator results in NULL.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class SumProcessor extends OperatorProcessor {
	
	/**
	 * Set the Sum Expression object to be processed.
	 * 
	 * @param Expression $expression A Sum object.
	 * @throws InvalidArgumentException If $expressions is not an instance of Sum.
	 */
	public function setExpression(Expression $expression) {
		if ($expression instanceof Sum) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The SumProcessor class only accepts a Sum Expression to be processed.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Process the Sum operator.
	 * 
	 * @return integer|float|null A single integer/float that corresponds to the sum of the numerical values of the sub-expressions. If any of the sub-expressions are NULL, the operator results in NULL.
	 * @throws OperatorProcessingException If invalid operands are given.
	 */
	public function process() {
		
		$operands = $this->getOperands();
		
		if ($operands->containsNull() === true) {
			return null;
		}
		else if ($operands->anythingButRecord() === false) {
			$msg = "The Sum operator only accepts operands with cardinality single, multiple or ordered.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_CARDINALITY);
		}
		else if ($operands->exclusivelyNumeric() === false) {
			$msg = "The Sum operator only accepts operands with an integer or float baseType.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_BASETYPE);
		}
		
		$returnValue = 0;
		$floatCount = 0;
		
		foreach ($this->getOperands() as $operand) {
			if ($operand instanceof Integer) {
				$returnValue += $operand->getValue();
			}
			else if ($operand instanceof Float) {
			    $returnValue += $operand->getValue();
			    $floatCount++;
			}
			else {
				foreach ($operand as $val) {
				    
				    if ($val !== null) {
				        
				        if ($val instanceof Float) {
				            $floatCount++;
				        }
				        
				        $returnValue += $val->getValue();
				    }
				}
			}
		}
		
		return ($floatCount > 0) ? new Float(floatval($returnValue)) : new Integer(intval($returnValue));
	}
}