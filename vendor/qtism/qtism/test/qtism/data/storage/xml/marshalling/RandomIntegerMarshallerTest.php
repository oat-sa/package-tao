<?php

use qtism\data\expressions\RandomInteger;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class RandomIntegerMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {

		$min = 3;
		$max = '{tplVariable1}';
		$step = 2;
		$component = new RandomInteger($min, $max, $step);
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals($min . '', $element->getAttribute('min'));
		$this->assertEquals($max, $element->getAttribute('max'));
		$this->assertEquals($step . '', $element->getAttribute('step'));
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML('<randomInteger xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" min="3" max="{tplVariable1}" step="2"/>');
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\expressions\\RandomInteger', $component);
		$this->assertEquals($component->getMin(), 3);
		$this->assertEquals($component->getMax(), '{tplVariable1}');
		$this->assertEquals($component->getStep(), 2);
	}
}
