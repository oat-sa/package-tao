<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package qtism
 *  
 *
 */

namespace qtism\runtime\rules;

use qtism\runtime\common\State;
use qtism\runtime\common\AbstractEngine;
use qtism\runtime\expressions\ExpressionEngine;
use qtism\data\QtiComponent;
use qtism\data\QtiComponentCollection;
use qtism\data\rules\OutcomeCondition;
use qtism\data\rules\Rule;
use \InvalidArgumentException;

/**
 * The AbstractConditionProcessor implements the common logic
 * of OutcomeCondition and ResponseCondition processors.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
abstract class AbstractConditionProcessor extends RuleProcessor {
	
	/**
	 * The trail stack, an array of Rule objects.
	 * 
	 * @var array
	 */
	private $trail = array();
	
	/**
	 * The RuleProcessorFactory object used to create
	 * appropriate rule processors.
	 * 
	 * @var RuleProcessorFactory
	 */
	private $ruleProcessorFactory;
	
	/**
	 * Create a new OutcomeConditionProcessor.
	 * 
	 * @param QtiComponent $rule An OutcomeCondition/ResponseCondition rule object.
	 * @throws InvalidArgumentException If $rule is not an OutcomeCondition nor a ResponseCondition object.
	 */
	public function __construct(QtiComponent $rule) {
		parent::__construct($rule);
		$this->setRuleProcessorFactory(new RuleProcessorFactory());
	}
	
	/**
	 * Set the OutcomeCondition/ResponseCondition object to be processed.
	 * 
	 * @param Rule $rule An OutcomeCondition/ResponseCondition object.
	 * @throws InvalidArgumentException If $rule is not an OutcomeCondition nor a ResponseCondition object.
	 */
	public function setRule(Rule $rule) {
		
		$className = ucfirst($this->getQtiNature()) . 'Condition';
		
		if (get_class($rule) === 'qtism\\data\\rules\\' . $className) {
			parent::setRule($rule);
		}
		else {
			$msg = "The ${className}Processor only accepts ${className} objects to be processed.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the QTI nature of the condition type to take care of e.g. 'outcome'
	 * in case of an implementation for OutcomeCondition objects or 'response' in
	 * case of an implementation for ResponseCondition.
	 * 
	 * @return string the QTI nature of the condition type to take care of.
	 */
	public abstract function getQtiNature();
	
	/**
	 * Set the trail stack.
	 * 
	 * @param array $trail An array of trailed QtiComponent objects.
	 */
	public function setTrail(array &$trail) {
		$this->trail = $trail;
	}
	
	/**
	 * Get the trail stack.
	 * 
	 * @return array An array of trailed Rule objects.
	 */
	public function &getTrail() {
		return $this->trail;
	}
	
	/**
	 * Push some Rule objects on the trail stack.
	 * 
	 * @param QtiComponentCollection|QtiComponent $components A collection of Rule objects.
	 */
	public function pushTrail($components) {
		$i = count($components);
		$trail = &$this->getTrail();
		
		if ($components instanceof QtiComponent) {
			array_push($trail, $components);
		}
		else {
			// collection
			while ($i >= 1) {
				$i--;
				array_push($trail, $components[$i]);
			}
		}
	}
	
	/**
	 * Pop a Rule object from the trail.
	 * 
	 * @return QtiComponent A Rule object.
	 */
	public function popTrail() {
		$trail = &$this->getTrail();
		return array_pop($trail);
	}
	
	/**
	 * Set the RuleProcessorFactory object used to create appropriate rule processors.
	 * 
	 * @param RuleProcessorFactory $ruleProcessorFactory A RuleProcessorFactory object.
	 */
	public function setRuleProcessorFactory(RuleProcessorFactory $ruleProcessorFactory) {
		$this->ruleProcessorFactory = $ruleProcessorFactory;
	}
	
	/**
	 * Get the RuleProcessorFactory object used to create appropriate rule processors.
	 * 
	 * @return RuleProcessorFactory A RuleProcessorFactory object.
	 */
	public function getRuleProcessorFactory() {
		return $this->ruleProcessorFactory;
	}
	
	/**
	 * Process the OutcomeCondition/ResponseCondition according to the current state.
	 * 
	 * @throws RuleProcessingException
	 */
	public function process() {
		
		$state = $this->getState();
		$this->pushTrail($this->getRule());
		
		$className = ucfirst($this->getQtiNature());
		$nsClass = 'qtism\\data\\rules\\' . $className . 'Condition';
		$ruleGetter = "get${className}Rules";
		$statementGetter = "get${className}"; // + 'If'|'ElseIf'|'Else'
		
		while (count($this->getTrail()) > 0) {

			$rule = $this->popTrail();
			
			if (get_class($rule) === $nsClass) {
				
				// Let's try for if.
				$ifStatement = call_user_func(array($rule, $statementGetter . 'If'));
				$ifExpression = $ifStatement->getExpression();
				$exprEngine = new ExpressionEngine($ifExpression, $state);
			    $value = $exprEngine->process();
				
				if ($value !== null && $value->getValue() === true) {
					// Follow the if.
					$this->pushTrail(call_user_func(array($ifStatement, $ruleGetter)));
				}
				else {
					// Let's try for else ifs.
					$followElseIf = false;
					$elseIfStatements = call_user_func(array($rule, $statementGetter . 'ElseIfs'));
					
					foreach ($elseIfStatements as $elseIfStatement) {
						$elseIfExpression = $elseIfStatement->getExpression();
						$exprEngine->setComponent($elseIfExpression);
						$value = $exprEngine->process();
						
						if ($value !== null && $value->getValue() === true) {
							// Follow the current else if.
							$this->pushTrail(call_user_func(array($elseIfStatement, $ruleGetter)));
							$followElseIf = true;
							break;
						}
					}
					
					$elseStatement = call_user_func(array($rule, $statementGetter . 'Else'));
					
					if ($followElseIf === false && is_null($elseStatement) === false) {
						// No else if followed, the last resort is the else.
						$this->pushTrail(call_user_func(array($elseStatement, $ruleGetter)));
					}
				}
			}
			else {
				// $rule is another Rule than OutcomeCondition/ResponseCondition.
				$processor = $this->getRuleProcessorFactory()->createProcessor($rule);
				$processor->setState($state);
				$processor->process();
			}
		}
	}
}