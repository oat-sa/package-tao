<?php

use qtism\data\content\FlowStaticCollection;
use qtism\data\content\TextRun;
use qtism\data\content\InlineStaticCollection;
use qtism\data\content\interactions\Prompt;
use qtism\data\content\interactions\UploadInteraction;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class UploadInteractionMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
	    
	    $uploadInteraction = new UploadInteraction('RESPONSE', 'my-upload');
	    $prompt = new Prompt();
	    $prompt->setContent(new FlowStaticCollection(array(new TextRun('Prompt...'))));
	    $uploadInteraction->setPrompt($prompt);
	    
        $element = $this->getMarshallerFactory()->createMarshaller($uploadInteraction)->marshall($uploadInteraction);
        
        $dom = new DOMDocument('1.0', 'UTF-8');
        $element = $dom->importNode($element, true);
        $this->assertEquals('<uploadInteraction id="my-upload" responseIdentifier="RESPONSE"><prompt>Prompt...</prompt></uploadInteraction>', $dom->saveXML($element));
	}
	
	public function testUnmarshall() {
        $element = $this->createDOMElement('
            <uploadInteraction id="my-upload" responseIdentifier="RESPONSE"><prompt>Prompt...</prompt></uploadInteraction>    
        ');
        
        $component = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
        $this->assertInstanceOf('qtism\\data\\content\\interactions\\UploadInteraction', $component);
        $this->assertEquals('my-upload', $component->getId());
        $this->assertEquals('RESPONSE', $component->getResponseIdentifier());
        
        $this->assertTrue($component->hasPrompt());
        $promptContent = $component->getPrompt()->getContent();
        $this->assertEquals('Prompt...', $promptContent[0]->getContent());
	}
}