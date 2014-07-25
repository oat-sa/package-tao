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

use qtism\data\content\ObjectFlowCollection;
use qtism\data\QtiComponentCollection;
use qtism\data\QtiComponent;
use \DOMElement;

/**
 * The Marshaller implementation for object elements of the content model.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class ObjectMarshaller extends ContentMarshaller {
    
    protected function unmarshallChildrenKnown(DOMElement $element, QtiComponentCollection $children) {
        
        // At item authoring time, we could admit that an empty data attribute
        // may occur.
        if (($data = self::getDOMElementAttributeAs($element, 'data')) === null) {
            $data = '';
        }
        
        if (($type = self::getDOMElementAttributeAs($element, 'type')) !== null) {
        
            $fqClass = $this->lookupClass($element);
            $component = new $fqClass($data, $type);
            $component->setContent(new ObjectFlowCollection($children->getArrayCopy()));
        
            if (($width = self::getDOMElementAttributeAs($element, 'width', 'integer')) !== null) {
                $component->setWidth($width);
            }
        
            if (($height = self::getDOMElementAttributeAs($element, 'height', 'integer')) !== null) {
                $component->setHeight($height);
            }
        
            if (($xmlBase = self::getXmlBase($element)) !== false) {
                $component->setXmlBase($xmlBase);
            }
        
            self::fillBodyElement($component, $element);
        
            return $component;
        }
        else {
            $msg = "The mandatory attribute 'type' is missign from the 'object' element.";
            throw new UnmarshallingException($msg, $element);
        }
    }
    
    protected function marshallChildrenKnown(QtiComponent $component, array $elements) {
        
        $element = self::getDOMCradle()->createElement($component->getQtiClassName());
        self::setDOMElementAttribute($element, 'data', $component->getData());
        self::setDOMElementAttribute($element, 'type', $component->getType());
        
        if ($component->hasWidth() === true) {
            self::setDOMElementAttribute($element, 'width', $component->getWidth());
        }
        
        if ($component->hasHeight() === true) {
            self::setDOMElementAttribute($element, 'height', $component->getHeight());
        }
        
        if ($component->hasXmlBase() === true) {
            self::setXmlBase($element, $component->getXmlBase());
        }
        
        foreach ($elements as $e) {
            $element->appendChild($e);
        }
        
        self::fillElement($element, $component);
        return $element;
    }
    
    protected function setLookupClasses() {
        $this->lookupClasses = array("qtism\\data\\content\\xhtml");
    }
}