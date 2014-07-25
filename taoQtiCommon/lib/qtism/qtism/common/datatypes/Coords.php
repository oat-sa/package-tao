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
 * Copyright (c) 2013-2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
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
use qtism\common\Comparable;
use qtism\common\collections\IntegerCollection;
use \InvalidArgumentException;

/**
 * Represents the QTI Coords Datatype.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Coords extends IntegerCollection implements QtiDatatype, Comparable {
	
    /**
     * A value from the Shape enumeration.
     * 
     * @var integer
     */
	private $shape;
	
	/**
	 * Create a new Coords object.
	 * 
	 * @param integer $shape A value from the Shape enumeration.
	 * @param array $coords An array of number values.
	 * @throws InvalidArgumentException If an error occurs while creating the Coords object.
	 */
	public function __construct($shape, array $coords = array()) {
		parent::__construct($coords);
		$this->setShape($shape);
		
		switch ($this->getShape()) {
			case Shape::DEF:
				if (count($this->getDataPlaceHolder()) > 0) {
					$msg = "No coordinates should be given when the default shape is used.";
					throw new InvalidArgumentException($msg);
				}
			break;
			
			case Shape::RECT:
				if (count($this->getDataPlaceHolder()) != 4) {
					$msg = "The rectangle coordinates must be composed by 4 values (x1, y1, x2, y2).";
					throw new InvalidArgumentException($msg);
				}
			break;
			
			case Shape::CIRCLE:
				if (count($this->getDataPlaceHolder()) != 3) {
					$msg = "The circle coordinates must be composed by 3 values (x, y, r).";
					throw new InvalidArgumentException($msg);
				}
			break;
			
			case Shape::POLY:
				if (count($this->getDataPlaceHolder()) % 2 > 0) {
					$msg = "The polygon coordinates must be composed by a pair amount of values (x1, y1, x2, y2, ...).";
					throw new InvalidArgumentException($msg);
				}
			break;
		}
	}
	
	protected function setShape($shape) {
		if (in_array($shape, Shape::asArray())) {
			$this->shape = $shape;
		}
		else {
			$msg = "The shape argument must be a value from the Shape enumeration except 'default', '" . $shape . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the shape associated to the coordinates.
	 * 
	 * @return integer A value from the Shape enumeration.
	 * 
	 */
	public function getShape() {
		return $this->shape;
	}
	
	/**
	 * Whether the given $point is inside the coordinates.
	 * 
	 * @param Point $point A Point object.
	 * @return boolean
	 */
	public function inside(Point $point) {
		if ($this->getShape() === Shape::DEF) {
			return true;
		}
		else if ($this->getShape() === Shape::RECT) {
			return $point->getX() >= $this[0] && $point->getX() <= $this[2] && $point->getY() >= $this[1] && $point->getY() <= $this[3];
		}
		else if ($this->getShape() === Shape::CIRCLE) {
			return pow($point->getX() - $this[0], 2) + pow($point->getY() - $this[1], 2) < pow($this[2], 2);
		}
		else {
			// we consider it is a polygon.
			// - Transform coordinates in vertices.
			// -- Use of the "point in polygon" algorithm.
			$vertices = array();
			for ($i = 0; $i < count($this); $i++) {
				$vertex = array();
				$vertex[] = $this[$i]; //x
				$i++;
				$vertex[] = $this[$i]; //y
				
				$vertices[] = $vertex;
			}
			
			$intersects = 0;
			for ($i = 1; $i < count($vertices); $i++) {
				$vertex1 = $vertices[$i -1];
				$vertex2 = $vertices[$i];
				
				if ($vertex1[1] === $vertex2[1] && $vertex1[1] === $point->getY() && $point->getX() > min($vertex1[0], $vertex2[0]) && $point->getX() < max($vertex1[0], $vertex2[0])) {
					// we are on a boundary.
					return true;
				}
				
				if ($point->getY() > min($vertex1[1], $vertex2[1]) && $point->getY() <= max($vertex1[1], $vertex2[1]) && $point->getX() <= max($vertex1[0], $vertex2[0]) && $vertex1[1] !== $vertex2[1]) {
					$xinters = ($point->getY() - $vertex1[1]) * ($vertex2[0] - $vertex1[0]) / ($vertex2[1] - $vertex1[1]) + $vertex1[0];
					
					if ($xinters === $point->getX()) {
						// Again, we are on a boundary.
						return true;
					}
					
					if ($vertex1[0] === $vertex2[0] || $point->getX() <= $xinters) {
						// We have a single intersection.
						$intersects++;
					}
				}
			}
			
			// If we passed through an odd number of edges, we are in the polygon!
			return $intersects % 2 !== 0;
		}
	}
	
	/**
	 * Return all the points of the coordinates, separated by commas (,).
	 * 
	 * @return string
	 */
	public function __toString() {
		return implode(",", $this->getDataPlaceHolder());
	}
	
	public function equals($obj) {
	    return $obj instanceof Coords && $this->getShape() === $obj->getShape() && $this->getArrayCopy() == $obj->getArrayCopy();
	}
	
	public function getBaseType() {
	    return BaseType::COORDS;
	}
	
	public function getCardinality() {
	    return Cardinality::SINGLE;
	}
}