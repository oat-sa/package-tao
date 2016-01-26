<?php

use qtism\data\rules\Ordering;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class OrderingMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {

		$shuffle = true;
		$component = new Ordering($shuffle);
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('true', $element->getAttribute('shuffle'));
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML('<ordering xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" shuffle="false"/>');
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\Rules\\Ordering', $component);
		$this->assertEquals($component->getShuffle(), false);
	}
}
