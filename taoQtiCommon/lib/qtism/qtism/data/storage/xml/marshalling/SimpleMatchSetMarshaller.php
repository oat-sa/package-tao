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

use qtism\data\content\interactions\SimpleAssociableChoiceCollection;
use qtism\data\QtiComponentCollection;
use qtism\data\QtiComponent;
use \DOMElement;
use \InvalidArgumentException;

/**
 * The Marshaller implementation for SimpleMatchSet elements of the content model.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class SimpleMatchSetMarshaller extends ContentMarshaller {
    
    protected function unmarshallChildrenKnown(DOMElement $element, QtiComponentCollection $children) {
        
        $fqClass = $this->lookupClass($element);
        $component = new $fqClass(new SimpleAssociableChoiceCollection($children->getArrayCopy()));
        
        return $component;
    }
    
    protected function marshallChildrenKnown(QtiComponent $component, array $elements) {
        
        $element = self::getDOMCradle()->createElement($component->getQtiClassName());
        
        foreach ($elements as $e) {
            $element->appendChild($e);
        }
        
        return $element;
    }
    
    protected function setLookupClasses() {
        $this->lookupClasses = array("qtism\\data\\content\\interactions");
    }
}