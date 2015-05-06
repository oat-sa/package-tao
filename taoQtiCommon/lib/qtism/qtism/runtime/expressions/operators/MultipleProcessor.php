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

use qtism\runtime\common\Utils as CommonUtils;
use qtism\runtime\common\MultipleContainer;
use qtism\data\expressions\Expression;
use qtism\data\expressions\operators\Multiple;
use \InvalidArgumentException;

/**
 * The MultipleProcessor class aims at processing Multiple QTI Data Model Expression objects.
 * 
 * From IMS QTI:
 * 
 * The multiple operator takes 0 or more sub-expressions all of which must have either single or multiple cardinality.
 * Although the sub-expressions may be of any base-type they must all be of the same base-type. The result is 
 * a container with multiple cardinality containing the values of the sub-expressions, sub-expressions with 
 * multiple cardinality have their individual values added to the result: containers cannot contain other containers.
 * For example, when applied to A, B and {C,D} the multiple operator results in {A,B,C,D}. All sub-expressions with 
 * NULL values are ignored. If no sub-expressions are given (or all are NULL) then the result is NULL.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class MultipleProcessor extends OperatorProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof Multiple) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The MultipleProcessor class only accepts Multiple QTI Data Model Expression objects to be processed.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Process the current expression.
	 * 
	 * @return MultipleContainer|null A MultipleContainer object or NULL.
	 * @throws OperatorProcessingException
	 */
	public function process() {
		$operands = $this->getOperands();
		
		if (count($operands) === 0) {
			return null;
		}
		
		if ($operands->exclusivelySingleOrMultiple() === false) {
			$msg = "The Multiple operator only accepts operands with single or omultiple cardinality.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_CARDINALITY);
		}
		
		$refType = null;
		$returnValue = null;
		
		foreach ($operands as $operand) {
			
			if (is_null($operand) || ($operand instanceof MultipleContainer && $operand->isNull())) {
				// As per specs, ignore.
				continue;
			}
			else {
				if ($refType !== null) {
					// A reference type as already been identified.
					if (CommonUtils::inferBaseType($operand) === $refType) {
						// $operand can be added to $returnValue.
						static::appendValue($returnValue, $operand);
					}
					else {
						// baseType mismatch.
						$msg = "The Multiple operator only accepts values with a similar baseType.";
						throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_BASETYPE);
					}
				}
				else if (($discoveryType = CommonUtils::inferBaseType($operand)) !== false) {
					// First value being identified as non-null.
					$refType = $discoveryType;
					$returnValue = new MultipleContainer($refType);
					static::appendValue($returnValue, $operand);
				}
			}
		}
		
		return $returnValue;
	}
	
	/**
	 * Append a value (A MultipleContainer or a primitive datatype) to a given $container.
	 * 
	 * @param MultipleContainer $container A MultipleContainer object you want to append something to.
	 * @param scalar|MultipleContainer $value A value to append to the $container.
	 */
	protected static function appendValue(MultipleContainer $container, $value) {
		if ($value instanceof MultipleContainer) {
			foreach ($value as $v) {
				$container[] = $v;
			}
		}
		else {
			// primitive type.
			$container[] = $value;
		}
	}
}