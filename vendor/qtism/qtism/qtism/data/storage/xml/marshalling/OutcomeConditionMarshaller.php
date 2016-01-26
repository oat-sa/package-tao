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

use qtism\data\expressions\Expression;
use qtism\data\rules\SetOutcomeValue;
use qtism\data\rules\LookupOutcomeValue;
use qtism\data\rules\ExitTest;
use qtism\data\QtiComponent;
use qtism\data\QtiComponentCollection;
use qtism\data\rules\OutcomeRuleCollection;
use qtism\data\rules\OutcomeCondition;
use qtism\data\rules\OutcomeIf;
use qtism\data\rules\OutcomeElseIf;
use qtism\data\rules\OutcomeElseIfCollection;
use qtism\data\rules\OutcomeElse;
use \DOMElement;
use \DOMNode;

class OutcomeConditionMarshaller extends RecursiveMarshaller {
	
	protected function unmarshallChildrenKnown(DOMElement $element, QtiComponentCollection $children) {
		if (count($children) > 0) {
			// The first element of $children must be an outcomeIf.
			$outcomeIf = $children[0];
			$outcomeCondition = new OutcomeCondition($outcomeIf);
			
			if (isset($children[1])) {
				$outcomeElseIfs = new OutcomeElseIfCollection();
				// We have at least one elseIf.
				for ($i = 1; $i < count($children) - 1; $i++) {
					$outcomeElseIfs[] = $children[$i];
				}
				
				$outcomeCondition->setOutcomeElseIfs($outcomeElseIfs);
				$lastOutcomeControl = $children[count($children) - 1];
				
				if ($lastOutcomeControl instanceof OutcomeElseIf) {
					// There is no else.
					$outcomeElseIfs[] = $lastOutcomeControl;
				}
				else {
					// We have an else.
					$outcomeCondition->setOutcomeElse($lastOutcomeControl);
				}
			}
			
			return $outcomeCondition;
		}
		else {
			$msg = "An 'outcomeCondition' element must contain at least one 'outcomeIf' element. None given.";
			throw new UnmarshallingException($msg, $element);
		}
	}
	
	protected function marshallChildrenKnown(QtiComponent $component, array $elements) {
		$element = self::getDOMCradle()->createElement($component->getQtiClassName());
		
		foreach ($elements as $elt) {
			$element->appendChild($elt);
		}
		
		return $element;
	}
	
	protected function isElementFinal(DOMNode $element) {
		$exclusion = array('outcomeIf', 'outcomeElseIf', 'outcomeElse', 'outcomeCondition');
		return !in_array($element->localName, $exclusion);
	}
	
	protected function isComponentFinal(QtiComponent $component) {
		return (!$component instanceof OutcomeIf &&
				 !$component instanceof OutcomeElseIf &&
				 !$component instanceof OutcomeElse &&
				 !$component instanceof OutcomeCondition);
	}
	
	protected function getChildrenElements(DOMElement $element) {
		return self::getChildElementsByTagName($element, array(
				'outcomeIf',
				'outcomeElseIf',
				'outcomeElse',
				'exitTest',
				'lookupOutcomeValue',
				'setOutcomeValue',
				'outcomeCondition'
		));
	}
	
	protected function getChildrenComponents(QtiComponent $component) {
		if ($component instanceof OutcomeIf || $component instanceof OutcomeElseIf || $component instanceof OutcomeElse) {
			// OutcomeControl
			return $component->getOutcomeRules()->getArrayCopy();
		}
		else {
			// OutcomeCondition
			$returnValue = array($component->getOutcomeIf());
			
			if (count($component->getOutcomeElseIfs()) > 0) {
				$returnValue = array_merge($returnValue, $component->getOutcomeElseIfs()->getArrayCopy());
			}
			
			if ($component->getOutcomeElse() !== null) {
				$returnValue[] = $component->getOutcomeElse();
			}
			
			return $returnValue;
		}
	}
	
	protected function createCollection(DOMElement $currentNode) {
		if ($currentNode->localName != 'outcomeCondition') {
			return new OutcomeRuleCollection();
		}
		else {
			return new QtiComponentCollection();
		}
		
	}
	
	public function getExpectedQtiClassName() {
		return '';
	}
}
