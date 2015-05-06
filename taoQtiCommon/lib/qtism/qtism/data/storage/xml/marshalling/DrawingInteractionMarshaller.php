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

use qtism\data\content\interactions\DrawingInteraction;
use qtism\data\QtiComponent;
use \InvalidArgumentException;
use \DOMElement;

/**
 * Marshalling/Unmarshalling implementation for DrawingInteraction.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class DrawingInteractionMarshaller extends Marshaller {
	
	/**
	 * Marshall a DrawingInteraction object into a DOMElement object.
	 * 
	 * @param QtiComponent $component A DrawingInteraction object.
	 * @return DOMElement The according DOMElement object.
	 * @throws MarshallingException
	 */
	protected function marshall(QtiComponent $component) {
        $element = self::getDOMCradle()->createElement('drawingInteraction');
        self::fillElement($element, $component);
        self::setDOMElementAttribute($element, 'responseIdentifier', $component->getResponseIdentifier());
        
        if ($component->hasXmlBase() === true) {
            self::setXmlBase($element, $component->getXmlBase());
        }
        
        if ($component->hasPrompt() === true) {
            $element->appendChild($this->getMarshallerFactory()->createMarshaller($component->getPrompt())->marshall($component->getPrompt()));
        }
        
        $element->appendChild($this->getMarshallerFactory()->createMarshaller($component->getObject())->marshall($component->getObject()));
        
        return $element;
	}
	
	/**
	 * Unmarshall a DOMElement object corresponding to a DrawingInteraction element.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return QtiComponent A DrawingInteraction object.
	 * @throws UnmarshallingException
	 */
	protected function unmarshall(DOMElement $element) {
	    
	    if (($responseIdentifier = self::getDOMElementAttributeAs($element, 'responseIdentifier')) !== null) {
            
            $objectElts = self::getChildElementsByTagName($element, 'object');
            if (count($objectElts) > 0) {
                $objectElt = $objectElts[0];
                $object = $this->getMarshallerFactory()->createMarshaller($objectElt)->unmarshall($objectElt);
                
                $component = new DrawingInteraction($responseIdentifier, $object);
                
                $promptElts = self::getChildElementsByTagName($element, 'prompt');
                if (count($promptElts) > 0) {
                    $promptElt = $promptElts[0];
                    $prompt = $this->getMarshallerFactory()->createMarshaller($promptElt)->unmarshall($promptElt);
                    $component->setPrompt($prompt);
                }
                
                if (($xmlBase = self::getXmlBase($element)) !== false) {
                    $component->setXmlBase($xmlBase);
                }
                
                self::fillBodyElement($component, $element);
                return $component;
            }
            else {
                $msg = "A 'drawingInteraction' element must contain exactly one 'object' element, none given.";
                throw new UnmarshallingException($msg, $element);
            }   
        }
        else {
            $msg = "The mandatory 'responseIdentifier' attribute is missing from the 'drawingInteraction' element.";
            throw new UnmarshallingException($msg, $element);
        }
	}
	
	public function getExpectedQtiClassName() {
		return 'drawingInteraction';
	}
}
