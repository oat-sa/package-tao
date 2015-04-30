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
use qtism\data\rules\Rule;
use qtism\runtime\common\Processable;

/**
 * The RuleProcessor class aims at processing QTI Data Model Rule objects which are:
 * 
 * * responseCondition
 * * outcomeCondition
 * * setOutcomeValue
 * * lookupOutcomeValue
 * * branchRule
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
abstract class RuleProcessor implements Processable {
	
	/**
	 * The Rule object to be processed.
	 * 
	 * @var Rule
	 */
	private $rule;
	
	/**
	 * The State object.
	 * 
	 * @var State
	 */
	private $state;
	
	/**
	 * Create a new RuleProcessor object aiming at processing the $rule Rule object.
	 * 
	 * @param Rule $rule A Rule object to be processed by the processor.
	 */
	public function __construct(Rule $rule) {
		$this->setRule($rule);
		$this->setState(new State());
	}
	
	/**
	 * Set the QTI Data Model Rule object to be processed by the 
	 * 
	 * @param Rule $rule
	 */
	public function setRule(Rule $rule) {
		$this->rule = $rule;
	}
	
	public function getRule() {
		return $this->rule;
	}
	
	/**
	 * Set the current State object.
	 *
	 * @param State $state A State object.
	 */
	public function setState(State $state) {
		$this->state = $state;
	}
	
	/**
	 * Get the current State object.
	 *
	 * @return State
	 */
	public function getState() {
		return $this->state;
	}
}