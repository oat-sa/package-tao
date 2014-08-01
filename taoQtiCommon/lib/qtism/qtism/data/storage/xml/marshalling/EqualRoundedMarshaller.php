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

use qtism\data\QtiComponentCollection;
use qtism\data\QtiComponent;
use qtism\data\expressions\operators\EqualRounded;
use qtism\data\expressions\operators\RoundingMode;
use qtism\common\utils\Format;
use \DOMElement;

/**
 * A complex Operator marshaller focusing on the marshalling/unmarshalling process
 * of EqualRounded QTI operators.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class EqualRoundedMarshaller extends OperatorMarshaller {
	
	/**
	 * Unmarshall an EqualRounded object into a QTI equalRounded element.
	 * 
	 * @param QtiComponent The EqualRounded object to marshall.
	 * @param array An array of child DOMEelement objects.
	 * @return DOMElement The marshalled QTI equalRounded element.
	 */
	protected function marshallChildrenKnown(QtiComponent $component, array $elements) {
		$element = self::getDOMCradle()->createElement($component->getQtiClassName());
		self::setDOMElementAttribute($element, 'roundingMode', RoundingMode::getNameByConstant($component->getRoundingMode()));
		self::setDOMElementAttribute($element, 'figures', $component->getFigures());
		
		foreach ($elements as $elt) {
			$element->appendChild($elt);
		}
		
		return $element;
	}
	
	/**
	 * Unmarshall a QTI equalRounded operator element into an EqualRounded object.
	 *
	 * @param DOMElement The EqualRounded element to unmarshall.
	 * @param QtiComponentCollection A collection containing the child Expression objects composing the Operator.
	 * @return QtiComponent An EqualRounded object.
	 * @throws UnmarshallingException
	 */
	protected function unmarshallChildrenKnown(DOMElement $element, QtiComponentCollection $children) {
		if (($figures = static::getDOMElementAttributeAs($element, 'figures')) !== null) {
			
			if (Format::isInteger($figures)) {
				$figures = intval($figures);
			}
			
			$object = new EqualRounded($children, $figures);
			
			if (($roundingMode = static::getDOMElementAttributeAs($element, 'roundingMode')) !== null) {
				$object->setRoundingMode(RoundingMode::getConstantByName($roundingMode));
			}
			
			return $object;
		}
		else {
			$msg = "The mandatory attribute 'figures' is missing from element '" . $element->localName . "'.";
			throw new UnmarshallingException($msg, $element);
		}
	}
}
