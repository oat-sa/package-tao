<?php

use qtism\data\expressions\RandomFloat;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class RandomFloatMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {

		$min = 1;
		$max = '{tplVariable1}';
		$component = new RandomFloat($min, $max);
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML('<randomFloat xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" min="1.3" max="{tplVariable1}"/>');
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\expressions\\RandomFloat', $component);
		$this->assertEquals($component->getMin(), 1.3);
		$this->assertEquals($component->getMax(), '{tplVariable1}');
	}
}
