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
use qtism\data\TestPart;
use qtism\data\TestFeedbackCollection;
use qtism\data\ItemSessionControl;
use qtism\data\AssessmentSectionCollection;
use qtism\data\rules\PreConditionCollection;
use qtism\data\rules\BranchRuleCollection;
use qtism\data\TimeLimits;
use qtism\data\NavigationMode;
use qtism\data\SubmissionMode;
use \DOMElement;

/**
 * Marshalling/Unmarshalling implementation for TestPart.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class TestPartMarshaller extends Marshaller {
	
	protected function marshall(QtiComponent $component) {
		$element = static::getDOMCradle()->createElement($component->getQtiClassName());
		
		self::setDOMElementAttribute($element, 'identifier', $component->getIdentifier());
		self::setDOMElementAttribute($element, 'navigationMode', NavigationMode::getNameByConstant($component->getNavigationMode()));
		self::setDOMElementAttribute($element, 'submissionMode', SubmissionMode::getNameByConstant($component->getSubmissionMode()));
		
		foreach ($component->getPreConditions() as $preCondition) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($preCondition);
			$element->appendChild($marshaller->marshall($preCondition));
		}
		
		foreach ($component->getBranchRules() as $branchRule) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($branchRule);
			$element->appendChild($marshaller->marshall($branchRule));
		}
		
		$itemSessionControl = $component->getItemSessionControl();
		if (!empty($itemSessionControl)) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($itemSessionControl);
			$element->appendChild($marshaller->marshall($itemSessionControl));
		}
		
		$timeLimits = $component->getTimeLimits();
		if (!empty($timeLimits)) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($timeLimits);
			$element->appendChild($marshaller->marshall($timeLimits));
		}
		
		foreach ($component->getAssessmentSections() as $assessmentSection) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($assessmentSection);
			$element->appendChild($marshaller->marshall($assessmentSection));
		}
		
		foreach ($component->getTestFeedbacks() as $testFeedback) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($testFeedback);
			$element->appendChild($marshaller->marshall($testFeedback));
		}
		
		return $element;
	}
	
	protected function unmarshall(DOMElement $element) {
		
		if (($identifier = static::getDOMElementAttributeAs($element, 'identifier')) !== null) {
			
			if (($navigationMode = static::getDOMElementAttributeAs($element, 'navigationMode')) !== null) {
				
				if (($submissionMode = static::getDOMElementAttributeAs($element, 'submissionMode')) !== null) {
				    
					// We do not use the regular DOMElement::getElementsByTagName method
					// because it is recursive. We only want the first level elements with
					// tagname = 'assessmentSection'.
					$assessmentSectionElts = self::getChildElementsByTagName($element, 'assessmentSection');
					$assessmentSections = new AssessmentSectionCollection();
					foreach ($assessmentSectionElts as $sectElt) {
						$marshaller = $this->getMarshallerFactory()->createMarshaller($sectElt);
						$assessmentSections[] = $marshaller->unmarshall($sectElt);
					}
					
					if (count($assessmentSections) > 0) {
						// We can instantiate because all mandatory attributes/elements were found.
						$navigationMode = NavigationMode::getConstantByName($navigationMode);
						$submissionMode = SubmissionMode::getConstantByName($submissionMode);
						$object = new TestPart($identifier, $assessmentSections, $navigationMode, $submissionMode);
						
						// preConditions
						$preConditionElts = self::getChildElementsByTagName($element, 'preCondition');
						$preConditions = new PreConditionCollection();
						foreach ($preConditionElts as $preConditionElt) {
						    $marshaller = $this->getMarshallerFactory()->createMarshaller($preConditionElt);
						    $preConditions[] = $marshaller->unmarshall($preConditionElt);
						}
						$object->setPreConditions($preConditions);
						
						// branchRules
						$branchRuleElts = self::getChildElementsByTagName($element, 'branchRule');
						$branchRules = new BranchRuleCollection();
						foreach ($branchRuleElts as $branchRuleElt) {
						    $marshaller = $this->getMarshallerFactory()->createMarshaller($branchRuleElt);
						    $branchRules[] = $marshaller->unmarshall($branchRuleElt);
						}
						$object->setBranchRules($branchRules);
						
						// itemSessionControl
						$itemSessionControlElts = self::getChildElementsByTagName($element, 'itemSessionControl');
						if (count($itemSessionControlElts) === 1) {
						    $marshaller = $this->getMarshallerFactory()->createMarshaller($itemSessionControlElts[0]);
						    $itemSessionControl = $marshaller->unmarshall($itemSessionControlElts[0]);
						    $object->setItemSessionControl($itemSessionControl);
						}
						
						// timeLimits
						$timeLimitsElts = self::getChildElementsByTagName($element, 'timeLimits');
						if (count($timeLimitsElts) === 1) {
						    $marshaller = $this->getMarshallerFactory()->createMarshaller($timeLimitsElts[0]);
						    $timeLimits = $marshaller->unmarshall($timeLimitsElts[0]);
						    $object->setTimeLimits($timeLimits);
						}
						
						// testFeedbacks
						$testFeedbackElts = self::getChildElementsByTagName($element, 'testFeedback');
						$testFeedbacks = new TestFeedbackCollection();
						foreach ($testFeedbackElts as $testFeedbackElt) {
							$marshaller = $this->getMarshallerFactory()->createMarshaller($testFeedbackElt);
							$testFeedbacks[] = $marshaller->unmarshall($testFeedbackElt);
						}
						$object->setTestFeedbacks($testFeedbacks);
						
						return $object;
					}
					else {
						$msg = "A testPart element must contain at least one assessmentSection.";
						throw new UnmarshallingException($msg, $element);
					}
				}
				else {
					$msg = "The mandatory attribute 'submissionMode' is missing from element '" . $element->localName . "'.";
					throw new UnmarshallingException($msg, $element);
				}
			}
			else {
				$msg = "The mandatory attribute 'navigationMode' is missing from element '" . $element->localName . "'.";
				throw new UnmarshallingException($msg, $element);
			}
		}
		else {
			$msg = "The mandatory attribute 'identifier' is missing from element '" . $element->localName . "'.";
			throw new UnmarshallingException($msg, $element);
		}
	}
	
	public function getExpectedQtiClassName() {
		return 'testPart';
	}
}
