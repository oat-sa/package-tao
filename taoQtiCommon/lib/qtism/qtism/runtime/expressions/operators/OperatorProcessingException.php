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

use qtism\runtime\expressions\ExpressionProcessingException;

/**
 * The OperatorProcessingException class represents an exception to be thrown
 * when an error occurs while processing an Operator at runtime.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class OperatorProcessingException extends ExpressionProcessingException {
	
	/**
	 * The code to use when an operand with a not compliant cardinality
	 * is processed by the operator.
	 * 
	 * @var integer
	 */
	const WRONG_CARDINALITY = 100;
	
	/**
	 * The code to use when an operand with a not compliant baseType is
	 * processed by the operator.
	 * 
	 * @var integer
	 */
	const WRONG_BASETYPE = 101;
	
	/**
	 * The code to use when an operand has a not compliant baseType OR
	 * cardinality.
	 * 
	 * @var integer
	 */
	const WRONG_BASETYPE_OR_CARDINALITY = 102;
	
	/**
	 * The code to use when not enough operands are given to a processor.
	 * 
	 * @var integer
	 */
	const NOT_ENOUGH_OPERANDS = 103;
	
	/**
	 * The code to use when too much operands are given to a processor.
	 * 
	 * @var integer
	 */
	const TOO_MUCH_OPERANDS = 104;
}