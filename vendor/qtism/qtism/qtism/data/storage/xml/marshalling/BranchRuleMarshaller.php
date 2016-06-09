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
use qtism\data\rules\BranchRule;
use \DOMElement;

/**
 * Marshalling/Unmarshalling implementation for branchRule.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class BranchRuleMarshaller extends Marshaller {
	
	/**
	 * Marshall a BranchRule object into a DOMElement object.
	 * 
	 * @param QtiComponent $component A BranchRule object.
	 * @return DOMElement The according DOMElement object.
	 */
	protected function marshall(QtiComponent $component) {
		$element = static::getDOMCradle()->createElement($component->getQtiClassName());
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component->getExpression());
		$element->appendChild($marshaller->marshall($component->getExpression()));
		static::setDOMElementAttribute($element, 'target', $component->getTarget());
		
		return $element;
	}
	
	/**
	 * Unmarshall a DOMElement object corresponding to a QTI branchRule element.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return QtiComponent A BranchRule object.
	 * @throws UnmarshallingException If the mandatory expression child element is missing from $element or if the 'target' element is missing.
	 */
	protected function unmarshall(DOMElement $element) {
		
		if (($target = static::getDOMElementAttributeAs($element, 'target')) !== null) {
			$expressionElt = self::getFirstChildElement($element);
			
			if ($expressionElt !== false) {
				$marshaller = $this->getMarshallerFactory()->createMarshaller($expressionElt);
				$object = new BranchRule($marshaller->unmarshall($expressionElt), $target);
				return $object;
			}
			else {
				$msg = "The mandatory child element 'expression' is missing from element '" . $element->localName . "'.";
				throw new UnmarshallingException($msg, $element);
			}
		}
		else {
			$msg = "The mandatory attribute 'target' is missing from element '" . $element->localName . "'.";
			throw new UnmarshallingException($msg, $element);
		}
	}
	
	public function getExpectedQtiClassName() {
		return 'branchRule';
	}
}
