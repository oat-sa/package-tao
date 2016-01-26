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
use qtism\data\expressions\operators\DurationLT;
use qtism\data\expressions\Expression;
use \InvalidArgumentException;

/**
 * The DurationLTProcessor class aims at processing DurationLT operators.
 * 
 * From IMS QTI:
 * 
 * The durationLT operator takes two sub-expressions which must both have single 
 * cardinality and base-type duration. The result is a single boolean with a value 
 * of true if the first duration is shorter than the second and false if it is 
 * longer than (or equal) to the second. If either sub-expression is NULL then 
 * the operator results in NULL.
 * 
 * There is no 'durationLTE' or 'durationGT' because equality of duration is 
 * meaningless given the variable precision allowed by duration. Given that 
 * duration values are obtained by truncation rather than rounding it makes 
 * sense to test only less-than or greater-than-equal inequalities only. 
 * For example, if we want to determine if a candidate took less than 10 
 * seconds to complete a task in a system that reports durations to a 
 * resolution of epsilon seconds (epsilon<1) then a value equal to 10 would 
 * cover all durations in the range [10,10+epsilon).
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class DurationLTProcessor extends OperatorProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof DurationLT) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The DurationLTProcessor class only processes DurationLT QTI Data Model objects.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Process the DurationLT operator.
	 * 
	 * @return boolean|null A boolean value of true if the first duration is shorter than the second or NULL if either sub-expression is NULL.
	 * @throws OperatorProcessingException
	 */
	public function process() {
		$operands = $this->getOperands();
		
		if ($operands->containsNull() === true) {
			return null;
		}
		
		if ($operands->exclusivelySingle() === false) {
			$msg = "The DurationLT operator only accepts operands with a single cardinality.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_CARDINALITY);
		}
		
		if ($operands->exclusivelyDuration() === false) {
			$msg = "The DurationLT operator only accepts operands with a duration baseType.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_BASETYPE);
		}
		
		return new Boolean($operands[0]->shorterThan($operands[1]));
	}
}