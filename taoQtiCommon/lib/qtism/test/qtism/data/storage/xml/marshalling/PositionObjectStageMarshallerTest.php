<?php

use qtism\data\content\interactions\PositionObjectInteractionCollection;
use qtism\data\content\interactions\PositionObjectStage;
use qtism\common\datatypes\Point;
use qtism\data\content\interactions\PositionObjectInteraction;
use qtism\data\content\xhtml\Object;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class PositionObjectStageMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
	    $interactionObject = new Object('airplane.jpg', 'image/jpeg');
	    $interactionObject->setHeight(16);
	    $interactionObject->setWidth(16);
	    
	    $interaction = new PositionObjectInteraction('RESPONSE', $interactionObject);
	    $interaction->setCenterPoint(new Point(8, 8));
	    
	    $stageObject = new Object('country.jpg', 'image/jpeg');
	    $positionObjectStage = new PositionObjectStage($stageObject, new PositionObjectInteractionCollection(array($interaction)));
	    
        $element = $this->getMarshallerFactory()->createMarshaller($positionObjectStage)->marshall($positionObjectStage);
        
        $dom = new DOMDocument('1.0', 'UTF-8');
        $element = $dom->importNode($element, true);
        $this->assertEquals('<positionObjectStage><object data="country.jpg" type="image/jpeg"/><positionObjectInteraction responseIdentifier="RESPONSE" maxChoices="1" centerPoint="8 8"><object data="airplane.jpg" type="image/jpeg" width="16" height="16"/></positionObjectInteraction></positionObjectStage>', $dom->saveXML($element));
	}
	
	public function testUnmarshall() {
        $element = $this->createDOMElement('
            <positionObjectStage>
                <object data="country.jpg" type="image/jpeg"/>
                <positionObjectInteraction responseIdentifier="RESPONSE" maxChoices="1" centerPoint="8 8">
                    <object data="airplane.jpg" type="image/jpeg" width="16" height="16"/>
                </positionObjectInteraction>
            </positionObjectStage>
        ');
        
        $component = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
        $this->assertInstanceOf('qtism\\data\\content\\interactions\\PositionObjectStage', $component);
        
        $object = $component->getObject();
        $this->assertEquals('country.jpg', $object->getData());
        $this->assertEquals('image/jpeg', $object->getType());
        
        $interactions = $component->getPositionObjectInteractions();
        $this->assertEquals(1, count($interactions));
        
        $interaction = $interactions[0];
        $this->assertEquals('RESPONSE', $interaction->getResponseIdentifier());
        $this->assertEquals(1, $interaction->getMaxChoices());
        $this->assertTrue($interaction->getCenterPoint()->equals(new Point(8, 8)));
        
        $interactionObject = $interaction->getObject();
        $this->assertEquals('airplane.jpg', $interactionObject->getData());
        $this->assertEquals('image/jpeg', $interactionObject->getType());
        $this->assertEquals(16, $interactionObject->getWidth());
        $this->assertEquals(16, $interactionObject->getHeight());
	}
}