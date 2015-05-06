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
 * The match operator takes two sub-expressions which must both have the same base-type 
 * and cardinality. The result is a single boolean with a value of true if the two 
 * expressions represent the same value and false if they do not. If either 
 * sub-expression is NULL then the operator results in NULL.
 * 
 * The match operator must not be confused with broader notions of equality such as 
 * numerical equality. To avoid confusion, the match operator should not be used to 
 * compare subexpressions with base-types of float and must not be used on 
 * sub-expressions with a base-type of duration.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Match extends Operator {
	
	public function __construct(ExpressionCollection $expressions) {
		parent::__construct($expressions, 2, 2, array(OperatorCardinality::SAME), array(OperatorCardinality::SAME));
	}
	
	public function getQtiClassName() {
		return 'match';
	}
}
