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

use qtism\data\expressions\operators\FieldValue;
use qtism\data\expressions\Expression;
use \InvalidArgumentException;

/**
 * The FieldValueProcessor class aims at processing FieldValue expressions.
 * 
 * From IMS QTI:
 * 
 * The field-value operator takes a sub-expression with a record container value. 
 * The result is the value of the field with the specified fieldIdentifier. If there 
 * is no field with that identifier then the result of the operator is NULL.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class FieldValueProcessor extends OperatorProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof FieldValue) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The FieldValueProcessor class only processes FieldValue QTI Data Model objects.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Process the FieldValue object.
	 * 
	 * @return mixed|null A QTI Runtime compliant value or null if there is no field with that identifier.
	 * @throws OperatorProcessingException
	 */
	public function process() {
		$operands = $this->getOperands();
		
		if ($operands->exclusivelyRecord() === false) {
			$msg = "The FieldValue operator only accepts operands with a cardinality of record.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_CARDINALITY);
		}
		
		$fieldIdentifier = $this->getExpression()->getFieldIdentifier();
		return $operands[0][$fieldIdentifier];
	}
}