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

use qtism\common\datatypes\Shape;
use qtism\common\datatypes\Coords;
use qtism\common\utils\Format;
use qtism\data\QtiComponentCollection;
use \InvalidArgumentException;

/**
 * The associableHotspot QTI class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class AssociableHotspot extends Choice implements AssociableChoice, Hotspot {
	
	/**
	 * From IMS QTI:
	 * 
	 * The maximum number of choices this choice may be associated with.
	 * If matchMax is 0 there is no restriction.
	 * 
	 * @var integer
	 * @qtism-bean-property
	 */
	private $matchMax;
	
	/**
	 * From IMS QTI:
	 * 
	 * The minimum number of choices this choice must be associated with to form a 
	 * valid response. If matchMin is 0 then the candidate is not required to 
	 * associate this choice with any others at all. matchMin must be less than or 
	 * equal to the limit imposed by matchMax.
	 * 
	 * @var integer
	 * @qtism-bean-property
	 */
	private $matchMin = 0;
	
	/**
	 * From IMS QTI:
	 * 
	 * The shape of the hotspot.
	 * 
	 * @var Shape
	 * @qtism-bean-property
	 */
	private $shape;
	
	/**
	 * From IMS QTI:
	 * 
	 * The size and position of the hotspot, interpreted in conjunction with the shape.
	 * 
	 * @var Coords
	 * @qtism-bean-property
	 */
	private $coords;
	
	/**
	 * From IMS QTI:
	 * 
	 * The alternative text for this (hot) area of the image, if specified it must be 
	 * treated in the same way as alternative text for img. For hidden hotspots this 
	 * label is ignored.
	 * 
	 * @var string
	 * @qtism-bean-property
	 */
	private $hotspotLabel = '';
	
	/**
	 * Create a new AssociableHotspot object.
	 * 
	 * @param string $identifier The identifier of the associableHotspot.
	 * @param integer $matchMax The matchMax attribute.
	 * @param integer $shape A value of the Shape enumeration.
	 * @param Coords $coords The coords of the associableHotspot.
	 * @param string $id The id of the bodyElement.
	 * @param string $class The class of the bodyElement.
	 * @param string $lang The language of the bodyElement.
	 * @param string $label The label of the bodyElement.
	 * @throws InvalidArgument If one of the constructor's argument is invalid.
	 */
	public function __construct($identifier, $matchMax, $shape, Coords $coords, $id = '', $class = '', $lang = '', $label = '') {
		parent::__construct($identifier, $id, $class, $lang, $label);
		$this->setMatchMax($matchMax);
		$this->setShape($shape);
		$this->setCoords($coords);
	}
	
	/**
	 * Set the matchMax of the associableHotspot.
	 * 
	 * @param integer $matchMax A positive (>= 0) integer.
	 * @throws InvalidArgumentException If $matchMax is not a positive integer.
	 */
	public function setMatchMax($matchMax) {
		if (is_int($matchMax) === true && $matchMax >= 0) {
			$this->matchMax = $matchMax;
		}
		else {
			$msg = "The 'matchMax' argument must be a positive integer, '" . gettype($matchMax) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the matchMax of the associableHotspot.
	 * 
	 * @return integer A positive integer.
	 */
	public function getMatchMax() {
		return $this->matchMax;
	}
	
	/**
	 * Set the matchMin of the associableHotspot.
	 * 
	 * @param integer $matchMin A positive (>= 0) integer.
	 * @throws InvalidArgumentException If $matchMin is not a positive integer.
	 */
	public function setMatchMin($matchMin) {
		if (is_int($matchMin) === true && $matchMin >= 0) {
			$this->matchMin = $matchMin;	
		}
		else {
			$msg = "The 'matchMin' argument must be a positive integer, '" . gettype($matchMin) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the matchMin of the associableHotspot.
	 * 
	 * @param integer $matchMin A positive (>= 0) integer.
	 */
	public function getMatchMin() {
		return $this->matchMin;
	}
	
	/**
	 * Set the shape of the associableHotspot.
	 * 
	 * @param integer $shape A value from the Shape enumeration.
	 */
	public function setShape($shape) {
	    if (in_array($shape, Shape::asArray()) === true) {
	        $this->shape = $shape;
	    }
		else {
		    $msg = "The 'shape' argument must be a value from the Shape enumeration, '" . $shape . "' given.";
		    throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the shape of the associableHotspot.
	 * 
	 * @return Shape A Shape object.
	 */
	public function getShape() {
		return $this->shape;
	}
	
	/**
	 * Set the coords of the associableHotspot.
	 * 
	 * @param Coords $coords A Coords object.
	 */
	public function setCoords(Coords $coords) {
		$this->coords = $coords;
	}
	
	/**
	 * Get the coords of the associableHotspot.
	 * 
	 * @return Coords A Coords object.
	 */
	public function getCoords() {
		return $this->coords;
	}
	
	/**
	 * Set the hotspotLabel of the associableHotspot.
	 * 
	 * @param string $hotspotLabel A string with at most 256 characters.
	 * @throws InvalidArgumentException If $hotspotLabel has more than 256 characters.
	 */
	public function setHotspotLabel($hotspotLabel) {
		if (Format::isString256($hotspotLabel) === true) {
			$this->hotspotLabel = $hotspotLabel;
		}
		else {
			$msg = "The 'hotspotLabel' argument must be a string value with at most 256 characters.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the hotspotLabel of the associableHotspot.
	 * 
	 * @return string A string with at most 256 characters.
	 */
	public function getHotspotLabel() {
		return $this->hotspotLabel;
	}
	
	public function hasHotspotLabel() {
	    return $this->getHotspotLabel() !== '';
	}
	
	public function getComponents() {
		return new QtiComponentCollection();
	}
	
	public function getQtiClassName() {
		return 'associableHotspot';
	}
}