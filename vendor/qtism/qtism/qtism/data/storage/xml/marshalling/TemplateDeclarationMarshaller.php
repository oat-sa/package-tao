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
use qtism\data\state\TemplateDeclaration;
use \DOMElement;
use \InvalidArgumentException;

/**
 * Marshalling/Unmarshalling implementation for templateDeclaration.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class TemplateDeclarationMarshaller extends VariableDeclarationMarshaller {
	
	/**
	 * Marshall a TemplateDeclaration object into a DOMElement object.
	 * 
	 * @param QtiComponent $component A TemplateDeclaration object.
	 * @return DOMElement The according DOMElement object.
	 */
	protected function marshall(QtiComponent $component) {
		$element = parent::marshall($component);
		
		if ($component->isParamVariable() === true) {
		    self::setDOMElementAttribute($element, 'paramVariable', true);
		}
		
		if ($component->isMathVariable() === true) {
		    self::setDOMElementAttribute($element, 'mathVariable', true);
		}
		
		return $element;
	}
	
	/**
	 * Unmarshall a DOMElement object corresponding to a QTI templateDeclaration element.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return QtiComponent A TemplateDeclaration object.
	 * @throws UnmarshallingException 
	 */
	protected function unmarshall(DOMElement $element) {
		
		try {
			$baseComponent = parent::unmarshall($element);
			$object = new TemplateDeclaration($baseComponent->getIdentifier());
			$object->setBaseType($baseComponent->getBaseType());
			$object->setCardinality($baseComponent->getCardinality());
			$object->setDefaultValue($baseComponent->getDefaultValue());
			
			if (($paramVariable = self::getDOMElementAttributeAs($element, 'paramVariable', 'boolean')) !== null) {
			    $object->setParamVariable($paramVariable);
			} 
			
			if (($mathVariable = self::getDOMElementAttributeAs($element, 'mathVariable', 'boolean')) !== null) {
			    $object->setMathVariable($mathVariable);
			}
			
			return $object;
		}
		catch (InvalidArgumentException $e) {
			$msg = "An unexpected error occured while unmarshalling the templateDeclaration.";
			throw new UnmarshallingException($msg, $element, $e);
		}
	}
	
	public function getExpectedQtiClassName() {
		return 'templateDeclaration';
	}
}
