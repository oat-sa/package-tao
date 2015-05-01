<?php

use qtism\data\content\FlowStaticCollection;
use qtism\data\content\TextRun;
use qtism\data\content\InlineStaticCollection;
use qtism\data\content\interactions\Prompt;
use qtism\data\content\interactions\MediaInteraction;
use qtism\data\content\xhtml\Object;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class MediaInteractionMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
	    
	    $object = new Object('my-video.mp4', 'video/mp4');
	    $object->setWidth(400);
	    $object->setHeight(300);
	    
	    $mediaInteraction = new MediaInteraction('RESPONSE', false, $object, 'my-media');
	    $mediaInteraction->setMinPlays(1);
	    $mediaInteraction->setMaxPlays(2);
	    $mediaInteraction->setLoop(true);
	    
	    $prompt = new Prompt();
	    $prompt->setContent(new FlowStaticCollection(array(new TextRun('Prompt...'))));
	    $mediaInteraction->setPrompt($prompt);
	    
        $element = $this->getMarshallerFactory()->createMarshaller($mediaInteraction)->marshall($mediaInteraction);
        
        $dom = new DOMDocument('1.0', 'UTF-8');
        $element = $dom->importNode($element, true);
        $this->assertEquals('<mediaInteraction id="my-media" responseIdentifier="RESPONSE" autostart="false" minPlays="1" maxPlays="2" loop="true"><prompt>Prompt...</prompt><object data="my-video.mp4" type="video/mp4" width="400" height="300"/></mediaInteraction>', $dom->saveXML($element));
	}
	
	public function testUnmarshall() {
        $element = $this->createDOMElement('
            <mediaInteraction id="my-media" responseIdentifier="RESPONSE" autostart="false" minPlays="1" maxPlays="2" loop="true"><prompt>Prompt...</prompt><object data="my-video.mp4" type="video/mp4" width="400" height="300"/></mediaInteraction>        
        ');
        
        $component = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
        $this->assertInstanceOf('qtism\\data\\content\\interactions\\MediaInteraction', $component);
        $this->assertEquals('RESPONSE', $component->getResponseIdentifier());
        $this->assertEquals('my-media', $component->getId());
        $this->assertFalse($component->mustAutostart());
        $this->assertEquals(1, $component->getMinPlays());
        $this->assertTrue($component->mustLoop());
        
        $object = $component->getObject();
        $this->assertEquals('my-video.mp4', $object->getData());
        $this->assertEquals('video/mp4', $object->getType());
        $this->assertEquals(400, $object->getWidth());
        $this->assertEquals(300, $object->getHeight());
        
        $this->assertTrue($component->hasPrompt());
        $promptContent = $component->getPrompt()->getContent();
        $this->assertEquals('Prompt...', $promptContent[0]->getContent());
	}
}