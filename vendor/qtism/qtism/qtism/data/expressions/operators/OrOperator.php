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
 * Please note that this class represents the QTI 'or' class.
 * We cannot use the 'Or' class name because it is a reserved word
 * in PHP.
 * 
 * From IMS QTI:
 * 
 * The or operator takes one or more sub-expressions each with a base-type of 
 * boolean and single cardinality. The result is a single boolean which is 
 * true if any of the sub-expressions are true and false if all of them are 
 * false. If one or more sub-expressions are NULL and all the others are 
 * false then the operator also results in NULL.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class OrOperator extends Operator {
	
	public function __construct(ExpressionCollection $expressions) {
		parent::__construct($expressions, 1, -1, array(OperatorCardinality::SINGLE), array(OperatorBaseType::BOOLEAN));
	}
	
	public function getQtiClassName() {
		return 'or';
	}
}
