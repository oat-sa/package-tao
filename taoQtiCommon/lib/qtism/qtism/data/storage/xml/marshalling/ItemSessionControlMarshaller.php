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
use qtism\data\ItemSessionControl;
use \DOMElement;

/**
 * Marshalling/Unmarshalling implementation for itemSessionControl.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class ItemSessionControlMarshaller extends Marshaller {
	
	protected function marshall(QtiComponent $component) {
		$element = static::getDOMCradle()->createElement($component->getQtiClassName());

		static::setDOMElementAttribute($element, 'maxAttempts', $component->getMaxAttempts());
		static::setDOMElementAttribute($element, 'showFeedback', $component->mustShowFeedback());
		static::setDOMElementAttribute($element, 'allowReview', $component->doesAllowReview());
		static::setDOMElementAttribute($element, 'showSolution', $component->mustShowSolution());
		static::setDOMElementAttribute($element, 'allowComment', $component->doesAllowComment());
		static::setDOMElementAttribute($element, 'allowSkipping', $component->doesAllowSkipping());
		static::setDOMElementAttribute($element, 'validateResponses', $component->mustValidateResponses());
		
		return $element;
	}
	
	protected function unmarshall(DOMElement $element) {
		
		$object = new ItemSessionControl();
		
		if (($value = static::getDOMElementAttributeAs($element, 'maxAttempts', 'integer')) !== null) {
			$object->setMaxAttempts($value);
		}
		
		if (($value = static::getDOMElementAttributeAs($element, 'showFeedback', 'boolean')) !== null) {
			$object->setShowFeedback($value);
		}
		
		if (($value = static::getDOMElementAttributeAs($element, 'allowReview', 'boolean')) !== null) {
			$object->setAllowReview($value);
		}
		
		if (($value = static::getDOMElementAttributeAs($element, 'showSolution', 'boolean')) !== null) {
			$object->setShowSolution($value);
		}
		
		if (($value = static::getDOMElementAttributeAs($element, 'allowComment', 'boolean')) !== null) {
			$object->setAllowComment($value);
		}
		
		if (($value = static::getDOMElementAttributeAs($element, 'allowSkipping', 'boolean')) !== null) {
			$object->setAllowSkipping($value);
		}
		
		if (($value = static::getDOMElementAttributeAs($element, 'validateResponses', 'boolean')) !== null) {
			$object->setValidateResponses($value);
		}
		
		return $object;
	}
	
	public function getExpectedQtiClassName() {
		return 'itemSessionControl';
	}
}
