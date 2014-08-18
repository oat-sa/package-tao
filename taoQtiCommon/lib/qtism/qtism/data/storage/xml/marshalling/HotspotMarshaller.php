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


use qtism\data\storage\Utils;
use qtism\data\content\interactions\HotspotChoice;
use qtism\data\content\interactions\AssociableHotspot;
use qtism\common\datatypes\Shape;
use qtism\data\ShowHide;
use qtism\data\QtiComponent;
use \DOMElement;

/**
 * Marshalling/Unmarshalling implementation for HotspotChoice/AssociableHotspot.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class HotspotMarshaller extends Marshaller {
	
	/**
	 * Marshall a HotspotChoice/AssociableHotspot object into a DOMElement object.
	 * 
	 * @param QtiComponent $component A HotspotChoice/AssociableHotspot object.
	 * @return DOMElement The according DOMElement object.
	 * @throws MarshallingException
	 */
	protected function marshall(QtiComponent $component) {
        $element = self::getDOMCradle()->createElement($component->getQtiClassName());
        self::setDOMElementAttribute($element, 'identifier', $component->getIdentifier());
        self::setDOMElementAttribute($element, 'shape', Shape::getNameByConstant($component->getShape()));
        self::setDOMElementAttribute($element, 'coords', $component->getCoords()->__toString());
        
        if ($component->isFixed() === true) {
            self::setDOMElementAttribute($element, 'fixed', true);
        }
        
        if ($component->hasTemplateIdentifier() === true) {
            self::setDOMElementAttribute($element, 'templateIdentifier', $component->getTemplateIdentifier());
        }
        
        if ($component->getShowHide() === ShowHide::HIDE) {
            self::setDOMElementAttribute($element, 'showHide', ShowHide::getNameByConstant($component->getShowHide()));
        }
        
        if ($component->hasHotspotLabel() === true) {
            self::setDOMElementAttribute($element, 'hotspotLabel', $component->getHotspotLabel());
        }
        
        if ($component instanceof AssociableHotspot) {
            self::setDOMElementAttribute($element, 'matchMax', $component->getMatchMax());
            
            if ($component->getMatchMin() !== 0) {
                self::setDOMElementAttribute($element, 'matchMin', $component->getMatchMin());
            }
        }
        
        self::fillElement($element, $component);
        return $element;
	}
	
	/**
	 * Unmarshall a DOMElement object corresponding to a hotspotChoice/associableHotspot element.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return QtiComponent A HotspotChoice/AssociableHotspot object.
	 * @throws UnmarshallingException
	 */
	protected function unmarshall(DOMElement $element) {
		
	    if (($identifier = self::getDOMElementAttributeAs($element, 'identifier')) !== null) {
	        
	        if (($shape = self::getDOMElementAttributeAs($element, 'shape')) !== null) {
	            
	            if (($coords = self::getDOMElementAttributeAs($element, 'coords')) !== null) {
	                
	                $shape = Shape::getConstantByName($shape);
	                if ($shape === false) {
	                    $msg = "The value of the mandatory attribute 'shape' is not a value from the 'shape' enumeration.";
	                    throw new UnmarshallingException($msg, $element);
	                }
	                
	                try {
	                    $coords = Utils::stringToCoords($coords, $shape);
	                }
	                catch (UnexpectedValueException $e) {
	                    $msg = "The coordinates 'coords' of element '" . $element->localName . "' are not valid regarding the shape they are bound to.";
	                    throw new UnmarshallingException($msg, $element, $e);
	                }
	                catch (InvalidArgumentException $e) {
	                    $msg = "The coordinates 'coords' of element '" . $element->localName . "' could not be converted.";
	                    throw new UnmarshallingException($msg, $element, $e);
	                }
	                
	                if ($element->localName === 'hotspotChoice') {
	                    $component = new HotspotChoice($identifier, $shape, $coords);
	                }
	                else {
	                    if (($matchMax = self::getDOMElementAttributeAs($element, 'matchMax', 'integer')) !== null) {
	                        $component = new AssociableHotspot($identifier, $matchMax, $shape, $coords);
	                        
	                        if (($matchMin = self::getDOMElementAttributeAs($element, 'matchMin', 'integer')) !== null) {
	                            $component->setMatchMin($matchMin);
	                        }
	                    }
	                    else {
	                        $msg = "The mandatory attribute 'matchMax' is missing from element 'associableHotspot'.";
	                        throw new UnmarshallingException($msg, $element);
	                    }
	                }
	                
	                if (($hotspotLabel = self::getDOMElementAttributeAs($element, 'hotspotLabel')) !== null) {
	                    $component->setHotspotLabel($hotspotLabel);
	                }
	                
	                if (($fixed = self::getDOMElementAttributeAs($element, 'fixed', 'boolean')) !== null) {
	                    $component->setFixed($fixed);
	                }
	                
	                if (($templateIdentifier = self::getDOMElementAttributeAs($element, 'templateIdentifier')) !== null) {
	                    $component->setTemplateIdentifier($templateIdentifier);
	                }
	                
	                if (($showHide = self::getDOMElementAttributeAs($element, 'showHide')) !== null) {
	                    
	                    if (($showHide = ShowHide::getConstantByName($showHide)) !== false) {
	                        $component->setShowHide($showHide);
	                    }
	                    else {
	                        $msg = "The value of the 'showHide' attribute of element '" . $element->localName . "' is not a value from the 'showHide' enumeration.";
	                        throw new UnmarshallingException($msg, $element);
	                    }
	                }
	                
	                self::fillBodyElement($component, $element);
	                return $component;
	            }
	            else {
	                $msg = "The mandatory attribute 'coords' is missing from element '" . $element->localName . "'.";
	                throw new UnmarshallingException($msg, $element);
	            }
	        }
	        else {
	            $msg = "The mandatory attribute 'shape' is missing from element '" . $element->localName . "'.";
	            throw new UnmarshallingException($msg, $element);
	        }
	    }
	    else {
	        $msg = "The mandatory attribute 'identifier' is missing from element '" . $element->localName . "'.";
	    }
	}
	
	public function getExpectedQtiClassName() {
		return '';
	}
}
