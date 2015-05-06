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
use qtism\data\QtiComponent;
use qtism\common\utils\Format as Format;
use \InvalidArgumentException as InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * Variable mappings allow outcome variables declared with the name sourceIdentifier 
 * in the corresponding item to be treated as if they were declared with the name 
 * targetIdentifier during outcomeProcessing. Use of variable mappings allows more 
 * control over the way outcomes are aggregated when using testVariables.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class VariableMapping extends QtiComponent {
	
	/**
	 * Source variable identifier.
	 *
	 * @var string
	 * @qtism-bean-property
	 */
	private $source;
	
	/**
	 * Target variable identifier.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $target;
	
	/**
	 * Create a new instance of VariableMapping.
	 * 
	 * @param string $source The source variable identifier.
	 * @param string $target The target variable identifier.
	 * @throws InvalidArgumentException If $source or $target are not valid QTI identifiers.
	 */
	public function __construct($source, $target) {
		$this->setSource($source);
		$this->setTarget($target);
	}
	
	/**
	 * Get the source variable identifier.
	 * 
	 * @return string A QTI identifier.
	 */
	public function getSource() {
		return $this->source;
	}
	
	/**
	 * Set the source variable identifier.
	 * 
	 * @param string $source A valid QTI identifier.
	 * @throws InvalidArgumentException If $source is not a valid QTI identifier.
	 */
	public function setSource($source) {
		if (Format::isIdentifier($source)) {
			$this->source = $source;
		}
		else {
			$msg = "'${source}' is not a valid QTI identifier.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the target variable identifier.
	 * 
	 * @return string A QTI identifier.
	 */
	public function getTarget() {
		return $this->target;
	}
	
	/**
	 * Set the target variable identifier.
	 * 
	 * @param string $target A valid QTI identifier.
	 * @throws InvalidArgumentException If $target is not a valid QTI identifier.
	 */
	public function setTarget($target) {
		if (Format::isIdentifier($target)) {
			$this->target = $target;
		}
		else {
			$msg = "'${target}' is not a valid QTI identifier.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	public function getQtiClassName() {
		return 'variableMapping';
	}
	
	public function getComponents() {
		return new QtiComponentCollection();
	}
}
