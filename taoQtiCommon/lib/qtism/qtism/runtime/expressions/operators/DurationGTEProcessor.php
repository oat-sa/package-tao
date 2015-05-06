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
use qtism\data\expressions\operators\DurationGTE;
use qtism\data\expressions\Expression;
use \InvalidArgumentException;

/**
 * The DurationGTEProcessor class aims at processing DurationGTE operators.
 * 
 * From IMS QTI:
 * 
 * The durationGTE operator takes two sub-expressions which must both have 
 * single cardinality and base-type duration. The result is a single boolean with a 
 * value of true if the first duration is longer (or equal, within the limits imposed 
 * by truncation as described above) than the second and false if it is shorter than 
 * the second. If either sub-expression is NULL then the operator results in NULL.
 * 
 * See durationLT for more information about testing the equality of durations.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class DurationGTEProcessor extends OperatorProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof DurationGTE) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The DurationGTEProcessor class only processes DurationGTE QTI Data Model objects.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Process the DurationGTE operator.
	 * 
	 * @return boolean|null A boolean with a value of true if the first duration is longer or equal to the second, otherwise false. If either sub-expression is NULL, the result of the operator is NULL.
	 * @throws OperatorProcessingException
	 */
	public function process() {
		$operands = $this->getOperands();
		
		if ($operands->containsNull() === true) {
			return null;
		}
		
		if ($operands->exclusivelySingle() === false) {
			$msg = "The DurationGTE operator only accepts operands with a single cardinality.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_CARDINALITY);
		}
		
		if ($operands->exclusivelyDuration() === false) {
			$msg = "The DurationGTE operator only accepts operands with a duration baseType.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_BASETYPE);
		}
		
		return new Boolean($operands[0]->longerThanOrEquals($operands[1]));
	}
}