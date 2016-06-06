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
 * looks up the value of an itemVariable in a sub-set of the items referred to 
 * in a test. Only variables with single cardinality are considered, all NULL 
 * values are ignored. The result has cardinality multiple and base-type as 
 * specified below.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class TestVariables extends ItemSubset {
	
	/**
	 * From IMS QTI:
	 * 
	 * The identifier of the variable to look up in each item. If a test brings 
	 * together items with different variable naming conventions variableMappings 
	 * may be used to reduce the complexity of outcomes processing and allow a 
	 * single testVariables expression to be used. Items with no matching variable 
	 * are ignored.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $variableIdentifier;
	
	/**
	 * Use -1 to state there is no specific baseType.
	 * 
	 * From IMS QTI:
	 * 
	 * If specified, matches only variables declared with this baseType. This also 
	 * becomes the base-type of the result (subject to type promotion through 
	 * weighting, as described below). If omitted, variables declared with 
	 * base-type integer or float are matched. The base-type of the result is 
	 * integer if all matching values have base-type integer, otherwise it is 
	 * float and integer values are subject to type promotion.
	 * 
	 * @var int
	 * @qtism-bean-property
	 */
	private $baseType = -1;
	
	/**
	 * From IMS QTI:
	 * 
	 * If specified, the defined weight is applied to each variable as described 
	 * in the definition of weightIdentifier for a single variable. The behaviour 
	 * of this attribute is only defined if the baseType attribute is float or 
	 * omitted . When a weighting is specified the result of the expression always 
	 * has base-type float. Note that this option is incomptable with baseType 
	 * integer. This restriction ensures that the value of the baseType attribute 
	 * remains consistent with the resulting container type.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $weightIdentifier = '';
	
	/**
	 * Create a new instance of TestVariables.
	 * 
	 * @param string $variableIdentifier A QTI Identifier.
	 * @param int $baseType A value from the BaseType enumeration or -1 if not specified.
	 * @param string $weightIdentifier A QTI Identifier or '' (empty string) if not specified.
	 */
	public function __construct($variableIdentifier,  $baseType = -1, $weightIdentifier = '') {
		$this->setVariableIdentifier($variableIdentifier);
		$this->setBaseType($baseType);
		$this->setWeightIdentifier($weightIdentifier);
	}
	
	/**
	 * Set the variable identifier.
	 * 
	 * @param string $variableIdentifier A QTI Identifier.
	 * @throws InvalidArgumentException If $variableIdentifier is not a valid QTI Identifier.
	 */
	public function setVariableIdentifier($variableIdentifier) {
		if (Format::isIdentifier($variableIdentifier)) {
			$this->variableIdentifier = $variableIdentifier;
		}
		else {
			$msg = "'${variableIdentifier}' is not a valid QTI Identifier.";
			throw new InvalidArgumentException($variableIdentifier);
		}
	}
	
	/**
	 * Get the variable identifier.
	 * 
	 * @return string A QTI Identifier.
	 */
	public function getVariableIdentifier() {
		return $this->variableIdentifier;
	}
	
	/**
	 * Set the baseType. $baseType = -1 if no baseType is specified.
	 * 
	 * @param int $baseType A value from the BaseType enumeration or -1.
	 * @throws InvalidArgumentException If $baseType is not a valid QTI Identifier nor -1.
	 */
	public function setBaseType($baseType) {
		if ($baseType == -1 || in_array($baseType, BaseType::asArray())) {
			$this->baseType = $baseType;
		}
		else {
			$msg = "The BaseType argument must be a value from the BaseType enumeration";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the baseType. If no baseType, the returned value is -1.
	 * 
	 * @return int A value from the BaseType enumeration or -1 if no baseType specified.
	 */
	public function getBaseType() {
		return $this->baseType;
	}
	
	/**
	 * Set the weight identifier.
	 * 
	 * @param string $weightIdentifier A QTI Identifier.
	 * @throws InvalidArgumentException If $weightIdentifier is not a valid QTI Identifier.
	 */
	public function setWeightIdentifier($weightIdentifier) {
		if (Format::isIdentifier($weightIdentifier) || empty($weightIdentifier)) {
			$this->weightIdentifier = $weightIdentifier;
		}
		else {
			$msg = "'${weightIdentifier}' is not a valid QTI Identifier.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the weight identifier.
	 * 
	 * @return string A QTI Identifier.
	 */
	public function getWeightIdentifier() {
		return $this->weightIdentifier;
	}
	
	public function getQtiClassName() {
		return 'testVariables';
	}
}
