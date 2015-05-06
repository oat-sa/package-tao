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
use qtism\data\processing\OutcomeProcessing;
use qtism\data\rules\OutcomeRuleCollection;
use \DOMElement;

/**
 * Marshalling/Unmarshalling implementation for outcomeProcessing.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class OutcomeProcessingMarshaller extends Marshaller {
	
	/**
	 * Marshall an OutcomeProcessing object into a DOMElement object.
	 * 
	 * @param QtiComponent $component An OutcomeProcessing object.
	 * @return DOMElement The according DOMElement object.
	 * @throws MarshallingException
	 */
	protected function marshall(QtiComponent $component) {
		$element = self::getDOMCradle()->createElement($component->getQtiClassName());
		
		foreach ($component->getOutcomeRules() as $outcomeRule) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($outcomeRule);
			$element->appendChild($marshaller->marshall($outcomeRule));
		}
		
		return $element;
	}
	
	/**
	 * Unmarshall a DOMElement object corresponding to a QTI outcomeProcessing element.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return QtiComponent An OutcomeProcessing object.
	 * @throws UnmarshallingException
	 */
	protected function unmarshall(DOMElement $element) {
		$outcomeRuleElts = self::getChildElements($element);
		
		$outcomeRules = new OutcomeRuleCollection();
		for ($i = 0; $i < count($outcomeRuleElts); $i++) {
			$marshaller = $this->getMarshallerFactory()->createMarshaller($outcomeRuleElts[$i]);
			$outcomeRules[] = $marshaller->unmarshall($outcomeRuleElts[$i]);
		}
		
		$object = new OutcomeProcessing($outcomeRules);
		return $object;
	}
	
	public function getExpectedQtiClassName() {
		return 'outcomeProcessing';
	}
}
