<?php

use qtism\data\ShowHide;

use qtism\data\content\ModalFeedback;
use qtism\data\content\TextRun;
use qtism\data\content\FlowStaticCollection;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class ModalFeedbackMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
	    $content = new FlowStaticCollection(array(new TextRun('Please show me!')));
	    $modalFeedback = new ModalFeedback('outcome1', 'hello', $content, 'Modal Feedback Example');
	    
	    $element = $this->getMarshallerFactory()->createMarshaller($modalFeedback)->marshall($modalFeedback);
	    
	    $dom = new DOMDocument('1.0', 'UTF-8');
	    $element = $dom->importNode($element, true);
	    
	    $this->assertEquals('<modalFeedback outcomeIdentifier="outcome1" identifier="hello" showHide="show" title="Modal Feedback Example">Please show me!</modalFeedback>', $dom->saveXML($element));
	}
	
	public function testUnmarshall() {
	    $element = $this->createDOMElement('
	        <modalFeedback outcomeIdentifier="outcome1" identifier="hello" showHide="show" title="Modal Feedback Example">Please show me!</modalFeedback>
	    ');
	    
	    $modalFeedback = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
	    $this->assertInstanceOf('qtism\\data\\content\\ModalFeedback', $modalFeedback);
	    $this->assertEquals('outcome1', $modalFeedback->getOutcomeIdentifier());
	    $this->assertEquals('hello', $modalFeedback->getIdentifier());
	    $this->assertEquals(ShowHide::SHOW, $modalFeedback->getShowHide());
	    $this->assertEquals('Modal Feedback Example', $modalFeedback->getTitle());
	    
	    $content = $modalFeedback->getContent();
	    $this->assertEquals(1, count($content));
	    $this->assertEquals('Please show me!', $content[0]->getContent());
	}
}