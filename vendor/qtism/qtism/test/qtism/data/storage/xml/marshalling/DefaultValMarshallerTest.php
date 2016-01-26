<?php

use qtism\data\expressions\DefaultVal;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class DefaultValMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
		$identifier = 'myDefault1';
		
		$component = new DefaultVal($identifier);
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('default', $element->nodeName);
		$this->assertEquals($identifier, $element->getAttribute('identifier'));
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML('<default xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" identifier="myDefault1"/>');
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\expressions\\DefaultVal', $component);
		$this->assertEquals($component->getIdentifier(), 'myDefault1');
	}
}
