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


namespace qtism\data\state;

use qtism\data\QtiComponentCollection;
use \InvalidArgumentException;
use qtism\common\enums\Cardinality;

/**
 * From IMS QTI:
 * 
 * Response variables are declared by response declarations and bound to 
 * interactions in the itemBody. Each response variable declared may be 
 * bound to one and only one interaction.
 * 
 * At runtime, response variables are instantiated as part of an item session.
 * Their values are always initialized to NULL (no value) regardless of 
 * whether or not a default value is given in the declaration. A response 
 * variable with a NULL value indicates that the candidate has not offered 
 * a response, either because they have not attempted the item at all or 
 * because they have attempted it and chosen not to provide a response.
 * 
 * If a default value has been provided for a response variable then 
 * the variable is set to this value at the start of the first attempt.
 * If the candidate never attempts the item, in other words, the item 
 * session passes straight from the initial state to the closed state 
 * without going through the interacting state, then the response 
 * variable remains NULL and the default value is never used.
 * 
 * Implementors of Delivery Engine's should take care when implementing
 * user interfaces for items with default response variable values.
 * If the associated interaction is left in the default state 
 * (i.e., representing the default value) then it is important 
 * that the system is confident that the candidate intended to 
 * submit this value and has not simply failed to notice that 
 * a default has been provided. This is especially true if the 
 * candidate's attempt ended due to some external event, such as 
 * running out of time. The techniques required to distinguish 
 * between these cases are an issue for user interface design and 
 * are therefore out of scope for this specification.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class ResponseDeclaration extends VariableDeclaration {
	
	/**
	 * From IMS QTI:
	 * 
	 * A response declaration may assign an optional correctResponse.
	 * This value may indicate the only possible value of the response 
	 * variable to be considered correct or merely just a correct value.
	 * For responses that are being measured against a more complex scale
	 * than correct/incorrect this value should be set to the (or an) 
	 * optimal value. Finally, for responses for which no such optimal 
	 * value is defined the correctResponse must be omitted. If a delivery
	 * system supports the display of a solution then it should display 
	 * the correct values of responses (where defined) to the candidate.
	 * When correct values are displayed they must be clearly distinguished
	 * from the candidate's own responses (which may be hidden completely 
	 * if necessary).
	 * 
	 * @var CorrectResponse
	 * @qtism-bean-property
	 */
	private $correctResponse;
	
	/**
	 * From IMS QTI:
	 * 
	 * The mapping provides a mapping from the set of base values to a set of 
	 * numeric values for the purposes of response processing. See mapResponse 
	 * for information on how to use the mapping.
	 * 
	 * @var Mapping
	 * @qtism-bean-property
	 */
	private $mapping = null;
	
	/**
	 * From IMS QTI:
	 * 
	 * The areaMapping, which may only be present in declarations of variables with 
	 * baseType point, provides an alternative form of mapping which tests against 
	 * areas of the coordinate space instead of mapping single values (i.e., 
	 * single points).
	 * 
	 * @var AreaMapping
	 * @qtism-bean-property
	 */
	private $areaMapping = null;
	
	/**
	 * Create a new instance of ResponseDeclaration.
	 * 
	 * @param string $identifier The identifier of the ResponseDeclaration.
	 * @param int $baseType A value from the BaseType enumeration or -1 if no baseType.
	 * @param int $cardinality A value from the Cardinality enumeration.
	 * @param DefaultValue $defaultValue A DefaultValue object.
	 */
	public function __construct($identifier, $baseType = -1, $cardinality = Cardinality::SINGLE, DefaultValue $defaultValue = null) {
		parent::__construct($identifier, $baseType, $cardinality, $defaultValue);
	}
	
	/**
	 * Set the correct response. Give null if no correct response is specified.
	 * 
	 * @param CorrectResponse $correctResponse A CorrectResponse object or null.
	 */
	public function setCorrectResponse(CorrectResponse $correctResponse = null) {
		$this->correctResponse = $correctResponse;
	}
	
	/**
	 * Get the correct response.
	 * 
	 * @return CorrectResponse A CorrectResponse object or null if no correct response is specified.
	 */
	public function getCorrectResponse() {
		return $this->correctResponse;
	}
	
	/**
	 * Set the mapping.
	 * 
	 * @param Mapping $mapping A Mapping object or null if no mapping is specified.
	 */
	public function setMapping(Mapping $mapping = null) {
		$this->mapping = $mapping;
	}
	
	/**
	 * Get the mapping.
	 * 
	 * @return Mapping A Mapping object or null if no mapping specified.
	 */
	public function getMapping() {
		return $this->mapping;
	}
	
	/**
	 * Set the area mapping.
	 * 
	 * @param AreaMapping $areaMapping An AreaMapping object or null if no area mapping was specified.
	 */
	public function setAreaMapping(AreaMapping $areaMapping = null) {
		$this->areaMapping = $areaMapping;
	}
	
	/**
	 * Get the area mapping
	 * 
	 * @return AreaMapping An AreaMapping object or null if not area mapping is specified.
	 */
	public function getAreaMapping() {
		return $this->areaMapping;
	}
	
	public function getQtiClassName() {
		return 'responseDeclaration';
	}
	
	public function getComponents() {
		$comp = parent::getComponents()->getArrayCopy();
		
		if ($this->getAreaMapping() !== null) {
			$comp[] = $this->getAreaMapping();
		}
		
		if ($this->getMapping() !== null) {
			$comp[] = $this->getMapping();
		}
		
		if ($this->getCorrectResponse() !== null) {
			$comp[] = $this->getCorrectResponse();
		}
		
		return new QtiComponentCollection($comp);
	}
}
