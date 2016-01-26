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

use qtism\data\content\interactions\CustomInteraction;
use qtism\data\storage\xml\Utils;
use qtism\data\QtiComponent;
use \DOMElement;

/**
 * Marshalling/Unmarshalling implementation for customInteraction.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class CustomInteractionMarshaller extends Marshaller {
	
	/**
	 * Marshall a CustomInteraction object into a DOMElement object.
	 * 
	 * @param QtiComponent $component A CustomInteraction object.
	 * @return DOMElement The according DOMElement object.
	 */
	protected function marshall(QtiComponent $component) {
		$element = static::getDOMCradle()->createElement('customInteraction');
		self::fillElement($element, $component);
		self::setDOMElementAttribute($element, 'responseIdentifier', $component->getResponseIdentifier());
		
		if ($component->hasXmlBase() === true) {
		    self::setXmlBase($element, $component->getXmlBase());
		}
		
		$xml = $component->getXml();
		Utils::importChildNodes($xml->documentElement, $element);
		Utils::importAttributes($xml->documentElement, $element);
		
		return $element;
	}
	
	/**
	 * Unmarshall a DOMElement object corresponding to a QTI customInteraction element.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return QtiComponent A CustomInteraction object.
	 * @throws UnmarshallingException.
	 */
	protected function unmarshall(DOMElement $element) {
		
	    if (($responseIdentifier = self::getDOMElementAttributeAs($element, 'responseIdentifier')) !== null) {
	        
	        $frag = $element->ownerDocument->createDocumentFragment();
	        $element = $element->cloneNode(true);
	        $frag->appendChild($element);
	        $xmlString = $frag->ownerDocument->saveXML($frag);
	        
	        $component = new CustomInteraction($responseIdentifier, $xmlString);
	        self::fillBodyElement($component, $element);
	    }
	    
	    return $component;
	}
	
	public function getExpectedQtiClassName() {
		return 'customInteraction';
	}
}
