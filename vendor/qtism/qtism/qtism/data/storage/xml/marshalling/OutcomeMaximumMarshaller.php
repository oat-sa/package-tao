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
use qtism\data\expressions\OutcomeMaximum;
use \DOMElement;

/**
 * A marshalling/unmarshalling implementation for the QTI OutcomeMaximum expression.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class OutcomeMaximumMarshaller extends ItemSubsetMarshaller {
	
	/**
	 * Marshall an outcomeMaximum object in its DOMElement equivalent.
	 * 
	 * @param QtiComponent A OutcomeMaximum object.
	 * @return DOMElement The corresponding outcomeMaximum QTI element.
	 */
	protected function marshall(QtiComponent $component) {
		$element = parent::marshall($component);
		self::setDOMElementAttribute($element, 'outcomeIdentifier', $component->getOutcomeIdentifier());
		
		$weightIdentifier = $component->getWeightIdentifier();
		if (!empty($weightIdentifier)) {
			self::setDOMElementAttribute($element, 'weightIdentifier', $weightIdentifier);
		}
		
		return $element;
	}
	
	/**
	 * Marshall an outcomeMaximum QTI element in its OutcomeMaximum object equivalent.
	 * 
	 * @param DOMElement A DOMElement object.
	 * @return QtiComponent The corresponding OutcomeMaximum object.
	 */
	protected function unmarshall(DOMElement $element) {
		$baseComponent = parent::unmarshall($element);
		
		if (($outcomeIdentifier = static::getDOMElementAttributeAs($element, 'outcomeIdentifier')) !== null) {
			$object = new OutcomeMaximum($outcomeIdentifier);
			$object->setSectionIdentifier($baseComponent->getSectionIdentifier());
			$object->setIncludeCategories($baseComponent->getIncludeCategories());
			$object->setExcludeCategories($baseComponent->getExcludeCategories());
			
			if (($weightIdentifier = static::getDOMElementAttributeAs($element, 'weightIdentifier')) !== null) {
				$object->setWeightIdentifier($weightIdentifier);
			}
			
			return $object;
		}
		else {
			$msg = "The mandatory attribute 'outcomeIdentifier' is missing from element '" . $element->localName . "'.";
			throw new UnmarshallingException($msg, $element);
		}
	}
	
	public function getExpectedQtiClassName() {
		return 'outcomeMaximum';
	}
}
