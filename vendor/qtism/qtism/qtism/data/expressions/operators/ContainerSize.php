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
 * The containerSize operator takes a sub-expression with any base-type and either 
 * multiple or ordered cardinality. The result is an integer giving the number of 
 * values in the sub-expression, in other words, the size of the container. If 
 * the sub-expression is NULL the result is 0. This operator can be used for 
 * determining how many choices were selected in a multiple-response 
 * choiceInteraction, for example.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class ContainerSize extends Operator {
	
	public function __construct(ExpressionCollection $expressions) {
		parent::__construct($expressions, 1, 1, array(Cardinality::MULTIPLE, Cardinality::ORDERED), array(OperatorBaseType::ANY));
	}
	
	public function getQtiClassName() {
		return 'containerSize';
	}
}
