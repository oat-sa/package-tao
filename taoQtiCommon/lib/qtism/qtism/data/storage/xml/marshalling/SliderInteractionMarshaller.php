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

use qtism\data\content\interactions\SliderInteraction;
use qtism\data\content\interactions\Orientation;
use qtism\data\QtiComponent;
use \InvalidArgumentException;
use \DOMElement;

/**
 * Marshalling/Unmarshalling implementation for SliderInteraction.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class SliderInteractionMarshaller extends Marshaller {
	
	/**
	 * Marshall a SliderInteraction object into a DOMElement object.
	 * 
	 * @param QtiComponent $component A SliderInteraction object.
	 * @return DOMElement The according DOMElement object.
	 * @throws MarshallingException
	 */
	protected function marshall(QtiComponent $component) {
	    
        $element = self::getDOMCradle()->createElement('sliderInteraction');
        self::fillElement($element, $component);
        self::setDOMElementAttribute($element, 'responseIdentifier', $component->getResponseIdentifier());
        self::setDOMElementAttribute($element, 'lowerBound', $component->getLowerBound());
        self::setDOMElementAttribute($element, 'upperBound', $component->getUpperBound());
        
        if ($component->hasStep() === true) {
            self::setDOMElementAttribute($element, 'step', $component->getStep());
        }
        
        if ($component->mustStepLabel() === true) {
            self::setDOMElementAttribute($element, 'stepLabel', true);
        }
        
        if ($component->getOrientation() === Orientation::VERTICAL) {
            self::setDOMElementAttribute($element, 'orientation', Orientation::getNameByConstant(Orientation::VERTICAL));
        }
        
        if ($component->mustReverse() === true) {
            self::setDOMElementAttribute($element, 'reverse', true);
        }
        
        if ($component->hasXmlBase() === true) {
            self::setXmlBase($element, $component->getXmlBase());
        }
        
        if ($component->hasPrompt() === true) {
            $element->appendChild($this->getMarshallerFactory()->createMarshaller($component->getPrompt())->marshall($component->getPrompt()));
        }
        
        return $element;
	}
	
	/**
	 * Unmarshall a DOMElement object corresponding to a SliderInteraction element.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return QtiComponent A SliderInteraction object.
	 * @throws UnmarshallingException
	 */
	protected function unmarshall(DOMElement $element) {
	    
	    if (($responseIdentifier = self::getDOMElementAttributeAs($element, 'responseIdentifier')) !== null) {
            
	        if (($lowerBound = self::getDOMElementAttributeAs($element, 'lowerBound', 'float')) !== null) {
	            
	            if (($upperBound = self::getDOMElementAttributeAs($element, 'upperBound', 'float')) !== null) {
	                
	                $component = new SliderInteraction($responseIdentifier, $lowerBound, $upperBound);
	                
	                $promptElts = self::getChildElementsByTagName($element, 'prompt');
	                if (count($promptElts) > 0) {
	                    $promptElt = $promptElts[0];
	                    $prompt = $this->getMarshallerFactory()->createMarshaller($promptElt)->unmarshall($promptElt);
	                    $component->setPrompt($prompt);
	                }
	                
	                if (($step = self::getDOMElementAttributeAs($element, 'step', 'integer')) !== null) {
	                    $component->setStep($step);
	                }
	                
	                if (($stepLabel = self::getDOMElementAttributeAs($element, 'stepLabel', 'boolean')) !== null) {
	                    $component->setStepLabel($stepLabel);
	                }
	                
	                if (($orientation = self::getDOMElementAttributeAs($element, 'orientation')) !== null) {
	                    try {
	                        $component->setOrientation(Orientation::getConstantByName($orientation));
	                    }
	                    catch (InvalidArgumentException $e) {
	                        $msg = "The value of the 'orientation' attribute of the 'sliderInteraction' is invalid.";
	                        throw new UnmarshallingException($msg, $element, $e);
	                    }
	                }
	                
	                if (($reverse = self::getDOMElementAttributeAs($element, 'reverse', 'boolean')) !== null) {
	                    $component->setReverse($reverse);
	                }
	                
	                if (($xmlBase = self::getXmlBase($element)) !== false) {
	                    $component->setXmlBase($xmlBase);
	                }
	                
	                self::fillBodyElement($component, $element);
	                return $component;
	            }
	            else {
	                $msg = "The mandatory 'upperBound' attribute is missing from the 'sliderInteraction' element.";
	                throw new UnmarshallingException($msg, $element);
	            }
	        }
	        else {
	            $msg = "The mandatory 'lowerBound' attribute is missing from the 'sliderInteraction' element.";
	            throw new UnmarshallingException($msg, $element);
	        }
        }
        else {
            $msg = "The mandatory 'responseIdentifier' attribute is missing from the 'sliderInteraction' element.";
            throw new UnmarshallingException($msg, $element);
        }
	}
	
	public function getExpectedQtiClassName() {
		return 'sliderInteraction';
	}
}
