<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package qtism
 *  
 *
 */
namespace qtism\common\datatypes;

use qtism\common\enums\Cardinality;
use qtism\common\enums\BaseType;
use \InvalidArgumentException;
use qtism\common\Comparable;

/**
 * From IMS QTI:
 * 
 * A point value represents an integer tuple corresponding to a 
 * graphic point. The two integers correspond to the horizontal (x-axis) 
 * and vertical (y-axis) positions respectively. The up/down and 
 * left/right senses of the axes are context dependent.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Point implements Comparable, QtiDatatype {
	
	/**
	 * The position on the x-axis.
	 * 
	 * @var int
	 */
	private $x;
	
	/**
	 * The position on the y-axis.
	 * 
	 * @var int
	 */
	private $y;
	
	/**
	 * Create a new Point object.
	 * 
	 * @param int $x A position on the x-axis.
	 * @param int $y A position on the y-axis.
	 * @throws InvalidArgumentException If $x or $y are not integer values.
	 */
	public function __construct($x, $y) {
		$this->setX($x);
		$this->setY($y);
	}
	
	/**
	 * Set the position on the x-axis.
	 * 
	 * @param int $x A position on the x-axis.
	 * @throws InvalidArgumentException If $x is nto an integer value.
	 */
	public function setX($x) {
		if (is_int($x)) {
			$this->x = $x;
		}
		else {
			$msg = "The X argument must be an integer value, '" . gettype($x) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the position on the x-axis.
	 * 
	 * @return int A position on the x-axis.
	 */
	public function getX() {
		return $this->x;
	}
	
	/**
	 * Set the position on y-axis.
	 * 
	 * @param int $y A position on the y-axis.
	 * @throws InvalidArgumentException If $y is not an integer value.
	 */
	public function setY($y) {
		if (is_int($y)) {
			$this->y = $y;
		}
		else {
			$msg = "The Y argument must be an integer value, '" . gettype($x) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the position on the y-axis.
	 * 
	 * @return int A position on the y-axis.
	 */
	public function getY() {
		return $this->y;
	}
	
	/**
	 * Wheter a given $obj is equal to this Point;
	 * 
	 * @param mixed $obj An object.
	 * @return boolean Whether the equality is established.
	 */
	public function equals($obj) {
		return (gettype($obj) === 'object' &&
			$obj instanceof self &&
			$obj->getX() === $this->getX() &&
			$obj->getY() === $this->getY());
	}
	
	public function __toString() {
		return $this->getX() . ' ' . $this->getY();
	}
	
	public function getBaseType() {
	    return BaseType::POINT;
	}
	
	public function getCardinality() {
	    return Cardinality::SINGLE;
	}
}