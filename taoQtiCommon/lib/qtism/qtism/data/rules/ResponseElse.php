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
 * The ResponseElse class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class ResponseElse extends QtiComponent {
	
	/**
	 * A collection of ResponseRule objects to be evaluated.
	 * 
	 * @var ResponseRuleCollection
	 * @qtism-bean-property
	 */
	private $responseRules;
	
	/**
	 * Create a new instance of ResponseElse.
	 * 
	 * @param ResponseRuleCollection $responseRules A collection of ResponseRule objects.
	 * @throws InvalidArgumentException If $responseRules is an empty collection.
	 */
	public function __construct(ResponseRuleCollection $responseRules) {
		$this->responseRules = $responseRules;
	}
	
	/**
	 * Get the ResponseRule objects to be evaluated.
	 * 
	 * @return ResponseRuleCollection A collection of ResponseRule objects.
	 */
	public function getResponseRules() {
		return $this->responseRules;
	}
	
	/**
	 * Set the ResponseRule objects to be evaluated.
	 * 
	 * @param ResponseRuleCollection $responseRules A collection of ResponseRule objects.
	 * @throws InvalidArgumentException If $responseRules is an empty collection.
	 */
	public function setOutcomeRules(ResponseRuleCollection $responseRules) {
		if (count($responseRules) > 0) {
			$this->responseRules = $responseRules;
		}
		else {
			$msg = "A ResponseElse object must be bound to at least one ResponseRule object.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	public function getQtiClassName() {
		return 'responseElse';
	}
	
	public function getComponents() {
		$comp = $this->getResponseRules()->getArrayCopy();
		return new QtiComponentCollection($comp);
	}
}
