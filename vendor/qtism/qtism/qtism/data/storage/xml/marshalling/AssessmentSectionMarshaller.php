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
use qtism\data\QtiComponentCollection;
use qtism\data\AssessmentSection;
use qtism\data\SectionPartCollection;
use qtism\data\content\RubricBlockCollection;
use \DOMElement;
use \DOMNode;
use \DOMXPath;
use \ReflectionClass;

class AssessmentSectionMarshaller extends RecursiveMarshaller {
	
	protected function unmarshallChildrenKnown(DOMElement $element, QtiComponentCollection $children, AssessmentSection $assessmentSection = null) {
		$baseMarshaller = new SectionPartMarshaller();
		$baseComponent = $baseMarshaller->unmarshall($element);
		
		if (($title = static::getDOMElementAttributeAs($element, 'title')) !== null) {
			
			if (($visible = static::getDOMElementAttributeAs($element, 'visible', 'boolean')) !== null) {
				
				if (empty($assessmentSection)) {
					$object = new AssessmentSection($baseComponent->getIdentifier(), $title, $visible);
				}
				else {
					$object = $assessmentSection;
					$object->setIdentifier($baseComponent->getIdentifier());
					$object->setTitle($title);
					$object->setVisible($visible);
				}
				
				
				// One day... We will be able to overload methods in PHP... :'(
				$object->setRequired($baseComponent->isRequired());
				$object->setFixed($baseComponent->isFixed());
				$object->setPreConditions($baseComponent->getPreConditions());
				$object->setBranchRules($baseComponent->getBranchRules());
				$object->setItemSessionControl($baseComponent->getItemSessionControl());
				$object->setTimeLimits($baseComponent->getTimeLimits());
				
				// Deal with the keepTogether attribute.
				if (($keepTogether = static::getDOMElementAttributeAs($element, 'keepTogether', 'boolean')) !== null) {
					$object->setKeepTogether($keepTogether);
				}
				
				// Deal with selection elements.
				$selectionElements = static::getChildElementsByTagName($element, 'selection');
				if (count($selectionElements) == 1) {
					$select = intval($selectionElements[0]->getAttribute('select'));
                    if ($select > 0) {
                        $marshaller = $this->getMarshallerFactory()->createMarshaller($selectionElements[0]);
                        $object->setSelection($marshaller->unmarshall($selectionElements[0]));
                    }
				}
				
				// Deal with ordering elements.
				$orderingElements = static::getChildElementsByTagName($element, 'ordering');
				if (count($orderingElements) == 1) {
					$marshaller = $this->getMarshallerFactory()->createMarshaller($orderingElements[0]);
					$object->setOrdering($marshaller->unmarshall($orderingElements[0]));
				}
				
				// Deal with rubrickBlocks.
				$rubricBlockElements = static::getChildElementsByTagName($element, 'rubricBlock');
				if (count($rubricBlockElements) > 0) {
					$rubricBlocks = new RubricBlockCollection();
					for ($i = 0; $i < count($rubricBlockElements); $i++) {
						$marshaller = $this->getMarshallerFactory()->createMarshaller($rubricBlockElements[$i]);
						$rubricBlocks[] = $marshaller->unmarshall($rubricBlockElements[$i]);
					}
					
					$object->setRubricBlocks($rubricBlocks);
				}
				
				// Deal with section parts... which are known :) !
				$object->setSectionParts($children);
				
				return $object;
			}
			else {
				$msg = "The mandatory attribute 'visible' is missing from element '" . $element->localName . "'.";
				throw new UnmarshallingException($msg, $element);
			}
		}
		else {
			$msg = "The mandatory attribute 'title' is missing from element '" . $element->localName . "'.";
			throw new UnmarshallingException($msg, $element);
		}
	}
	
	protected function marshallChildrenKnown(QtiComponent $component, array $elements) {
		$baseMarshaller = new SectionPartMarshaller();
		$element = $baseMarshaller->marshall($component);
		
		self::setDOMElementAttribute($element, 'title', $component->getTitle());
		self::setDOMElementAttribute($element, 'visible', $component->isVisible());
		self::setDOMElementAttribute($element, 'keepTogether', $component->mustKeepTogether());
		
		// Deal with selection element
		$selection = $component->getSelection();
		if (!empty($selection)) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($selection);
			$element->appendChild($marshaller->marshall($selection));
		}
		
		// Deal with ordering element.
		$ordering = $component->getOrdering();
		if (!empty($ordering)) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($ordering);
			$element->appendChild($marshaller->marshall($ordering));
		}
		
		// Deal with rubricBlock elements.
		foreach ($component->getRubricBlocks() as $rubricBlock) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($rubricBlock);
			$element->appendChild($marshaller->marshall($rubricBlock));
		}
		
		// And finally...
		// Deal with sectionPart elements that are actually known...
		foreach ($elements as $elt) {
			$element->appendChild($elt);
		}
		
		return $element;
	}
	
	protected function isElementFinal(DOMNode $element) {
		return $element->localName != 'assessmentSection';
	}
	
	protected function isComponentFinal(QtiComponent $component) {
		return !$component instanceof AssessmentSection;
	}
	
	protected function getChildrenElements(DOMElement $element) {
		if ($element->localName == 'assessmentSection') {
			$doc = $element->ownerDocument;
			$xpath = new DOMXPath($doc);
			$nodeList = $xpath->query('assessmentSection | assessmentSectionRef | assessmentItemRef', $element);
			
			if ($nodeList->length == 0) {
				$xpath->registerNamespace('qti', $doc->lookupNamespaceURI($doc->namespaceURI));
				$nodeList = $xpath->query('qti:assessmentSection | qti:assessmentSectionRef | qti:assessmentItemRef', $element);
			}
			
			$returnValue = array();
			
			for ($i = 0; $i < $nodeList->length; $i++) {
				$returnValue[] = $nodeList->item($i);
			}
			
			return $returnValue;
		}
		else {
			return array();
		}
	}
	
	protected function getChildrenComponents(QtiComponent $component) {
		if ($component instanceof AssessmentSection) {
			return $component->getSectionParts()->getArrayCopy();
		}
		else {
			return array();
		}
	}
	
	protected function createCollection(DOMElement $currentNode) {
		return new SectionPartCollection();
	}
	
	public function getExpectedQtiClassName() {
		return '';
	}
}
