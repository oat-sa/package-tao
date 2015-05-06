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

use qtism\data\content\InlineCollection;
use qtism\data\content\xhtml\lists\Dt;
use qtism\data\content\FlowCollection;
use qtism\data\QtiComponentCollection;
use qtism\data\QtiComponent;
use \DOMElement;

/**
 * The Marshaller implementation for dlElement elements of the content model.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class DlElementMarshaller extends ContentMarshaller {
    
    protected function unmarshallChildrenKnown(DOMElement $element, QtiComponentCollection $children) {
        
        $fqClass = $this->lookupClass($element);
        $component = new $fqClass();
        $childrenArray = $children->getArrayCopy();
        $component->setContent(($component instanceof Dt) ? new InlineCollection($childrenArray) : new FlowCollection($childrenArray));
        
        self::fillBodyElement($component, $element);
        return $component;
    }
    
    protected function marshallChildrenKnown(QtiComponent $component, array $elements) {
        
        $element = self::getDOMCradle()->createElement($component->getQtiClassName());
        
        foreach ($elements as $elt) {
            $element->appendChild($elt);
        }
        
        self::fillElement($element, $component);
        return $element;
    }
    
    protected function setLookupClasses() {
        $this->lookupClasses = array("qtism\\data\\content\\xhtml\\lists");
    }
}