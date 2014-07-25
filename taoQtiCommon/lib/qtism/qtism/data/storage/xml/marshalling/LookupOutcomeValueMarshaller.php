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

use qtism\data\rules\LookupOutcomeValue;
use qtism\data\QtiComponent;
use \DOMElement;

/**
 * Marshalling/Unmarshalling implementation for LookupOutcomeValue.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class LookupOutcomeValueMarshaller extends Marshaller {
	
	/**
	 * Marshall a LookupOutcomeValue object into a DOMElement object.
	 * 
	 * @param QtiComponent $component A LookupOutcomeValue object.
	 * @return DOMElement The according DOMElement object.
	 */
	protected function marshall(QtiComponent $component) {
		$element = static::getDOMCradle()->createElement($component->getQtiClassName());
		
		self::setDOMElementAttribute($element, 'identifier', $component->getIdentifier());
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component->getExpression());
		$element->appendChild($marshaller->marshall($component->getExpression()));
		
		return $element;
	}
	
	/**
	 * Unmarshall a DOMElement object corresponding to a QTI lookupOutcomeValue element.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return QtiComponent A LookupOutcomeValue object.
	 * @throws UnmarshallingException
	 */
	protected function unmarshall(DOMElement $element) {
		if (($identifier = static::getDOMElementAttributeAs($element, 'identifier')) !== null) {
			
			$expressionElt = self::getFirstChildElement($element);
			if ($expressionElt !== false) {
				$marshaller = $this->getMarshallerFactory()->createMarshaller($expressionElt);
				$expression = $marshaller->unmarshall($expressionElt);
				
				$object = new LookupOutcomeValue($identifier, $expression);
				return $object;
			}
			else {
				$msg = "The mandatory child element 'expression' is missing from element '" . $element->localName . "'.";
				throw new UnmarshallingException($msg, $element);
			}
		}
		else {
			$msg = "The mandatory attribute 'identifier' is missing from element '" . $element->localName . "'.";
			throw new UnmarshallingException($msg, $element);
		}
	}
	
	public function getExpectedQtiClassName() {
		return 'lookupOutcomeValue';
	}
}
