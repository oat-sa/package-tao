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

use qtism\common\datatypes\Integer;
use qtism\data\expressions\Expression;
use qtism\data\expressions\RandomInteger;
use \InvalidArgumentException;
use \OutOfRangeException;

/**
 * The RandomIntegerProcessor class aims at processing RandomInteger QTI Data Model Expression objects.
 * 
 * From IMS QTI:
 * 
 * Selects a random integer from the specified range [min,max] satisfying min + step * n for 
 * some integer n. For example, with min=2, max=11 and step=3 the values {2,5,8,11} are possible.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class RandomIntegerProcessor extends ExpressionProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof RandomInteger) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The RandomIntegerProcessor class only accepts to process RandomInteger Expression objects.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Process the RandomInteger expression.
	 * 
	 * * Throws an ExpressionProcessingException if 'min' is greater than 'max'.
	 * * Throws an ExpressionProcessingException if a variable reference is not found in the current state.
	 * * Throws an ExpressionProcessingException if a variable reference's value is not an integer.
	 * 
	 * @return integer A random integer value.
	 * @throws ExpressionProcessingException
	 */
	public function process() {
		$expr = $this->getExpression();
		$min = $expr->getMin();
		$max = $expr->getMax();
		$step = $expr->getStep();
		$state = $this->getState();

		$min = (gettype($min) === 'integer') ? $min : $state[Utils::sanitizeVariableRef($min)]->getValue();
		$max = (gettype($max) === 'integer') ? $max : $state[Utils::sanitizeVariableRef($max)]->getValue();
		$step = (gettype($step) === 'integer') ? $step : $state[Utils::sanitizeVariableRef($step)]->getValue();
		
		if (gettype($min) === 'integer' && gettype($max) === 'integer' && gettype($step) === 'integer') {
			if ($min > $max) {
				$msg = "'min':'${min}' is greater than 'max':'${max}'.";
				throw new ExpressionProcessingException($msg, $this, ExpressionProcessingException::LOGIC_ERROR);
			}
			
			if ($step === 1) {
				return new Integer(mt_rand($min, $max));
			}
			else {
				$distance = ($min < 0) ? ($max + abs($min)) : ($max - $min);
				$mult = mt_rand(0, intval(floor($distance / $step)));
				$random = new Integer($min + ($mult * $step));
				return $random;
			}
		}
		else {
			$msg = "At least one of the following variables is not an integer: 'min', 'max', 'step' while processing RandomInteger.";
			throw new ExpressionProcessingException($msg, $this, ExpressionProcessingException::WRONG_VARIABLE_BASETYPE);
		}
	}
}