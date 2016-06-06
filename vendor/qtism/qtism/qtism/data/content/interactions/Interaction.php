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

namespace qtism\data\content\interactions;

use qtism\data\content\BodyElement;
use qtism\common\utils\Format;
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * Interactions allow the candidate to interact with the item. Through an interaction, the candidate
 * selects or constructs a response. The candidate's responses are stored in the response variables.
 * Each interaction is associated with (at least) one response variable.
 * 
 * The state of the interaction is used to set the value of the associated response variable. 
 * The declaration of the associated response variable constrains the value of the response 
 * to be of a particular baseType and cardinality. Some interactions impose additional constraints 
 * on the set of allowable responses, for example, through constraining the minimum and/or maximum 
 * number of choices that can be selected in a choiceInteraction. In some interactive delivery 
 * engines it is possible to check these constraints while the candidate is interacting with the item. 
 * As the candidate navigates around a test they may see an indication of which items have valid responses 
 * and which require attention, or the candidate may be prevented from progressing through a test until a 
 * valid response has been selected/constructed. (See validateResponses for how to enforce this mode of 
 * operation during a test.) In some cases, delivery engines may even provide interactive controls that 
 * eliminate certains types of invalid response, for example, by restricting the number of choices that 
 * can be selected to prevent it exceeding the maximum specified for the interaction.
 * 
 * Given the possibility that a candidate may place an interaction into a state that does not satisfy 
 * these additional constraints, item authors must ensure that the response processing rules (if provided) 
 * deal appropriately with invalid values of the response variables.
 * 
 * The current version of the specification does not support the embedding of interactions in other 
 * interactions. This may change in future versions of the specification.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
abstract class Interaction extends BodyElement {
	
	/**
	 * From IMS QTI:
	 * 
	 * The response variable associated with the interaction.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $responseIdentifier;
	
	/**
	 * Create an Interaction object.
	 * 
	 * @param string $responseIdentifier The identifier of the associated response.
	 * @param string $id The id of the bodyElement.
	 * @param string $class The class of the bodyElement.
	 * @param string $lang The language of the bodyElement.
	 * @param string $label The label of the bodyElement.
	 * @throws InvalidArgumentException If one of the argument is invalid.
	 */
	public function __construct($responseIdentifier, $id = '', $class = '', $lang = '', $label = '') {
		parent::__construct($id, $class, $lang, $label);
		$this->setResponseIdentifier($responseIdentifier);
	}
	
	/**
	 * Set the response variable associated with the interaction. 
	 * 
	 * @param string $responseIdentifier A QTI identifier.
	 * @throws InvalidArgumentException If $responseIdentifier is not a valid QTI identifier.
	 */
	public function setResponseIdentifier($responseIdentifier) {
		if (Format::isIdentifier($responseIdentifier, false) === true) {
			$this->responseIdentifier = $responseIdentifier;
		}
		else {
			$msg = "The 'responseIdentifier' argument must be a valid QTI identifier.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the response variable associated with the interaction.
	 * 
	 * @return string A QTI identifier.
	 */
	public function getResponseIdentifier() {
		return $this->responseIdentifier;
	}
}