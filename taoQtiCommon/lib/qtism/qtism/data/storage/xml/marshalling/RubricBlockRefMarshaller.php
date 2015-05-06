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

use qtism\data\content\RubricBlockRef;
use qtism\data\QtiComponent;
use \DOMElement;

/**
 * Marshalling implementation for rubricBlockRef extended QTI class.
 * 
 * @author jerome
 *
 */
class RubricBlockRefMarshaller extends Marshaller {
    
    /**
     * Marshall a RubricBlockRef object to its XML counterpart.
     * 
     * @return DOMElement
     */
    public function marshall(QtiComponent $component) {
        $element = self::getDOMCradle()->createElement('rubricBlockRef');
        self::setDOMElementAttribute($element, 'identifier', $component->getIdentifier());
        self::setDOMElementAttribute($element, 'href', $component->getHref());
        
        return $element;
    }
    
    /**
     * Unmarshall a DOMElement to its RubricBlockRef data model representation.
     * 
     * @return QtiComponent A RubricBlockRef object.
     * @throws UnmarshallingException If the 'identifier' or 'href' attribute is missing from the XML definition.
     */
    public function unmarshall(DOMElement $element) {
        if (($identifier = self::getDOMElementAttributeAs($element, 'identifier')) !== null) {
            
            if (($href = self::getDOMElementAttributeAs($element, 'href')) !== null) {
                return new RubricBlockRef($identifier, $href);
            }
            else {
                $msg = "The mandatory 'href' attribute is missing from element 'rubricBlockRef'.";
                throw new UnmarshallingException($msg, $element);
            }
        }
        else {
            $msg = "The mandatory 'identifier' attribute is missing from element 'rubricBlockRef'.";
            throw new UnmarshallingException($msg, $element);
        }
    }
    
    public function getExpectedQtiClassName() {
        return 'rubricBlockRef';
    }
}