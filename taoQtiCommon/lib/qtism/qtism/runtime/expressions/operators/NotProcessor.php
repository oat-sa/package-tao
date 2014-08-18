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

use qtism\data\expressions\Expression;
use qtism\data\expressions\operators\Not;

/**
 * The NotProcessor class aims at processing Not QTI DataModel expressions.
 * 
 * From IMS QTI:
 * 
 * The not operator takes a single sub-expression with a base-type of boolean and single 
 * cardinality. The result is a single boolean with a value obtained by the logical 
 * negation of the sub-expression's value. If the sub-expression is NULL then the not 
 * operator also results in NULL.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class NotProcessor extends OperatorProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof Not) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The NotProcessor class only processes Not QTI Data Model objects.";
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
		
		if ($operands->exclusivelySingle() === false) {
			$msg = "The Not Expression only accept operands with single cardinality.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_CARDINALITY);
		}
		
		if ($operands->exclusivelyBoolean() === false) {
			$msg = "The Not Expression only accept operands with boolean baseType.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_BASETYPE);
		}
		
		$operand = $operands[0];
		return new Boolean(!$operand->getValue());
	}
}