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


namespace qtism\data\expressions;

use qtism\common\utils\Format;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * This expression, which can only be used in outcomes processing, simultaneously 
 * looks up the normalMaximum value of an outcome variable in a sub-set of the 
 * items referred to in a test. Only variables with single cardinality are considered. 
 * If any of the items within the given subset have no declared maximum the result is 
 * NULL, otherwise the result has cardinality multiple and base-type float.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class OutcomeMaximum extends ItemSubset {
	
	/**
	 * From IMS QTI:
	 * 
	 * As per the variableIdentifier attribute of testVariables.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $outcomeIdentifier;
	
	/**
	 * From IMS QTI:
	 * 
	 * As per the weightIdentifier attribute of testVariables.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $weightIdentifier;
	
	/**
	 * Create a new instance of OutcomeMaximum.
	 * 
	 * @param string $outcomeIdentifier A QTI Identifier.
	 * @param string $weightIdentifier A QTI Identifier or '' (empty string) if not specified.
	 * @throws InvalidArgumentException If one of the arguments is not a valid QTI Identifier.
	 */
	public function __construct($outcomeIdentifier, $weightIdentifier = '') {
		$this->setOutcomeIdentifier($outcomeIdentifier);
		$this->setWeightIdentifier($weightIdentifier);
	}
	
	/**
	 * Set the outcome identifier.
	 * 
	 * @param string $outcomeIdentifier A QTI Identifier.
	 * @throws InvalidArgumentException If $outcomeIdentifier is not a valid QTI Identifier.
	 */
	public function setOutcomeIdentifier($outcomeIdentifier) {
		if (Format::isIdentifier($outcomeIdentifier)) {
			$this->outcomeIdentifier = $outcomeIdentifier;
		}
		else {
			$msg = "'${outcomeIdentifier}' is not a valid QTI Identifier.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the outcome identifier.
	 * 
	 * @return string A QTI Identifier.
	 */
	public function getOutcomeIdentifier() {
		return $this->outcomeIdentifier;
	}
	
	/**
	 * Set the weight identifier. Can be '' (empty string) if no weight specified.
	 * 
	 * @param string $weightIdentifier A QTI Identifier or '' (empty string) if not specified.
	 * @throws InvalidArgumentException If $weightIdentifier is not a valid QTI Identifier nor '' (empty string).
	 */
	public function setWeightIdentifier($weightIdentifier) {
		if (Format::isIdentifier($weightIdentifier) || $weightIdentifier == '') {
			$this->weightIdentifier = $weightIdentifier;
		}
		else {
			$msg = "'${weightIdentifier}' is not a valid QTI Identifier.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the weight identifier. Can be '' (empty string) if no weight was specified.
	 * 
	 * @return string A QTI Identifier or '' (empty string).
	 */
	public function getWeightIdentifier() {
		return $this->weightIdentifier;
	}
	
	public function getQtiClassName() {
		return 'outcomeMaximum';
	}
}
