<?php

use qtism\data\content\interactions\EndAttemptInteraction;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class EndAttemptInteractionMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
	    $endAttemptInteraction = new EndAttemptInteraction('BOOL_RESP', 'End the attempt now!', 'my-end', 'ending');
        $element = $this->getMarshallerFactory()->createMarshaller($endAttemptInteraction)->marshall($endAttemptInteraction);
        
        $dom = new DOMDocument('1.0', 'UTF-8');
        $element = $dom->importNode($element, true);
        $this->assertEquals('<endAttemptInteraction id="my-end" class="ending" responseIdentifier="BOOL_RESP" title="End the attempt now!"/>', $dom->saveXML($element));
	}
	
	public function testUnmarshall() {
        $element = $this->createDOMElement('
            <endAttemptInteraction id="my-end" class="ending" responseIdentifier="BOOL_RESP" title="End the attempt now!"/>
        ');
        
        $component = $this->getMarshallerFactory()->createMarshaller($element)->unmarshall($element);
        $this->assertInstanceOf('qtism\\data\\content\\interactions\\EndAttemptInteraction', $component);
        $this->assertEquals('my-end', $component->getId());
        $this->assertEquals('ending', $component->getClass());
        $this->assertEquals('BOOL_RESP', $component->getResponseIdentifier());
        $this->assertEquals('End the attempt now!', $component->getTitle());
	}
}