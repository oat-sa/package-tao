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

use qtism\common\enums\Cardinality;
use qtism\common\datatypes\String;
use qtism\runtime\expressions\operators\OperatorProcessingException;
use qtism\runtime\expressions\operators\CustomOperatorProcessor;

/**
 * A custom operator implementing PHP core's implode function.
 * 
 * The implode operator takes two sub-expressions. The first sub-expression must have a single cardinality
 * and a string base-type. It acts as the "glue" for imploding the second sub-expression. The later must
 * have a multiple or ordered cardinality, and a string base-type.
 * 
 * If either sub-expressions is NULL, then the result of this operator is NULL.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @see http://www.php.net/manual/en/function.implode.php PHP implode manual
 */
class Implode extends CustomOperatorProcessor {
	
	/**
	 * Process the expression by implementing PHP core's implode function.
	 * 
	 * @return String The split value of the second sub-expression given as a parameter.
	 * @throws OperatorProcessingException If something goes wrong.
	 */
	public function process() {
	    $operands = $this->getOperands();
	    
	    if (($c = count($operands)) < 2) {
	        $msg = "The 'qtism.runtime.expressions.operators.custom.Implode' custom operator takes 2 sub-expressions as parameters, ${c} given.";
	        throw new OperatorProcessingException($msg, $this, OperatorProcessingException::NOT_ENOUGH_OPERANDS);
	    }
	    else if ($operands->containsNull() === true) {
	        return null;
	    }
	    else if ($operands->exclusivelyString() === false) {
	        $msg = "The 'qtism.runtime.expressions.operators.custom.Implode' custom operator only accepts operands with a string baseType.";
	        throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_BASETYPE);
	    }
	    else if ($operands[0]->getCardinality() !== Cardinality::SINGLE) {
	        $msg = "The 'qtism.runtime.expressions.operators.custom.Implode' custom operator only accepts a first operand with single cardinality.";
	        throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_CARDINALITY);
	    }
	    else if ($operands[1]->getCardinality() !== Cardinality::MULTIPLE && $operands[1]->getCardinality() !== Cardinality::ORDERED) {
	        $msg = "The 'qtism.runtime.expressions.operators.custom.Implode' custom operator only accepts a second operand with multiple or ordered cardinality.";
	        throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_CARDINALITY);
	    }
	    
	    $glue = $operands[0]->getValue();
	    $pieces = $operands[1];
	    
	    // Note: implode() is binary-safe \0/!
	    $string = implode($glue, $pieces->getArrayCopy());
	    
	    return new String($string);
	}
}