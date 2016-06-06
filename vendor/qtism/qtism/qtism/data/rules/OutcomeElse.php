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
use \InvalidArgumentException;

/**
 * The OutcomeElse class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class OutcomeElse extends QtiComponent {
	
	/**
	 * A collection of OutcomeRule objects to be evaluated.
	 * 
	 * @var OutcomeRuleCollection
	 * @qtism-bean-property
	 */
	private $outcomeRules;
	
	/**
	 * Create a new instance of OutcomeElse.
	 * 
	 * @param OutcomeRuleCollection $outcomeRules A collection of OutcomeRule objects.
	 * @throws InvalidArgumentException If $outcomeRules is an empty collection.
	 */
	public function __construct(OutcomeRuleCollection $outcomeRules) {
		$this->outcomeRules = $outcomeRules;
	}
	
	/**
	 * Get the OutcomeRule objects to be evaluated.
	 * 
	 * @return OutcomeRuleCollection A collection of OutcomeRule objects.
	 */
	public function getOutcomeRules() {
		return $this->outcomeRules;
	}
	
	/**
	 * Set the OutcomeRule objects to be evaluated.
	 * 
	 * @param OutcomeRuleCollection $outcomeRules A collection of OutcomeRule objects.
	 * @throws InvalidArgumentException If $outcomeRules is an empty collection.
	 */
	public function setOutcomeRules(OutcomeRuleCollection $outcomeRules) {
		if (count($outcomeRules) > 0) {
			$this->outcomeRules = $outcomeRules;
		}
		else {
			$msg = "An OutcomeElse object must be bound to at least one OutcomeRule object.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	public function getQtiClassName() {
		return 'outcomeElse';
	}
	
	public function getComponents() {
		$comp = $this->getOutcomeRules()->getArrayCopy();
		return new QtiComponentCollection($comp);
	}
}
