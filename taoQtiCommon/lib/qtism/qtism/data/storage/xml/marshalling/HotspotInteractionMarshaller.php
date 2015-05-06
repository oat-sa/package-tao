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

use qtism\data\content\interactions\HotspotChoiceCollection;
use qtism\data\QtiComponentCollection;
use qtism\data\QtiComponent;
use \DOMElement;
use \InvalidArgumentException;

/**
 * The Marshaller implementation for HotspotInteraction elements of the content model.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class HotspotInteractionMarshaller extends ContentMarshaller {
    
    protected function unmarshallChildrenKnown(DOMElement $element, QtiComponentCollection $children) {
            
        if (($responseIdentifier = self::getDOMElementAttributeAs($element, 'responseIdentifier')) !== null) {
            
            $objectElts = self::getChildElementsByTagName($element, 'object');
            if (count($objectElts) > 0) {
                
                $object = $this->getMarshallerFactory()->createMarshaller($objectElts[0])->unmarshall($objectElts[0]);
                
                if (($maxChoices = self::getDOMElementAttributeAs($element, 'maxChoices', 'integer')) !== null) {
                
                    try {
                        $choices = new HotspotChoiceCollection($children->getArrayCopy());
                    }
                    catch (InvalidArgumentException $e) {
                        $msg = "An 'hotspotInteraction' element can only contain 'hotspotChoice' choices.";
                        throw new UnmarshallingException($msg, $element, $e); 
                    }
                    
                    if (count($choices) > 0) {
                        
                        $fqClass = $this->lookupClass($element);
                        $component = new $fqClass($responseIdentifier, $object, $maxChoices, $choices);
                        
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
                        $msg = "An 'hotspotInteraction' element must contain at least one 'hotspotChoice' element, none given";
                        throw new UnmarshallingException($msg, $element);
                    }
                }
                else {
                    $msg = "The mandatory 'maxChoices' attribute is missing from the 'hotspotInteraction' element.";
                    throw new UnmarshallingException($msg, $element);
                }
                
            }
            else {
                $msg = "A 'hotspotInteraction' element must contain exactly one 'object' element, none given.";
                throw new UnmarshallingException($msg, $element);
            }
        }
        else {
            $msg = "The mandatory 'responseIdentifier' attribute is missing from the '" . $element->localName . "' element.";
            throw new UnmarshallingException($msg, $element);
        }
    }
    
    protected function marshallChildrenKnown(QtiComponent $component, array $elements) {
        
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
        
        foreach ($elements as $e) {
            $element->appendChild($e);
        }
        
        return $element;
    }
    
    protected function setLookupClasses() {
        $this->lookupClasses = array("qtism\\data\\content\\interactions");
    }
}