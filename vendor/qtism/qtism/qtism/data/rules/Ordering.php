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
 * The ordering class specifies the rule used to arrange the child elements of a section 
 * following selection. If no ordering rule is given we assume that the elements are to 
 * be ordered in the order in which they are defined.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Ordering extends QtiComponent {
	
	/**
	 * If true causes the order of the child elements to be randomized, 
	 * if false uses the order in which the child elements are defined.
	 * 
	 * @var boolean
	 * @qtism-bean-property
	 */
	private $shuffle = false;
	
	/**
	 * Create a new instance of Ordering.
	 * 
	 * @param boolean $shuffle If child elements must be randomized.
	 * @throws InvalidArgumentException If $shuffle is not a boolean.
	 */
	public function __construct($shuffle = false) {
		$this->setShuffle($shuffle);
	}
	
	/**
	 * Returns if the child elements must be randomized.
	 * 
	 * @return boolean true if they must be randomized, false otherwise.
	 */
	public function getShuffle() {
		return $this->shuffle;
	}
	
	/**
	 * Set if the child elements must be randomized.
	 * 
	 * @param boolean $shuffle true if they must be randomized, false otherwise.
	 * @throws InvalidArgumentException If $shuffle is not a boolean.
	 */
	public function setShuffle($shuffle) {
		if (is_bool($shuffle)) {
			$this->shuffle = $shuffle;
		}
		else {
			$msg = "Shuffle must be a boolean, '" . gettype($shuffle) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	public function getQtiClassName() {
		return 'ordering';
	}
	
	public function getComponents() {
		return new QtiComponentCollection();
	}
}
