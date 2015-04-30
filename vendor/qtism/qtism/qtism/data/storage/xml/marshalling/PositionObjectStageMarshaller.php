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

use qtism\data\content\interactions\PositionObjectInteractionCollection;
use qtism\data\content\interactions\PositionObjectStage;
use qtism\data\QtiComponent;
use \DOMElement;

/**
 * Marshalling/Unmarshalling implementation for PositionObjectStage.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class PositionObjectStageMarshaller extends Marshaller {
	
	/**
	 * Marshall an PositionObjectStage object into a DOMElement object.
	 * 
	 * @param QtiComponent $component A PositionObjectStage object.
	 * @return DOMElement The according DOMElement object.
	 * @throws MarshallingException
	 */
	protected function marshall(QtiComponent $component) {
        $element = self::getDOMCradle()->createElement('positionObjectStage');
        $object = $component->getObject();
        $element->appendChild($this->getMarshallerFactory()->createMarshaller($object)->marshall($object));
        
        foreach ($component->getPositionObjectInteractions() as $interaction) {
            $element->appendChild($this->getMarshallerFactory()->createMarshaller($interaction)->marshall($interaction));
        }
        
        return $element;
	}
	
	/**
	 * Unmarshall a DOMElement object corresponding to an positionObjectStage element.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return QtiComponent A PositionObjectStage object.
	 * @throws UnmarshallingException
	 */
	protected function unmarshall(DOMElement $element) {
	    
	    $objectElts = self::getChildElementsByTagName($element, 'object');
	    if (count($objectElts) > 0) {
	        
	        $object = $this->getMarshallerFactory()->createMarshaller($objectElts[0])->unmarshall($objectElts[0]);
	        
	        $positionObjectInteractionElts = self::getChildElementsByTagName($element, 'positionObjectInteraction');
	        if (count($positionObjectInteractionElts) > 0) {
	            
	            $positionObjectInteractions = new PositionObjectInteractionCollection();
	            
	            foreach ($positionObjectInteractionElts as $interactionElt) {
	                $positionObjectInteractions[] = $this->getMarshallerFactory()->createMarshaller($interactionElt)->unmarshall($interactionElt);
	            }
	            
	            return new PositionObjectStage($object, $positionObjectInteractions);
	        }
	        else {
	            $msg = "A 'positionObjectStage' element must contain at least one 'positionObjectInteraction' element, none given.";
	            throw new UnmarshallingException($msg, $element);
	        }
	    }
	    else {
	        $msg = "A 'positionObjectStage' element must contain exactly one 'object' element, none given.";
	        throw new UnmarshallingException($msg, $element);
	    }
	}
	
	public function getExpectedQtiClassName() {
		return 'positionObjectStage';
	}
}
