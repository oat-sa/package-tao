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
use qtism\data\rules\PreCondition;
use \DOMElement;

/**
 * Marshalling/Unmarshalling implementation for preCondition.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class PreConditionMarshaller extends Marshaller {
	
	/**
	 * Marshall a PreCondition object into a DOMElement object.
	 * 
	 * @param QtiComponent $component A PreCondition object.
	 * @return DOMElement The according DOMElement object.
	 */
	protected function marshall(QtiComponent $component) {
		$element = static::getDOMCradle()->createElement($component->getQtiClassName());
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component->getExpression());
		$element->appendChild($marshaller->marshall($component->getExpression()));
		
		return $element;
	}
	
	/**
	 * Unmarshall a DOMElement object corresponding to a QTI preCondition element.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return QtiComponent A Precondition object.
	 * @throws UnmarshallingException If $element does not contain any QTI expression element.
	 */
	protected function unmarshall(DOMElement $element) {
		
		$expressionElt = self::getFirstChildElement($element);
		
		if ($expressionElt !== false) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($expressionElt);
			$object = new PreCondition($marshaller->unmarshall($expressionElt));
			return $object;
		}
		else {
			$msg = "The mandatory 'expression' child element is missing from element '" . $element->localName . "'.";
			throw new UnmarshallingException($msg, $element);
		}
	}
	
	public function getExpectedQtiClassName() {
		return 'preCondition';
	}
}
