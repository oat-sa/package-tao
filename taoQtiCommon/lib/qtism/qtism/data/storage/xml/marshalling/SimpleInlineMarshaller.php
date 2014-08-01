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

use qtism\data\content\xhtml\A;
use qtism\data\content\xhtml\text\Q;
use qtism\data\content\InlineCollection;
use qtism\data\QtiComponentCollection;
use qtism\data\QtiComponent;
use \DOMElement;
use \InvalidArgumentException;

/**
 * The Marshaller implementation for SimpleInline elements of the content model.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class SimpleInlineMarshaller extends ContentMarshaller {
    
    protected function unmarshallChildrenKnown(DOMElement $element, QtiComponentCollection $children) {
        
        $fqClass = $this->lookupClass($element);
                
        if ($element->localName === 'a')  {
            
            if (($href = self::getDOMElementAttributeAs($element, 'href')) !== null) {
                $component = new $fqClass($href);
                
                if (($xmlBase = self::getXmlBase($element)) !== false) {
                    $component->setXmlBase($xmlBase);
                }
                
                if (($type = self::getDOMElementAttributeAs($element, 'type')) !== null) {
                    $component->setType($type);
                }
            }
            else {
                $msg = "The mandatory 'href' attribute of the 'a' element is missing.";
                throw new UnmarshallingException($msg, $element);
            }
            
        }
        else {
            $component = new $fqClass();
        }
        
        $component->setContent(new InlineCollection($children->getArrayCopy()));
        self::fillBodyElement($component, $element);
        
        // The q class has a specific cite (URI) attribute.
        if ($component instanceof Q && ($cite = self::getDOMElementAttributeAs($element, 'cite')) !== null) {
            
            try {
                $component->setCite($cite);
            }
            catch (InvalidArgumentException $e) {
                $msg = "The 'cite' attribute of a 'q' element must be a valid URI, '" . $cite . "' given.";
                throw new UnmarshallingException($msg, $element, $e);
            }
        }
        
        return $component;
    }
    
    protected function marshallChildrenKnown(QtiComponent $component, array $elements) {
        
        $element = self::getDOMCradle()->createElement($component->getQtiClassName());
        self::fillElement($element, $component);
        
        if ($component->hasXmlBase() === true) {
            self::setXmlBase($element, $component->getXmlBase());
        }
        
        if ($element->localName === 'a') {
            $element->setAttribute('href', $component->getHref());
            
            if (($type = $component->getType()) !== '') {
                $element->setAttribute('type', $type);
            }
        }
        else if ($element->localName === 'q' && ($cite = $component->getCite()) !== '') {
            $element->setAttribute('cite', $cite);
        }
        
        foreach ($elements as $e) {
            $element->appendChild($e);
        }
        
        return $element;
    }
    
    protected function setLookupClasses() {
        $this->lookupClasses = array("qtism\\data\\content\\xhtml",
                                      "qtism\\data\\content\\xhtml\\text",
                                      "qtism\\data\\content\\xhtml\\presentation");
    }
}