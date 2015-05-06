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

use qtism\data\content\FlowCollection;
use qtism\data\content\xhtml\tables\TableCellScope;
use qtism\common\collections\IdentifierCollection;
use qtism\data\content\InlineCollection;
use qtism\data\QtiComponentCollection;
use qtism\data\QtiComponent;
use \DOMElement;
use \InvalidArgumentException;

/**
 * The Marshaller implementation for TableCell elements of the content model.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class TableCellMarshaller extends ContentMarshaller {
    
    protected function unmarshallChildrenKnown(DOMElement $element, QtiComponentCollection $children) {
        
        $fqClass = $this->lookupClass($element);
        $component = new $fqClass();
        
        if (($headers = self::getDOMElementAttributeAs($element, 'headers')) !== null) {
            try {
                $headersCollection = new IdentifierCollection();
                
                foreach (explode("\x20", $headers) as $h) {
                    $headersCollection[] = $h;
                } 
                
                $component->setHeaders($headersCollection);
            }
            catch (InvalidArgumentException $e) {
                $msg = "The 'headers' attribute does not contain valid QTI identifiers.";
                throw new UnmarshallingException($msg, $element);
            }
        }
        
        if (($scope = self::getDOMElementAttributeAs($element, 'scope')) !== null) {
            $component->setScope(TableCellScope::getConstantByName($scope));
        }
        
        if (($abbr = self::getDOMElementAttributeAs($element, 'abbr')) !== null) {
            $component->setAbbr($abbr);
        }
        
        if (($axis = self::getDOMElementAttributeAs($element, 'axis')) !== null) {
            $component->setAxis($axis);
        }
        
        if (($rowspan = self::getDOMElementAttributeAs($element, 'rowspan', 'integer')) !== null) {
            $component->setRowspan($rowspan);
        }
        
        if (($colspan = self::getDOMElementAttributeAs($element, 'colspan', 'integer')) !== null) {
            $component->setColspan($colspan);
        }
        
        try {
            $component->setContent(new FlowCollection($children->getArrayCopy()));
            self::fillBodyElement($component, $element);
            
            return $component;
        }
        catch (InvalidArgumentException $e) {
            $msg = "A '" . $element->localName . "' element can only contain QTI flow elements.";
            throw new UnmarshallingException($msg, $element);
        }
    }
    
    protected function marshallChildrenKnown(QtiComponent $component, array $elements) {
        
        $element = self::getDOMCradle()->createElement($component->getQtiClassName());
        
        $headers = $component->getHeaders();
        if (count($headers) > 0) {
            self::setDOMElementAttribute($element, 'headers', implode("\x20", $headers->getArrayCopy()));
        }
        
        if ($component->hasScope() === true) {
            self::setDOMElementAttribute($element, 'scope', TableCellScope::getNameByConstant($component->getScope()));
        }
        
        if ($component->hasAbbr() === true) {
            self::setDOMElementAttribute($element, 'abbr', $component->getAbbr());
        }
        
        if ($component->hasAxis() === true) {
            self::setDOMElementAttribute($element, 'axis', $component->getAxis());
        }
        
        if ($component->hasRowspan() === true) {
            self::setDOMElementAttribute($element, 'rowspan', $component->getRowspan());
        }
        
        if ($component->hasColspan() === true) {
            self::setDOMElementAttribute($element, 'colspan', $component->getColspan());
        }
        
        foreach ($component->getContent() as $c) {
            $marshaller = $this->getMarshallerFactory()->createMarshaller($c);
            $element->appendChild($marshaller->marshall($c));
        }
        
        self::fillElement($element, $component);
        return $element;
    }
    
    protected function setLookupClasses() {
        $this->lookupClasses = array("qtism\\data\\content\\xhtml\\tables");
    }
}