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
use qtism\data\state\AreaMapping;
use qtism\data\state\AreaMapEntry;
use qtism\data\state\AreaMapEntryCollection;
use \DOMElement;
use \Exception;
use \InvalidArgumentException;

/**
 * Marshalling/Unmarshalling implementation for AreaMapping.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class AreaMappingMarshaller extends Marshaller {
	
	/**
	 * Marshall an AreaMapping object into a DOMElement object.
	 * 
	 * @param QtiComponent $component An AreaMapping object.
	 * @return DOMElement The according DOMElement object.
	 */
	protected function marshall(QtiComponent $component) {
		$element = static::getDOMCradle()->createElement($component->getQtiClassName());
		
		self::setDOMElementAttribute($element, 'defaultValue', $component->getDefaultValue());
		
		if ($component->hasLowerBound() === true) {
			self::setDOMElementAttribute($element, 'lowerBound', $component->getLowerBound());
		}
		
		if ($component->hasUpperBound() === true) {
			self::setDOMElementAttribute($element, 'upperBound', $component->getUpperBound());
		}
		
		foreach ($component->getAreaMapEntries() as $entry) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($entry);
			
			$element->appendChild($marshaller->marshall($entry));
		}
		
		return $element;
	}
	
	/**
	 * Unmarshall a DOMElement object corresponding to a QTI areaMapping element.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return QtiComponent An AreaMapping object.
	 * @throws UnmarshallingException
	 */
	protected function unmarshall(DOMElement $element) {
		
		$areaMapEntries = new AreaMapEntryCollection();
		$areaMapEntryElts = static::getChildElementsByTagName($element, 'areaMapEntry');
		
		foreach ($areaMapEntryElts as $areaMapEntryElt) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($areaMapEntryElt);
			$areaMapEntries[] = $marshaller->unmarshall($areaMapEntryElt);
		}
		
		$object = new AreaMapping($areaMapEntries);
		
		if (($defaultValue = static::getDOMElementAttributeAs($element, 'defaultValue', 'float')) !== null) {
			$object->setDefaultValue($defaultValue);
		}
		
		if (($lowerBound = static::getDOMElementAttributeAs($element, 'lowerBound', 'float')) !== null) {
			$object->setLowerBound($lowerBound);
		}
		
		if (($upperBound = static::getDOMElementAttributeAs($element, 'upperBound', 'float')) !== null) {
			$object->setUpperBound($upperBound);
		}
		
		return $object;
	}
	
	public function getExpectedQtiClassName() {
		return 'areaMapping';
	}
}
