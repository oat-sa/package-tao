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

/**
 * From IMS QTI:
 * 
 * If the expression given in the outcomeIf or outcomeElseIf evaluates to true then 
 * the sub-rules contained within it are followed and any following outcomeElseIf 
 * or outcomeElse parts are ignored for this outcome condition.
 * 
 * If the expression given in the outcomeIf or outcomeElseIf does not evaluate 
 * to true then consideration passes to the next outcomeElseIf or, if there are 
 * no more outcomeElseIf parts then the sub-rules of the outcomeElse are 
 * followed (if specified).
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class OutcomeCondition extends QtiComponent implements OutcomeRule {
	
	/**
	 * An OutcomeIf object.
	 * 
	 * @var OutcomeIf
	 * @qtism-bean-property
	 */
	private $outcomeIf;
	
	/**
	 * A collection of OutcomeElseIf objects.
	 * 
	 * @var OutcomeElseIfCollection
	 * @qtism-bean-property
	 */
	private $outcomeElseIfs;
	
	/**
	 * An optional OutcomeElse object.
	 * 
	 * @var OutcomeElse
	 * @qtism-bean-property
	 */
	private $outcomeElse = null;
	
	/**
	 * Create a new instance of OutcomeCondition.
	 * 
	 * @param OutcomeIf $outcomeIf An OutcomeIf object.
	 * @param OutcomeElseIfCollection $outcomeElseIfs A collection of OutcomeElseIf objects.
	 * @param OutcomeElse $outcomeElse An OutcomeElse object.
	 */
	public function __construct(OutcomeIf $outcomeIf, OutcomeElseIfCollection $outcomeElseIfs = null, OutcomeElse $outcomeElse = null) {
		$this->setOutcomeIf($outcomeIf);
		$this->setOutcomeElse($outcomeElse);
		$this->setOutcomeElseIfs((is_null($outcomeElseIfs)) ? new OutcomeElseIfCollection() : $outcomeElseIfs);
	}
	
	/**
	 * Get the OutcomeIf object.
	 * 
	 * @return OutcomeIf An OutcomeIf object.
	 */
	public function getOutcomeIf() {
		return $this->outcomeIf;
	}
	
	/**
	 * Set the OutcomeIf object.
	 * 
	 * @param OutcomeIf $outcomeIf An OutcomeIf object.
	 */
	public function setOutcomeIf(OutcomeIf $outcomeIf) {
		$this->outcomeIf = $outcomeIf;
	}
	
	/**
	 * Get the collection of OutcomeElseIf objects.
	 * 
	 * @return OutcomeElseIfCollection An OutcomeElseIfCollection object.
	 */
	public function getOutcomeElseIfs() {
		return $this->outcomeElseIfs;
	}
	
	/**
	 * Set the collection of OutcomeElseIf objects.
	 * 
	 * @param OutcomeElseIfCollection $outcomeElseIfs An OutcomeElseIfCollection object.
	 */
	public function setOutcomeElseIfs(OutcomeElseIfCollection $outcomeElseIfs) {
		$this->outcomeElseIfs = $outcomeElseIfs;
	}
	
	/**
	 * Get the optional OutcomeElse object. Returns null if not specified.
	 * 
	 * @return OutcomeElse An OutcomeElse object.
	 */
	public function getOutcomeElse() {
		return $this->outcomeElse;
	}
	
	/**
	 * Set the optional OutcomeElse object. A null value means there is no else.
	 * 
	 * @param OutcomeElse $outcomeElse An OutcomeElse object.
	 */
	public function setOutcomeElse(OutcomeElse $outcomeElse = null) {
		$this->outcomeElse = $outcomeElse;
	}
	
	/**
	 * Whether or not an OutcomeElse object is defined for the outcome condition.
	 * 
	 * @return boolean
	 */
	public function hasOutcomeElse() {
	    return $this->getOutcomeElse() !== null;
	}
	
	public function getQtiClassName() {
		return 'outcomeCondition';
	}
	
	public function getComponents() {
		$comp = array_merge(array($this->getOutcomeIf()),
							$this->getOutcomeElseIfs()->getArrayCopy());
		
		if (!is_null($this->getOutcomeElse())) {
			$comp[] = $this->getOutcomeElse();
		}
		
		return new QtiComponentCollection($comp);
	}
}
