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


use qtism\data\content\xhtml\tables\Col;
use qtism\data\QtiComponent;
use \DOMElement;

/**
 * Marshalling/Unmarshalling implementation for Col.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class ColMarshaller extends Marshaller {
	
	/**
	 * Marshall a Col object into a DOMElement object.
	 * 
	 * @param QtiComponent $component A Col object.
	 * @return DOMElement The according DOMElement object.
	 * @throws MarshallingException
	 */
	protected function marshall(QtiComponent $component) {
        $element = self::getDOMCradle()->createElement('col');
        
        if ($component->getSpan() !== 1) {
            self::setDOMElementAttribute($element, 'span', $component->getSpan());
        }
        
        self::fillElement($element, $component);
        return $element;
	}
	
	/**
	 * Unmarshall a DOMElement object corresponding to an XHTML col element.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return QtiComponent A Col object.
	 * @throws UnmarshallingException
	 */
	protected function unmarshall(DOMElement $element) {
		$component = new Col();
		
		if (($span = self::getDOMElementAttributeAs($element, 'span', 'integer')) !== null) {
		    $component->setSpan($span);
		}
		
		self::fillBodyElement($component, $element);
		return $component;
	}
	
	public function getExpectedQtiClassName() {
		return 'col';
	}
}
