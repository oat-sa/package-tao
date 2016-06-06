<?php

use qtism\data\state\Weight;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class WeightMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {
		
		$identifier = 'myWeight1';
		$value = 3.45;
		
		$component = new Weight($identifier, $value);
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('weight', $element->nodeName);
		$this->assertEquals($identifier, $element->getAttribute('identifier'));
		$this->assertEquals($value . '', $element->getAttribute('value'));
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML('<weight xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" identifier="myWeight1" value="3.45"/>');
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\state\\Weight', $component);
		$this->assertEquals($component->getIdentifier(), 'myWeight1');
		$this->assertEquals($component->getValue(), 3.45);
	}
}
