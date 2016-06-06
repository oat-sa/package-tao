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

use qtism\data\content\interactions\SelectPointInteraction;
use qtism\data\QtiComponent;
use \DOMElement;

/**
 * Marshalling/Unmarshalling implementation for SelectPointInteraction.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class SelectPointInteractionMarshaller extends Marshaller {
	
	/**
	 * Marshall a SelectPointInteraction object into a DOMElement object.
	 * 
	 * @param QtiComponent $component A SelectPointInteraction object.
	 * @return DOMElement The according DOMElement object.
	 * @throws MarshallingException
	 */
	protected function marshall(QtiComponent $component) {
	    
        $element = self::getDOMCradle()->createElement($component->getQtiClassName());
        self::fillElement($element, $component);
        self::setDOMElementAttribute($element, 'responseIdentifier', $component->getResponseIdentifier());
        self::setDOMElementAttribute($element, 'maxChoices', $component->getMaxChoices());
        
        if ($component->hasPrompt() === true) {
            $element->appendChild($this->getMarshallerFactory()->createMarshaller($component->getPrompt())->marshall($component->getPrompt()));
        }
        
        $element->appendChild($this->getMarshallerFactory()->createMarshaller($component->getObject())->marshall($component->getObject()));
        
        if ($component->getMinChoices() !== 0) {
            self::setDOMElementAttribute($element, 'minChoices', $component->getMinChoices());
        }
        
        if ($component->hasXmlBase() === true) {
            self::setXmlBase($element, $component->getXmlBase());
        }
        
        return $element;
	}
	
	/**
	 * Unmarshall a DOMElement object corresponding to a selectPointInteraction element.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return QtiComponent A SelectPointInteraction object.
	 * @throws UnmarshallingException
	 */
	protected function unmarshall(DOMElement $element) {
	    
	    if (($responseIdentifier = self::getDOMElementAttributeAs($element, 'responseIdentifier')) !== null) {
            
            $objectElts = self::getChildElementsByTagName($element, 'object');
            if (count($objectElts) > 0) {
                
                $object = $this->getMarshallerFactory()->createMarshaller($objectElts[0])->unmarshall($objectElts[0]);
                
                if (($maxChoices = self::getDOMElementAttributeAs($element, 'maxChoices', 'integer')) !== null) {

                    $component = new SelectPointInteraction($responseIdentifier, $object, $maxChoices);
                    
                    if (($minChoices = self::getDOMElementAttributeAs($element, 'minChoices', 'integer')) !== null) {
                        $component->setMinChoices($minChoices);
                    }
                    
                    if (($xmlBase = self::getXmlBase($element)) !== false) {
                        $component->setXmlBase($xmlBase);
                    }
                    
                    $promptElts = self::getChildElementsByTagName($element, 'prompt');
                    if (count($promptElts) > 0) {
                        $promptElt = $promptElts[0];
                        $prompt = $this->getMarshallerFactory()->createMarshaller($promptElt)->unmarshall($promptElt);
                        $component->setPrompt($prompt);
                    }
                    
                    self::fillBodyElement($component, $element);
                    return $component;
                }
                else {
                    $msg = "The mandatory 'maxChoices' attribute is missing from the 'selectPointInteraction' element.";
                    throw new UnmarshallingException($msg, $element);
                }
                
            }
            else {
                $msg = "A 'selectPointInteraction' element must contain exactly one 'object' element, none given.";
                throw new UnmarshallingException($msg, $element);
            }
        }
        else {
            $msg = "The mandatory 'responseIdentifier' attribute is missing from the 'selectPointInteraction' element.";
            throw new UnmarshallingException($msg, $element);
        }
	}
	
	public function getExpectedQtiClassName() {
		return 'selectPointInteraction';
	}
}
