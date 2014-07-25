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
 * responseElseIf is defined in an identical way to responseIf.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class ResponseElseIf extends QtiComponent {
	
	/**
	 * The expression to be evaluated with the Else If statement.
	 * 
	 * @var Expression
	 * @qtism-bean-property
	 */
	private $expression;
	
	/**
	 * The collection of ResponseRule objects to be evaluated as sub expressions
	 * if the expression bound to the Else If statement is evaluated to true.
	 * 
	 * @var ResponseRuleCollection
	 * @qtism-bean-property
	 */
	private $responseRules;
	
	/**
	 * Create a new instance of ResponseElseIf.
	 * 
	 * @param Expression $expression An expression to be evaluated with the Else If statement.
	 * @param ResponseRuleCollection $responseRules A collection of ResponseRule objects.
	 * @throws InvalidArgumentException If $responseRules is an empty collection.
	 */
	public function __construct(Expression $expression, ResponseRuleCollection $responseRules) {
		$this->setExpression($expression);
		$this->setResponseRules($responseRules);
	}
	
	/**
	 * Get the expression to be evaluated with the Else If statement.
	 * 
	 * @return Expression An Expression object.
	 */
	public function getExpression() {
		return $this->expression;
	}
	
	/**
	 * Set the expression to be evaluated with the Else If statement.
	 * 
	 * @param Expression $expression An Expression object.
	 */
	public function setExpression(Expression $expression) {
		$this->expression = $expression;
	}
	
	/**
	 * Get the ResponseRules to be evaluated as sub expressions if the expression bound
	 * to the Else If statement returns true.
	 * 
	 * @return ResponseRuleCollection A collection of OutcomeRule objects.
	 */
	public function getResponseRules() {
		return $this->responseRules;
	}
	
	/**
	 * Set the ResponseRules to be evaluated as sub expressions if the expression bound
	 * to the Else If statement returns true.
	 * 
	 * @param ResponseRuleCollection $responseRules A collection of ResponseRule objects.
	 * @throws InvalidArgumentException If $responseRules is an empty collection.
	 */
	public function setResponseRules(ResponseRuleCollection $responseRules) {
		if (count($responseRules) > 0) {
			$this->responseRules = $responseRules;
		}
		else {
			$msg = "A ResponseElseIf object must be bound to at lease one ResponseRule object.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	public function getQtiClassName() {
		return 'responseElseIf';
	}
	
	public function getComponents() {
		$comp = array_merge(array($this->getExpression()), $this->getResponseRules()->getArrayCopy());
		return new QtiComponentCollection($comp);
	}
}
