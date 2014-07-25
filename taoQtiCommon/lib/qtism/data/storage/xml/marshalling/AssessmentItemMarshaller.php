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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 * 
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package 
 */


namespace qtism\data\storage\xml\marshalling;

use qtism\data\state\OutcomeDeclarationCollection;
use qtism\data\state\ResponseDeclarationCollection;
use qtism\data\QtiComponent;
use qtism\data\AssessmentItem;
use \DOMElement;

/**
 * Marshalling/Unmarshalling implementation for AssessmentItem.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class AssessmentItemMarshaller extends Marshaller {
	
	/**
	 * Marshall an AssessmentItem object into a DOMElement object.
	 * 
	 * @param QtiComponent $component An AssessmentItem object.
	 * @return DOMElement The according DOMElement object.
	 */
	protected function marshall(QtiComponent $component) {
		$element = static::getDOMCradle()->createElement($component->getQtiClassName());
		
		self::setDOMElementAttribute($element, 'identifier', $component->getIdentifier());
		self::setDOMElementAttribute($element, 'timeDependent', $component->isTimeDependent());
		self::setDOMElementAttribute($element, 'adaptive', $component->isAdaptive());
		
		if ($component->hasLang() === true) {
			self::setDOMElementAttribute($element, 'lang', $component->getLang());
		}
		
		foreach ($component->getResponseDeclarations() as $responseDeclaration) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($responseDeclaration);
			$element->appendChild($marshaller->marshall($responseDeclaration));
		}
		
		foreach ($component->getOutcomeDeclarations() as $outcomeDeclaration) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($outcomeDeclaration);
			$element->appendChild($marshaller->marshall($outcomeDeclaration));
		}
		
		if ($component->hasResponseProcessing() === true) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($component->getResponseProcessing());
			$element->appendChild($marshaller->marshall($component->getResponseProcessing()));
		}
		
		return $element;
	}
	
	/**
	 * Unmarshall a DOMElement object corresponding to a QTI assessmentItem element.
	 * 
	 * If $assessmentItem is provided, it will be used as the unmarshalled component instead of creating
	 * a new one.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @param AssessmentItem $assessmentItem An optional AssessmentItem object to be decorated.
	 * @return QtiComponent An AssessmentItem object.
	 * @throws UnmarshallingException
	 */
	protected function unmarshall(DOMElement $element, AssessmentItem $assessmentItem = null) {

		if (($identifier = static::getDOMElementAttributeAs($element, 'identifier')) !== null) {
			
			if (($timeDependent = static::getDOMElementAttributeAs($element, 'timeDependent', 'boolean')) !== null) {
				
				if (empty($assessmentItem)) {
					$object = new AssessmentItem($identifier, $timeDependent);
				}
				else {
					$object = $assessmentItem;
					$object->setIdentifier($identifier);
					$object->setTimeDependent($timeDependent);
				}
				
				
				if (($lang = static::getDOMElementAttributeAs($element, 'lang')) !== null) {
					$object->setLang($lang);
				}
				
				if (($adaptive = static::getDOMElementAttributeAs($element, 'adaptive', 'boolean')) !== null) {
					$object->setAdaptive($adaptive);
				}
				
				$responseDeclarationElts = static::getChildElementsByTagName($element, 'responseDeclaration');
				if (!empty($responseDeclarationElts)) {
					
					$responseDeclarations = new ResponseDeclarationCollection();
					
					foreach ($responseDeclarationElts as $responseDeclarationElt) {
						$marshaller = $this->getMarshallerFactory()->createMarshaller($responseDeclarationElt);
						$responseDeclarations[] = $marshaller->unmarshall($responseDeclarationElt);
					}

					$object->setResponseDeclarations($responseDeclarations);
				}
				
				$outcomeDeclarationElts = static::getChildElementsByTagName($element, 'outcomeDeclaration');
				if (!empty($outcomeDeclarationElts)) {
					
					$outcomeDeclarations = new OutcomeDeclarationCollection();
					
					foreach ($outcomeDeclarationElts as $outcomeDeclarationElt) {
						$marshaller = $this->getMarshallerFactory()->createMarshaller($outcomeDeclarationElt);
						$outcomeDeclarations[] = $marshaller->unmarshall($outcomeDeclarationElt);
					}
					
					$object->setOutcomeDeclarations($outcomeDeclarations);
				}
				
				$responseProcessingElts = static::getChildElementsByTagName($element, 'responseProcessing');
				if (!empty($responseProcessingElts)) {
					$marshaller = $this->getMarshallerFactory()->createMarshaller($responseProcessingElts[0]);
					$object->setResponseProcessing($marshaller->unmarshall($responseProcessingElts[0]));
				}
				
				return $object;
			}
			else {
				$msg = "The mandatory attribute 'timeDependent' is missing from element '" . $element->nodeName . "'.";
				throw new UnmarshallingException($msg, $element);
			}
		}
		else {
			$msg = "The mandatory attribute 'identifier' is missing from element '" . $element->nodeName . "'.";
			throw new UnmarshallingException($msg, $element);
		}
	}
	
	public function getExpectedQtiClassName() {
		return 'assessmentItem';
	}
}
