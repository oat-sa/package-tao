<?php

use qtism\data\rules\ExitTest;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class ExitTestMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {

		$component = new ExitTest();
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('exitTest', $element->nodeName);
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML('<exitTest xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1"/>');
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\rules\\ExitTest', $component);
		$this->assertEquals('exitTest', $component->getQtiClassName());
	}
}
