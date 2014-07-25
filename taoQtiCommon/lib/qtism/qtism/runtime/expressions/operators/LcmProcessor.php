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
use qtism\common\datatypes\Scalar;
use qtism\data\expressions\operators\Lcm;
use qtism\data\expressions\Expression;
use \InvalidArgumentException;

/**
 * The LcmProcessor class aims at processing Lcm operators.
 * 
 * From IMS QTI:
 * 
 * The lcm operator takes 1 or more sub-expressions which all have base-type integer and
 * may have single, multiple or ordered cardinality. The result is a single integer equal
 * in value to the lowest common multiple (lcm) of the argument values. If any argument
 * is zero, the result is 0, lcm(0,n)=0; authors should beware of this in calculations
 * which require division by the lcm of random values. If any of the sub-expressions is
 * NULL, the result is NULL. If any of the sub-expressions is not a numerical value, 
 * then the result is NULL.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class LcmProcessor extends OperatorProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof Lcm) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The LcmProcessor class only processes Lcm QTI Data Model objects.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Process the Lcm operator.
	 * 
	 * @return integer|null A single integer equal in value to the lowest common multiple of the sub-expressions. If all arguments are 0, the result is 0, If any of the sub-expressions is NULL, the result is NULL.
	 * @throws OperatorProcessingException
	 */
	public function process() {
		$operands = $this->getOperands();
		
		if ($operands->containsNull() === true) {
			return null;
		}
		
		if ($operands->anythingButRecord() === false) {
			$msg = "The Lcm operator only accepts operands with a cardinality of single, multiple or ordered.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_CARDINALITY);
		}
		
		if ($operands->exclusivelyInteger() === false) {
			$msg = "The Lcm operator only accepts operands with an integer baseType.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_BASETYPE);
		}
		
		// Make a flat collection first.
		$flatCollection = new OperandsCollection();
		
		foreach ($operands as $operand) {
			if ($operand instanceof Scalar) {
		
				if ($operand->getValue() !== 0) {
					$flatCollection[] = $operand;
				}
				else {
					// Operand is 0, return 0.
					return new Integer(0);
				}
			}
			else if ($operand->contains(null)) {
				// Container with at least one null value inside.
				// -> If any of the sub-expressions is null or not numeric, returns null.
				return null;
			}
			else {
				// Container with no null values.
				foreach ($operand as $o) {
						
					if ($o->getValue() !== 0) {
						$flatCollection[] = $o;
					}
					else {
						// If any of the operand is 0, return 0.
						return new Integer(0);
					}
				}
			}
		}
		
		$g = $flatCollection[0];
		$loopLimit = count($flatCollection) - 1;
		$i = 0;
			
		while ($i < $loopLimit) {
			$g = new Integer(Utils::lcm($g->getValue(), $flatCollection[$i + 1]->getValue()));
			$i++;
		}
			
		return $g;
	}
}