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
use qtism\data\AssessmentItemRef;
use qtism\common\collections\IdentifierCollection;
use qtism\data\state\VariableMappingCollection;
use qtism\data\state\WeightCollection;
use qtism\data\state\TemplateDefaultCollection;
use \DOMElement;

/**
 * Marshalling/Unmarshalling implementation for assessmentItemRef.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class AssessmentItemRefMarshaller extends SectionPartMarshaller {
	
	/**
	 * Marshall an AssessmentItemRef object into a DOMElement object.
	 * 
	 * @param QtiComponent $component An assessmentItemRef object.
	 * @return DOMElement The according DOMElement object.
	 * @throws MarshallingException
	 */
	protected function marshall(QtiComponent $component) {
		$element = parent::marshall($component);
		
		self::setDOMElementAttribute($element, 'href', $component->getHref());
		
		// Deal with categories.
		$categories = $component->getCategories();
		if (count($categories) > 0) {
			self::setDOMElementAttribute($element, 'category', implode("\x20", $categories->getArrayCopy()));
		}
		
		// Deal with variableMappings.
		$variableMappings = $component->getVariableMappings();
		foreach ($variableMappings as $mapping) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($mapping);
			$element->appendChild($marshaller->marshall($mapping));
		}
		
		// Deal with weights.
		$weights = $component->getWeights();
		foreach ($weights as $weight) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($weight);
			$element->appendChild($marshaller->marshall($weight));
		}
		
		// Deal with templateDefaults.
		$templateDefaults = $component->getTemplateDefaults();
		foreach ($templateDefaults as $default) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($default);
			$element->appendChild($marshaller->marshall($default));
		}
		
		return $element;
	}
	
	/**
	 * Unmarshall a DOMElement object corresponding to a QTI assessmentItemRef element.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return QtiComponent An AssessmentItemRef object.
	 * @throws UnmarshallingException If the mandatory attribute 'href' is missing.
	 */
	protected function unmarshall(DOMElement $element) {
		
		$baseComponent = parent::unmarshall($element);
		
		if (($href = static::getDOMElementAttributeAs($element, 'href')) !== null) {
			$object = new AssessmentItemRef($baseComponent->getIdentifier(), $href);
			$object->setRequired($baseComponent->isRequired());
			$object->setFixed($baseComponent->isFixed());
			$object->setPreConditions($baseComponent->getPreConditions());
			$object->setBranchRules($baseComponent->getBranchRules());
			$object->setItemSessionControl($baseComponent->getItemSessionControl());
			$object->setTimeLimits($baseComponent->getTimeLimits());
			
			// Deal with categories.
			if (($category = static::getDOMElementAttributeAs($element, 'category')) !== null) {
				$object->setCategories(new IdentifierCollection(explode("\x20", $category)));
			}
			
			// Deal with variableMappings.
			$variableMappingElts = $element->getElementsByTagName('variableMapping');
			$variableMappings = new VariableMappingCollection();
			for ($i = 0; $i < $variableMappingElts->length; $i++) {
				$marshaller = $this->getMarshallerFactory()->createMarshaller($variableMappingElts->item($i));
				$variableMappings[] = $marshaller->unmarshall($variableMappingElts->item($i));
			}
			$object->setVariableMappings($variableMappings);
			
			// Deal with weights.
			$weightElts = $element->getElementsByTagName('weight');
			$weights = new WeightCollection();
			for ($i = 0; $i < $weightElts->length; $i++) {
				$marshaller = $this->getMarshallerFactory()->createMarshaller($weightElts->item($i));
				$weights[] = $marshaller->unmarshall($weightElts->item($i));
			}
			$object->setWeights($weights);
			
			// Deal with templateDefaults.
			$templateDefaultElts = $element->getElementsByTagName('templateDefault');
			$templateDefaults = new TemplateDefaultCollection();
			for ($i = 0; $i < $templateDefaultElts->length; $i++) {
				$marshaller = $this->getMarshallerFactory()->createMarshaller($templateDefaultElts->item($i));
				$templateDefaults[] = $marshaller->unmarshall($templateDefaultElts->item($i));
			}
			$object->setTemplateDefaults($templateDefaults);
			
			return $object;
		}
		else {
			$msg = "The mandatory attribute 'href' is missing from element '" . $element->localName . "'.";
			throw new UnmarshallingException($msg, $element);
		}
	}
	
	public function getExpectedQtiClassName() {
		return 'assessmentItemRef';
	}
}
