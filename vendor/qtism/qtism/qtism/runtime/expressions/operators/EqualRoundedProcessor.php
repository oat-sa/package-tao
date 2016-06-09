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
use qtism\common\datatypes\Integer;
use qtism\data\expressions\ExpressionCollection;
use qtism\data\expressions\BaseValue;
use qtism\data\expressions\operators\RoundTo;
use qtism\runtime\common\Utils as RuntimeUtils;
use qtism\data\expressions\operators\EqualRounded;
use qtism\data\expressions\Expression;
use qtism\runtime\expressions\Utils as ProcessingUtils;
use \InvalidArgumentException;

/**
 * The EqualRoundedProcessor class aims at processing EqualRounded operators.
 * 
 * From IMS QTI:
 * 
 * The equalRounded operator takes two sub-expressions which must both have single 
 * cardinality and have a numerical base-type. The result is a single boolean with 
 * a value of true if the two expressions are numerically equal after rounding and 
 * false if they are not. If either sub-expression is NULL then the operator results 
 * in NULL.
 * 
 * Numbers are rounded to a given number of significantFigures or decimalPlaces.
 * 
 * The number of figures to round to. If roundingMode= "significantFigures", the 
 * value of figures must be a non-zero positive integer. If roundingMode="decimalPlaces",
 * the value of figures must be an integer greater than or equal to zero.
 * 
 * For example, if significantFigures mode is used with figures=3, and the values 
 * are 3.175 and 3.183, the result is true, but for 3.175 and 3.1749, the result 
 * is false; if decimalPlaces mode is used with figures=2, 1.68572 and 1.69 the 
 * result is true, but for 1.68572 and 1.68432, the result is false.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class EqualRoundedProcessor extends OperatorProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof EqualRounded) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The EqualRoundedProcessor class only processes EqualRounded QTI Data Model objects.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Process the EqualRounded operator.
	 * 
	 * @return boolean|null A boolean with a value of true if the two expressions are numerically equal after rounding and false if they are not. If either sub-expression is NULL, the operator results in NULL.
	 * @throws OperatorProcessingException
	 */
	public function process() {
		$operands = $this->getOperands();
		
		if ($operands->containsNull()) {
			return null;
		}
		
		if ($operands->exclusivelySingle() === false) {
			$msg = "The EqualRounded operator only accepts operands with a single cardinality.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_CARDINALITY);
		}
		
		if ($operands->exclusivelyNumeric() === false) {
			$msg = "The EqualRounded operator only accepts operands with an integer or float baseType.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_BASETYPE);
		}
		
		// delegate the rounding to the RoundTo operator.
		$expression = $this->getExpression();
		$roundingMode = $expression->getRoundingMode();
		$figures = $expression->getFigures();
		
		if (gettype($figures) === 'string') {
			// Variable reference to deal with.
			$state = $this->getState();
			$varName = ProcessingUtils::sanitizeVariableRef($figures);
			$varValue = $state[$varName];
			
			if (is_null($varValue) === true) {
				$msg = "The variable with name '${varName}' could not be resolved.";
				throw new OperatorProcessingException($msg, $this, OperatorProcessingException::NONEXISTENT_VARIABLE);
			}
			else if (!$varValue instanceof Integer) {
				$msg = "The variable with name '${varName}' is not an integer.";
				throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_VARIABLE_BASETYPE);
			}
			
			$figures = $varValue->getValue();
		}
		
		$rounded = new OperandsCollection(); // will contain the rounded operands.
		
		foreach ($operands as $operand) {
			$baseType = RuntimeUtils::inferBaseType($operand);
			$subExpression = new BaseValue($baseType, $operand);
			$roundToExpression = new RoundTo(new ExpressionCollection(array($subExpression)), $figures, $roundingMode);
			$roundToProcessor = new RoundToProcessor($roundToExpression, new OperandsCollection(array($operand)));
			
			try {
				$rounded[] = $roundToProcessor->process();
			}
			catch (OperatorProcessingException $e) {
				$msg = "An error occured while rounding '${operand}'.";
				throw new OperatorProcessingException($msg, $this, OperatorProcessingException::LOGIC_ERROR, $e);
			}
		}
		
		return new Boolean($rounded[0]->getValue() == $rounded[1]->getValue());
	}
}
