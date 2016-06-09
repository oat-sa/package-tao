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
 * The member operator takes two sub-expressions which must both have the same base-type. 
 * The first sub-expression must have single cardinality and the second must be a 
 * multiple or ordered container. The result is a single boolean with a value 
 * of true if the value given by the first sub-expression is in the container 
 * defined by the second sub-expression. If either sub-expression is NULL then 
 * the result of the operator is NULL.
 * 
 * The member operator should not be used on sub-expressions with a base-type 
 * of float because of the poorly defined comparison of values. It must not 
 * be used on sub-expressions with a base-type of duration.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Member extends Operator {
	
	public function __construct(ExpressionCollection $expressions) {
		parent::__construct($expressions, 2, 2, array(OperatorCardinality::SINGLE, OperatorCardinality::MULTIPLE, OperatorCardinality::ORDERED), array(OperatorBaseType::SAME));
	}
	
	public function getQtiClassName() {
		return 'member';
	}
}
