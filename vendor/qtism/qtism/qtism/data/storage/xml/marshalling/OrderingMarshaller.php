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
use qtism\data\rules\Ordering;
use \DOMElement;

/**
 * Marshalling/Unmarshalling implementation for ordering.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class OrderingMarshaller extends Marshaller {
	
	/**
	 * Marshall an Ordering object into a DOMElement object.
	 * 
	 * @param QtiComponent $component An Ordering object.
	 * @return DOMElement The according DOMElement object.
	 */
	protected function marshall(QtiComponent $component) {
		$element = static::getDOMCradle()->createElement($component->getQtiClassName());
		
		self::setDOMElementAttribute($element, 'shuffle', $component->getShuffle());
		
		return $element;
	}
	
	/**
	 * Unmarshall a DOMElement object corresponding to a QTI Ordering element.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return QtiComponent An Ordering object.
	 */
	protected function unmarshall(DOMElement $element) {
		$object = new Ordering();
		
		if (($value = static::getDOMElementAttributeAs($element, 'shuffle', 'boolean')) !== null) {
			$object->setShuffle($value);
		}
		
		return $object;
	}
	
	public function getExpectedQtiClassName() {
		return 'ordering';
	}
}
