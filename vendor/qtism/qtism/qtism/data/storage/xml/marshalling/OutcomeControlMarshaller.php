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

use qtism\common\utils\Reflection;
use qtism\data\expressions\Expression;
use qtism\data\rules\SetOutcomeValue;
use qtism\data\rules\LookupOutcomeValue;
use qtism\data\rules\ExitTest;
use qtism\data\QtiComponent;
use qtism\data\QtiComponentCollection;
use qtism\data\rules\OutcomeRuleCollection;
use qtism\data\rules\OutcomeIf;
use qtism\data\rules\OutcomeElseIf;
use \ReflectionClass;
use \DOMElement;
use \DOMNode;

class OutcomeControlMarshaller extends RecursiveMarshaller {
	
	protected function unmarshallChildrenKnown(DOMElement $element, QtiComponentCollection $children) {
		
		$expressionElts = self::getChildElementsByTagName($element, Expression::getExpressionClassNames());

		if (count($expressionElts) > 0) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($expressionElts[0]);
			$expression = $marshaller->unmarshall($expressionElts[0]);
		}
		else if (($element->localName == 'outcomeIf' || $element->localName == 'outcomeElseIf') && count($expressionElts) == 0) {
			$msg = "An '" . $element->localName . "' must contain an 'expression' element. None found at line " . $element->getLineNo() . "'.";
			throw new UnmarshallingException($msg, $element);
		}
		
		if ($element->localName == 'outcomeIf' || $element->localName == 'outcomeElseIf') {
			$className = 'qtism\\data\\rules\\' . ucfirst($element->localName);
			$class = new ReflectionClass($className);
			$object = Reflection::newInstance($class, array($expression, $children));
		}
		else {
			$className = 'qtism\\data\\rules\\' . ucfirst($element->localName);
			$class = new ReflectionClass($className);
			$object = Reflection::newInstance($class, array($children));
		}

		return $object;
	}
	
	protected function marshallChildrenKnown(QtiComponent $component, array $elements) {
		$element = self::getDOMCradle()->createElement($component->getQtiClassName());
		
		if ($component instanceof OutcomeIf || $component instanceof OutcomeElseIf) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($component->getExpression());
			$element->appendChild($marshaller->marshall($component->getExpression()));
		}
		
		foreach ($elements as $elt) {
			$element->appendChild($elt);
		}
		
		return $element;
	}
	
	protected function isElementFinal(DOMNode $element) {
		return in_array($element->localName, array_merge(array(
					'exitTest',
					'lookupOutcomeValue',
					'setOutcomeValue'	
				)));
	}
	
	protected function isComponentFinal(QtiComponent $component) {
		return ($component instanceof ExitTest || 
				$component instanceof LookupOutcomeValue || 
				$component instanceof SetOutcomeValue);
	}
	
	protected function getChildrenElements(DOMElement $element) {
		return self::getChildElementsByTagName($element, array(
					'exitTest',
					'lookupOutcomeValue',
					'setOutcomeValue',
					'outcomeCondition'
				));
	}
	
	protected function getChildrenComponents(QtiComponent $component) {
		return $component->getOutcomeRules()->getArrayCopy();
	}
	
	protected function createCollection(DOMElement $currentNode) {
		return new OutcomeRuleCollection();
	}
	
	public function getExpectedQtiClassName() {
		return '';
	}
}
