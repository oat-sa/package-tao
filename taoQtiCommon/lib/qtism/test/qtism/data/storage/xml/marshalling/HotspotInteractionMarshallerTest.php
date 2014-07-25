<?php

use qtism\data\content\FlowStaticCollection;
use qtism\common\datatypes\Coords;
use qtism\data\content\interactions\HotspotInteraction;
use qtism\data\content\xhtml\Object;
use qtism\common\datatypes\Shape;
use qtism\data\content\interactions\HotspotChoice;
use qtism\data\content\interactions\HotspotChoiceCollection;
use qtism\data\content\TextRun;
use qtism\data\content\InlineStaticCollection;
use qtism\data\content\interactions\Prompt;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class HotspotInteractionMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
        $prompt = new Prompt();
        $prompt->setContent(new FlowStaticCollection(array(new TextRun('Prompt...'))));
        
        $choice1 = new HotspotChoice('hotspotchoice1', Shape::CIRCLE, new Coords(Shape::CIRCLE, array(77, 115, 8)));
        $choice2 = new HotspotChoice('hotspotchoice2', Shape::CIRCLE, new Coords(Shape::CIRCLE, array(118, 184, 8)));
        $choice3 = new HotspotChoice('hotspotchoice3', Shape::CIRCLE, new Coords(Shape::CIRCLE, array(150, 235, 8)));
        
        $object = new Object('./img/img.png', 'image/png');
	    $hotspotInteraction = new HotspotInteraction('RESPONSE', $object, 1, new HotspotChoiceCollection(array($choice1, $choice2, $choice3)), 'my-hotspot');
	    $hotspotInteraction->setPrompt($prompt);
        
        $element = $this->getMarshallerFactory()->createMarshaller($hotspotInteraction)->marshall($hotspotInteraction);
        
        $dom = new DOMDocument('1.0', 'UTF-8');
        $element = $dom->importNode($element, true);
        
        $this->assertEquals('<hotspotInteraction id="my-hotspot" responseIdentifier="RESPONSE" maxChoices="1"><prompt>Prompt...</prompt><object data="./img/img.png" type="image/png"/><hotspotChoice identifier="hotspotchoice1" shape="circle" coords="77,115,8"/><hotspotChoice identifier="hotspotchoice2" shape="circle" coords="118,184,8"/><hotspotChoice identifier="hotspotchoice3" shape="circle" coords="150,235,8"/></hotspotInteraction>', $dom->saveXML($element));
	}
	
	public function testUnmarshall() {
        $element = $this->createDOMElement('
            <hotspotInteraction id="my-hotspot" responseIdentifier="RESPONSE" maxChoices="1"><prompt>Prompt...</prompt><object data="./img/img.png" type="image/png"/><hotspotChoice identifier="hotspotchoice1" shape="circle" coords="77,115,8"/><hotspotChoice identifier="hotspotchoice2" shape="circle" coords="118,184,8"/><hotspotChoice identifier="hotspotchoice3" shape="circle" coords="150,235,8"/></hotspotInteraction>
        ');
        
        $component = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
        $this->assertInstanceOf('qtism\\data\\content\\interactions\\HotspotInteraction', $component);
        $this->assertEquals('RESPONSE', $component->getResponseIdentifier());
        $this->assertEquals('my-hotspot', $component->getId());
        $this->assertEquals(1, $component->getMaxChoices());
        $this->assertEquals(0, $component->getMinChoices());
        
        $this->assertTrue($component->hasPrompt());
        $promptContent = $component->getPrompt()->getContent();
        $this->assertEquals('Prompt...', $promptContent[0]->getContent());
        
        $object = $component->getObject();
        $this->assertEquals('./img/img.png', $object->getData());
        $this->assertEquals('image/png', $object->getType());
        
        $choices = $component->getHotspotChoices();
        $this->assertEquals(3, count($choices));
        $this->assertEquals('hotspotchoice1', $choices[0]->getIdentifier());
        $this->assertEquals('hotspotchoice2', $choices[1]->getIdentifier());
        $this->assertEquals('hotspotchoice3', $choices[2]->getIdentifier());
	}
}