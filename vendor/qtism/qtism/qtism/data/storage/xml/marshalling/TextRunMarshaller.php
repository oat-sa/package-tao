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

use qtism\data\content\TextRun;
use qtism\data\QtiComponent;
use \DOMElement;

/**
 * Marshalling/Unmarshalling implementation for TextRun.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class TextRunMarshaller extends Marshaller {
	
	/**
	 * Marshall a TextRun object into a DOMElement object.
	 * 
	 * @param QtiComponent $component A TextRun object.
	 * @return DOMElement The according DOMElement object.
	 */
	protected function marshall(QtiComponent $component) {
		$element = static::getDOMCradle()->createTextNode($component->getContent());
		return $element;
	}
	
	/**
	 * Unmarshall a DOMElement object corresponding to a QTI textRun element.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return QtiComponent A TextRun object.
	 * @throws UnmarshallingException
	 */
	protected function unmarshall(DOMElement $element) {
		
		$object = new TextRun($element->nodeValue);
		return $object;
	}
	
	public function getExpectedQtiClassName() {
		return 'textRun';
	}
}