<?php

use qtism\data\expressions\MathConstant;
use qtism\data\expressions\MathEnumeration;

require_once (dirname(__FILE__) . '/../../../../../QtiSmTestCase.php');

class MathConstantMarshallerTest extends QtiSmTestCase {

	public function testMarshall() {

		$name = MathEnumeration::PI;
		
		$component = new MathConstant($name);
		$marshaller = $this->getMarshallerFactory()->createMarshaller($component);
		$element = $marshaller->marshall($component);
		
		$this->assertInstanceOf('\\DOMElement', $element);
		$this->assertEquals('mathConstant', $element->nodeName);
		$this->assertEquals('pi', $element->getAttribute('name'));
	}
	
	public function testUnmarshall() {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->loadXML('<mathConstant xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" name="pi"/>');
		$element = $dom->documentElement;
		
		$marshaller = $this->getMarshallerFactory()->createMarshaller($element);
		$component = $marshaller->unmarshall($element);
		
		$this->assertInstanceOf('qtism\\data\\expressions\\MathConstant', $component);
		$this->assertEquals($component->getName(), MathEnumeration::PI);
	}
}
