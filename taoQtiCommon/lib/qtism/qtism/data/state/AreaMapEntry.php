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
use qtism\common\datatypes\Shape;
use qtism\common\datatypes\Coords;
use \InvalidArgumentException;

/**
 * The AreaMapEntry QTI class implementation.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class AreaMapEntry extends QtiComponent {
	
	/**
	 * From IMS QTI:
	 * 
	 * The shape of the area.
	 * 
	 * @var int
	 * @qtism-bean-property
	 */
	private $shape;
	
	/**
	 * From IMS QTI:
	 * 
	 * The size and position of the area, interpreted in conjunction
	 * with the shape.
	 * 
	 * @var Coords
	 * @qtism-bean-property
	 */
	private $coords;
	
	/**
	 * From IMS QTI:
	 * 
	 * The mapped value.
	 * 
	 * @var float
	 * @qtism-bean-property
	 */
	private $mappedValue;
	
	/**
	 * Create a new AreaMapEntry object.
	 * 
	 * @param int $shape A value from the Shape enumeration.
	 * @param Coords $coords A Coords object.
	 * @param float $mappedValue A mapped value.
	 * @throws InvalidArgumentException If $shape is not a value from the Shape enumeration or if $mappedValue is not a float.
	 */
	public function __construct($shape, Coords $coords, $mappedValue) {
		$this->setShape($shape);
		$this->setCoords($coords);
		$this->setMappedValue($mappedValue);
	}
	
	/**
	 * Set the shape of the area.
	 * 
	 * @param int $shape A value from the Shape enumeration.
	 * @throws InvalidArgumentException If $shape is not a value from the Shape enumeration.
	 */
	public function setShape($shape) {
		if (in_array($shape, Shape::asArray())) {
			$this->shape = $shape;
		}
		else {
			$msg = "The shape argument must be a value from the Shape enumeration, '" . $shape . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the shape of the area.
	 * 
	 * @return int A value from the Shape enumeration.
	 */
	public function getShape() {
		return $this->shape;
	}
	
	/**
	 * Set the size and position of the area, in conjunction with the
	 * shape.
	 * 
	 * @param Coords $coords A Coords object.
	 */
	public function setCoords(Coords $coords) {
		$this->coords = $coords;
	}
	
	/**
	 * Get the size and position of the area, in conjunction with the
	 * shape.
	 * 
	 * @return Coords A Coords object.
	 */
	public function getCoords() {
		return $this->coords;
	}
	
	/**
	 * Set the mapped value.
	 * 
	 * @param float $mappedValue A mapped value.
	 * @throws InvalidArgumentException If $mappedValue is not a float value.
	 */
	public function setMappedValue($mappedValue) {
		if (is_float($mappedValue) || is_double($mappedValue)) {
			$this->mappedValue = $mappedValue;
		}
		else {
			$msg = "The mappedValue argument must be a float, '" . gettype($mappedValue) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the mapped value.
	 * 
	 * @return float A mapped value.
	 */
	public function getMappedValue() {
		return $this->mappedValue;
	}
	
	public function getQtiClassName() {
		return 'areaMapEntry';
	}
	
	public function getComponents() {
		return new QtiComponentCollection();
	}
}
