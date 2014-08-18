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

use qtism\data\content\interactions\GraphicGapMatchInteraction;
use qtism\data\content\interactions\GapImgCollection;
use qtism\data\content\interactions\AssociableHotspotCollection;
use qtism\data\QtiComponent;
use \DOMElement;
use \InvalidArgumentException;

/**
 * The Marshaller implementation for GraphicGapMatchInteraction elements of the content model.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class GraphicGapMatchInteractionMarshaller extends Marshaller {
    
    /**
	 * Unmarshall a DOMElement object corresponding to a graphicGapMatchInteraction element.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return QtiComponent A GraphicGapMatchInteraction object.
	 * @throws UnmarshallingException
	 */
	protected function unmarshall(DOMElement $element) {
            
        if (($responseIdentifier = self::getDOMElementAttributeAs($element, 'responseIdentifier')) !== null) {
            
            $objectElts = self::getChildElementsByTagName($element, 'object');
            if (count($objectElts) > 0) {
                
                $object = $this->getMarshallerFactory()->createMarshaller($objectElts[0])->unmarshall($objectElts[0]);
                
                $associableHotspotElts = self::getChildElementsByTagName($element, 'associableHotspot');
                
                if (count($associableHotspotElts) > 0) {
                    
                    $associableHotspots = new AssociableHotspotCollection();
                    
                    foreach ($associableHotspotElts as $associableHotspotElt) {
                        $associableHotspots[] = $this->getMarshallerFactory()->createMarshaller($associableHotspotElt)->unmarshall($associableHotspotElt);
                    }
                    
                    $gapImgElts = self::getChildElementsByTagName($element, 'gapImg');
                    
                    if (count($gapImgElts) > 0) {
                        
                        $gapImgs = new GapImgCollection();
                        
                        foreach ($gapImgElts as $gapImgElt) {
                            $gapImgs[] = $this->getMarshallerFactory()->createMarshaller($gapImgElt)->unmarshall($gapImgElt);
                        }
                        
                        $component = new GraphicGapMatchInteraction($responseIdentifier, $object, $gapImgs, $associableHotspots);
                        
                        $promptElts = self::getChildElementsByTagName($element, 'prompt');
                        if (count($promptElts) > 0) {
                            $promptElt = $promptElts[0];
                            $prompt = $this->getMarshallerFactory()->createMarshaller($promptElt)->unmarshall($promptElt);
                            $component->setPrompt($prompt);
                        }
                        
                        if (($xmlBase = self::getXmlBase($element)) !== false) {
                            $component->setXmlBase($xmlBase);
                        }
                        
                        self::fillBodyElement($component, $element);
                        return $component;
                    }
                    else {
                        $msg = "A 'graphicGapMatchInteraction' element must contain at least one 'gapImg' element, none given.";
                        throw new UnmarshallingException($msg, $element);
                    }
                    
                    
                }
                else {
                    $msg = "A 'graphiGapMatchInteraction' element must contain at least one 'associableHotspot' element, none given.";
                    throw new UnmarshallingException($msg, $element);
                }
            }
            else {
                $msg = "A 'graphicGapMatchInteraction' element must contain exactly one 'object' element, none given.";
                throw new UnmarshallingException($msg, $element);
            }
        }
        else {
            $msg = "The mandatory 'responseIdentifier' attribute is missing from the 'graphicGapMatchInteraction' element.";
            throw new UnmarshallingException($msg, $element);
        }
    }
    
    /**
	 * Marshall an GraphicGapMatchInteraction object into a DOMElement object.
	 * 
	 * @param QtiComponent $component A GraphicGapMatchInteraction object.
	 * @return DOMElement The according DOMElement object.
	 * @throws MarshallingException
	 */
	protected function marshall(QtiComponent $component) {
        
        $element = self::getDOMCradle()->createElement('graphicGapMatchInteraction');
        self::fillElement($element, $component);
        self::setDOMElementAttribute($element, 'responseIdentifier', $component->getResponseIdentifier());
        
        if ($component->hasPrompt() === true) {
            $element->appendChild($this->getMarshallerFactory()->createMarshaller($component->getPrompt())->marshall($component->getPrompt()));
        }
        
        if ($component->hasXmlBase() === true) {
            self::setXmlBase($element, $component->getXmlBase());
        }
        
        $element->appendChild($this->getMarshallerFactory()->createMarshaller($component->getObject())->marshall($component->getObject()));
        
        foreach ($component->getGapImgs() as $gapImg) {
            $element->appendChild($this->getMarshallerFactory()->createMarshaller($gapImg)->marshall($gapImg));
        }
        
        foreach ($component->getAssociableHotspots() as $associableHotspot) {
            $element->appendChild($this->getMarshallerFactory()->createMarshaller($associableHotspot)->marshall($associableHotspot));
        }
        
        return $element;
    }
    
    public function getExpectedQtiClassName() {
		return 'graphicGapMatchInteraction';
	}
}