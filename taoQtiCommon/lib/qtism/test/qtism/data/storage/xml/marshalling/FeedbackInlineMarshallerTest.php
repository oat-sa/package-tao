<?php

use qtism\data\ShowHide;
use qtism\data\content\FeedbackInline;
use qtism\data\content\TextRun;
use qtism\data\content\InlineCollection;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class FeedbackInlineMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
	    
	    $content = new InlineCollection(array(new TextRun('This is text...')));
	    $feedback = new FeedbackInline('outcome1', 'please_hide_me', ShowHide::HIDE, 'my-feedback', 'super feedback');
	    $feedback->setContent($content);
	    
	    $element = $this->getMarshallerFactory()->createMarshaller($feedback)->marshall($feedback);
	    
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    $this->assertEquals('<feedbackInline id="my-feedback" class="super feedback" outcomeIdentifier="outcome1" identifier="please_hide_me" showHide="hide">This is text...</feedbackInline>', $dom->saveXML($element));
	}
	
	public function testUnmarshall() {
	    $element = $this->createDOMElement('
	        <feedbackInline id="my-feedback" class="super feedback" outcomeIdentifier="outcome1" identifier="please_hide_me" showHide="hide">This is text...</feedbackInline>
	    ');
	    
	    $component = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
	    $this->assertInstanceOf('qtism\\data\\content\\FeedbackInline', $component);
	    $this->assertEquals('my-feedback', $component->getId());
	    $this->assertEquals('super feedback', $component->getClass());
	    $this->assertEquals('outcome1', $component->getOutcomeIdentifier());
	    $this->assertEquals('please_hide_me', $component->getIdentifier());
	    $this->assertEquals(ShowHide::HIDE, $component->getShowHide());
	    
	    $content = $component->getContent();
	    $this->assertEquals(1, count($content));
	    $this->assertEquals('This is text...', $content[0]->getContent());
	}
}