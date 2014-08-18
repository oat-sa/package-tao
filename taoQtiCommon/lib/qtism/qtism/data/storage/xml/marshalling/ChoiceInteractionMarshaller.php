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

use qtism\data\content\interactions\OrderInteraction;
use qtism\data\content\interactions\ChoiceInteraction;
use qtism\data\content\interactions\Orientation;
use qtism\data\content\interactions\SimpleChoiceCollection;
use qtism\data\QtiComponentCollection;
use qtism\data\QtiComponent;
use \DOMElement;
use \InvalidArgumentException;

/**
 * The Marshaller implementation for ChoiceInteraction/OrderInteraction elements of the content model.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class ChoiceInteractionMarshaller extends ContentMarshaller {
    
    protected function unmarshallChildrenKnown(DOMElement $element, QtiComponentCollection $children) {
            
            if (($responseIdentifier = self::getDOMElementAttributeAs($element, 'responseIdentifier')) !== null) {
                
                $fqClass = $this->lookupClass($element);
                $component = new $fqClass($responseIdentifier, new SimpleChoiceCollection($children->getArrayCopy()));
                
                if (($shuffle = self::getDOMElementAttributeAs($element, 'shuffle', 'boolean')) !== null) {
                    $component->setShuffle($shuffle);
                }
                
                if (($maxChoices = self::getDOMElementAttributeAs($element, 'maxChoices', 'integer')) !== null) {
                    if ($element->localName === 'orderInteraction') {
                        
                        if ($maxChoices !== 0) {
                            $component->setMaxChoices($maxChoices);
                        }
                    }
                    else {
                        $component->setMaxChoices($maxChoices);
                    }
                }
                
                if (($minChoices = self::getDOMElementAttributeAs($element, 'minChoices', 'integer')) !== null) {
                    if ($element->localName === 'orderInteraction') {
                        /*
                         * Lots of QTI implementations output minChoices = 0 while
                         * dealing with orderInteraction unmarshalling. However, regarding
                         * the IMS QTI Specification, it is invalid.
                         * 
                         * "If specified, minChoices must be 1 or greater but must not exceed the 
                         * number of choices available."
                         * 
                         * See http://www.imsglobal.org/question/qtiv2p1/imsqti_infov2p1.html#element10283
                         */
                        if ($minChoices !== 0) {
                            $component->setMinChoices($minChoices);
                        }
                    }
                    else {
                        $component->setMinChoices($minChoices);
                    }
                }
                
                if (($orientation = self::getDOMElementAttributeAs($element, 'orientation')) !== null) {
                    $component->setOrientation(Orientation::getConstantByName($orientation));
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
                $msg = "The mandatory 'responseIdentifier' attribute is missing from the " . $element->localName . " element.";
                throw new UnmarshallingException($msg, $element);
            }
    }
    
    protected function marshallChildrenKnown(QtiComponent $component, array $elements) {
        
        $element = self::getDOMCradle()->createElement($component->getQtiClassName());
        self::fillElement($element, $component);
        self::setDOMElementAttribute($element, 'responseIdentifier', $component->getResponseIdentifier());
        
        if ($component->hasPrompt() === true) {
            $element->appendChild($this->getMarshallerFactory()->createMarshaller($component->getPrompt())->marshall($component->getPrompt()));
        }
        
        if ($component->mustShuffle() !== false) {
            self::setDOMElementAttribute($element, 'shuffle', true);
        }
        
        if (($component instanceof ChoiceInteraction && $component->getMaxChoices() !== 1) || ($component instanceof OrderInteraction && $component->getMaxChoices() !== -1)) {
            self::setDOMElementAttribute($element, 'maxChoices', $component->getMaxChoices());
        }
        
        if (($component instanceof ChoiceInteraction && $component->getMinChoices() !== 0) || ($component instanceof OrderInteraction && $component->getMinChoices() !== -1)) {
            self::setDOMElementAttribute($element, 'minChoices', $component->getMinChoices());
        }
        
        if ($component->getOrientation() !== Orientation::VERTICAL) {
            self::setDOMElementAttribute($element, 'orientation', Orientation::getNameByConstant(Orientation::HORIZONTAL));
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