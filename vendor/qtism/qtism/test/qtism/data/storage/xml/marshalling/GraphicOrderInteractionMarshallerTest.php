<?php

use qtism\data\content\FlowStaticCollection;
use qtism\data\content\xhtml\Object;
use qtism\data\content\interactions\GraphicOrderInteraction;
use qtism\data\content\interactions\HotspotChoiceCollection;
use qtism\common\datatypes\Coords;
use qtism\common\datatypes\Shape;
use qtism\data\content\interactions\HotspotChoice;
use qtism\data\content\TextRun;
use qtism\data\content\InlineStaticCollection;
use qtism\data\content\interactions\Prompt;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class GraphicOrderInteractionMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
        
	    $prompt = new Prompt();
	    $prompt->setContent(new FlowStaticCollection(array(new TextRun('Prompt...'))));
	    
	    $choice1 = new HotspotChoice('choice1', Shape::CIRCLE, new Coords(Shape::CIRCLE, array(0, 0, 15)));
	    $choice2 = new HotspotChoice('choice2', Shape::CIRCLE, new Coords(Shape::CIRCLE, array(2, 2, 15)));
	    $choice3 = new HotspotChoice('choice3', Shape::CIRCLE, new Coords(Shape::CIRCLE, array(4, 4, 15)));
	    $choices = new HotspotChoiceCollection(array($choice1, $choice2, $choice3));
	    
	    $object = new Object('my-img.png', 'image/png');
	    
	    $graphicOrderInteraction = new GraphicOrderInteraction('RESPONSE', $object, $choices, 'my-graphicOrder');
	    $graphicOrderInteraction->setPrompt($prompt);
	    $graphicOrderInteraction->setMinChoices(2);
	    $graphicOrderInteraction->setMaxChoices(3);
	    
        $element = $this->getMarshallerFactory()->createMarshaller($graphicOrderInteraction)->marshall($graphicOrderInteraction);
        
        $dom = new DOMDocument('1.0', 'UTF-8');
        $element = $dom->importNode($element, true);
        $this->assertEquals('<graphicOrderInteraction id="my-graphicOrder" responseIdentifier="RESPONSE" minChoices="2" maxChoices="3"><prompt>Prompt...</prompt><object data="my-img.png" type="image/png"/><hotspotChoice identifier="choice1" shape="circle" coords="0,0,15"/><hotspotChoice identifier="choice2" shape="circle" coords="2,2,15"/><hotspotChoice identifier="choice3" shape="circle" coords="4,4,15"/></graphicOrderInteraction>', $dom->saveXML($element));
	}
	
	public function testUnmarshall() {
         $element = $this->createDOMElement('
	            <graphicOrderInteraction id="my-graphicOrder" responseIdentifier="RESPONSE" minChoices="2" maxChoices="3"><prompt>Prompt...</prompt><object data="my-img.png" type="image/png"/><hotspotChoice identifier="choice1" shape="circle" coords="0,0,15"/><hotspotChoice identifier="choice2" shape="circle" coords="2,2,15"/><hotspotChoice identifier="choice3" shape="circle" coords="4,4,15"/></graphicOrderInteraction>
         ');
        
         $component = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
         $this->assertInstanceOf('qtism\\data\\content\\interactions\\GraphicOrderInteraction', $component);
         $this->assertEquals('my-graphicOrder', $component->getId());
         $this->assertEquals('RESPONSE', $component->getResponseIdentifier());
         $this->assertEquals(2, $component->getMinChoices());
         $this->assertEquals(3, $component->getMaxChoices());
         
         $this->assertTrue($component->hasPrompt());
         $promptContent = $component->getPrompt()->getContent();
         $this->assertEquals('Prompt...', $promptContent[0]->getContent());
         
         $object = $component->getObject();
         $this->assertEquals('my-img.png', $object->getData());
         $this->assertEquals('image/png', $object->getType());
         
         $choices = $component->getHotspotChoices();
         $this->assertEquals(3, count($choices));
         $this->assertEquals('choice1', $choices[0]->getIdentifier());
         $this->assertEquals('choice2', $choices[1]->getIdentifier());
         $this->assertEquals('choice3', $choices[2]->getIdentifier());
	}
}