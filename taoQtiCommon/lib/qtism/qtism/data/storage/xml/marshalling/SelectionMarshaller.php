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
use qtism\data\rules\Selection;
use \DOMElement;

/**
 * Marshalling/Unmarshalling implementation for selection.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class SelectionMarshaller extends Marshaller {
	
	/**
	 * Marshall a Selection object into a DOMElement object.
	 * 
	 * @param QtiComponent $component A Selection object.
	 * @return DOMElement The according DOMElement object.
	 */
	protected function marshall(QtiComponent $component) {
		$element = static::getDOMCradle()->createElement($component->getQtiClassName());
		
		self::setDOMElementAttribute($element, 'select', $component->getSelect());
		self::setDOMElementAttribute($element, 'withReplacement', $component->isWithReplacement());
		
		return $element;
	}
	
	/**
	 * Unmarshall a DOMElement object corresponding to a QTI Selection object.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return QtiComponent A Selection object.
	 * @throws UnmarshallingException If the mandatory 'select' attribute is missing from $element.
	 */
	protected function unmarshall(DOMElement $element) {
		
		// select is a mandatory value, retrieve it first.
		if (($value = static::getDOMElementAttributeAs($element, 'select', 'integer')) !== null) {
			$object = new Selection($value);
			
			if (($value = static::getDOMElementAttributeAs($element, 'withReplacement', 'boolean')) !== null) {
				$object->setWithReplacement($value);
			}
		}
		else {
			$msg = "The mandatory attribute 'select' is missing.";
			throw new UnmarshallingException($msg, $element);
		}
		
		return $object;
	}
	
	public function getExpectedQtiClassName() {
		return 'selection';
	}
}
