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
use qtism\data\state\Weight;
use \InvalidArgumentException;

/**
 * From IMS QTI:
 *
 * This expression looks up the value of an itemVariable that has been declared in a 
 * corresponding variableDeclaration or is one of the built-in variables. The result 
 * has the base-type and cardinality declared for the variable subject to the type 
 * promotion of weighted outcomes (see below).
 * 
 * During outcomes processing, values taken from an individual item session can be looked 
 * up by prefixing the name of the item variable with the identifier assigned to the item 
 * in the assessmentItemRef, separated by a period character. For example, to obtain the 
 * value of the SCORE variable in the item referred to as Q01 you would use a variable 
 * instance with identifier Q01.SCORE.
 * 
 * In adaptive tests that contain items that are allowed to be replaced (i.e. that have the 
 * withReplacement attribute set to "true"), the same item can be instantiated more than once. 
 * In order to access the outcome variable values of each instantiation, a number that denotes 
 * the instance's place in the sequence of the item's instantiation is inserted between the 
 * item variable identifier and the item variable, separated by a period character. For example, 
 * to obtain the value of the SCORE variable in the item referred to as Q01 in its second 
 * instantiation you would use a variable instance, prefixed by the instantiation sequence 
 * number, prefixed by an identifier Q01.2.SCORE.
 * 
 * When looking up the value of a response variable it always takes the value assigned to it 
 * by the candidate's last submission. Unsubmitted responses are not available during expression 
 * evaluation.
 * 
 * The value of an item variable taken from an item instantiated multiple times from the 
 * same assessmentItemRef (through the use of selection withReplacement) is taken from 
 * the last instance submitted if submission is simultaneous, otherwise it is undefined.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Variable extends Expression {
	
	/**
	 * QTI Identifier of the variable.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $identifier;
	
	/**
	 * From IMS QTI:
	 * 
	 * An optional weighting to be applied to the value of the variable. Weights are defined 
	 * only in the test context (and hence only in outcomes processing) and only when the item 
	 * identifier prefixing technique (see above) is being used to look up the value of an item 
	 * variable. The weight identifier refers to a weight definition in the corresponding 
	 * assessmentItemRef. If no matching definition is found the weight is assumed to be 1.0.
	 * 
	 * Weights only apply to item variables with base types integer and float. If the item 
	 * variable is of any other type the weight is ignored. All weights are treated as having 
	 * base type float and the resulting value is obtained by multiplying the variable's value 
	 * by the associated weight. When applying a weight to the value of a variable with base 
	 * type integer the value is subject to type promotion and the result of the expression 
	 * has base type float.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $weightIdentifier ='';
	
	/**
	 * Create a new instance of Variable.
	 * 
	 * @param string $identifier A QTI Identifier.
	 * @param string $weightIdentifier A QTI Identifier.
	 * @throws InvalidArgumentException If $identifier or $weightIdentifier are not valid QTI Identifiers.
	 */
	public function __construct($identifier, $weightIdentifier = '') {
		$this->setIdentifier($identifier);
		$this->setWeightIdentifier($weightIdentifier);
	}
	
	/**
	 * Set the identifier of the variable.
	 * 
	 * @param string $identifier A QTI Identifier.
	 * @throws InvalidArgumentException If $identifier is not a valid QTI Identifier.
	 */
	public function setIdentifier($identifier) {
		if (Format::isIdentifier($identifier, false)) {
			$this->identifier = $identifier;
		}
		else {
			$msg = "'${identifier}' is not a valid QTI Identifier.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the identifier of the variable.
	 * 
	 * @return string A QTI Identifier.
	 */
	public function getIdentifier() {
		return $this->identifier;
	}
	
	/**
	 * Set the identifier of the weight of the variable in a test context.
	 * Give an empty string to state that there is no weight identifier.
	 * 
	 * @param string $weightIdentifier A QTI identifier.
	 * @throws InvalidArgumentException If $weightIdentifier is not empty but is an invalid QTI Identifier.
	 */
	public function setWeightIdentifier($weightIdentifier) {
		if (empty($weightIdentifier) || Format::isIdentifier($weightIdentifier)) {
			$this->weightIdentifier = $weightIdentifier;
		}
		else {
			$msg = "'${weightIdentifier}' is not a valid QTI Identifier.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the identifier of the weight of of the variable in a test context.
	 * If it returns an empty string, it means there is no weight identifier.
	 * 
	 * @return string A QTI identifier or an empty string if no weight identifier is specified.
	 */
	public function getWeightIdentifier() {
		return $this->weightIdentifier;
	}
	
	public function getQtiClassName() {
		return 'variable';
	}
}
