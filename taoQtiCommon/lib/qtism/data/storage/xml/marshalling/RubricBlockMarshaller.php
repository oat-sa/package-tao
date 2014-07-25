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

use qtism\data\StylesheetCollection;

use qtism\data\QtiComponent;
use qtism\data\RubricBlock;
use qtism\data\ViewCollection;
use qtism\data\View;
use \DOMElement;

/**
 * Marshalling/Unmarshalling implementation for rubrickBlock.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class RubricBlockMarshaller extends Marshaller {
	
	/**
	 * Marshall a RubricBlock object into a DOMElement object.
	 * 
	 * @param QtiComponent $component A RubricBlock object.
	 * @return DOMElement The according DOMElement object.
	 */
	protected function marshall(QtiComponent $component) {
		$element = static::getDOMCradle()->createElement($component->getQtiClassName());
		
		$arrayViews = array();
		foreach ($component->getViews() as $view) {
			$key = array_search($view, View::asArray());
			// replace '_' by the space char.
			$arrayViews[] = strtolower(str_replace("\xf2", "\x20", $key));
		}
		
		if (count($arrayViews) > 0) {
			static::setDOMElementAttribute($element, 'view', implode("\x20", $arrayViews));
		}
		
		if ($component->getUse() != '') {
			static::setDOMElementAttribute($element, 'use', $component->getUse());
		}
		
		foreach ($component->getStylesheets() as $stylesheet) {
			$stylesheetMarshaller = $this->getMarshallerFactory()->createMarshaller($stylesheet);
			$element->appendChild($stylesheetMarshaller->marshall($stylesheet));
		}
		
		// A RubricBlock contains... blocks !
		if ($component->getContent() !== '') {
			$blocks = static::getDOMCradle()->createDocumentFragment();
			$blocks->appendXML($component->getContent());
			$element->appendChild($blocks);
		}
		
		return $element;
	}
	
	/**
	 * Unmarshall a DOMElement object corresponding to a QTI rubrickBlock element.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return RubricBlock A RubricBlock object.
	 * @throws UnmarshallingException If the mandatory attribute 'href' is missing from $element.
	 */
	protected function unmarshall(DOMElement $element) {
		
		// First we retrieve the mandatory views.
		if (($value = static::getDOMElementAttributeAs($element, 'view', 'string')) !== null) {
			$viewsArray = explode("\x20", $value);
			$viewsCollection = new ViewCollection();
			$ref = View::asArray();
			
			foreach ($viewsArray as $viewString) {
				$key = strtoupper(str_replace("\xf2", "\x20", $viewString));
				if (array_key_exists($key, $ref)) {
					$viewsCollection[] = $ref[$key];
				}
			}
			
			$object = new RubricBlock($viewsCollection);
			
			if (($value = static::getDOMElementAttributeAs($element, 'use', 'string')) !== null) {
				$object->setUse($value);
			}
			
			$stylesheetElements = $element->getElementsByTagName('stylesheet');
			$stylesheetCollection = new StylesheetCollection();
			for ($i = 0; $i < $stylesheetElements->length; $i++) {
				$marshaller = $this->getMarshallerFactory()->createMarshaller($stylesheetElements->item($i));
				$stylesheetCollection[] = $marshaller->unmarshall($stylesheetElements->item($i));
			}
			$object->setStylesheets($stylesheetCollection);
			
			$content = self::extractContent($element);
			if (!empty($content)) {
				$object->setContent($content);
			}
		}
		else {
			$msg = "The mandatory attribute 'views' is missing.";
			throw new UnmarshallingException($msg, $element);
		}
		
		return $object;
	}
	
	public function getExpectedQtiClassName() {
		return 'rubricBlock';
	}
	
	/**
	 * Extract the content (direct children) of a rubricBlock element.
	 *
	 * @param DOMElement $element The rubricBlock element you want to extract the content.
	 * @return string The content of the rubricBlock element as a string. If there is no extractable content, an empty string is returned.
	 * @throws InvalidArgumentException If $element is not a rubricBlock element.
	 */
	protected static function extractContent(DOMElement $element) {
		if ($element->nodeName == 'rubricBlock') {
			$markup = $element->ownerDocument->saveXML($element);
			$innerMarkup = preg_replace('#</{0,1}rubricBlock.*?>#iu', '', $markup);
			return trim(preg_replace('#\s*<\s*stylesheet.*?>\s*#', '', $innerMarkup));
		}
		else {
			throw new InvalidArgumentException("The element must be a QTI rubricBlock, '" . $element->nodeName . "' element given.");
		}
	}
}
