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
use qtism\data\expressions\operators\StringMatch;
use qtism\data\expressions\Expression;
use \InvalidArgumentException;

/**
 * The StringMatchProcessor class aims at processing StringMatch operators.
 * 
 * Please note that this implementation does not take care of the deprecated
 * attribute 'substring'.
 * 
 * From IMS QTI:
 * 
 * The stringMatch operator takes two sub-expressions which must have single and 
 * a base-type of string. The result is a single boolean with a value of true if 
 * the two strings match according to the comparison rules defined by the attributes 
 * below and false if they don't. If either sub-expression is NULL then the operator 
 * results in NULL.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class StringMatchProcessor extends OperatorProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof StringMatch) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The StringMatchProcessor class only processes StringMatch QTI Data Model objects.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Process the StringMatch operator.
	 * 
	 * @return boolean Whether the two string match according to the comparison rules of the operator's attributes or NULL if either of the sub-expressions is NULL.
	 * @throws OperatorProcessingException
	 */
	public function process() {
		$operands = $this->getOperands();
		
		if ($operands->containsNull() === true) {
			return null;
		}
		
		if ($operands->exclusivelySingle() === false) {
			$msg = "The StringMatch operator only accepts operands with a single cardinality.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_CARDINALITY);
		}
		
		if ($operands->exclusivelyString() === false) {
			$msg = "The StringMatch operator only accepts operands with a string baseType.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_BASETYPE);
		}
		
		$expression = $this->getExpression();
		
		// choose the correct comparison function according comparison rules
		// of the operator.
		// Please note that strcmp and strcasecmp are binary-safe *\0/* Hourray! *\0/* 
		$func = ($expression->isCaseSensitive() === true) ? 'strcmp' : 'strcasecmp';
		return new Boolean($func($operands[0]->getValue(), $operands[1]->getValue()) === 0);
	}
}