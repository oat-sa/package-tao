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


namespace qtism\data\rules;

use qtism\data\QtiComponentCollection;
use qtism\data\QtiComponent;
use qtism\data\expressions\Expression;
use qtism\common\utils\Format;
use \InvalidArgumentException;

/**
 * From IMS QTI
 * 
 * A branch-rule is a simple expression attached to an assessmentItemRef, assessmentSection 
 * or testPart that is evaluated after the item, section or part has been presented to 
 * the candidate. If the expression evaluates to true the test jumps forward to the item, 
 * section or part referred to by the target identifier. In the case of an item or section, 
 * the target must refer to an item or section in the same testPart that has not yet been 
 * presented. For testParts, the target must refer to another testPart.
 * 
 * [Comment] The above definition restricts the navigation paths through a linear test 
 * part to being trees. In other words, cycles are not allowed. In most cases, repitition 
 * can be achieved by using a section that selects withReplacement up to a suitable upper 
 * bound of repitition in combination with a preCondition or branchRule that terminates 
 * the section early when (or if) a certain outcome has been achieved. (This technique
 * might be used in conjunction with one or more Item Templates to achieve drill and 
 * practice, for example.) However, unbounded repitition is not supported. Comments 
 * are sought on whether this approach is too restrictive.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class BranchRule extends QtiComponent implements Rule {
	
	/**
	 * The expression of the BranchRule.
	 * 
	 * @var Expression
	 * @qtism-bean-property
	 */
	private $expression;

	/**
	 * The following values are reserved and have special meaning when used as a target 
	 * identifier: EXIT_SECTION jumps over all the remaining children of the current 
	 * section to the item (or section) immediately following it; EXIT_TESTPART finishes 
	 * the current testPart immediately and EXIT_TEST finishes the entire assessmentTest 
	 * immediately.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $target;
	
	/**
	 * Create a new instance of BranchRule.
	 * 
	 * @param Expression $expression The expression of the BranchRule.
	 * @param string $target The target identifier of the BranchRule.
	 * @throws InvalidArgumentException If $target is not a valid QTI Identifier.
	 */
	public function __construct(Expression $expression, $target) {
		$this->setExpression($expression);
		$this->setTarget($target);
	}
	
	/**
	 * Get the expression of the BranchRule.
	 * 
	 * @return Expression A QTI Expression.
	 */
	public function getExpression() {
		return $this->expression;
	}
	
	/**
	 * Set the expression of the BranchRule.
	 * 
	 * @param Expression $expression A QTI Expression.
	 */
	public function setExpression(Expression $expression) {
		$this->expression = $expression;
	}
	
	/**
	 * Set the target identifier of the BranchRule.
	 * 
	 * @return string A QTI Identifier.
	 */
	public function getTarget() {
		return $this->target;
	}
	
	/**
	 * Get the target identifier of the BranchRule.
	 * 
	 * @param string $target A QTI Identifier.
	 * @throws InvalidArgumentException If $target is not a valid QTI Identifier.
	 */
	public function setTarget($target) {
		if (Format::isIdentifier($target)) {
			$this->target = $target;
		}
		else {
			$msg = "'Target' must be a valid QTI Identifier.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	public function getQtiClassName() {
		return 'branchRule';
	}
	
	public function getComponents() {
		$comp = array($this->getExpression());
		return new QtiComponentCollection($comp);
	}
}
