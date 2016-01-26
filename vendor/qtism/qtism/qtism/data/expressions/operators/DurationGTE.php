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
 * The durationGTE operator takes two sub-expressions which must both have single 
 * cardinality and base-type duration. The result is a single boolean with a value
 * of true if the first duration is longer (or equal, within the limits imposed by
 * truncation as described above) than the second and false if it is shorter than
 * the second. If either sub-expression is NULL then the operator results in NULL.
 * 
 * See durationLT for more information about testing the equality of durations.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class DurationGTE extends Operator {
	
	public function __construct(ExpressionCollection $expressions) {
		parent::__construct($expressions, 2, 2, array(OperatorCardinality::SINGLE), array(OperatorBaseType::DURATION));
	}
	
	public function getQtiClassName() {
		return 'durationGTE';
	}
}
