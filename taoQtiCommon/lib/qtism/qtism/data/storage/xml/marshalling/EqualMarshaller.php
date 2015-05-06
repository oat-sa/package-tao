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
use qtism\data\expressions\operators\Equal;
use qtism\data\expressions\operators\ToleranceMode;
use qtism\common\utils\Format;
use \DOMElement;

/**
 * A complex Operator marshaller focusing on the marshalling/unmarshalling process
 * of equal QTI operators.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class EqualMarshaller extends OperatorMarshaller {
	
	/**
	 * Unmarshall an Equal object into a QTI equal element.
	 * 
	 * @param QtiComponent The Equal object to marshall.
	 * @param array An array of child DOMEelement objects.
	 * @return DOMElement The marshalled QTI equal element.
	 */
	protected function marshallChildrenKnown(QtiComponent $component, array $elements) {
		$element = self::getDOMCradle()->createElement($component->getQtiClassName());
		self::setDOMElementAttribute($element, 'toleranceMode', ToleranceMode::getNameByConstant($component->getToleranceMode()));
		
		$tolerance = $component->getTolerance();
		if (!empty($tolerance)) {
			self::setDOMElementAttribute($element, 'tolerance', implode("\x20", $tolerance));
		}
		
		if ($component->doesIncludeLowerBound() === false) {
		    self::setDOMElementAttribute($element, 'includeLowerBound', false);
		}
		
		if ($component->doesIncludeUpperBound() === false) {
		    self::setDOMElementAttribute($element, 'includeUpperBound', false);
		}

		foreach ($elements as $elt) {
			$element->appendChild($elt);
		}
		
		return $element;
	}
	
	/**
	 * Unmarshall a QTI equal operator element into an Equal object.
	 *
	 * @param DOMElement The equal element to unmarshall.
	 * @param QtiComponentCollection A collection containing the child Expression objects composing the Operator.
	 * @return QtiComponent An Equal object.
	 * @throws UnmarshallingException
	 */
	protected function unmarshallChildrenKnown(DOMElement $element, QtiComponentCollection $children) {
		$object = new Equal($children);
		
		if (($toleranceMode = static::getDOMElementAttributeAs($element, 'toleranceMode')) !== null) {
			$toleranceMode = ToleranceMode::getConstantByName($toleranceMode);
			$object->setToleranceMode($toleranceMode);
		}
		
		if (($tolerance = static::getDOMElementAttributeAs($element, 'tolerance')) !== null) {
			$tolerance = explode("\x20", $tolerance);
			
			if (count($tolerance) < 1) {
				$msg = "No 'tolerance' could be extracted from element '" . $element->localName . "'.";
				throw new UnmarshallingException($msg, $element);
			}
			else if (count($tolerance) > 2) {
				$msg = "'tolerance' attribute not correctly formatted in element '" . $element->localName . "'.";
				throw new UnmarshallingException($msg, $element);
			}
			else {
				$finalTolerance = array();
				foreach ($tolerance as $t) {
					$finalTolerance[] = (Format::isFloat($t)) ? floatval($t) : $t;
				}
				
				$object->setTolerance($finalTolerance);
			}
		}
		
		if (($includeLowerBound = static::getDOMElementAttributeAs($element, 'includeLowerBound', 'boolean')) !== null) {
			$object->setIncludeLowerBound($includeLowerBound);
		}
		
		if (($includeUpperBound = static::getDOMElementAttributeAs($element, 'includeUpperBound', 'boolean')) !== null) {
			$object->setIncludeUpperBound($includeUpperBound);
		}
		
		return $object;
	}
}
