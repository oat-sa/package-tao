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
use qtism\data\state\VariableDeclaration;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\data\ViewCollection;
use qtism\data\View;
use \DOMElement;
use \InvalidArgumentException;

/**
 * Marshalling/Unmarshalling implementation for variableDeclaration.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class VariableDeclarationMarshaller extends Marshaller {
	
	/**
	 * Marshall a VariableDeclaration object into a DOMElement object.
	 * 
	 * @param QtiComponent $component An OutcomeDeclaration object.
	 * @return DOMElement The according DOMElement object.
	 */
	protected function marshall(QtiComponent $component) {
		$element = static::getDOMCradle()->createElement($component->getQtiClassName());
		
		self::setDOMElementAttribute($element, 'identifier', $component->getIdentifier());
		self::setDOMElementAttribute($element, 'cardinality', Cardinality::getNameByConstant($component->getCardinality()));
		
		if ($component->getBaseType() != -1) {
			self::setDOMElementAttribute($element, 'baseType', BaseType::getNameByConstant($component->getBaseType()));
		}
		
		// deal with default value.
		if ($component->getDefaultValue() != null) {
			$defaultValue = $component->getDefaultValue();
			$defaultValueMarshaller = $this->getMarshallerFactory()->createMarshaller($defaultValue, array($component->getBaseType()));
			$element->appendChild($defaultValueMarshaller->marshall($defaultValue));
		}	
		
		return $element;
	}
	
	/**
	 * Unmarshall a DOMElement object corresponding to a QTI variableDeclaration element.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return QtiComponent A VariableDeclaration object.
	 * @throws UnmarshallingException 
	 */
	protected function unmarshall(DOMElement $element) {
		
		try {
			// identifier is a mandatory value for the variableDeclaration element.
			if (($identifier = static::getDOMElementAttributeAs($element, 'identifier')) !== null) {
					
				// cardinality is a mandatory value too.
				if (($cardinality = static::getDOMElementAttributeAs($element, 'cardinality')) !== null) {
					$object = new VariableDeclaration($identifier, -1, Cardinality::getConstantByName($cardinality));
			
					// deal with baseType.
					$baseType = static::getDOMElementAttributeAs($element, 'baseType');
					if (!empty($baseType)) {
						$object->setBaseType(BaseType::getConstantByName($baseType));
					}
					
					// set up optional default value.
					$defaultValueElements = $element->getElementsByTagName('defaultValue');
					if ($defaultValueElements->length == 1) {
						$defaultValueElement = $defaultValueElements->item(0);
						$defaultValueMarshaller = $this->getMarshallerFactory()->createMarshaller($defaultValueElements->item(0),  array($object->getBaseType()));
						
						$object->setDefaultValue($defaultValueMarshaller->unmarshall($defaultValueElement));
					}
					
					return $object;
				}
				else {
					$msg = "The mandatory attribute 'cardinality' is missing from element '" . $element->localName . "'.";
					throw new UnmarshallingException($msg, $element);
				}
			}
			else {
				$msg = "The mandatory attribute 'identifier' is missing from element '" . $element->localName . "'.";
				throw new UnmarshallingException($msg, $element);
			}
		}
		catch (InvalidArgumentException $e) {
			$msg = "An unexpected error occured while unmarshalling the variableDeclaration.";
			throw new UnmarshallingException($msg, $element, $e);
		}
	}
	
	public function getExpectedQtiClassName() {
		return 'variableDeclaration';
	}
}
