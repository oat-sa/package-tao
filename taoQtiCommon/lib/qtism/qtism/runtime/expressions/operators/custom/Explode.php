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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package qtism
 *  
 */
namespace qtism\runtime\expressions\operators\custom;

use qtism\common\datatypes\String;
use qtism\common\enums\BaseType;
use qtism\runtime\common\OrderedContainer;
use qtism\runtime\expressions\operators\CustomOperatorProcessor;
use qtism\runtime\expressions\operators\OperatorProcessingException;

/**
 * A custom operator implementing PHP core's explode function.
 * 
 * The explode operator takes two sub-expressions which must both have a single cardinality and a string base-type. The first
 * sub-expression is a string to be split on boundaries formed by the second sub-expression.
 * 
 * If either sub-expressions is NULL, then the result of this operator is NULL.
 * 
 * As a first second example, if the first sub-expression is "Hello-World" and the second sub-expression is "-", the result will be
 * an ordered container "Hello", "World".
 * 
 * As a second example, if the first sub-expression is "Dear Customer," and the second sub-expression is "+", the result will be
 * a single valued ordered container containing the "Dear Customer," string. Indeed, no boundary "+" could be found in the 
 * "Dear Customer," string. 
 * 
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @see http://www.php.net/manual/en/function.explode.php PHP explode manual.
 */
class Explode extends CustomOperatorProcessor {
	
	/**
	 * Process the expression by implementing PHP core's explode function.
	 * 
	 * @return OrderedContainer The split value of the second sub-expression given as a parameter.
	 * @throws OperatorProcessingException If something goes wrong.
	 */
	public function process() {
	    $operands = $this->getOperands();
	    
	    if (($c = count($operands)) < 2) {
	        $msg = "The 'qtism.runtime.expressions.operators.custom.Explode' custom operator takes 2 sub-expressions as parameters, ${c} given.";
	        throw new OperatorProcessingException($msg, $this, OperatorProcessingException::NOT_ENOUGH_OPERANDS);
	    }
	    else if ($operands->containsNull() === true) {
	        return null;
	    }
	    else if ($operands->exclusivelySingle() === false) {
	        $msg = "The 'qtism.runtime.expressions.operators.custom.Explode' custom operator only accepts operands with single cardinality.";
	        throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_CARDINALITY);
	    }
	    else if ($operands->exclusivelyString() === false) {
	        $msg = "The 'qtism.runtime.expressions.operators.custom.Explode' custom operator only accepts operands with a string baseType.";
	        throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_BASETYPE);
	    }
	    
	    $delimiter = $operands[0]->getValue();
	    $string = $operands[1]->getValue();
	    
	    // Note: explode() is binary-safe \0/!
	    $strings = explode($delimiter, $string);
	    
	    $ordered = new OrderedContainer(BaseType::STRING);
	    foreach ($strings as $str) {
	        $ordered[] = new String($str);
	    }
	    
	    return $ordered;
	}
}