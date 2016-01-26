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

use qtism\data\QtiComponentCollection;
use qtism\data\content\RubricBlockRefCollection;
use qtism\data\ExtendedAssessmentSection;
use qtism\data\QtiComponent;
use \DOMElement;

/**
 * Marshalling implementation for the ExtendedAssessmentSection class.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class ExtendedAssessmentSectionMarshaller extends AssessmentSectionMarshaller {
    
    /**
     * Marshall an ExtendedAssessmentSection object into its DOMElement representation.
     *
     * @param QtiComponent $component
     * @param array $elements
     * @return DOMElement The according DOMElement object.
     */
    protected function marshallChildrenKnown(QtiComponent $component, array $elements) {
        $element = parent::marshallChildrenKnown($component, $elements);
        
        foreach ($component->getRubricBlockRefs() as $rubricBlockRef) {
            $marshaller = $this->getMarshallerFactory()->createMarshaller($rubricBlockRef);
            $element->appendChild($marshaller->marshall($rubricBlockRef));
        }
        
        return $element;
    }
    
    /**
     * Unmarshall an extendedAssessmentSection element into its QTI data model representation.
     * 
     * @param DOMElement $element
     * @param QtiComponentCollection $children
     * @return QtiComponent
     */
    protected function unmarshallChildrenKnown(DOMElement $element, QtiComponentCollection $children) {
        $baseComponent = parent::unmarshallChildrenKnown($element, $children);
        $component = ExtendedAssessmentSection::createFromAssessmentSection($baseComponent);
        
        $rubricBlockRefElts = self::getChildElementsByTagName($element, 'rubricBlockRef');
        if (count($rubricBlockRefElts) > 0) {
            
            $rubricBlockRefs = new RubricBlockRefCollection();
            
            foreach ($rubricBlockRefElts as $rubricBlockRefElt) {
                $marshaller = $this->getMarshallerFactory()->createMarshaller($rubricBlockRefElt);
                $rubricBlockRefs[] = $marshaller->unmarshall($rubricBlockRefElt);
            }
            
            $component->setRubricBlockRefs($rubricBlockRefs);
        }
        
        return $component;
    }
}