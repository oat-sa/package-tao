<?php

use qtism\data\expressions\Correct;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class CorrectMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
		$identifier = 'myCorrect1';
		
		$component = new Correct($identifier);
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('correct', $element->nodeName);
		$this->assertEquals($identifier, $element->getAttribute('identifier'));
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML('<correct xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" identifier="myCorrect1"/>');
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\expressions\\Correct', $component);
		$this->assertEquals($component->getIdentifier(), 'myCorrect1');
	}
}
