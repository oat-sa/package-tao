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

use \InvalidArgumentException;
use qtism\common\utils\Format;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;

/**
 * From IMS QTI:
 * 
 * Selects a random float from the specified range [min,max].
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class RandomFloat extends Expression {
	
    /**
     * The min attribute value.
     * 
     * @var float
     */
	private $min = 0.0;
	
	/**
	 * The max attribute value.
	 * 
	 * @var float
	 * @qtism-bean-property
	 */
	private $max;
	
	/**
	 * Create a new instance of RandomFloat.
	 * 
	 * @param number|string $min A variableRef or a float value.
	 * @param number|string $max A variableRef or a float value.
	 * @throws InvalmidArgumentException If $min or $max are not valid numerics/variableRefs.
	 */
	public function __construct($min, $max) {
		$this->setMin($min);
		$this->setMax($max);
	}
	
	/**
	 * Get the min attribute value.
	 * 
	 * @return number|string A numeric value or a variableRef.
	 */
	public function getMin() {
		return $this->min;
	}
	
	/**
	 * Set the min attribute value.
	 * 
	 * @param number|string $min A float value, int value or a variableRef.
	 * @throws InvalidArgumentException If $min is not a numeric value nor a variableRef.
	 */
	public function setMin($min) {
		if (is_numeric($min) || Format::isVariableRef($min)) {
			$this->min = $min;
		}
		else {
			$msg = "'Min' must be a numeric value or a variableRef, '" . gettype($min) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the max attribute value.
	 * 
	 * @return number|string A numeric value or a variableRef.
	 */
	public function getMax() {
		return $this->max;
	}
	
	/**
	 * Set the max attribute.
	 * 
	 * @param number|string $max A numeric value or a variableRef.
	 * @throws InvalidArgumentException If $max is not a numeric value nor a variableRef.
	 */
	public function setMax($max) {
		if (is_numeric($max) || Format::isVariableRef($max)) {
			$this->max = $max;
		}
		else {
			$msg = "'Max must be a numeric value or a variableRef, '" . gettype($max) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	public function getQtiClassName() {
		return 'randomFloat';
	}
}
