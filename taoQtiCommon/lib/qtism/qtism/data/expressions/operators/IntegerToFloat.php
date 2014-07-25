<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *   
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * 
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package 
 */


namespace qtism\data\expressions\operators;

use qtism\data\expressions\ExpressionCollection;

/**
 * From IMS QTI:
 * 
 * The round operator takes a single sub-expression which must have single cardinality
 * and a numerical base-type. The result is a value of base-type integer formed 
 * by rounding the value of the sub-expression. The result is the integer n for 
 * all input values in the range [n-0.5,n+0.5). In other words, 6.8 and 6.5 both 
 * round up to 7, 6.49 rounds down to 6 and -6.5 rounds up to -6. If the 
 * sub-expression is NULL then the operator results in NULL. If the 
 * sub-expression is NaN, then the result is NULL. If the sub-expression 
 * is INF, then the result is INF. If the sub-expression is -INF, then 
 * the result is -INF.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class IntegerToFloat extends Operator {
	
	public function __construct(ExpressionCollection $expressions) {
		parent::__construct($expressions, 1, 1, array(OperatorCardinality::SINGLE), array(OperatorBaseType::INTEGER));
	}
	
	public function getQtiClassName() {
		return 'integerToFloat';
	}
}
