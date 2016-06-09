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
use qtism\data\expressions\MathEnumeration;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;

/**
 * From IMS QTI:
 * 
 * The result is a mathematical constant returned as a single float, e.g. π and e.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class MathConstant extends Expression {
	
	/**
	 * The name of the math constant.
	 * 
	 * @var int
	 * @qtism-bean-property
	 */
	private $name;
	
	/**
	 * Create a new instance of MathConstant.
	 * 
	 * @param int $value A value from the MathEnumeration enumeration.
	 */
	public function __construct($name) {
		$this->setName($name);
	}
	
	/**
	 * Get the name of the mathematical constant.
	 * 
	 * @return int A value from the MathEnumeration enumeration.
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Set the name of the math constant.
	 * 
	 * @param string $name The name of the math constant.
	 * @throws InvalidArgumentException If $name is not a valid QTI math constant name.
	 */
	public function setName($name) {
		if (in_array($name, MathEnumeration::asArray())) {
			$this->name = $name;
		}
		else {
			$msg = "${name} is not a valid QTI Math constant.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	public function getQtiClassName() {
		return 'mathConstant';
	}
}
