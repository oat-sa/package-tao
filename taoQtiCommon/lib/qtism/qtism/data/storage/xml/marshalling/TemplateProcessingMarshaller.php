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

use qtism\data\processing\TemplateProcessing;
use qtism\data\rules\TemplateRuleCollection;
use qtism\data\QtiComponent;
use \DOMElement;
use \InvalidArgumentException;

/**
 * Marshalling/Unmarshalling implementation for TemplateProcessing.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class TemplateProcessingMarshaller extends Marshaller {
	
	/**
	 * Marshall a TemplateProcessing object into a DOMElement object.
	 * 
	 * @param QtiComponent $component A TemplateProcessing object.
	 * @return DOMElement The according DOMElement object.
	 * @throws MarshallingException
	 */
	protected function marshall(QtiComponent $component) {
		$element = self::getDOMCradle()->createElement('templateProcessing');
		
		foreach ($component->getTemplateRules() as $templateRule) {
		    $element->appendChild($this->getMarshallerFactory()->createMarshaller($templateRule)->marshall($templateRule));
		}
		
		return $element;
	}
	
	/**
	 * Unmarshall a DOMElement object corresponding to a QTI templateProcessing element.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return QtiComponent A TemplateProcessing object.
	 * @throws UnmarshallingException
	 */
	protected function unmarshall(DOMElement $element) {
	    $childrenTagNames = array('exitTemplate', 'setCorrectResponse', 'setDefaultValue', 'setTemplateValue', 'templateCondition', 'templateConstraint');
		$templateRuleElts = self::getChildElementsByTagName($element, $childrenTagNames);
		$templateRules = new TemplateRuleCollection();
		
		foreach ($templateRuleElts as $templateRuleElt) {
		    $templateRules[] = $this->getMarshallerFactory()->createMarshaller($templateRuleElt)->unmarshall($templateRuleElt);
		}
		
		try {
		    return new TemplateProcessing($templateRules);
		}
		catch (InvalidArgumentException $e) {
		    $msg = "A 'templateProcessing' element must contain at least one 'templateRule' element, none given.";
		    throw new UnmarshallingException($msg, $element, $e);
		}
	}
	
	public function getExpectedQtiClassName() {
		return 'templateProcessing';
	}
}