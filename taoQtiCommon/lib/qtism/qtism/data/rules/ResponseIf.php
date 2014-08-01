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
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * A responseIf part consists of an expression which must have an effective 
 * baseType of boolean and single cardinality. For more information about the 
 * runtime data model employed see Expressions. It also contains a set of 
 * sub-rules. If the expression is true then the sub-rules are processed, 
 * otherwise they are skipped (including if the expression is NULL) and the 
 * following responseElseIf or responseElse parts (if any) are considered 
 * instead.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class ResponseIf extends QtiComponent {
	
	/**
	 * The expression to be evaluated with the If statement.
	 * 
	 * @var Expression
	 * @qtism-bean-property
	 */
	private $expression;
	
	/**
	 * The sub rules to execute if the Expression returns true;
	 * 
	 * @var ResponseRuleCollection
	 * @qtism-bean-property
	 */
	private $responseRules;
	
	/**
	 * Create a new instance of ResponseIf.
	 * 
	 * @param Expression $expression The expression to be evaluated with the If statement.
	 * @param ResponseRuleCollection $responseRules A collection of sub expressions to be evaluated if the Expression returns true.
	 * @throws InvalidArgumentException If $responseRules does not contain any ResponseRule object.
	 */
	public function __construct(Expression $expression, ResponseRuleCollection $responseRules) {
		$this->setExpression($expression);
		$this->setResponseRules($responseRules);
	}
	
	/**
	 * Get the expression to be evaluated with the If statement.
	 * 
	 * @return Expression An expression.
	 */
	public function getExpression() {
		return $this->expression;
	}
	
	/**
	 * Set the expression to be evaluated with the If statement.
	 * 
	 * @param Expression $expression An expression.
	 */
	public function setExpression(Expression $expression) {
		$this->expression = $expression;
	}
	
	/**
	 * Set the ResponseRule objects to be evaluated as sub expressions if the expression
	 * evaluated with the If statement returns true.
	 * 
	 * @param ResponseRuleCollection $responseRules A collection of ResponseRule objects.
	 * @throws InvalidArgumentException If $responseRules is an empty collection.
	 */
	public function setResponseRules(ResponseRuleCollection $responseRules) {
		if (count($responseRules) > 0) {
			$this->responseRules = $responseRules;
		}
		else {
			$msg = "A ResponseIf object must be bound to at least one ResponseRule.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the ResponseRule objects to be evaluated as sub expressions if the expression
	 * evaluated with the If statement returns true.
	 * 
	 * @return ResponseRuleCollection A collection of ResponseRule objects.
	 */
	public function getResponseRules() {
		return $this->responseRules;
	}
	
	public function getQtiClassName() {
		return 'responseIf';
	}
	
	public function getComponents() {
		$comp = array_merge(array($this->getExpression()), 
							$this->getResponseRules()->getArrayCopy());
		
		return new QtiComponentCollection($comp);
	}
}
