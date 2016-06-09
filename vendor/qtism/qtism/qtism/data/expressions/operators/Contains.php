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
use qtism\common\enums\Cardinality;

/**
 * From IMS QTI:
 * 
 * The contains operator takes two sub-expressions which must both have the same 
 * base-type and cardinality - either multiple or ordered. The result is a single 
 * boolean with a value of true if the container given by the first sub-expression 
 * contains the value given by the second sub-expression and false if it doesn't.
 * Note that the contains operator works differently depending on the cardinality 
 * of the two sub-expressions. For unordered containers the values are compared 
 * without regard for ordering, for example, [A,B,C] contains [C,A]. Note 
 * that [A,B,C] does not contain [B,B] but that [A,B,B,C] does. For ordered 
 * containers the second sub-expression must be a strict sub-sequence within 
 * the first. In other words, [A,B,C] does not contain [C,A] but it does 
 * contain [B,C].
 * 
 * If either sub-expression is NULL then the result of the operator is NULL. Like 
 * the member operator, the contains operator should not be used on sub-expressions 
 * with a base-type of float and must not be used on sub-expressions with a base-type 
 * of duration.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Contains extends Operator {
	
	public function __construct(ExpressionCollection $expressions) {
		parent::__construct($expressions, 2, 2, array(OperatorCardinality::SAME), array(OperatorBaseType::SAME));
	}
	
	public function getQtiClassName() {
		return 'contains';
	}
}
