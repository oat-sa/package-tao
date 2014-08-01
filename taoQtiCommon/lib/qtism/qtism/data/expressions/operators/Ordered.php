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
 * The ordered operator takes 0 or more sub-expressions all of which must have 
 * either single or ordered cardinality. Although the sub-expressions may be of 
 * any base-type they must all be of the same base-type. The result is a container 
 * with ordered cardinality containing the values of the sub-expressions, 
 * sub-expressions with ordered cardinality have their individual values 
 * added (in order) to the result: contains cannot contain other containers. 
 * For example, when applied to A, B, {C,D} the ordered operator results 
 * in {A,B,C,D}. Note that the ordered operator never results in an empty 
 * container. All sub-expressions with NULL values are ignored. If no 
 * sub-expressions are given (or all are NULL) then the result is NULL
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Ordered extends Operator {
	
	public function __construct(ExpressionCollection $expressions) {
		parent::__construct($expressions, 0, -1, array(Cardinality::SINGLE, Cardinality::ORDERED), array(OperatorBaseType::SAME));
	}
	
	public function getQtiClassName() {
		return 'ordered';
	}
}
