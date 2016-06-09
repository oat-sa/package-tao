<?php

use qtism\data\expressions\NullValue;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class NullValueMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {

		$component = new NullValue();
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('null', $element->nodeName);
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML('<null xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1"/>');
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\expressions\\NullValue', $component);
	}
}
