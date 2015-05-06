<?php

use qtism\data\content\FlowStaticCollection;
use qtism\data\content\interactions\GraphicAssociateInteraction;
use qtism\data\content\interactions\AssociableHotspotCollection;
use qtism\common\datatypes\Coords;
use qtism\common\datatypes\Shape;
use qtism\data\content\interactions\AssociableHotspot;
use qtism\data\content\xhtml\Object;
use qtism\data\content\TextRun;
use qtism\data\content\InlineStaticCollection;
use qtism\data\content\interactions\Prompt;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class GraphicAssociateInteractionMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
        
	    $prompt = new Prompt();
	    $prompt->setContent(new FlowStaticCollection(array(new TextRun('Prompt...'))));
	    
	    $object = new Object('myimg.png', 'image/png');
	    
	    $choice1 = new AssociableHotspot('choice1', 2, Shape::CIRCLE, new Coords(Shape::CIRCLE, array(0, 0, 15)));
	    $choice1->setMatchMin(1);
	    $choice2 = new AssociableHotspot('choice2', 1, Shape::CIRCLE, new Coords(Shape::CIRCLE, array(2, 2, 15)));
	    $choice3 = new AssociableHotspot('choice3', 1, Shape::CIRCLE, new Coords(Shape::CIRCLE, array(4, 4, 15)));
	    $choices = new AssociableHotspotCollection(array($choice1, $choice2, $choice3));
	    
	    $graphicAssociateInteraction = new GraphicAssociateInteraction('RESPONSE', $object, $choices, 'prout');
	    $graphicAssociateInteraction->setPrompt($prompt);
	    
        $element = $this->getMarshallerFactory()->createMarshaller($graphicAssociateInteraction)->marshall($graphicAssociateInteraction);
        
        $dom = new DOMDocument('1.0', 'UTF-8');
        $element = $dom->importNode($element, true);
        
        $this->assertEquals('<graphicAssociateInteraction id="prout" responseIdentifier="RESPONSE"><prompt>Prompt...</prompt><object data="myimg.png" type="image/png"/><associableHotspot identifier="choice1" shape="circle" coords="0,0,15" matchMax="2" matchMin="1"/><associableHotspot identifier="choice2" shape="circle" coords="2,2,15" matchMax="1"/><associableHotspot identifier="choice3" shape="circle" coords="4,4,15" matchMax="1"/></graphicAssociateInteraction>', $dom->saveXML($element));
	}
	
	public function testUnmarshall() {
        $element = $this->createDOMElement('
            <graphicAssociateInteraction responseIdentifier="RESPONSE" id="prout"><prompt>Prompt...</prompt><object data="myimg.png" type="image/png"/><associableHotspot identifier="choice1" shape="circle" coords="0,0,15" matchMax="2" matchMin="1"/><associableHotspot identifier="choice2" shape="circle" coords="2,2,15" matchMax="1"/><associableHotspot identifier="choice3" shape="circle" coords="4,4,15" matchMax="1"/></graphicAssociateInteraction>
        ');
        
        $component = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
        $this->assertInstanceOf('qtism\\data\\content\\interactions\\GraphicAssociateInteraction', $component);
        $this->assertEquals('RESPONSE', $component->getResponseIdentifier());
        $this->assertEquals('prout', $component->getId());
        
        $this->assertTrue($component->hasPrompt());
        $promptContent = $component->getPrompt()->getContent();
        $this->assertEquals('Prompt...', $promptContent[0]->getContent());
        
        $object = $component->getObject();
        $this->assertEquals('myimg.png', $object->getData());
        $this->assertEquals('image/png', $object->getType());
        
        $choices = $component->getAssociableHotspots();
        $this->assertEquals(3, count($choices));
        
        $this->assertEquals('choice1', $choices[0]->getIdentifier());
        $this->assertEquals(2, $choices[0]->getMatchMax());
        $this->assertEquals(1, $choices[0]->getMatchMin());
        $this->assertEquals(Shape::CIRCLE, $choices[0]->getShape());
        $this->assertTrue($choices[0]->getCoords()->equals(new Coords(Shape::CIRCLE, array(0, 0, 15))));
        
        $this->assertEquals('choice2', $choices[1]->getIdentifier());
        $this->assertEquals(1, $choices[1]->getMatchMax());
        $this->assertEquals(0, $choices[1]->getMatchMin());
        $this->assertEquals(Shape::CIRCLE, $choices[1]->getShape());
        $this->assertTrue($choices[1]->getCoords()->equals(new Coords(Shape::CIRCLE, array(2, 2, 15))));
        
        $this->assertEquals('choice3', $choices[2]->getIdentifier());
        $this->assertEquals(1, $choices[2]->getMatchMax());
        $this->assertEquals(0, $choices[2]->getMatchMin());
        $this->assertEquals(Shape::CIRCLE, $choices[2]->getShape());
        $this->assertTrue($choices[2]->getCoords()->equals(new Coords(Shape::CIRCLE, array(4, 4, 15))));
	}
}