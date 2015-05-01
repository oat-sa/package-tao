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

use qtism\data\content\interactions\TextFormat;
use qtism\data\content\interactions\ExtendedTextInteraction;
use qtism\data\content\interactions\TextEntryInteraction;
use qtism\data\QtiComponent;
use \InvalidArgumentException;
use \DOMElement;

/**
 * Marshalling/Unmarshalling implementation for TextEntryInteraction/ExtendedTextInteraction.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class TextInteractionMarshaller extends Marshaller {
	
	/**
	 * Marshall a TextEntryInteraction/ExtendedTextInteraction object into a DOMElement object.
	 * 
	 * @param QtiComponent $component A TextEntryInteraction/ExtendedTextInteraction object.
	 * @return DOMElement The according DOMElement object.
	 * @throws MarshallingException
	 */
	protected function marshall(QtiComponent $component) {
        $element = self::getDOMCradle()->createElement($component->getQtiClassName());
        
        self::setDOMElementAttribute($element, 'responseIdentifier', $component->getResponseIdentifier());
        
        if ($component->getBase() !== 10) {
            self::setDOMElementAttribute($element, 'base', $component->getBase());
        }
        
        if ($component->hasStringIdentifier() === true) {
            self::setDOMElementAttribute($element, 'stringIdentifier', $component->getStringIdentifier());
        }
        
        if ($component->hasExpectedLength() === true) {
            self::setDOMElementAttribute($element, 'expectedLength', $component->getExpectedLength());
        }
        
        if ($component->hasPatternMask() === true) {
            self::setDOMElementAttribute($element, 'patternMask', $component->getPatternMask());
        }
        
        if ($component->hasPlaceholderText() === true) {
            self::setDOMElementAttribute($element, 'placeholderText', $component->getPlaceholderText());
        }
        
        if ($component->hasXmlBase() === true) {
            self::setXmlBase($element, $component->setXmlBase());
        }
        
        if ($element->localName === 'extendedTextInteraction') {
            if ($component->hasMaxStrings() === true) {
                self::setDOMElementAttribute($element, 'maxStrings', $component->getMaxStrings());
            }
            
            if ($component->getMinStrings() !== 0) {
                self::setDOMElementAttribute($element, 'minStrings', $component->getMinStrings());
            }
            
            if ($component->hasExpectedLines() === true) {
                self::setDOMElementAttribute($element, 'expectedLines', $component->getExpectedLines());
            }
            
            if ($component->getFormat() !== TextFormat::PLAIN) {
                self::setDOMElementAttribute($element, 'format', TextFormat::getNameByConstant($component->getFormat()));
            }
            
            if ($component->hasPrompt() === true) {
                $element->appendChild($this->getMarshallerFactory()->createMarshaller($component->getPrompt())->marshall($component->getPrompt()));
            }
        }
        
        self::fillElement($element, $component);
        return $element;
	}
	
	/**
	 * Unmarshall a DOMElement object corresponding to a textEntryInteraction/extendedTextInteraction element.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return QtiComponent A TextEntryInteraction/ExtendedTextInteraction object.
	 * @throws UnmarshallingException
	 */
	protected function unmarshall(DOMElement $element) {
	    
	    if (($responseIdentifier = self::getDOMElementAttributeAs($element, 'responseIdentifier')) !== null) {
            
            try {
                $class = 'qtism\\data\\content\\interactions\\' . ucfirst($element->localName);
                $component = new $class($responseIdentifier);
            }
            catch (InvalidArgumentException $e) {
                $msg = "The value '${responseIdentifier}' of the 'responseIdentifier' attribute of the '" . $element->localName . "' element is not a valid identifier.";
                throw new UnmarshallingException($msg, $element, $e);
            }
            
            if (($base = self::getDOMElementAttributeAs($element, 'base', 'integer')) !== null) {
                $component->setBase($base);
            }
            
            if (($stringIdentifier = self::getDOMElementAttributeAs($element, 'stringIdentifier')) !== null) {
                $component->setStringIdentifier($stringIdentifier);
            }
            
            if (($expectedLength = self::getDOMElementAttributeAs($element, 'expectedLength', 'integer')) !== null) {
                $component->setExpectedLength($expectedLength);
            }
            
            if (($patternMask = self::getDOMElementAttributeAs($element, 'patternMask')) !== null) {
                $component->setPatternMask($patternMask);
            }
            
            if (($placeholderText = self::getDOMElementAttributeAs($element, 'placeholderText')) !== null) {
                $component->setPlaceholderText($placeholderText);
            }
            
            if (($xmlBase = self::getXmlBase($element)) !== false) {
                $component->setXmlBase($xmlBase);
            }
            
            if ($element->localName === 'extendedTextInteraction') {
                
                if (($maxStrings = self::getDOMElementAttributeAs($element, 'maxStrings', 'integer')) !== null) {
                    $component->setMaxStrings($maxStrings);
                }
                
                if (($minStrings = self::getDOMElementAttributeAs($element, 'minStrings', 'integer')) !== null) {
                    $component->setMinStrings($minStrings);
                }
                
                if (($expectedLines = self::getDOMElementAttributeAs($element, 'expectedLines', 'integer')) !== null) {
                    $component->setExpectedLines($expectedLines);
                }
                
                if (($format = self::getDOMElementAttributeAs($element, 'format')) !== null) {
                    $component->setFormat(TextFormat::getConstantByName($format));
                }
                
                $promptElts = self::getChildElementsByTagName($element, 'prompt');
                if (count($promptElts) > 0) {
                    $component->setPrompt($this->getMarshallerFactory()->createMarshaller($promptElts[0])->unmarshall($promptElts[0]));
                }
            }
             
            self::fillBodyElement($component, $element);
		    return $component;
        }
        else {
            $msg = "The mandatory 'responseIdentifier' attribute is missing from the '" . $element->localName . "' element.";
            throw new UnmarshallingException($msg, $element);
        }
	}
	
	public function getExpectedQtiClassName() {
		return '';
	}
}
