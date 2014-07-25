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

use \InvalidArgumentException;
use qtism\data\content\FlowStaticCollection;

/**
 * The simpleAssociableChoice QTI class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class SimpleAssociableChoice extends Choice implements AssociableChoice {
	
	/**
	 * The elements composing the SimpleAssociableChoice.
	 * 
	 * @var FlowStaticCollection
	 * @qtism-bean-property
	 */
	private $content;
	
	/**
	 * From IMS QTI:
	 * 
	 * The maximum number of choices this choice may be associated with.
	 * If matchMax is 0 then there is no restriction.
	 * 
	 * @var integer
	 * @qtism-bean-property
	 */
	private $matchMax;
	
	/**
	 * From IMS QTI:
	 * 
	 * The minimum number of choices this choice must be associated with 
	 * to form a valid response. If matchMin is 0 then the candidate is not 
	 * required to associate this choice with any others at all. matchMin 
	 * must be less than or equal to the limit imposed by matchMax.The 
	 * minimum number of choices this choice must be associated with to 
	 * form a valid response. If matchMin is 0 then the candidate is not 
	 * required to associate this choice with any others at all. matchMin 
	 * must be less than or equal to the limit imposed by matchMax.
	 * 
	 * @var integer
	 * @qtism-bean-property
	 */
	private $matchMin = 0;
	
	/**
	 * Create a new SimpleAssociableChoice object.
	 * 
	 * @param string $identifier The identifier of the choice.
	 * @param integer $matchMax A positive (>= 0) integer.
	 * @param string $id The id of the bodyElement.
	 * @param string $class The class of the bodyElement.
	 * @param string $lang The lang of the bodyElement.
	 * @param string $label The label of the bodyElement.
	 */
	public function __construct($identifier, $matchMax, $id = '', $class = '', $lang = '', $label = '') {
		parent::__construct($identifier, $id, $class, $lang, $label);
		$this->setMatchMax($matchMax);
		$this->setMatchMin(0);
		$this->setContent(new FlowStaticCollection());
	}
	
	/**
	 * Set the matchMax attribute.
	 * 
	 * @param integer $matchMax A positive (>= 0) integer.
	 * @throws InvalidArgumentException If $matchMax is not a positive integer.
	 */
	public function setMatchMax($matchMax) {
		if (is_int($matchMax) === true && $matchMax >= 0) {
			$this->matchMax = $matchMax;
		}
		else {
			$msg = "The 'matchMax' argument must be a positive (>= 0) integer, '" . gettype($matchMax) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the matchMax attribute.
	 * 
	 * @return integer A positive (>= 0) integer.
	 */
	public function getMatchMax() {
		return $this->matchMax;
	}
	
	/**
	 * Get the matchMin attribute.
	 * 
	 * @param integer $matchMin A positive (>= 0) integer.
	 * @throws InvalidArgumentException If $matchMin is not a positive integer.
	 */
	public function setMatchMin($matchMin) {
		if (is_int($matchMin) === true && $matchMin >= 0) {
			$this->matchMin = $matchMin;
		}
		else {
			$msg = "The 'matchMin' argument must be a positive (>= 0) integer, '" . gettype($matchMin);
			throw new InvalidArgumentException($msg);
		}
	}
	
	public function getComponents() {
		return $this->getContent();
	}
	
	/**
	 * Set the elements composing the simpleAssociableChoice.
	 * 
	 * @param FlowStaticCollection $content A collection of FlowStatic objects.
	 */
	public function setContent(FlowStaticCollection $content) {
		$this->content = $content;
	}
	
	/**
	 * Get the elements composing the simpleAssociableChoice.
	 * 
	 * @return FlowStaticCollection A collection of FlowStatic objects.
	 */
	public function getContent() {
	    return $this->content;
	}
	
	/**
	 * Set the matchMin attribute.
	 * 
	 * @return integer A positive (>= 0) integer.
	 */
	public function getMatchMin() {
		return $this->matchMin;
	}
	
	public function getQtiClassName() {
		return 'simpleAssociableChoice';
	}
}