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
use qtism\data\state\OutcomeDeclaration;
use qtism\common\enums\BaseType;
use qtism\common\enums\Cardinality;
use qtism\data\ViewCollection;
use qtism\data\View;
use \DOMElement;
use \InvalidArgumentException;

/**
 * Marshalling/Unmarshalling implementation for outcomeDeclaration.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class OutcomeDeclarationMarshaller extends VariableDeclarationMarshaller {
	
	/**
	 * Marshall an OutcomeDeclaration object into a DOMElement object.
	 * 
	 * @param QtiComponent $component An OutcomeDeclaration object.
	 * @return DOMElement The according DOMElement object.
	 */
	protected function marshall(QtiComponent $component) {
		$element = parent::marshall($component);
		
		// deal with views.
		// !!! If $arrayViews contain all possible views, it means that the treated
		// !!! outcome is relevant to all views, as per QTI 2.1 spec.
		if (!in_array($component->getViews()->getArrayCopy(), View::asArray())) {
			$arrayViews = array();
			foreach ($component->getViews() as $view) {
				$arrayViews[] = View::getNameByConstant($view);
			}
			
			if (count($arrayViews) > 0) {
				static::setDOMElementAttribute($element, 'view', implode("\x20", $arrayViews));
			}
		}
		
		// deal with interpretation.
		if ($component->getInterpretation() != '') {
			static::setDOMElementAttribute($element, 'interpretation', $component->getInterpretation());
		}
		
		// deal with long interpretation.
		if ($component->getLongInterpretation() != '') {
			static::setDOMElementAttribute($element, 'longInterpretation', $component->getLongInterpretation());
		}
		
		// Deal with normal maximum.
		if ($component->getNormalMaximum() !== false) {
			static::setDOMElementAttribute($element, 'normalMaximum', $component->getNormalMaximum());
		}
		
		// Deal with normal minimum.
		if ($component->getNormalMinimum() !== false) {
			static::setDOMElementAttribute($element, 'normalMinimum', $component->getNormalMinimum());
		}
		
		// Deal with mastery value.
		if ($component->getMasteryValue() !== false) {
			static::setDOMElementAttribute($element, 'masteryValue', $component->getMasteryValue());
		}
		
		// Deal with lookup table.
		if ($component->getLookupTable() != null) {
			$lookupTableMarshaller = $this->getMarshallerFactory()->createMarshaller($component->getLookupTable(), array($component->getBaseType()));
			$element->appendChild($lookupTableMarshaller->marshall($component->geTLookupTable()));
		}
		
		
		return $element;
	}
	
	/**
	 * Unmarshall a DOMElement object corresponding to a QTI outcomeDeclaration element.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return QtiComponent An OutcomeDeclaration object.
	 * @throws UnmarshallingException 
	 */
	protected function unmarshall(DOMElement $element) {
		
		try {
			
			$baseComponent = parent::unmarshall($element);
			$object = new OutcomeDeclaration($baseComponent->getIdentifier());
			$object->setBaseType($baseComponent->getBaseType());
			$object->setCardinality($baseComponent->getCardinality());
			$object->setDefaultValue($baseComponent->getDefaultValue());
			
			// deal with views.
			if (($views = static::getDOMElementAttributeAs($element, 'view')) != null) {
				$viewCollection = new ViewCollection();
				foreach (explode("\x20", $views) as $viewName) {
					$viewCollection[] = View::getConstantByName($viewName);
				}
				
				$object->setViews($viewCollection);
			}
			
			// deal with interpretation.
			if (($interpretation = static::getDOMElementAttributeAs($element, 'interpretation')) != null) {
				$object->setInterpretation($interpretation);
			}
			
			// deal with longInterpretation.
			if (($longInterpretation = static::getDOMElementAttributeAs($element, 'longInterpretation')) != null) {
				$object->setLongInterpretation($longInterpretation);
			}
			
			// deal with normalMaximum.
			if (($normalMaximum = static::getDOMElementAttributeAs($element, 'normalMaximum', 'float')) !== null) {
				$object->setNormalMaximum($normalMaximum);
			}
			
			// deal with normalMinimum.
			if (($normalMinimum = static::getDOMElementAttributeAs($element, 'normalMinimum', 'float')) !== null) {
				$object->setNormalMinimum($normalMinimum);
			}
			
			// deal with matseryValue.
			if (($masteryValue = static::getDOMElementAttributeAs($element, 'masteryValue', 'float')) !== null) {
				$object->setMasteryValue($masteryValue);
			}
			
			// deal with lookupTable.
			$interpolationTables = $element->getElementsByTagName('interpolationTable');
			$matchTable = $element->getElementsByTagName('matchTable');
			
			if ($interpolationTables->length == 1 || $matchTable->length == 1) {
				// we have a lookupTable defined.
				$lookupTable = null;
				
				if ($interpolationTables->length == 1) {
					$lookupTable = $interpolationTables->item(0);
				}
				else {
					$lookupTable = $matchTable->item(0);
				}
				
				$lookupTableMarshaller = $this->getMarshallerFactory()->createMarshaller($lookupTable, array($object->getBaseType()));
				$object->setLookupTable($lookupTableMarshaller->unmarshall($lookupTable));
			}
			
			return $object;
		}
		catch (InvalidArgumentException $e) {
			$msg = "An unexpected error occured while unmarshalling the outcomeDeclaration.";
			throw new UnmarshallingException($msg, $element, $e);
		}
	}
	
	public function getExpectedQtiClassName() {
		return 'outcomeDeclaration';
	}
}
