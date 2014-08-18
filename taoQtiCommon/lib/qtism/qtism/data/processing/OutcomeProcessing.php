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


namespace qtism\data\processing;

use qtism\data\QtiComponentCollection;
use qtism\data\QtiComponent;
use qtism\data\rules\OutcomeRuleCollection;

/**
 * From IMS QTI:
 * 
 * Outcome processing takes place each time the candidate submits the responses for an item 
 * (when in individual submission mode) or a group of items (when in simultaneous submission mode).
 * It happens after any (item level) response processing triggered by the submission. 
 * The values of the test's outcome variables are always reset to their defaults prior 
 * to carrying out the instructions described by the outcomeRules. Because outcome 
 * processing happend each time the candidate submits responses the resulting values 
 * of the test-level outcomes may be used to activate test-level feedback during the 
 * test or to control the behaviour of subsequent parts through the use of preConditions 
 * and branchRules.
 * 
 * The structure of outcome processing is similar to that or responseProcessing.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class OutcomeProcessing extends QtiComponent {
	
	/**
	 * A collection of OutcomeRule objects.
	 * 
	 * @var OutcomeRuleCollection
	 * @qtism-bean-property
	 */
	private $outcomeRules;
	
	/**
	 * Create a new instance of OutcomeProcessing.
	 * 
	 * @param OutcomeRuleCollection $outcomeRules A collection of OutcomeRule objects.
	 */
	public function __construct(OutcomeRuleCollection $outcomeRules = null) {
		if (empty($outcomeRules)) {
			$outcomeRules = new OutcomeRuleCollection();
		}
		
		$this->setOutcomeRules($outcomeRules);
	}
	
	/**
	 * Get the OutcomeRule objects that form the OutcomeProcessing.
	 * 
	 * @return OutcomeRuleCollection A collection of OutcomeRule object.
	 */
	public function getOutcomeRules() {
		return $this->outcomeRules;
	}
	
	/**
	 * Set the OutcomeRule objects that form the OutcomeProcessing.
	 * 
	 * @param OutcomeRuleCollection $outcomeRules A collection of OutcomeRule objects.
	 */
	public function setOutcomeRules(OutcomeRuleCollection $outcomeRules) {
		$this->outcomeRules = $outcomeRules;
	}
	
	public function getQtiClassName() {
		return 'outcomeProcessing';
	}
	
	public function getComponents() {
		return new QtiComponentCollection($this->getOutcomeRules()->getArrayCopy());
	}
}
