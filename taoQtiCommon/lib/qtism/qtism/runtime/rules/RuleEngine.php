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

use qtism\data\rules\Rule;
use qtism\runtime\common\State;
use qtism\data\QtiComponent;
use qtism\runtime\common\AbstractEngine;
use \InvalidArgumentException;

/**
 * The RuleEngine class provides a way to execute any Rule object on basis
 * of a given execution context.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class RuleEngine extends AbstractEngine {
	
	/**
	 * The RuleProcessorFactory object used
	 * to instantiate the right processor depending
	 * on the given Rule object to execute.
	 * 
	 * @var RuleProcessorFactory
	 */
	private $ruleProcessorFactory;
	
	/**
	 * Create a new RuleEngine object.
	 * 
	 * @param QtiComponent $rule A rule object to be executed.
	 * @param State $context An execution context if needed.
	 */
	public function __construct(QtiComponent $rule, State $context = null) {
		parent::__construct($rule, $context);
		$this->setRuleProcessorFactory(new RuleProcessorFactory());
	}
	
	/**
	 * Set the Rule object to be executed by the engine.
	 * 
	 * @param QtiComponent $rule A Rule object to be executed.
	 * @throws InvalidArgumentException If $rule is not a Rule object.
	 */
	public function setComponent(QtiComponent $rule) {
		if ($rule instanceof Rule) {
			parent::setComponent($rule);
		}
		else {
			$msg = "The RuleEngine class only accepts to execute Rule objects.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Set the RuleProcessorFactory to be used.
	 * 
	 * @param RuleProcessorFactory $ruleProcessorFactory A RuleProcessorFactory object.
	 */
	protected function setRuleProcessorFactory(RuleProcessorFactory $ruleProcessorFactory) {
		$this->ruleProcessorFactory = $ruleProcessorFactory;
	}
	
	/**
	 * Get the RuleProcessorFactory to be used.
	 * 
	 * @return RuleProcessorFactory A RuleProcessorFactory object.
	 */
	protected function getRuleProcessorFactory() {
		return $this->ruleProcessorFactory;
	}
	
	/**
	 * Execute the current Rule object.
	 * 
	 * @throws RuleProcessingException
	 */
	public function process() {
		$rule = $this->getComponent();
		$context = $this->getContext();
		
		$processor = $this->getRuleProcessorFactory()->createProcessor($rule);
		$processor->setState($context);
		$processor->process();
		
		$this->trace($rule->getQtiClassName());
	}
}