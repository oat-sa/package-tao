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


namespace qtism\data\storage\xml\marshalling;

use qtism\common\datatypes\Shape;
use qtism\data\QtiComponent;
use qtism\data\state\AreaMapEntry;
use qtism\data\storage\Utils;
use \DOMElement;
use \Exception;
use \InvalidArgumentException;

/**
 * Marshalling/Unmarshalling implementation for AreaMapEntry.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class AreaMapEntryMarshaller extends Marshaller {
	
	/**
	 * Marshall an AreaMapEntry object into a DOMElement object.
	 * 
	 * @param QtiComponent $component An AreaMapEntry object.
	 * @return DOMElement The according DOMElement object.
	 */
	protected function marshall(QtiComponent $component) {
		$element = static::getDOMCradle()->createElement($component->getQtiClassName());
		
		self::setDOMElementAttribute($element, 'shape', Shape::getNameByConstant($component->getShape()));
		self::setDOMElementAttribute($element, 'coords', $component->getCoords());
		self::setDOMElementAttribute($element, 'mappedValue', $component->getMappedValue());
		
		return $element;
	}
	
	/**
	 * Unmarshall a DOMElement object corresponding to a QTI areaMapEntry element.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return QtiComponent An AreaMapEntry object.
	 * @throws UnmarshallingException
	 */
	protected function unmarshall(DOMElement $element) {
		
		if (($shape = static::getDOMElementAttributeAs($element, 'shape')) !== null) {
			
			$shape = Shape::getConstantByName($shape);
			
			if ($shape !== false) {
				
				if (($coords = static::getDOMElementAttributeAs($element, 'coords')) !== null) {
					
					try {
						$coords = Utils::stringToCoords($coords, $shape);
						
						if (($mappedValue = static::getDOMElementAttributeAs($element, 'mappedValue', 'float')) !== null) {
							
							return new AreaMapEntry($shape, $coords, $mappedValue);
						}
						else {
							$msg = "The mandatory attribute 'mappedValue' is missing from element '" . $element->localName . "'.";
							throw new UnmarshallingException($msg, $element);
						}
					}
					catch (Exception $e) {
						$msg = "The attribute 'coords' with value '${coords}' is has an invalid value.";
						throw new UnmarshallingException($msg, $element, $e);
					}
				}
				else {
					$msg = "The mandatory attribute 'coords' is missing from element '" . $element->localName . "'.";
					throw new UnmarshallingException($msg, $element);
				}
			}
			else {
				$msg = "The 'shape' attribute value '${shape}' is not a valid value to represent QTI shapes.";
				throw new UnmarshallingException($msg, $element);
			}
			
		}
		else {
			$msg = "The mandatory attribute 'shape' is missing from element '" . $element->localName . "'.";
			throw new UnmarshallingException($msg, $element);
		}
	}
	
	public function getExpectedQtiClassName() {
		return 'areaMapEntry';
	}
}
