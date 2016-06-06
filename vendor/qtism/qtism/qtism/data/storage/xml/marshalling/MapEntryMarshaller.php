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

use qtism\data\QtiComponent;
use qtism\common\enums\BaseType;
use qtism\data\storage\Utils;
use qtism\data\state\MapEntry;
use \DOMElement;
use \InvalidArgumentException;
use \UnexpectedValueException;

/**
 * Marshalling/Unmarshalling implementation for mapEntry.
 * 
 * This marshaller is a parametric one. It allows you to know 
 * the baseType of the 'mapKey' attribute while unmarshalling
 * it. The value of the given baseType is found in the related
 * responseDeclaration element.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class MapEntryMarshaller extends Marshaller {
	
	private $baseType;
	
	/**
	 * Set a baseType to this marshaller implementation in order
	 * to force the datatype used for the unserialization of the
	 * 'mapKey' attribute.
	 * 
	 * @param int $baseType A baseType from the BaseType enumeration.
	 * @throws InvalidArgumentException If $baseType is not a value from the BaseType enumeration.
	 */
	protected function setBaseType($baseType) {
		if (in_array($baseType, BaseType::asArray())) {
			$this->baseType = $baseType;
		}
		else {
			$msg = 'The baseType argument must be a valid QTI baseType value from the BaseType enumeration.';
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the baseType that is used to force the unserialization of 
	 * the 'mapKey' attribute.
	 * 
	 * @return int A baseType from the BaseType enumeration.
	 */
	public function getBaseType() {
		return $this->baseType;
	}
	
	/**
	 * Create a new instance of ValueMarshaller.
	 * 
	 * @param int $baseType A value from the BaseType enumeration.
	 * @throws InvalidArgumentException if $baseType is not a value from the BaseType enumeration.
	 */
	public function __construct($baseType) {
		$this->setBaseType($baseType);
	}
	
	/**
	 * Marshall a MapEntry object into a DOMElement object.
	 * 
	 * @param QtiComponent $component A MapEntry object.
	 * @return DOMElement The according DOMElement object.
	 */
	protected function marshall(QtiComponent $component) {
		$element = static::getDOMCradle()->createElement($component->getQtiClassName());
		
		self::setDOMElementAttribute($element, 'mapKey', $component->getMapKey());
		self::setDOMElementAttribute($element, 'mappedValue', $component->getMappedValue());
		self::setDOMElementAttribute($element, 'caseSensitive', $component->isCaseSensitive());
		
		return $element;
	}
	
	/**
	 * Unmarshall a DOMElement object corresponding to a QTI mapEntry element.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return QtiComponent A MapEntry object.
	 * @throws UnmarshallingException
	 */
	protected function unmarshall(DOMElement $element) {
		
		if (($mapKey = static::getDOMElementAttributeAs($element, 'mapKey')) !== null) {
			
			try {
				$mapKey = Utils::stringToDatatype($mapKey, $this->getBaseType());
				
				if (($mappedValue = static::getDOMElementAttributeAs($element, 'mappedValue', 'float')) !== null) {
					
					$object = new MapEntry($mapKey, $mappedValue);
					
					if (($caseSensitive = static::getDOMElementAttributeAs($element, 'caseSensitive', 'boolean')) !== null) {
						$object->setCaseSensitive($caseSensitive);
					}
					
					return $object;
				}
				else {
					$msg = "The mandatory 'mappedValue' attribute is missing from element '" . $element->nodName . "'.";
					throw new UnmarshallingException($msg, $element);
				}
			}
			catch (UnexpectedValueException $e) {
				$msg = "The value of the 'mapKey' attribute '${mapKey}' could not be converted to qti:valueType.";
				throw new UnmarshallingException($msg, $element, $e);
			}
			
		}
		else {
			$msg = "The mandatory 'mapKey' attribute is missing from the '" . $element->localName . "' element";
			throw new 	UnmarshallingException($msg, $element);
		}
	}
	
	public function getExpectedQtiClassName() {
		return 'mapEntry';
	}
}
