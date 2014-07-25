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

use qtism\data\content\BlockStaticCollection;
use qtism\data\content\interactions\GapChoiceCollection;
use qtism\data\QtiComponentCollection;
use qtism\data\QtiComponent;
use \DOMElement;
use \InvalidArgumentException;

/**
 * The Marshaller implementation for GapMatchInteraction elements of the content model.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class GapMatchInteractionMarshaller extends ContentMarshaller {
    
    protected function unmarshallChildrenKnown(DOMElement $element, QtiComponentCollection $children) {
        
        if (($responseIdentifier = self::getDOMElementAttributeAs($element, 'responseIdentifier')) !== null) {
            
            $gapChoiceElts = self::getChildElementsByTagName($element, array('gapText', 'gapImg'));
            if (count($gapChoiceElts) > 0) {

                $gapChoices = new GapChoiceCollection();
                foreach ($gapChoiceElts as $g) {
                    $gapChoices[] = $this->getMarshallerFactory()->createMarshaller($g)->unmarshall($g);
                }
                
                $fqClass = $this->lookupClass($element);
                $component = new $fqClass($responseIdentifier, $gapChoices, new BlockStaticCollection($children->getArrayCopy()));
                
                $promptElts = self::getChildElementsByTagName($element, 'prompt');
                if (count($promptElts) === 1) {
                    $component->setPrompt($this->getMarshallerFactory()->createMarshaller($promptElts[0])->unmarshall($promptElts[0]));
                }
                
                if (($shuffle = self::getDOMElementAttributeAs($element, 'shuffle', 'boolean')) !== null) {
                    $component->setShuffle($shuffle);
                }
                
                if (($xmlBase = self::getXmlBase($element)) !== false) {
                    $component->setXmlBase($xmlBase);
                }
                
                self::fillBodyElement($component, $element);
                return $component;
            }
            else {
                $msg = "A 'gapMatchInteraction' element must contain at least 1 'gapChoice' element, none given.";
                throw new UnmarshallingException($msg, $element);
            }
        }
        else {
            $msg = "The mandatory 'responseIdentifier' attribute is missing from the 'gapMatchInteraction' element.";
            throw new UnmarshallingException($msg, $element);
        }
        
        
    }
    
    protected function marshallChildrenKnown(QtiComponent $component, array $elements) {
        
        $element = self::getDOMCradle()->createElement($component->getQtiClassName());
        self::fillElement($element, $component);
        self::setDOMElementAttribute($element, 'responseIdentifier', $component->getResponseIdentifier());
        
        if ($component->mustShuffle() === true) {
            self::setDOMElementAttribute($element, 'shuffle', true);
        }
        
        if ($component->hasXmlBase() === true) {
            self::setXmlBase($element, $component->getXmlBase());
        }
        
        if ($component->hasPrompt() === true) {
            $element->appendChild($this->getMarshallerFactory()->createMarshaller($component->getPrompt())->marshall($component->getPrompt()));
        }
        
        foreach ($component->getGapChoices() as $g) {
            $element->appendChild($this->getMarshallerFactory()->createMarshaller($g)->marshall($g));
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