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
namespace qtism\runtime\expressions;

use qtism\common\datatypes\Float;
use qtism\data\expressions\Expression;
use qtism\data\expressions\RandomFloat;
use \InvalidArgumentException;

/**
 * The RandomFloatProcessor class aims at processing RandomFloat QTI Data Model Expression objects.
 * 
 * From IMS QTI:
 * 
 * Selects a random float from the specified range [min,max].
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class RandomFloatProcessor extends ExpressionProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof RandomFloat) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The RandomFloatProcessor class can only process RandomFloat Expression objects.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Process the RandomFloat expression.
	 * 
	 * * Throws an ExpressionProcessingException if 'min' is greater than 'max'.
	 * 
	 * @return float A Random float value.
	 * @throws ExpressionProcessingException
	 */
	public function process() {
		$expr = $this->getExpression();
		$min = $expr->getMin();
		$max = $expr->getMax();
		
		$state = $this->getState();
		
		$min = (is_float($min)) ? $min : $state[Utils::sanitizeVariableRef($min)]->getValue();
		$max = (is_float($max)) ? $max : $state[Utils::sanitizeVariableRef($max)]->getValue();

		if (is_float($min) && is_float($max)) {
			
			if ($min <= $max) {
				return new Float(($min + lcg_value() * (abs($max - $min))));
			}
			else {
				$msg = "'min':'${min}' is greater than 'max':'${max}'.";
				throw new ExpressionProcessingException($msg, $this, ExpressionProcessingException::LOGIC_ERROR);
			}
		}
		else {
			$msg = "At least one of the following values is not a float: 'min', 'max'.";
			throw new ExpressionProcessingException($msg, $this, ExpressionProcessingException::WRONG_VARIABLE_BASETYPE);
		}
	}
}