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
 * From IMS QTI:
 * 
 * The selection class specifies the rules used to select the child elements of a 
 * section for each test session. If no selection rules are given we assume that 
 * all elements are to be selected.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Selection extends QtiComponent {
	
	/**
	 * The number of child elements to be selected.
	 * 
	 * @var int
	 * @qtism-bean-property
	 */
	private $select;
	
	/**
	 * Selection (combinations) with or without replacement.
	 * 
	 * @var boolean
	 * @qtism-bean-property
	 */
	private $withReplacement = false;
	
	/**
	 * Create a new instance of selection.
	 * 
	 * @param int $select The number of child elements to be selected.
	 * @param boolean $withReplacement Selection (combinations) with or without replacement.
	 * @throws InvalidArgumentException If $select is not a valid integer or if $withReplacement is not a valid boolean.
	 */
	public function __construct($select, $withReplacement = false) {
		$this->setSelect($select);
		$this->setWithReplacement($withReplacement);
	}
	
	/**
	 * Get the number of child elements to be selected.
	 * 
	 * @return integer An integer.
	 */
	public function getSelect() {
		return $this->select;
	}
	
	/**
	 * Set the number of child elements to be selected.
	 * 
	 * @param integer $select An integer.
	 * @throws InvalidArgumentException If $select is not an integer.
	 */
	public function setSelect($select) {
		if (is_int($select)) {
			$this->select = $select;
		}
		else {
			$msg = "Select must be an integer, '" . gettype($select) . "' given.";
		}
	}
	
	/**
	 * Is the selection of items with or without replacements?
	 * 
	 * @return boolean true if it must be with replacements, false otherwise.
	 */
	public function isWithReplacement() {
		return $this->withReplacement;
	}
	
	/**
	 * Set if the selection of items must be with or without replacements.
	 * 
	 * @param boolean $withReplacement true if it must be with replacements, false otherwise.
	 * @throws InvalidArgumentException If $withReplacement is not a boolean.
	 */
	public function setWithReplacement($withReplacement) {
		if (is_bool($withReplacement)) {
			$this->withReplacement = $withReplacement;
		}
		else {
			$msg = "WithReplacement must be a boolean, '" . gettype($withReplacement) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	public function getQtiClassName() {
		return 'selection';
	}
	
	public function getComponents() {
		return new QtiComponentCollection();
	}
}
